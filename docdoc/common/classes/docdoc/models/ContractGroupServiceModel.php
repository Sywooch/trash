<?php

namespace dfs\docdoc\models;

/**
 * This is the model class for table "contract_group_service".
 *
 * The followings are the available columns in table 'contract_group_service':
 *
 * @property integer $contract_group_id
 * @property integer $service_id
 *
 * The followings are the available model relations:
 *
 *
 * @method ContractGroupServiceModel findByPk
 */
class ContractGroupServiceModel extends \CActiveRecord
{

	/**
	 * Любая услуга
	 */
	const ANY_SERVICE = 0;

	/**
	 * Returns the static model of the specified AR class.
	 *
	 * @param string $className
	 *
	 * @return ContractGroupServiceModel the static model class
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
		return 'contract_group_service';
	}

	public function primaryKey()
	{
		return ['contract_group_id', 'service_id'];
	}

}
