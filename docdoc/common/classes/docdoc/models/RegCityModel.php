<?php
namespace dfs\docdoc\models;

/**
 * This is the model class for table "reg_city".
 *
 * The followings are the available columns in table 'reg_city':
 *
 * @property integer $id
 * @property string $name
 * @property string $rewrite_name
 * @property integer $city_id
 * @property StationModel $stations Модели станций метро
 *
 * @method RegCityModel findByPk
 * @method RegCityModel[] findAll
 * @method RegCityModel find
 *
 */

class RegCityModel extends \CActiveRecord
{

	/**
	 * Returns the static model of the specified AR class.
	 *
	 * @param string $className
	 *
	 * @return RegCityModel the static model class
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
		return 'reg_city';
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
	public function relations() {
		return array(
			'stations' => array(
				self::MANY_MANY,
				'dfs\docdoc\models\StationModel',
				'underground_station_4_reg_city(reg_city_id, station_id)'
			),
		);
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
			'condition' => "rewrite_name = :rewrite_name",
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
			'condition' => "city_id = :city",
			'params'    => array(':city' => $city),
		));

		return $this;
	}

	/**
	 * Получение id станций метро
	 *
	 * @return array
	 */
	public function getStationIds()
	{
		$data = array();
		foreach ($this->stations as $station) {
			$data[] = $station->id;
		}

		return $data;
	}

}
