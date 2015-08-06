<?php
namespace dfs\docdoc\models;
use \Yii;
/**
 * This is the model class for table "closest_station".
 *
 * The followings are the available columns in table 'closest_station':
 *
 * @property integer $id
 * @property string $name
 * @property integer $underground_line_id
 * @property integer $index
 * @property string $rewrite_name
 * @property float $longitude
 * @property float $latitude
 *
 */

class ClosestStationModel extends \CActiveRecord
{

	/**
	 * Returns the static model of the specified AR class.
	 *
	 * @param string $className
	 *
	 * @return ClinicModel the static model class
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
		return 'closest_station';
	}

	/**
	 * @return string имя первичного ключа
	 */
	public function primaryKey()
	{
		return 'id';
	}

	/**
	 * @return array
	 */
	public function relations()
	{
		return array(
			'stations' => array(
				self::BELONGS_TO,
				'dfs\docdoc\models\StationModel',
				'station_id'
			)
		);
	}







}
