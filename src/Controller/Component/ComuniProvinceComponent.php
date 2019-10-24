<?php
/* 
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.

 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.

 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Network\Exception\NotAcceptableException;
use Cake\ORM\TableRegistry;

class ComuniProvinceComponent extends Component
{
  protected $_defaultConfig = [];
  protected $fileName = "Elenco-comuni-italiani.csv";

  public function initialize(array $config)
  {
    parent::initialize($config);

    $this->Comuni = TableRegistry::get('Comuni');
    $this->Province = TableRegistry::get('Province');
    $this->Regioni = TableRegistry::get('Regioni');
  }

  public function inizializeDatabase($url = null, $filePath = null, $delimeter = ',')
  {
    if (empty($url)) {
      throw new NotAcceptableException(__("Non è stato specificato un URL."), 400);
    } else {
      if (filter_var($url, FILTER_VALIDATE_URL) == FALSE) {
        throw new NotAcceptableException(__("URL non valido"), 400);
      } else {
        $fullFilePath = $filePath . $this->fileName;

        if (!file_exists($fullFilePath)) {
          file_put_contents($fullFilePath, fopen($url, 'r'));
        }

        $csv = $this->csvToArray($fullFilePath, $delimeter);
        $header = array_keys($csv[0]);

        if (!empty($csv)) {
          foreach ($csv as $row) {
            $regione_id = $this->Regioni->getId($row['Denominazione regione'], [
              'codice_ripartizione_geografica' => $row['Codice Ripartizione Geografica'],
              'ripartizione_geografica' => $row['Ripartizione geografica']
            ]);

            $provincia_id = $this->Province->getId(
              $row[$header[11]],
              [
                'sigla_automobilistica' => $row['Sigla automobilistica'],
                'regione_id' => $regione_id
              ]
            );

            $comune = $this->Comuni->saveEntity(
              $row['Denominazione in italiano'],
              $row['Flag Comune capoluogo di provincia/città metropolitana/libero consorzio'],
              $provincia_id
            );
          }
        }else{
          throw new NotAcceptableException(__("Si è verificato un errore nell'estrazione dati dal CSV"), 400);
        }
        unlink($fullFilePath);

        return true;
      }
    }
  }

  private function csvToArray($filename='', $delimiter=',')
  {
      if(!file_exists($filename) || !is_readable($filename))
          return FALSE;

      $header = NULL;
      $data = array();
      if (($handle = fopen($filename, 'r')) !== FALSE)
      {
          while (($row = fgetcsv($handle, 1000, $delimiter)) !== FALSE)
          {
              $row = array_map("utf8_encode", $row );
              if(!$header):
                  $header = $row;
              else:
                  $data[] = array_combine($header, $row);
              endif;
          }
          fclose($handle);
      }
      return $data;
  }

  public function getCapoluoghi()
  {
    $result = $this->Comuni->find("list")
      ->where(['capoluogo' => true])
      ->toList();

    return $result;
  }

  public function getCapoluoghiRegione($regione = null)
  {
    $regione = strtolower($regione);

    $result = $this->Comuni->find()
      ->where([
        'capoluogo' => true,
        'Regioni.denominazione' => $regione,
      ])
      ->contain(['Province' => ['Regioni']])
      ->toArray();
    // debug($result);exit;
    return $result;
  }

  public function getListaCapoluoghiRegione($regione = null, $visualizza_id = false)
  {
    $regione = strtolower($regione);

    $result = $this->Comuni->find("list")
      ->where([
        'capoluogo' => true,
        'Regioni.denominazione' => $regione
      ])
      ->contain(['Province' => ['Regioni']]);

    if ($visualizza_id) {
      $result = $result->toArray();
    } else {
      $result = $result->toList();
    }
    // debug($result);exit;
    return $result;
  }

  public function getProvinceRegioni()
  {
    $result = $this->Regioni->find()
      ->contain(['Province'])
      ->toArray();

    return $result;
  }

  public function getProvinceRegione($regione = null)
  {
    $regione = strtolower($regione);

    $result = $this->Province->find()
      ->where(['Regioni.denominazione' => $regione])
      ->contain(['Regioni'])
      ->toArray();

    return $result;
  }

  public function getListaProvinceRegione($regione = null, $visualizza_id = false)
  {
    $regione = strtolower($regione);

    $regione = $this->Regioni->find()
      ->where(['denominazione' => $regione])
      ->first();

    $result = $this->Province->find('list')
      ->where(['regione_id' => $regione['id']]);

    if ($visualizza_id) {
      return $result->toArray();
    } else {
      return $result->toList();
    }
  }

  public function getListaComuniRegione($regione = null, $visualizza_id = false)
  {
    $regione = strtolower($regione);

    $province = $this->getListaProvinceRegione($regione);

    $result = $this->Comuni->find('list')
      ->contain(['Province'])
      ->where(['Province.denominazione IN ' => $province]);

    if ($visualizza_id) {
      return $result->toArray();
    } else {
      return $result->toList();
    }
  }

  public function getArrayComuniProvincia($provincia = null)
  {
    if (is_string($provincia)) {
      $provincia = strtolower($provincia);

      if (strlen($provincia) == 2) {
        if ($this->Province->exists(['sigla_automobilistica' => $provincia])) {
          $result = $this->Province->find()
            ->where(['sigla_automobilistica' => $provincia])
            ->contain(['Comuni'])
            ->first();

          return $result['comuni'];
        } else {
          return null;
        }
      } else {
        if ($this->Province->exists(['denominazione' => $provincia])) {
          $result = $this->Province->find()
            ->where(['denominazione' => $provincia])
            ->contain(['Comuni'])
            ->first();

          return $result['comuni'];
        } else {
          return null;
        }
      }
    } else {
      return null;
    }
  }

  public function getListaComuniProvincia($provincia = null, $visualizza_id = false)
  {
    if (is_string($provincia)) {
      $provincia = strtolower($provincia);

      if (strlen($provincia) == 2 and $this->Province->exists(['sigla_automobilistica' => $provincia])) {
        $provincia = $this->Province->find()
          ->where(['sigla_automobilistica' => $provincia])
          ->first();
      } else if (strlen($provincia) > 2 and $this->Province->exists(['denominazione' => $provincia])) {
        $provincia = $this->Province->find()
          ->where(['denominazione' => $provincia])
          ->first();
      }

      if (!empty($provincia)) {
        $result = $this->Comuni->find("list")
          ->where(['provincia_id' => $provincia['id']]);

        if ($visualizza_id) {
          $result = $result->toArray();
        } else {
          $result = $result->toList();
        }
        return $result;
      } else {
        return null;
      }
    }
  }
}
