<?php

namespace dfs\docdoc\models;

/**
 * This is the model class for table "contract_group".
 *
 * The followings are the available columns in table 'contract_group':
 *
 * @property integer $id
 * @property string $name
 * @property integer $kind
 *
 * The followings are the available model relations:
 *
 * @property ContractGroupServiceModel[] $services
 *
 * @method ContractGroupModel findByPk
 * @method ContractGroupModel find
 * @method ContractGroupModel[] findAll
 */
class ContractGroupModel extends \CActiveRecord
{
	/**
	 * Группа услуг по врачам
	 */
	const KIND_DOCTOR       = 0;
	/**
	 * Группа услуг по диагностике
	 */
	const KIND_DIAGNOSTICS  = 1;

	/**
	 * Группа - все специальности
	 */
	const ALL_SECTORS       = 1;
	/**
	 * Группа - все диагностики
	 */
	const ALL_DIAGNOSTICS   = 2;
	/**
	 * Группа - диагностики МРТ/КТ
	 */
	const MRT_KT = 4;

	/**
	 * Returns the static model of the specified AR class.
	 *
	 * @param string $className
	 *
	 * @return ContractGroupModel the static model class
	 */
	public static function model($className = __CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'contract_group';
	}

	/**
	 * Зависимости
	 *
	 * @return array
	 */
	public function relations()
	{
		return [
			'services'   => [
				self::HAS_MANY,
				ContractGroupServiceModel::class,
				'contract_group_id'
			],
		];
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
		);
	}

	/**
	 * Поиск по типу услуг
	 *
	 * @param integer $kind
	 * @return $this
	 */
	public function byKind($kind)
	{
		$this->getDbCriteria()
			->mergeWith([
				'condition' => 'kind = :kind',
				'params' => [':kind' => $kind],
		]);

		return $this;
	}

	/**
	 * Поиск услуге
	 *
	 * @param integer $serviceId
	 * @param integer $kind
	 * @return $this
	 */
	public function forService($serviceId, $kind)
	{
		$criteria = new \CDbCriteria();
		$criteria->with = [
			'services' => [
				'condition' => 'services.service_id = :service_id',
				'params' => [':service_id' => $serviceId],
			]
		];
		$this->getDbCriteria()
			->mergeWith($criteria);

		return $this->byKind($kind);
	}


	/**
	 * Поиск групп услуг по диагностике
	 *
	 * @return $this
	 */
	public function onlyDiagnostic()
	{
		return $this->byKind(self::KIND_DIAGNOSTICS);
	}

	/**
	 * Поиск групп услуг по врачам
	 *
	 * @return $this
	 */
	public function onlyDoctor()
	{
		return $this->byKind(self::KIND_DOCTOR);
	}

	/**
	 * Возвращает список идентификаторов услуг, которые находятся в данной группе
	 * для диагностики возвращаются идентификаторы диагностик и дочерних поддиагностик
	 *
	 * @return int[]
	 */
	public function getServicesInGroup()
	{
		$services = [];

		foreach ($this->services as $s) {
			$services[$s->service_id] = $s->service_id;
		}

		if ($this->kind == self::KIND_DIAGNOSTICS && count($services) && !isset($services[ContractGroupServiceModel::ANY_SERVICE])) {
			$childs = DiagnosticaModel::model()
				->childsForParents($services)
				->findAll(['select' => 'id']);

			foreach ($childs as $c) {
				$services[$c->id] = $c->id;
			}
		}

		return $services;
	}

}
