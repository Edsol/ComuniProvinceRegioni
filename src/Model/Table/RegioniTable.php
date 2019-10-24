<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Regioni Model
 *
 * @method \App\Model\Entity\Regioni get($primaryKey, $options = [])
 * @method \App\Model\Entity\Regioni newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Regioni[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Regioni|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Regioni saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Regioni patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Regioni[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Regioni findOrCreate($search, callable $callback = null, $options = [])
 */
class RegioniTable extends Table
{
    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('regioni');
        $this->setDisplayField('denominazione');
        $this->setPrimaryKey('id');

        $this->hasMany('Province', [
            'foreignKey' => 'regione_id',
            'bindingKey' => 'id',
            'joinType' => 'INNER'
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->integer('id')
            ->allowEmptyString('id', 'create');

        $validator
            ->scalar('denominazione')
            ->requirePresence('denominazione', 'create')
            ->allowEmptyString('denominazione', false);

        $validator
            ->integer('codice_ripartizione_geografica')
            ->requirePresence('codice_ripartizione_geografica', 'create')
            ->allowEmptyString('codice_ripartizione_geografica', false);

        $validator
            ->scalar('ripartizione_geografica')
            ->requirePresence('ripartizione_geografica', 'create')
            ->allowEmptyString('ripartizione_geografica', false);

        return $validator;
    }

    /**
     * Returns the database connection name to use by default.
     *
     * @return string
     */
    public static function defaultConnectionName()
    {
        return 'comuni_province_regioni';
    }

    public function getId($denominazione=null,$args = []){
        $denominazione = explode("/",$denominazione)[0];
        $regione = $this->find()
        ->where(['denominazione' => strtolower($denominazione)])
        ->first();

        if(!empty($regione)){
            return $regione['id'];
        }else{
            $args['denominazione'] = $denominazione;
            return $this->saveEntity($args);
        }
    }

    public function saveEntity($args = []){
        $args = array_map('strtolower', $args);
        $args['denominazione'] = explode("/",$args['denominazione'])[0];
        
        $regione = $this->newEntity($args);
        if(!$regione['hasErrors']){
            $this->save($regione);
            return $regione['id'];
        }else{
            return null;
        }
    }
}
