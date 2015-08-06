<?php
/**
 * This is the model class for table "closest_station".
 *
 * The followings are the available columns in table 'closest_station':
 *
 * @property integer              $station_id
 * @property integer              $closest_station_id
 * @property integer              $priority
 *
 * @property UndergroundStation[] $stations
 *
 */
class ClosestStation extends CActiveRecord
{

	/**
	 * Returns the static model of the specified AR class.
	 *
	 * @param string $className
	 *
	 * @return ClosestStation the static model class
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
		return 'closest_station';
	}

	/**
	 * @return array
	 */
	public function relations()
	{
		return [
			'stations' => [
				self::BELONGS_TO,
				'UndergroundStation',
				'station_id'
			],
		];
	}
}
