<?php

/**
 * This is the model class for table "doctor_request_address".
 *
 * The followings are the available columns in table 'doctor_request_address':
 * @property integer $doctor_request_id
 * @property integer $underground_station_id
 */
class DoctorRequestAddress extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return DoctorRequestAddress the static model class
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
		return 'doctor_request_address';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('doctor_request_id', 'required'),
			array('doctor_request_id, underground_station_id', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('doctor_request_id, underground_station_id', 'safe', 'on'=>'search'),
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
			'doctor_request_id' => 'Doctor Request',
			'underground_station_id' => 'Underground Station',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('doctor_request_id',$this->doctor_request_id);
		$criteria->compare('underground_station_id',$this->underground_station_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}