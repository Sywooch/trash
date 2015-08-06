<?php

namespace dfs\docdoc\models;

use \CActiveRecord;
use \CDbCriteria;
use \CActiveDataProvider;

/**
 * Файл класса UserRightDictModel
 *
 * Модель для работы с правами пользователей
 *
 * @author  Mikhail Vasilyev <m.vasilyev@likefifa.ru>
 * @link    https://docdoc.megaplan.ru/task/1003744/card/
 * @package dfs.docdoc.models
 *
 * @property int    $right_id ID
 * @property string $title    Название
 * @property string $code     Код
 */
class UserRightDictModel extends CActiveRecord
{

	/**
	 * Возвращает имя связанной таблицы базы данных
	 *
	 * @return string
	 */
	public function tableName()
	{
		return 'user_right_dict';
	}

	/**
	 * Возвращает правила проверки для атрибутов модели
	 *
	 * @return string[]
	 */
	public function rules()
	{
		return array(
			array('title, code', 'required'),
			array('title', 'length', 'max' => 50),
			array('code', 'length', 'max' => 3),
			array('title, code', 'filter', 'filter' => 'strip_tags'),
			array('right_id, title, code', 'safe', 'on' => 'search'),
		);
	}

	/**
	 * Возвращает связи между объектами
	 *
	 * @return string[]
	 */
	public function relations()
	{
		return array();
	}

	/**
	 * Возвращает подписей полей
	 *
	 * @return string[]
	 */
	public function attributeLabels()
	{
		return array(
			'right_id' => 'ID',
			'title'    => 'Название',
			'code'     => 'Код',
		);
	}

	/**
	 * Получает список моделей на основе условий поиска / фильтров.
	 *
	 * @return CActiveDataProvider
	 */
	public function search()
	{
		$criteria = new CDbCriteria;

		$criteria->compare('right_id', $this->right_id);
		$criteria->compare('title', $this->title, true);
		$criteria->compare('code', $this->code, true);

		return new CActiveDataProvider($this, array(
			'criteria' => $criteria,
		));
	}

	/**
	 * Возвращает статическую модель указанного класса.
	 *
	 * @param string $className название класса
	 *
	 * @return UserRightDictModel
	 */
	public static function model($className = __CLASS__)
	{
		return parent::model($className);
	}
}
