<?php

/**
 * This is the model class for table "academic_degree".
 *
 * The followings are the available columns in table 'academic_degree':
 * @property integer $id
 * @property string $name
 * @property integer $weight
 *
 * The followings are the available model relations:
 * @property Doctor[] $doctors
 */
class AcademicDegree extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return AcademicDegree the static model class
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
		return 'academic_degree';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name', 'filter', 'filter' => 'strip_tags'),
			array('name', 'required'),
			array('weight', 'numerical', 'integerOnly'=>true),
			array('name', 'length', 'max'=>512),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, name, weight', 'safe', 'on'=>'search'),
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
			'doctors' => array(self::HAS_MANY, 'Doctor', 'academic_degree_id'),
		);
	}
	
	public function scopes() {
		return array(
			'ordered' => array(
				'order' => 't.weight DESC',
			),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'name' => 'Название учёной степени',
			'weight' => 'Вес',
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
		$criteria->compare('name',$this->name,true);
		$criteria->compare('weight',$this->weight);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'pagination'=>array(
				'pageSize' => 20,
			),
		));
	}
	
	public function getListItems() {
		$items = array();
		
		$records = $this->ordered()->findAll();
		foreach ($records as $record) {
			$items[$record->id] = $record->name;
		}

		return $items;
	}
}