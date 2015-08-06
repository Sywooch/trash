<?php
namespace dfs\docdoc\models;

/**
 * This is the model class for table "area".
 *
 * The followings are the available columns in table 'area':
 *
 * @property integer $id
 * @property string $name
 * @property string $rewrite_name
 * @property string $full_name
 * @property DistrictModel $districts модели районов
 *
 * @method AreaModel findByPk
 * @method AreaModel[] findAll
 * @method AreaModel find
 *
 *
 */

class AreaModel extends \CActiveRecord
{

	/**
	 * Returns the static model of the specified AR class.
	 *
	 * @param string $className
	 *
	 * @return AreaModel the static model class
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
		return 'area_moscow';
	}

	/**
	 * @return array
	 */
	public function relations() {
		return array(
			'districts' => array(
				self::HAS_MANY,
				'dfs\docdoc\models\DistrictModel',
				'id_area'
			),
		);
	}

	/**
	 * Получает список округов
	 *
	 * @return string
	 */
	public function getAreaList()
	{
		$list = array();

		foreach ($this->findAll() as $model) {
			$list[$model->id] = $model->name;
		}

		return $list;
	}

	/**
	 * Поведения
	 *
	 * CaseArBehavior - класс реализующий склонение слов
	 *
	 * @return array
	 */
	public function behaviors()
	{
		return array(
			'CaseArBehavior' => array(
				'class' => 'dfs\docdoc\components\behaviors\CaseArBehavior',
			),
		);
	}

	/**
	 * Поиск по алиасу
	 *
	 * @param string $alias
	 * @return AreaModel
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
	 * Получение id районов
	 *
	 * @return array
	 */
	public function getDistrictIds()
	{
		$data = array();
		foreach ($this->districts as $district) {
			$data[] = $district->id;
		}

		return $data;
	}

	/**
	 * Получение id станций метро
	 *
	 * @return array
	 */
	public function getStationIds()
	{
		$sql = 'SELECT station_id FROM area_underground_station WHERE area_id = :areaId';

		$result = \Yii::app()->db
			->createCommand($sql)
			->bindValue(':areaId', $this->id)
			->queryAll();

		$data = [];
		foreach ($result as $v) {
			$data[] = intval($v['station_id']);
		}

		return $data;
	}
}
