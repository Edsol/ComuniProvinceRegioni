<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Province Model
 *
 * @property \App\Model\Table\RegionesTable|\Cake\ORM\Association\BelongsTo $Regiones
 *
 * @method \App\Model\Entity\Province get($primaryKey, $options = [])
 * @method \App\Model\Entity\Province newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Province[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Province|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Province saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Province patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Province[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Province findOrCreate($search, callable $callback = null, $options = [])
 */
class ProvinceTable extends Table
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

        $this->setTable('province');
        $this->setDisplayField('denominazione');
        $this->setPrimaryKey('id');

        $this->belongsTo('Regioni', [
            'foreignKey' => 'regione_id',
            'joinType' => 'INNER'
        ]);

        $this->hasMany('Comuni', [
            'foreignKey' => 'provincia_id',
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
            ->scalar('sigla_automobilistica')
            ->requirePresence('sigla_automobilistica', 'create')
            ->allowEmptyString('sigla_automobilistica', false);

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->existsIn(['regione_id'], 'Regioni'));

        return $rules;
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

        $provincia = $this->find()
        ->where(['denominazione' => strtolower($denominazione)])
        ->first();

        if(!empty($provincia)){
            return $provincia['id'];
        }else{
            $args['denominazione'] = $denominazione;
            $args = array_map('strtolower', $args);
            return $this->saveEntity($args);
        }
    }

    public function saveEntity($args = []){
        $args['denominazione'] = explode("/",$args['denominazione'])[0];
        $provincia = $this->newEntity($args);
        if(!$provincia['hasErrors']){
            $this->save($provincia);
            return $provincia['id'];
        }else{
            return null;
        }
    }
}
