<?php
namespace likefifa\models;

use CActiveRecord;
use LfAppointment;

/**
 * Модель для работы с таблицей lf_appointment_favorites
 * Хранит в себе избранные места
 *
 * Колонки таблицы:
 *
 * @property integer       $id
 * @property integer       $appointment_id
 * @property integer       $admin_id
 *
 * Связи:
 *
 * @property LfAppointment $appointment
 * @property AdminModel    $admin
 */
class LfAppointmentFavorite extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'lf_appointment_favorites';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return [
			['appointment_id, admin_id', 'numerical', 'integerOnly' => true],
			['appointment_id, admin_id', 'required'],
		];
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return [
			'appointment' => [self::BELONGS_TO, 'LfAppointment', 'appointment_id'],
			'admin'       => [self::BELONGS_TO, 'AdminModel', 'admin_id'],
		];
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 *
	 * @param string $className active record class name.
	 *
	 * @return LfAppointmentFavorite the static model class
	 */
	public static function model($className = __CLASS__)
	{
		return parent::model($className);
	}
}