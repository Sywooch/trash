<?php

namespace dfs\docdoc\models;

/**
 * This is the model class for table "clinic_schedule".
 *
 * The followings are the available columns in table 'clinic_schedule':
 *
 * @property integer $id
 * @property integer $week_day
 * @property integer $clinic_id
 * @property string  $start_time
 * @property string  $end_time
 *
 * The followings are the available model relations:
 *
 *
 * @method ClinicScheduleModel findByPk
 * @method ClinicScheduleModel find
 * @method ClinicScheduleModel[] findAll
 */
class ClinicScheduleModel extends \CActiveRecord
{
	/**
	 * Значения для поля week_day
	 */
	public static $weekDays = array(
		0 => 'пн-пт',
		1 => 'пн',
		2 => 'вт',
		3 => 'ср',
		4 => 'чт',
		5 => 'пт',
		6 => 'сб',
		7 => 'вс',
	);

	/**
	 * Returns the static model of the specified AR class.
	 *
	 * @param string $className
	 *
	 * @return ClinicScheduleModel the static model class
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
		return 'clinic_schedule';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array();
	}

	/**
	 * Поиск по идентификатору клиники
	 *
	 * @param $clinicId
	 *
	 * @return $this
	 */
	public function searchByClinic($clinicId)
	{
		$this->getDbCriteria()->mergeWith(
			array(
				'condition' => 'clinic_id=:id',
				'params'    => array(':id' => $clinicId),
				'order'     => 'week_day',
			)
		);

		return $this;
	}

	/**
	 * Выполнение действий после выборки
	 *
	 * 1. Форматируем время начала и конца работы
	 *
	 */
	protected function afterFind()
	{
		$this->start_time = \Yii::app()->dateFormatter->format('HH:mm', $this->start_time);
		$this->end_time = \Yii::app()->dateFormatter->format('HH:mm', $this->end_time);
		if ($this->end_time === '00:00') {
			$this->end_time = '24:00';
		}

		return parent::afterFind();
	}

	/**
	 * Название дня недели
	 *
	 * @return string
	 */
	public function getWeekDayTitle()
	{
		return self::$weekDays[$this->week_day];
	}
}
