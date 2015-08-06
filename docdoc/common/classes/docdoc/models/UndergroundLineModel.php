<?php

namespace dfs\docdoc\models;

use dfs\docdoc\models\CityModel;
use dfs\docdoc\models\UndergroundStationModel;
use CActiveRecord;
use CDbCriteria;
use CActiveDataProvider;

/**
 * Файл класса UndergroundLineModel
 *
 * Модель для работы с таблицей "underground_line"
 *
 * @author  Mikhail Vasilyev <m.vasilyev@likefifa.ru>
 * @link    https://docdoc.megaplan.ru/task/1003801/card/
 * @package dfs.docdoc.models
 *
 * @property int                       $id                  идентификатор
 * @property string                    $name                название
 * @property string                    $color               цвет
 * @property int                       $city_id             идентификатор города
 *
 * @property CityModel                 $city                модель города
 * @property UndergroundStationModel[] $undergroundStations модели станций метро
 */
class UndergroundLineModel extends CActiveRecord
{

	/**
	 * Возвращает имя связанной таблицы базы данных
	 *
	 * @return string
	 */
	public function tableName()
	{
		return 'underground_line';
	}

	/**
	 * Возвращает правила проверки для атрибутов модели
	 *
	 * @return string[]
	 */
	public function rules()
	{
		return array(
			array('name, color', 'required'),
			array('city_id', 'numerical', 'integerOnly' => true),
			array('name', 'length', 'max' => 512),
			array('color', 'length', 'max' => 16),
			array('id, name, color, city_id', 'safe', 'on' => 'search'),
			array('name, color', 'filter', 'filter' => 'strip_tags'),
		);
	}

	/**
	 * Возвращает связи между объектами
	 *
	 * @return string[]
	 */
	public function relations()
	{
		return array(
			'city'                => array(
				self::BELONGS_TO,
				'dfs\docdoc\models\CityModel',
				'city_id'
			),
			'undergroundStations' => array(
				self::HAS_MANY,
				'dfs\docdoc\models\UndergroundStation',
				'underground_line_id'
			),
		);
	}

	/**
	 * Возвращает подписей полей
	 *
	 * @return string[]
	 */
	public function attributeLabels()
	{
		return array(
			'id'      => 'ID',
			'name'    => 'Название',
			'color'   => 'Цвет',
			'city_id' => 'Город',
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

		$criteria->compare('id', $this->id);
		$criteria->compare('name', $this->name, true);
		$criteria->compare('color', $this->color, true);
		if ($this->city_id) {
			$criteria->compare('city_id', $this->city_id);
		}

		return new CActiveDataProvider(
			$this, array(
				'criteria'   => $criteria,
				'pagination' => array(
					'pageSize' => 50,
				),
			)
		);
	}

	/**
	 * Возвращает статическую модель указанного класса.
	 *
	 * @param string $className название класса
	 *
	 * @return UndergroundLineModel
	 */
	public static function model($className = __CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * Поиск по городу
	 *
	 * @param $city
	 *
	 * @return $this
	 */
	public function inCity($city)
	{
		$this->getDbCriteria()->mergeWith(array(
				'condition' => 't.city_id = :city_id',
				'params'    => array(':city_id' => $city),
			));

		return $this;
	}

	/**
	 * Получает название города
	 *
	 * @return string
	 */
	public function getCityTitle()
	{
		$city = $this->city;
		if ($city) {
			return $city->title;
		}

		return null;
	}

	/**
	 * Получает список линий метро
	 *
	 * @return string[]
	 */
	public function getLineList()
	{
		$list = array();

		foreach ($this->findAll() as $model) {
			$list[$model->id] = $model->name;
		}

		return $list;
	}
}