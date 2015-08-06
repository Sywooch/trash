<?php

/**
 * This is the model class for table "underground_line".
 *
 * The followings are the available columns in table 'underground_line':
 * @property integer $id
 * @property string $name
 * @property string $color
 *
 * The followings are the available model relations:
 * @property UndergroundStation[] $undergroundStations
 */
class UndergroundLine extends CActiveRecord
{
	const MOSCOW_ID = 1;
	/**
	 * Returns the static model of the specified AR class.
	 * @return UndergroundLine the static model class
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
		return 'underground_line';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name, color', 'filter', 'filter' => 'strip_tags'),
			array('name, color', 'required'),
			array('name', 'length', 'max'=>512),
			array('color', 'length', 'max'=>16),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('name', 'safe', 'on'=>'search'),
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
			'undergroundStations' => array(self::HAS_MANY, 'UndergroundStation', 'underground_line_id'),
			//'doctorAddresses' => array(self::HAS_MANY, 'DoctorAddress', 'id', 'through' => 'undergroundStations'),
			'doctors' => array(self::HAS_MANY, 'Doctor', 'doctor_id', 'through' => 'undergroundStations.doctorAddresses'),
		);
	}
	
	public function scopes() {
		return array(
			'ordered' => array(
				'order' => 't.name ASC',
			),
			'moscow' => array(
				'condition' => 'metro_id = '.self::MOSCOW_ID,
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
			'name' => 'Название ветки',
			'color' => 'Цвет',
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

		$criteria->compare('name',$this->name,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'pagination' => array(
				'pageSize' => 20,
			),
		));
	}
	
	public function getListItems() {
		$items = array();
		
		$lines = $this->ordered()->findAll();
		foreach ($lines as $line) {
			$items[$line->id] = $line->name;
		}

		return $items;
	}
	
}