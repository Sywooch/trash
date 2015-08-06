<?php

namespace dfs\docdoc\models;

/**
 * This is the model class for table "traffic_params_dict".
 *
 * The followings are the available columns in table 'traffic_params_dict':
 *
 * @property int    $id
 * @property string $name
 * @property string $title
 *
 * @method TrafficParamsDictModel[] findAll
 * @method TrafficParamsDictModel find
 */
class TrafficParamsDictModel extends \CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 *
	 * @param string $className
	 *
	 * @return TrafficParamsDictModel the static model class
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
		return 'traffic_params_dict';
	}
}
