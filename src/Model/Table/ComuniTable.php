<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Comuni Model
 *
 * @property \App\Model\Table\ProvinciasTable|\Cake\ORM\Association\BelongsTo $Provincias
 *
 * @method \App\Model\Entity\Comuni get($primaryKey, $options = [])
 * @method \App\Model\Entity\Comuni newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Comuni[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Comuni|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Comuni saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Comuni patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Comuni[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Comuni findOrCreate($search, callable $callback = null, $options = [])
 */
class ComuniTable extends Table
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

        $this->setTable('comuni');
        $this->setDisplayField('denominazione');
        $this->setPrimaryKey('id');

        $this->belongsTo('Province', [
            'foreignKey' => 'provincia_id',
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
            ->boolean('capoluogo')
            ->requirePresence('capoluogo', 'create')
            ->allowEmptyString('capoluogo', false);

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
        $rules->add($rules->existsIn(['provincia_id'], 'Province'));

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

    public function saveEntity($denominazione=null,$capoluogo=null,$provincia_id=null){
        if(!$this->exists(['denominazione' => $denominazione])){
            $comune = $this->newEntity([
                'denominazione' => strtolower($denominazione),
                'capoluogo' => $capoluogo,
                'provincia_id' => $provincia_id
            ]);

            return ($this->save($comune))? true : false;
        }else{
            return true;
        }
    }
}
