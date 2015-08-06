<?php

namespace dfs\docdoc\models;


/**
 * This is the model class for table "tips".
 *
 * The followings are the available columns in table 'tips':
 *
 * @property int    $id
 * @property string $name
 * @property int    $category
 * @property int    $weight
 * @property string $color
 *
 * @method TipsModel[] findAll
 * @method TipsModel find
 */
class TipsModel extends \CActiveRecord
{
	/**
	 * сообщения, связанные с заявками
	 */
	const CATEGORY_REQUEST = 1;

	/**
	 * сообщения, связанные с отзывами
	 */
	const CATEGORY_OPINION = 2;

	/**
	 * сообщения, связанные с расписанием
	 */
	const CATEGORY_SCHEDULE = 3;

	/**
	 * сообщения для лучших врачей, клиник
	 */
	const CATEGORY_THE_BEST = 4;

	/**
	 * Returns the static model of the specified AR class.
	 *
	 * @param string $className
	 *
	 * @return TipsModel the static model class
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
		return 'tips';
	}
}
