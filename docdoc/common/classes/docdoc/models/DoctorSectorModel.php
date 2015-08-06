<?php
namespace dfs\docdoc\models;

/**
 * This is the model class for table "doctor_sector".
 *
 * The followings are the available columns in table 'doctor_sector':
 * @property integer $doctor_id
 * @property integer $sector_id
 *
 * @method integer deleteAll
 */
class DoctorSectorModel extends \CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return DoctorSectorModel the static model class
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
		return 'doctor_sector';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('doctor_id, sector_id', 'required'),
			array('doctor_id, sector_id', 'numerical', 'integerOnly'=>true),
			array('doctor_id, sector_id', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'doctor_id' => 'Doctor',
			'sector_id' => 'Sector',
		);
	}


}