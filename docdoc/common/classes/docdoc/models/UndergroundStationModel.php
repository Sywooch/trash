<?php

namespace dfs\docdoc\models;

use dfs\docdoc\models\UndergroundLineModel;
use CActiveRecord;
use CDbCriteria;
use CActiveDataProvider;

/**
 * Файл класса UndergroundStationModel
 *
 * Модель для работы с таблицей "underground_station"
 *
 * @author  Mikhail Vasilyev <m.vasilyev@likefifa.ru>
 * @link    https://docdoc.megaplan.ru/task/1003801/card/
 * @package dfs.docdoc.models
 *
 * @property int                  $id                  идентификатор
 * @property string               $name                название
 * @property int                  $underground_line_id идентификатор линии метро
 * @property int                  $index               индекс
 * @property string               $rewrite_name        абривиатура URL
 * @property float                $longitude           долгота
 * @property float                $latitude            широта
 *
 * @property UndergroundLineModel $undergroundLine     модель линии метро
 * @property \dfs\docdoc\models\ClosestStationModel[] $closestStations
 *
 * @method UndergroundStationModel cache
 * @method UndergroundStationModel findAll
 */
class UndergroundStationModel extends CActiveRecord
{

	/**
	 * Возвращает имя связанной таблицы базы данных
	 *
	 * @return string
	 */
	public function tableName()
	{
		return 'underground_station';
	}

	/**
	 * Возвращает правила проверки для атрибутов модели
	 *
	 * @return string[]
	 */
	public function rules()
	{
		return array(
			array('name, underground_line_id', 'required'),
			array('underground_line_id, index', 'numerical', 'integerOnly' => true),
			array('name, rewrite_name', 'length', 'max' => 512),
			array('id, name, underground_line_id, index, rewrite_name, longitude, latitude', 'safe', 'on' => 'search'),
			array('rewrite_name', 'dfs\docdoc\validators\StringValidator', 'type' => "uid"),
			array('name, rewrite_name', 'filter', 'filter' => 'strip_tags'),
			array(
				'longitude, latitude',
				'numerical',
				'numberPattern' => '/^[+-]?((\d+(\.\d*)?)|(\.\d+))([Ee][+-]?\d+)?$/'
			),
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
			'undergroundLine' => array(
				self::BELONGS_TO,
				'dfs\docdoc\models\UndergroundLineModel',
				'underground_line_id'
			),
			'closestStations' => [
				self::HAS_MANY,
				'dfs\docdoc\models\ClosestStationModel',
				'station_id',
				'order' => 'closestStations.priority'
			],
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
			'id'                  => 'ID',
			'name'                => 'Название',
			'underground_line_id' => 'Линия метро',
			'index'               => 'Индекс',
			'rewrite_name'        => 'Абривиатура URL',
			'longitude'           => 'Долгота',
			'latitude'            => 'Широта',
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
		$criteria->compare('underground_line_id', $this->underground_line_id);
		$criteria->compare('index', $this->index);
		$criteria->compare('rewrite_name', $this->rewrite_name, true);

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
	 * @return UndergroundStationModel
	 */
	public static function model($className = __CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * Получает название ветки метро
	 *
	 * @return string
	 */
	public function getLineName()
	{
		$undergroundLine = $this->undergroundLine;
		if ($undergroundLine) {
			return $undergroundLine->name;
		}

		return null;
	}

	/**
	 * Получает ближайшие станции метро от указанных координат
	 * Возвращается массив из записей (расстояние в метрах и модель станции)
	 *
	 * @param float $latitude широта
	 * @param float $longitude долгота
	 * @param int $limit лимит
	 *
	 * @return array
	 */
	public function getClosestStationsByCoordinates($latitude, $longitude, $limit = 2)
	{
		$list = [];

		$criteria = new CDbCriteria();
		$criteria->select =
			"t.*, SQRT(POW((t.latitude-{$latitude}), 2) + POW((t.longitude-{$longitude}), 2)) AS distance";
		$criteria->order = "distance";
		$criteria->limit = $limit;
		$criteria->group = "t.name";
		foreach ($this->cache(3600)->findAll($criteria) as $station) {
			$list[] = [
				"distance" => intval(
					sqrt(pow(($station->latitude - $latitude), 2) + pow(($station->longitude - $longitude), 2)) *
					40008 /
					360 *
					1000
				),
				"model"    => $station
			];
		}

		return $list;
	}
}