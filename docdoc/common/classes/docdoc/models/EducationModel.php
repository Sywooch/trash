<?php

namespace dfs\docdoc\models;

/**
 * Class EducationModel
 * @package dfs\docdoc\models
 *
 * @property integer $education_id
 * @property string $title
 * @property string $type
 *
 * @method EducationModel findByPk
 * @method EducationModel[] findAll
 * @method EducationModel find
 */
class EducationModel extends \CActiveRecord
{

	/**
	 * Returns the static model of the specified AR class.
	 *
	 * @param string $className
	 *
	 * @return EducationModel the static model class
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
		return 'education_dict';
	}

	/**
	 * @return array
	 */
	public function relations() {
		return array(
		);
	}

}