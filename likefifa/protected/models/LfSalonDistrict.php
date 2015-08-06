<?php

/**
 * This is the model class for table "lf_salon_district".
 *
 * The followings are the available columns in table 'lf_salon_district':
 * @property integer $id
 * @property integer $salon_id
 * @property integer $district_id
 *
 * The followings are the available model relations:
 * @property LfSalons $salon
 * @property DistrictMoscow $district
 */
class LfSalonDistrict extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return LfSalonDistrict the static model class
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
		return 'lf_salon_district';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('salon_id, district_id', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, salon_id, district_id', 'safe', 'on'=>'search'),
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
			'salon' => array(self::BELONGS_TO, 'LfSalon', 'salon_id'),
			'district' => array(self::BELONGS_TO, 'DistrictMoscow', 'district_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'salon_id' => 'Salon',
			'district_id' => 'District',
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

		$criteria->compare('id',$this->id);
		$criteria->compare('salon_id',$this->salon_id);
		$criteria->compare('district_id',$this->district_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}