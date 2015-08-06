<?php

namespace dfs\docdoc\models;

/**
 * Class DegreeModel
 * @package dfs\docdoc\models
 *
 * @property integer $degree_id
 * @property string $title
 *
 * @method DegreeModel findByPk
 * @method DegreeModel[] findAll
 * @method DegreeModel find
 */
class DegreeModel extends \CActiveRecord
{

	/**
	 * Returns the static model of the specified AR class.
	 *
	 * @param string $className
	 *
	 * @return DegreeModel the static model class
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
		return 'degree_dict';
	}

	/**
	 * @return array
	 */
	public function relations() {
		return array(
		);
	}

} 