<?php

namespace dfs\docdoc\models;


/**
 * This is the model class for table "clinic_phone".
 *
 * The followings are the available columns in table 'clinic_phone':
 *
 * @property int    $phone_id
 * @property string $number_p
 * @property string $label
 * @property int    $clinic_id
 *
 * The followings are the available model relations:
 * @property AreaModel $areas модели округов
 *
 *
 * @method ClinicPhoneModel[] findAll
 * @method ClinicPhoneModel find
 */
class ClinicPhoneModel extends \CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 *
	 * @param string $className
	 *
	 * @return CityModel the static model class
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
		return 'clinic_phone';
	}

	/**
	 * @return string имя первичного ключа
	 */
	public function primaryKey()
	{
		return 'id';
	}
}
