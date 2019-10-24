<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Regioni Entity
 *
 * @property int $id
 * @property string $denominazione
 * @property int $codice_ripartizione_geografica
 * @property string $ripartizione_geografica
 */
class Regioni extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array
     */
    protected $_accessible = [
        'denominazione' => true,
        'codice_ripartizione_geografica' => true,
        'ripartizione_geografica' => true
    ];
}
