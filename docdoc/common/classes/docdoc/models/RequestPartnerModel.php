<?php

namespace dfs\docdoc\models;

/**
 * This is the model class for table "request_partner".
 *
 * The followings are the available columns in table 'request_partner':
 *
 * @property integer $request_id
 * @property integer $partner_id
 * @property integer $external_status
 * @property integer $updated_status
 * @property integer $updated
 *
 * The followings are the available model relations:
 *
 * @property RequestModel $request
 * @property PartnerModel $partner
 *
 */
class RequestPartnerModel extends \CActiveRecord
{

	const EXTERNAL_STATUS_NEW = 0;
	const EXTERNAL_STATUS_REGISTERED = 1;
	const EXTERNAL_STATUS_DECLINED = 2;

	/**
	 * значение по-умолчанию для external_status
	 * @var int
	 */
	public $external_status = 0;

	/**
	 * значение по-умолчанию для updated_status
	 * @var string
	 */
	public $updated_status = 'no';

	/**
	 * Returns the static model of the specified AR class.
	 *
	 * @param string $className
	 *
	 * @return ClinicModel the static model class
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
		return 'request_partner';
	}

	/**
	 * @return string the associated primary key
	 */
	public function primaryKey()
	{
		return array('request_id','partner_id');
	}

	/**
	 * Связи таблиц
	 *
	 * @return array
	 */
	public function relations()
	{
		return array(
			'request' => array(
				self::BELONGS_TO, 'dfs\docdoc\models\RequestModel', 'request_id'
			),
			'partner' => array(
				self::BELONGS_TO, 'dfs\docdoc\models\PartnerModel', 'partner_id'
			)
		);
	}

}