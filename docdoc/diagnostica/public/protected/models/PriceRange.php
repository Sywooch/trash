<?php

/**
 * This is the model class for table "price_range".
 *
 * The followings are the available columns in table 'price_range':
 * @property integer $id
 * @property integer $price_from
 * @property integer $price_to
 *
 * The followings are the available model relations:
 * @property Doctor[] $doctors
 */
class PriceRange extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return PriceRange the static model class
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
		return 'price_range';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('price_from, price_to', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, price_from, price_to', 'safe', 'on'=>'search'),
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
			'doctors' => array(self::HAS_MANY, 'Doctor', 'price_range_id'),
		);
	}
	
	public function scopes()
	{
		return array(
			'ordered' => array(
				'order' => 'price_from ASC',
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
			'price_from' => 'Стоимость от',
			'price_to' => 'Стоимость до',
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
		$criteria->compare('price_from',$this->price_from);
		$criteria->compare('price_to',$this->price_to);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'pagination'=>array(
				'pageSize' => 20,
			),
		));
	}
	
	public function getRussianName() {
		return
			($this->price_from ? 'от '.number_format($this->price_from, 0, '.', ' ') : '')
			.($this->price_from && $this->price_to ? ' ' : '')
			.($this->price_to ? 'до '.number_format($this->price_to, 0, '.', ' ') : '');
	}
	
	public function getListItems() {
		$items = array();
		
		$records = $this->ordered()->findAll();
		foreach ($records as $record) {
			$items[$record->id] = 
				$record->getRussianName();
		}

		return $items;
	}
}