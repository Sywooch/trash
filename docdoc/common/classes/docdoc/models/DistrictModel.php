<?php

namespace dfs\docdoc\models;

use CActiveRecord;
use CDbCriteria;
use CActiveDataProvider;

/**
 * Файл класса DistrictModel
 *
 * Модель для работы с таблицей "district"
 *
 * @author  Mikhail Vasilyev <m.vasilyev@likefifa.ru>
 * @link    https://docdoc.megaplan.ru/task/1003804/card/
 * @package dfs.docdoc.models
 *
 * @property int          $id           идентификатор
 * @property string       $name         название
 * @property string       $rewrite_name абривиатура URL
 * @property int          $id_city      идентификатор города
 * @property int          $id_area      идентификатор округа
 *
 * @property StationModel $stations     модели станций метро
 * @property AreaModel    $area         модель округа
 * @property CityModel    $city         модель города
 * @property ClosestDistrictModel[] $closestDistricts Ближайшие районы
 *
 * @method DistrictModel find
 * @method DistrictModel findByPk
 * @method DistrictModel[] findAll
 * @method DistrictModel cache
 * @method DistrictModel with
 */
class DistrictModel extends CActiveRecord
{

	/**
	 * Возвращает статическую модель указанного класса.
	 *
	 * @param string $className название класса
	 *
	 * @return DistrictModel
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * Возвращает имя связанной таблицы базы данных
	 *
	 * @return string
	 */
	public function tableName()
	{
		return 'district';
	}

