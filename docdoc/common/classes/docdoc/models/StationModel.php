<?php

namespace dfs\docdoc\models;

use dfs\docdoc\objects\Coordinate;

/**
 * Class StationModel
 * @package dfs\docdoc\models
 *
 *
 * The followings are the available columns in table 'underground_station':
 *
 * @property integer $id
 * @property string $name
 * @property integer $underground_line_id
 * @property integer $index
 * @property string $rewrite_name
 * @property float $longitude
 * @property float $latitude
 * @property int $only_coord_search
 *
 * @property UndergroundLineModel $undergroundLine
 *
 *
 * @method StationModel findByPk
 * @method StationModel[] findAll
 * @method StationModel find
 * @method StationModel cache
 *
 *
 */
class StationModel extends \CActiveRecord
{

	/**
	 * Returns the static model of the specified AR class.
	 *
	 * @param string $className
	 *
	 * @return StationModel the static model class
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
		return 'underground_station';
	}

	/**
	 * Зависимости
	 * @return array
	 */
	public function relations()
	{
		return array(
			'clinics' => array(
				self::MANY_MANY,
				'dfs\docdoc\models\ClinicModel',
				'underground_station_4_clinic(undegraund_station_id, clinic_id)'
			),
			'districts' => array(
				self::MANY_MANY,
				'dfs\docdoc\models\DistrictModel',
				'district_has_underground_station(id_station,id_district)'
			),
			'closestStations' => array(
				self::HAS_MANY,
				'dfs\docdoc\models\ClosestStationModel',
				'closest_station_id'
			),
			'regCities' => array(
				self::MANY_MANY,
				'dfs\docdoc\models\RegCityModel',
				'underground_station_4_reg_city(station_id, reg_city_id)'
			),
			'undergroundLine' => array(
				self::BELONGS_TO,
				'dfs\docdoc\models\UndergroundLineModel',
				'underground_line_id'
			),
		);
	}


