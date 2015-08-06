<?php


namespace likefifa\models;

use CActiveRecord;
use CDbExpression;

/**
 * Модель события (для вывода на графике главной страницы БО). Таблица dev_events
 *
 * The followings are the available columns in table 'dev_events':
 *
 * @property integer $id
 * @property string  $value
 * @property string  $date
 */
class DevEvent extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'dev_events';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array('value, date', 'required'),
			array('value', 'length', 'max' => 50),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id'    => 'ID',
			'value' => 'Описание события',
			'date'  => 'Дата события',
		);
	}

	/**
	 * @param string $className active record class name.
	 *
	 * @return DevEvent the static model class
	 */
	public static function model($className = __CLASS__)
	{
		return parent::model($className);
	}
}