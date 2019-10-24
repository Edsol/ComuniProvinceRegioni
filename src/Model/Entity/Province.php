<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Province Entity
 *
 * @property int $id
 * @property string $denominazione
 * @property string $sigla_automobilistica
 * @property int $regione_id
 *
 * @property \App\Model\Entity\Regione $regione
 */
class Province extends Entity
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
        'sigla_automobilistica' => true,
        'regione_id' => true,
        'regione' => true
    ];
}
