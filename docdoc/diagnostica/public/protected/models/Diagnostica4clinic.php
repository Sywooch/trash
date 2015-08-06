<?php

/**
 * This is the model class for table "diagnostica4clinic".
 *
 * The followings are the available columns in table 'diagnostica4clinic':
 * @property integer $diagnostica_id
 * @property integer $clinic_id
 * @property double $price
 * @property double $special_price
 */
class Diagnostica4clinic extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return Diagnostica4clinic the static model class
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
		return 'diagnostica4clinic';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('diagnostica_id, clinic_id', 'numerical', 'integerOnly'=>true),
			array('price, special_price', 'numerical'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('diagnostica_id, clinic_id, price, special_price', 'safe', 'on'=>'search'),
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
			'diagnostica_id' => 'Diagnostica',
			'clinic_id' => 'Clinic',
			'price' => 'Price',
			'special_price' => 'Special Price',
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

		$criteria->compare('diagnostica_id',$this->diagnostica_id);
		$criteria->compare('clinic_id',$this->clinic_id);
		$criteria->compare('price',$this->price);
		$criteria->compare('special_price',$this->special_price);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

}