<?php

namespace dfs\docdoc\models;

/**
 * This is the model class for table "clinic_billing".
 *
 * The followings are the available columns in table 'clinic_billing':
 *
 * @property integer  $id
 * @property integer  $clinic_billing_id
 * @property string   $payment_date
 * @property float  $sum
 *
 * The followings are the available model relations:
 *
 * @property ClinicBillingModel $billing
 *
 * @method ClinicPaymentModel findByPk
 */
class ClinicPaymentModel extends \CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 *
	 * @param string $className
	 *
	 * @return ClinicBillingModel the static model class
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
		return 'clinic_payment';
	}

	/**
	 * Зависимости
	 *
	 * @return array
	 */
	public function relations()
	{
		return [
			'billing'   => [
				self::BELONGS_TO,
				ClinicBillingModel::class,
				'clinic_billing_id'
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
		return [
			[
				'clinic_billing_id, sum',
				'numerical',
				'allowEmpty' => false,
			],
			[
				'clinic_billing_id, sum, payment_date',
				'safe',
				'on' => ['insert', 'update']
			]
		];
	}

	/**
	 * Перед сохранением
	 *
	 * @return bool
	 */
	public function beforeSave()
	{
		if ($this->isNewRecord && empty($this->payment_date)) {
			$this->payment_date = date('Y-m-d');
		}

		return true;
	}

	/**
	 * после сохранения
	 */
	public function afterSave()
	{
		$this->billing->onPaymentChange();
	}

	/**
	 * После удаления
	 */
	public function afterDelete()
	{
		$this->billing->onPaymentChange();
	}

}
