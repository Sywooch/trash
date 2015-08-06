<?php

namespace dfs\docdoc\models;

/**
 * This is the model class for table "booking_history".
 *
 * The followings are the available columns in table 'booking_history':
 *
 * @property integer $id
 * @property integer $book_id
 * @property integer $status
 * @property string $date_status
 *
 * The followings are the available model relations:
 *
 *
 * @method BookingHistoryModel findByPk
 */
class BookingHistoryModel extends \CActiveRecord
{

	/**
	 * Returns the static model of the specified AR class.
	 *
	 * @param string $className
	 *
	 * @return BookingHistoryModel the static model class
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
		return 'booking_history';
	}

	/**
	 * @return string имя первичного ключа
	 */
	public function primaryKey()
	{
		return 'id';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(

		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id'            => 'ID',
			'book_id'       => 'Бронь',
			'status'        => 'Статус',
			'date_status'   => 'Дата операции',
		);
	}
}