<?php

namespace dfs\docdoc\models;


/**
 * This is the model class for table "clinic_request_limit".
 *
 * The followings are the available columns in table 'clinic_request_limit':
 *
 * @property int    $id
 * @property int    $group_uid
 * @property int    $limit
 * @property string $date_notice
 * @property int    $clinic_contract_id
 *
 * @property ClinicContractModel clinicContract
 * @property ContractGroupModel contractGroup
 *
 * @method ClinicRequestLimitModel[] findAll
 */
class ClinicRequestLimitModel extends \CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 *
	 * @param string $className
	 *
	 * @return ClinicRequestLimitModel the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'clinic_request_limit';
	}

	/**
	 * @return string имя первичного ключа
	 */
	public function primaryKey()
	{
		return 'id';
	}

	/**
	 * Зависимости
	 *
	 * @return array
	 */
	public function relations()
	{
		return [
			'clinicContract'   => [
				self::BELONGS_TO,
				ClinicContractModel::class,
				'clinic_contract_id',
			],
			'contractGroup'   => [
				self::BELONGS_TO,
				ContractGroupModel::class,
				'group_uid',
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
	 * Выборка лимитов, по которым не отправлено уведомления
	 *
	 * @return $this
	 */
	public function actual()
	{
		$this->getDbCriteria()
			->mergeWith([
				'condition' => '(date_notice < :date OR date_notice IS NULL) AND t.limit > 0',
				'params' => [':date' => date("Y-m-01")],
			]);

		return $this;
	}
}
