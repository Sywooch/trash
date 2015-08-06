<?php

use likefifa\models\CityModel;

/**
 * Файл класса UndergroundLine
 *
 * Модель для работы с ветками метро
 *
 * @author  Mikhail Vasilyev <m.vasilyev@likefifa.ru>
 * @link    https://docdoc.megaplan.ru/task/1003365/card/
 * @package models
 *
 * @property int                  $id                  идентификатор
 * @property string               $name                название
 * @property string               $color               цвет
 * @property int                  $city_id             идентификатор города
 *
 * @property UndergroundStation[] $undergroundStations модели станций метро
 * @property CityModel            $city                модель города
 *
 * @method   UndergroundLine      ordered()
 */
class UndergroundLine extends CActiveRecord
{
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
		return array(
			array('name, color', 'filter', 'filter' => 'strip_tags'),
			array('name, color, city_id', 'required'),
			array('name', 'length', 'max'=>512),
			array('color', 'length', 'max'=>16),
			array('name', 'safe', 'on'=>'search'),
			array('city_id', 'numerical', 'integerOnly' => true),
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
			'city'                => array(
				self::BELONGS_TO,
				'likefifa\models\CityModel',
				'city_id'
			),
		);
	}
	
	public function scopes() {
		$alias = $this->getTableAlias();

		return array(
			'ordered' => array(
				'order' => "{$alias}.name ASC",
			),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id'      => 'ID',
			'name'    => 'Название ветки',
			'color'   => 'Цвет',
			'city_id' => 'Город',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		$criteria=new CDbCriteria;

		$criteria->compare('name', $this->name, true);
		$criteria->compare('city_id', $this->city_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'pagination' => array(
				'pageSize' => 50,
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

	/**
	 * Получает название города
	 *
	 * @return string
	 */
	public function getCityName()
	{
		$model = $this->city;
		if ($model) {
			return $model->name;
		}

		return null;
	}
}