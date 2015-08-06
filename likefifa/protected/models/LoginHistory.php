<?php


namespace likefifa\models;
use CActiveRecord;

/**
 * This is the model class for table "login_history".
 *
 * The followings are the available columns in table 'login_history':
 *
 * @property integer $id
 * @property string  $type
 * @property string  $entity_id
 * @property string  $date
 * @property string  $ip
 * @property string  $ua
 * @property string  $ga
 */
class LoginHistory extends CActiveRecord
{
	const TYPE_MASTER = 'master';
	const TYPE_SALON = 'salon;';
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'login_history';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array('entity_id, date', 'required'),
			array('type', 'length', 'max' => 6),
			array('entity_id', 'length', 'max' => 11),
			array('ip, ga', 'length', 'max' => 45),
			array('ua', 'length', 'max' => 255),
		);
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 *
	 * @param string $className active record class name.
	 *
	 * @return LoginHistory the static model class
	 */
	public static function model($className = __CLASS__)
	{
		return parent::model($className);
	}
}