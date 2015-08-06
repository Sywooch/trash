<?php

namespace dfs\docdoc\models;

/**
 * This is the model class for table "traffic_params".
 *
 * The followings are the available columns in table 'traffic_params':
 *
 * @property int    $id
 * @property int    $obj_id
 * @property int    $obj_type
 * @property int    $param_id
 * @property string $value
 *
 * @method TrafficParamsModel[] findAll
 * @method TrafficParamsModel find
 */
class TrafficParamsModel extends \CActiveRecord
{
	/**
	 * Тип объекта - заявка
	 */
	const OBJECT_REQUEST = 1;
	/**
	 * Тип объекта - клиент
	 */
	const OBJECT_CLIENT = 2;

	/**
	 * Returns the static model of the specified AR class.
	 *
	 * @param string $className
	 *
	 * @return TrafficParamsModel the static model class
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
		return 'traffic_params';
	}

	/**
	 * Поиск по типу объекта
	 *
	 * @param int $type
	 * @return $this
	 */
	public function byType($type)
	{
		$this
			->getDbCriteria()
			->mergeWith([
			'condition' => "t.obj_type = :type",
			'params'    => array(':type' => $type),
		]);

		return $this;
	}

	/**
	 * Сохранение параметров траффика
	 *
	 * @param array $params
	 * @param int $objId
	 * @param int $objType
	 */
	public function saveByParams($params, $objId, $objType)
	{
		$allTrafficParams = \CHtml::listData(TrafficParamsDictModel::model()->findAll(), 'id', 'name');
		foreach ($params as $param => $value) {
			if (in_array($param, $allTrafficParams)) {
				$model = new self();
				$model->obj_id = $objId;
				$model->obj_type = $objType;
				$model->param_id = array_search($param, $allTrafficParams);
				$model->value = $value;
				$model->save();
			}
		}
	}
}
