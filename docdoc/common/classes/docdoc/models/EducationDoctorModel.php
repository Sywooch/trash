<?php

namespace dfs\docdoc\models;

/**
 * Class EducationDoctorModel
 * @package dfs\docdoc\models
 *
 * @property integer $education_id
 * @property integer $doctor_id
 * @property integer $year
 *
 * @method EducationDoctorModel findByPk
 * @method EducationDoctorModel[] findAll
 * @method EducationDoctorModel find
 * @method integer deleteAll
 *
 * * The followings are the available model relations:
 * @property EducationModel $education
 *
 */
class EducationDoctorModel extends \CActiveRecord
{

	/**
	 * Returns the static model of the specified AR class.
	 *
	 * @param string $className
	 *
	 * @return EducationDoctorModel the static model class
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
		return 'education_4_doctor';
	}

	/**
	 * @return array
	 */
	public function relations() {
		return array(
			'education' => array(
				self::BELONGS_TO,
				'dfs\docdoc\models\EducationModel',
				'education_id'
			),
		);
	}

}