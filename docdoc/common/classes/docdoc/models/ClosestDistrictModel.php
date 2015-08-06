<?php
namespace dfs\docdoc\models;
use \Yii;
/**
 * This is the model class for table "closest_district".
 *
 * The followings are the available columns in table 'closest_district':
 *
 * @property integer $district_id
 * @property integer $closest_district_id
 * @property integer $priority
 *
 * @property DistrictModel $closest
 *
 * @method ClosestDistrictModel[] findAll
 */

class ClosestDistrictModel extends \CActiveRecord
{

	/**
	 * Returns the static model of the specified AR class.
	 *
	 * @param string $className
	 *
	 * @return ClosestDistrictModel the static model class
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
		return 'closest_district';
	}

	/**
	 * @return string имя первичного ключа
	 */
	public function primaryKey()
	{
		return  array('district_id', 'closest_district_id');
	}

	/**
	 * @return array
	 */
	public function relations()
	{
		return array(
			'districts' => array(
				self::BELONGS_TO,
				'dfs\docdoc\models\DistrictModel',
				'district_id'
			),
			'closest' => array(
				self::BELONGS_TO,
				'dfs\docdoc\models\DistrictModel',
				'closest_district_id'
			)
		);
	}

	/**
	 * Фильтр по районам
	 *
	 * @param array $districtIds
	 *
	 * @return $this
	 */
	public function inDistricts($districtIds)
	{
		$criteria = $this->getDbCriteria();
		$criteria->addInCondition('t.district_id', $districtIds);
		$criteria->group = 't.closest_district_id';

		return $this;
	}
}