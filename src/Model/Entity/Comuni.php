<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Comuni Entity
 *
 * @property int $id
 * @property string $denominazione
 * @property bool $capoluogo
 * @property int $provincia_id
 *
 * @property \App\Model\Entity\Provincia $provincia
 */
class Comuni extends Entity
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
        'capoluogo' => true,
        'provincia_id' => true,
        'provincia' => true
    ];
}
