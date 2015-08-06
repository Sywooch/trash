<?php

/**
 * This is the model class for table "diagnostic_center_address".
 *
 * The followings are the available columns in table 'diagnostic_center_address':
 * @property integer $diagnostic_center_id
 * @property integer $underground_station_id
 */
class DiagnosticCenterAddress extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return DiagnosticCenterAddress the static model class
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
		return 'diagnostic_center_address';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('diagnostic_center_id, underground_station_id', 'required'),
			array('diagnostic_center_id, underground_station_id', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('diagnostic_center_id, underground_station_id', 'safe', 'on'=>'search'),
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
			'diagnostic_center_id' => 'Diagnostic Center',
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

		$criteria->compare('diagnostic_center_id',$this->diagnostic_center_id);
		$criteria->compare('underground_station_id',$this->underground_station_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}