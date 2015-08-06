<?php

/**
 * This is the model class for table "lf_salon_specialization".
 *
 * The followings are the available columns in table 'lf_salon_specialization':
 * @property integer $salon_id
 * @property integer $specialization_id
 */
class LfSalonSpecialization extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return LfSalonSpecialization the static model class
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
		return 'lf_salon_specialization';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('salon_id, specialization_id', 'required'),
			array('salon_id, specialization_id', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('salon_id, specialization_id', 'safe', 'on'=>'search'),
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
				'specialization' => array(self::BELONGS_TO, 'LfSpecialization', 'specialization_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'salon_id' => 'Salon',
			'specialization_id' => 'Specialization',
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

		$criteria->compare('salon_id',$this->salon_id);
		$criteria->compare('specialization_id',$this->specialization_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}