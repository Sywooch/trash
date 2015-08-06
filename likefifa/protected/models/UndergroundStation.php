<?php

use likefifa\models\CityModel;

/**
 * * Модель для таблицы "underground_station".
 * Содержит в себе список возможных станций метро.
 *
 * The followings are the available columns in table 'underground_station':
 *
 * @property integer         $id
 * @property string          $name
 * @property integer         $underground_line_id
 * @property integer         $index
 * @property string          $rewrite_name
 *
 * The followings are the available model relations:
 * @property UndergroundLine $undergroundLine
 * @property DistrictMoscow  $district
 * @property CityModel       $city
 *
 * @method UndergroundStation ordered
 * @method UndergroundStation find
 * @method UndergroundStation[] findAll
 */
class UndergroundStation extends CActiveRecord
{
	const ANY_STATION = 'любом районе Москвы';

	/**
	 * Returns the static model of the specified AR class.
	 *
	 * @param string $className
	 *
	 * @return UndergroundStation the static model class
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
		return 'underground_station';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array('name, underground_line_id', 'required'),
			array('underground_line_id, index, district_id', 'numerical', 'integerOnly' => true),
			array('name', 'length', 'max' => 512),
			array('name, undergroundLine', 'safe', 'on' => 'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
			'undergroundLine' => array(self::BELONGS_TO, 'UndergroundLine', 'underground_line_id'),
			'stationCity'     => array(self::HAS_MANY, 'UndergroundStationCity', 'underground_station_id'),
			'city'            => array(
				self::HAS_MANY,
				'likefifa\models\CityModel',
				array('city_id' => 'id'),
				'through' => 'stationCity'
			),
			'district'        => array(self::BELONGS_TO, 'DistrictMoscow', 'district_id'),
			'closestStations' => array(
				self::HAS_MANY,
				'ClosestStation',
				'closest_station_id'
			),
		);
	}

	public function scopes()
	{
		return array(
			'ordered' => array(
				'order' => 't.name ASC',
			),
		);
	}

	public function behaviors()
	{
		return array(
			'CArRewriteBehavior' => array(
				'class' => 'application.extensions.CArRewriteBehavior',
			),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id'                  => 'ID',
			'name'                => 'Название станции',
			'underground_line_id' => 'Ветка метро',
			'undergroundLine'     => 'Ветка метро',
			'index'               => 'Index',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$sort = new CSort;
		$sort->attributes = array(
			'id',
			'name',
			'undergroundLine' => array(
				'asc'  => 'undergroundLine.name',
				'desc' => 'undergroundLine.name DESC',
			),
		);

		$criteria = new CDbCriteria;
		$criteria->with = 'undergroundLine';

		$criteria->compare('t.name', $this->name, true);
		$criteria->compare('undergroundLine.id', $this->undergroundLine);

		return new CActiveDataProvider($this, array(
			'criteria'   => $criteria,
			'pagination' => array(
				'pageSize' => 50,
			),
			'sort'       => $sort,
		));
	}

	public function getListItems($withEmpty = false)
	{
		$items = array();
		if ($withEmpty) {
			$items[''] = 'Нет метро';
		}
		$model = new UndergroundStation();
		$stations = $model->ordered()->findAll();
		foreach ($stations as $station) {
			$items[$station->id] = $station->name;
		}

		return $items;
	}

	public function getColoredListItems()
	{
		$items = array();

		$model = new UndergroundStation();
		$stations = $model->ordered()->findAll();
		foreach ($stations as $station) {
			$items[$station->id] =
				'<span style="color: #' . $station->undergroundLine->color . '">' . $station->name . '</span>';
		}

		return $items;
	}

	public function getIcon()
	{
		switch ($this->underground_line_id) {
			case 1:
				$line = '<i style="background-position: 0 -81px"></i>';
				break;
			case 2:
				$line = '<i style="background-position: 0 -90px"></i>';
				break;
			case 3:
				$line = '<i style="background-position: 0 0"></i>';
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
	 * Формирует запрос на выборку станций метро, которые расположены рядом со станциями $stations
	 *
	 * @param int[] $stations
	 * @param int   $priority
	 *
	 * @return $this
	 */
	public function near(array $stations, $priority = null)
	{

		$criteria = new CDbCriteria();
		$criteria->addInCondition('closestStations.station_id', $stations);
		$criteria->addNotInCondition('t.id', $stations);
		$criteria->with = [
			'closestStations'          => [
				'joinType' => 'INNER JOIN',
			],
			'closestStations.stations' => [
				'joinType' => 'INNER JOIN'
			],
		];

		if ($priority != null) {
			$criteria->addCondition('closestStations.priority <= :priority');
			$criteria->params[':priority'] = $priority;
		}

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
	 * @param int[]   $stations
	 * @param integer $limit
	 *
	 * @return array
	 */
	public function getNearestStations(array $stations, $limit = 0)
	{
		$condition = [];
		if ($limit > 0) {
			$condition['limit'] = $limit;
		}
		$stations = $this
			->near($stations)
			->findAll($condition);

		$nearestStations = [];

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
	 * @param int[]   $stations
	 * @param integer $limit
	 *
	 * @return array
	 */
	public function getNearestStationIds(array $stations, $limit = 0)
	{
		$data = [];

		$items = $this->getNearestStations($stations, $limit);
		foreach ($items as $item) {
			$data[] = $item['Id'];
		}

		return $data;
	}

	/**
	 * Возвращает крайние для подмосковья станции
	 *
	 * @return UndergroundStation[]
	 */
	public static function getEnds()
	{
		$criteria = new CDbCriteria();
		$criteria->join = 'INNER JOIN city_near_stations ns ON ns.station_id = t.id';
		$criteria->addCondition('ns.priority = 1');
		return UndergroundStation::model()->findAll($criteria);
	}
}