	/**
	 * Возвращает правила проверки для атрибутов модели
	 *
	 * @return string[]
	 */
	public function rules()
	{
		return array(
			array('name, rewrite_name', 'required'),
			array('id_city, id_area', 'numerical', 'integerOnly' => true),
			array('name, rewrite_name', 'length', 'max' => 50),
			array(
				'id, name, rewrite_name, id_city, id_area',
				'safe',
				'on' => 'search'
			),
			array(
				'rewrite_name',
				'dfs\docdoc\validators\StringValidator',
				'type' => "uid"
			),
			array(
				'name, rewrite_name',
				'filter',
				'filter' => 'strip_tags'
			),
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
		$criteria->compare('rewrite_name', $this->rewrite_name, true);
		if ($this->id_city) {
			$criteria->compare('id_city', $this->id_city);
		}
		if ($this->id_area) {
			$criteria->compare('id_area', $this->id_area);
		}

		return new CActiveDataProvider(
			$this, array(
				'criteria' => $criteria,
				'pagination' => array(
					'pageSize' => 50,
				),
			)
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
			'id'               => 'ID',
			'name'             => 'Название',
			'rewrite_name'     => 'Абривиатура URL',
			'id_city'          => 'Город',
			'id_area'          => 'Округ',
			'closestDistricts' => 'Ближайшие районы'
		);
	}

	/**
	 * Получает имя первичного ключа
	 *
	 * @return string
	 */
	public function primaryKey()
	{
		return 'id';
	}

	/**
	 * Возвращает связи между объектами
	 *
	 * @return string[]
	 */
	public function relations() {
		return array(
			'stations' => array(
				self::MANY_MANY,
				'dfs\docdoc\models\StationModel',
				'district_has_underground_station(id_district, id_station)'
			),
			'area' => array(
				self::BELONGS_TO,
				'dfs\docdoc\models\AreaModel',
				'id_area'
			),
			'city' => array(
				self::BELONGS_TO,
				'dfs\docdoc\models\CityModel',
				'id_city'
			),
			'closestDistricts' => array(
				self::HAS_MANY,
				'dfs\docdoc\models\ClosestDistrictModel',
				'district_id',
				'order' => 'closestDistricts.priority'
			),
		);
	}

	/**
	 * Формирует запрос на выборку ближайших районов, которые расположены рядом со станциями метро $stations
	 *
	 * @param int[] $stations
	 * @return DistrictModel
	 */
	public function near($stations)
	{
		$criteria = new CDbCriteria();
		$criteria->addInCondition('closestStations.station_id', $stations);

		$criteria->with = array(
			'stations'                 => array(
				'joinType' => 'INNER JOIN',
			),
			'stations.closestStations' => array(
				'joinType' => 'INNER JOIN',
			),
			'area'                     => array(
				'joinType' => 'LEFT JOIN',
			),
		);
		$criteria->order = 'closestStations.priority';
		$criteria->group = 't.id';
		$criteria->together = true;
		$this
			->getDbCriteria()
			->mergeWith($criteria);

		return $this;
	}

	/**
	 * возвращает массив с информацией о станциях,
	 * которые находятся рядом со станциями $stations в порядке их близсти
	 *
	 * @param int[] $stations
	 * @param int $limit
	 *
	 * @return array
	 */
	public function getNearestDistricts($stations, $limit = 0)
	{
		$condition = array();
		if($limit > 0) {
			$condition['limit'] = $limit;
		}

		$districts = [];
		if (!empty($stations[0])) {
			$districts = $this->near($stations)->findAll($condition);
		} else {
			$this->getClosestDistricts();
		}

		$nearestDistricts = array();

		foreach ($districts as $i => $district) {
			$nearestDistricts[$i]['DistrictName'] = $district->name;
			$nearestDistricts[$i]['RewriteName'] = $district->rewrite_name;
			$nearestDistricts[$i]['Area'] = ($district->area !== null) ? $district->area->rewrite_name : '';
		}

		return $nearestDistricts;
	}

	/**
	 * Получает ближайшие районы
	 *
	 * @param integer $limit
	 *
	 * @return DistrictModel[]
	 */
	public function getClosestDistricts($limit = 0)
	{
		$list = [];
		$i = 0;

		foreach ($this->closestDistricts as $closestDistrict) {
			if ((!$limit || $limit > $i) && $closestDistrict->closest->id != $this->id) {
				$list[] = $closestDistrict->closest;
				$i++;
			}
		}

		return $list;
	}

	/**
	 * возвращает массив с информацией о соседних районах,
	 * в порядке их близсти
	 *
	 *
	 * @return array
	 */
	public function getNeighborDistrictIds()
	{
		$neighborDistricts = array();

		foreach ($this->closestDistricts as $i => $district) {
			$neighborDistricts[$i] = $district->closest_district_id;
		}

		return $neighborDistricts;
	}

	/**
	 * Поиск по алиасу
	 *
	 * @param string $alias
	 * @return DistrictModel
	 */
	public function searchByAlias($alias)
	{
		$this->getDbCriteria()->mergeWith(array(
			'condition' => "t.rewrite_name = :rewrite_name",
			'params'    => array(':rewrite_name' => $alias),
		));

		return $this;
	}

	/**
	 * Поиск по городу
	 *
	 * @param integer $city
	 *
	 * @return $this
	 */
	public function inCity($city)
	{
		$this->getDbCriteria()->mergeWith(array(
			'condition' => "id_city = :city",
			'params'    => array(':city' => $city),
		));

		return $this;
	}

	/**
	 * Поиск по акругу
	 *
	 * @param integer $areaId
	 *
	 * @return $this
	 */
	public function inArea($areaId)
	{
		$this->getDbCriteria()->mergeWith(array(
			'condition' => "id_area = :area",
			'params'    => array(':area' => $areaId),
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
	 * Получает название округа
	 *
	 * @return string
	 */
	public function getAreaName()
	{
		$area = $this->area;
		if ($area) {
			return $area->name;
		}

		return null;
	}

	/**
	 * Получение массива данных
	 *
	 * @return array
	 */
	public function getData()
	{
		return array(
			'Id'    => $this->id,
			'Name'  => $this->name,
			'Alias' => $this->rewrite_name,
		);
	}

	/**
	 * Сортировка по имени
	 *
	 * @return DistrictModel
	 */
	public function ordered()
	{
		$this->getDbCriteria()
			->mergeWith(
				[
					'order' => $this->getTableAlias() . '.name ASC',
				]
			);

		return $this;
	}
}