	/**
	 * Формирует запрос на выборку станций метро, которые расположены рядом со станциями $stations
	 *
	 * @param int[] $stations
	 * @return $this
	 */
	public function near($stations)
	{

		$criteria = new \CDbCriteria();
		$criteria->addInCondition('closestStations.station_id', $stations);
		$criteria->addNotInCondition('t.id', $stations);
		$criteria->with = array(
			'closestStations'          => array(
				'joinType' => 'INNER JOIN',
			),
			'closestStations.stations' => array(
				'joinType' => 'INNER JOIN',
			),
		);

		$criteria->group = 't.id';
		$criteria->order = 'closestStations.priority';
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
	 * @param integer $limit
	 *
	 * @return array
	 */
	public function getNearestStations($stations, $limit = 0)
	{
		$condition = array();
		if($limit > 0) {
			$condition['limit'] = $limit;
		}
		$stations = $this
			->near($stations)
			->findAll($condition);

		$nearestStations = array();

		foreach ($stations as $i => $station) {
			$nearestStations[$i]['Name'] = $station->name;
			$nearestStations[$i]['Id'] = $station->id;
			$nearestStations[$i]['RewriteName'] = $station->rewrite_name;
		}

		return $nearestStations;
	}

	/**
	 * возвращает массив с ид ближайших станций
	 *
	 * @param int[] $stations
	 * @param integer $limit
	 *
	 * @return array
	 */
	public function getNearestStationIds($stations, $limit = 0)
	{
		$data = array();

		$items = $this->getNearestStations($stations, $limit);
		foreach ($items as $item) {
			$data[] = $item['Id'];
		}

		return $data;
	}

	/**
	 * Поиск по алиасу
	 *
	 * @param string $alias
	 *
	 * @return $this
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
	 * Поиск по названию
	 *
	 * @param string $name
	 *
	 * @return $this
	 */
	public function searchByName($name)
	{
		$this->getDbCriteria()->mergeWith(array(
				'condition' => 'LOWER(t.name) LIKE LOWER(:name)',
				'params'    => array(':name' => '%' . trim($name) . '%'),
			));

		return $this;
	}

	/**
	 * Поиск по районам
	 *
	 * @param array $districts
	 *
	 * @return $this
	 */
	public function searchByDistricts($districts)
	{
		if (count($districts)) {
			$criteria = new \CDbCriteria();
			$criteria->with = array(
				'districts' => array('joinType' => 'INNER JOIN')
			);
			$criteria->addInCondition('districts.id', $districts);
			$this->getDbCriteria()->mergeWith($criteria);
		}
		return $this;
	}

	/**
	 * Поиск по городу Подмосковья
	 *
	 * @param integer $regCity
	 *
	 * @return $this
	 */
	public function searchByRegCity($regCity)
	{

		$criteria = new \CDbCriteria();
		$criteria->with = array(
			'regCities' => array('joinType' => 'INNER JOIN')
		);
		$criteria->condition = 'regCities.id = :id';
		$criteria->params = array(':id' => $regCity);
		$this->getDbCriteria()->mergeWith($criteria);

		return $this;
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
		$criteria = new \CDbCriteria();
		$criteria->with = array(
			'undergroundLine' => array('joinType' => 'INNER JOIN')
		);
		$criteria->condition = 'undergroundLine.city_id = :id';
		$criteria->params = array(':id' => $city);
		$this->getDbCriteria()->mergeWith($criteria);

		return $this;
	}

	/**
	 * Получение иконки
	 *
	 * @param $lineId
	 * @return string
	 */
	static function getIcon($lineId) {
		switch ($lineId) {
			case 1:
				$line = '<i style="background-position: 0 -81px"></i>';
				break;
			case 2:
				$line = '<i style="background-position: 0 -90px"></i>';
				break;
			case 3:
				$line = '<i style="background-position: 0 0px"></i>';
				break;
			case 4:
				$line = '<i style="background-position: 0 -36px"></i>';
				break;
			case 5:
				$line = '<i style="background-position: 0 -54px"></i>';
				break;
			case 6:
				$line = '<i style="background-position: 0 -27px"></i>';
				break;
			case 7:
				$line = '<i style="background-position: 0 -18px"></i>';
				break;
			case 8:
				$line = '<i style="background-position: 0 -72px"></i>';
				break;
			case 9:
				$line = '<i style="background-position: 0 -45px"></i>';
				break;
			case 10:
				$line = '<i style="background-position: 0 -9px"></i>';
				break;
			case 11:
				$line = '<i style="background-position: 0 -63px"></i>';
				break;
			case 12:
				$line = '<i style="background-position: 0 -99px"></i>';
				break;
			default:
				$line = '';
				break;
		}

		return $line;
	}

	/**
	 * Расчет расстояния от станции метро до клиники
	 *
	 * @param ClinicModel $clinic
	 * @return float
	 */
	public function calcDistanceToClinic(ClinicModel $clinic)
	{
		$coord = new Coordinate($this->latitude, $this->longitude);
		$dist = $coord->getDistance($clinic->latitude, $clinic->longitude);

		return $dist < 1000 ? round($dist / 10) * 10 : round($dist / 100) * 100;
	}

	/**
	 * Получение расстояния от станции метро до клиники
	 *
	 * @param int $clinicId
	 * @return string
	 */
	public function getDistanceToClinic($clinicId)
	{
		$model = ClinicStationModel::model()
			->byClinic($clinicId)
			->byStation($this->id)
			->find();

		return $model->distance > 1000
			? ($model->distance / 1000) . ' км'
			: $model->distance . ' м';
	}

	/**
	 * Сортировка по имени
	 *
	 * @return StationModel
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

	/**
	 * Получает список станций для клиники
	 *
	 * @param int $clinicId идентификатор клиники
	 *
	 * @return array
	 */
	public function findAllForClinic($clinicId)
	{
		$sql = "SELECT t1.id, t1.name, t3.name as lineName, t3.color as lineColor, t3.city_id as cityId
			FROM underground_station t1
				LEFT JOIN underground_station_4_clinic t2 ON (t2.undegraund_station_id = t1.id)
				LEFT JOIN underground_line t3 ON (t3.id = t1.underground_line_id)
			WHERE t2.clinic_id = :clinicId
			GROUP BY t1.rewrite_name
			ORDER BY t1.name";

		return $this->dbConnection
			->createCommand($sql)
			->bindValue('clinicId', $clinicId)
			->queryAll();
	}

	/**
	 * Получает список связей станций по районам
	 *
	 * @param int $cityId
	 *
	 * @return array
	 */
	public function findStationForDistrict($cityId)
	{
		$sql = 'SELECT dhus.id_district as district_id, dhus.id_station as station_id, d.id_area as area_id
				FROM district d
					INNER JOIN district_has_underground_station AS dhus  ON (d.id = dhus.id_district)
				WHERE d.id_city = :cityId';

		return $this->dbConnection
			->cache(3600)
			->createCommand($sql)
			->bindValue('cityId', $cityId)
			->queryAll();
	}
}