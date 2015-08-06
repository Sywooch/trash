<?php

namespace likefifa\models;

use CActiveRecord;
use CDbCriteria;
use Yii;
use CActiveDataProvider;

/**
 * Файл класса AdminController
 *
 * Модель для работы с доступными для администрирования в БО контроллерами
 *
 * @author  Mikhail Vasilyev <m.vasilyev@likefifa.ru>
 * @link    https://docdoc.megaplan.ru/task/1002402/card/
 * @package models
 *
 * @property int              $id              идентификатор
 * @property string           $name            название
 * @property string           $rewrite_name    абривиатура URL
 * @property integer          $sort
 * @property string           $col_group
 * @property string           $icon
 */
class AdminControllerModel extends CActiveRecord
{

	/**
	 * Получает название таблицы в БД для модели
	 *
	 * @return string
	 */
	public function tableName()
	{
		return 'admin_controller';
	}

	/**
	 * Правила валидации для атрибутов модели
	 *
	 * @return string[]
	 */
	public function rules()
	{
		return array(
			array('name, rewrite_name, sort', 'required'),
			array('name, rewrite_name', 'length', 'max' => 128),
			array('col_group, icon', 'length', 'max' => 25),
			array('id, name, rewrite_name', 'safe', 'on' => 'search'),
		);
	}

	/**
	 * Связи с другими моделями
	 *
	 * @return string[]
	 */
	public function relations()
	{
		return array();
	}

	/**
	 * Названия меток для атрибутов
	 *
	 * @return string[]
	 */
	public function attributeLabels()
	{
		return array(
			'id'           => 'ID',
			'name'         => 'Название',
			'rewrite_name' => 'Абривиатура URL',
			'col_group'    => 'Группировка',
			'icon'         => "Иконка меню",
		);
	}

	/**
	 * Поиск в списке администраторов в БО
	 *
	 * @return CActiveDataProvider
	 */
	public function search()
	{
		$criteria = new CDbCriteria;

		$criteria->compare('id', $this->id);
		$criteria->compare('name', $this->name, true);
		$criteria->compare('rewrite_name', $this->rewrite_name, true);

		return new CActiveDataProvider($this, array('criteria' => $criteria));
	}

	/**
	 * Получает модель класса
	 *
	 * @param string $className название класса
	 *
	 * @return AdminControllerModel
	 */
	public static function model($className = __CLASS__)
	{
		return parent::model($className);
	}
}