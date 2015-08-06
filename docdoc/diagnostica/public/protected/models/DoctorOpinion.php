<?php

/**
 * This is the model class for table "doctor_opinion".
 *
 * The followings are the available columns in table 'doctor_opinion':
 * @property integer $id
 * @property integer $doctor_id
 * @property string $created
 * @property integer $allowed
 * @property integer $rating_qualification
 * @property integer $rating_attention
 * @property integer $rating_room
 * @property string $name
 * @property string $text
 *
 * The followings are the available model relations:
 * @property Doctor $doctor
 */
class DoctorOpinion extends CActiveRecord
{
	const CUT_COUNT = 3;

	/**
	 * Returns the static model of the specified AR class.
	 * @return DoctorOpinion the static model class
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
		return 'doctor_opinion';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('doctor_id, allowed, name, phone, text', 'required'),
			array('doctor_id, allowed, age', 'numerical', 'integerOnly'=>true),
			array('rating_qualification, rating_attention, rating_room', 'numerical', 'integerOnly' => true, 'min' => 1, 'max' => 5),
			array('age', 'numerical', 'min' => 1),
			array('name, phone', 'length', 'max'=>512),
			array('name, phone, text', 'filter', 'filter' => 'strip_tags'),
			array('name, phone, text', 'filter', 'filter' => 'htmlspecialchars'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, doctor, created, allowed, rating_qualification, rating_attention, rating_room, name, phone, text', 'safe', 'on'=>'search'),
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
			'doctor' => array(self::BELONGS_TO, 'Doctor', 'doctor_id'),
		);
	}

	public function scopes() {
		return array(
			'onlyNew' => array(
				'condition' => '(allowed = 0 OR allowed IS NULL)',
			),
			'onlyAllowed' => array(
				'condition' => '(allowed = 1)',
			),
			'ordered' => array(
				'order' => 'created ASC',
			)
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'doctor_id' => 'Врач',
			'doctor' => 'Врач',
			'created' => 'Дата создания',
			'allowed' => 'Отзыв опубликован',
			'rating_qualification' => 'Оценка квалификации',
			'rating_attention' => 'Оценка внимания',
			'rating_room' => 'Оценка кабинета',
			'name' => 'Имя пациента',
			'age' => 'Возраст пациента',
			'phone' => 'Контактный телефон',
			'text' => 'Текст отзыва',
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
		$criteria->with = array('doctor');

		$criteria->compare('id',$this->id);
		$criteria->compare('doctor.id',$this->doctor);
		$criteria->compare('t.created',$this->created,true);
		$criteria->compare('t.allowed',$this->allowed);
		$criteria->compare('t.rating_qualification',$this->rating_qualification);
		$criteria->compare('t.rating_attention',$this->rating_attention);
		$criteria->compare('t.rating_room',$this->rating_room);
		$criteria->compare('t.name',$this->name,true);
		$criteria->compare('t.phone',$this->phone,true);
		$criteria->compare('t.text',$this->text,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'pagination'=>array(
				'pageSize' => 20,
			),
		));
	}

	public function ageInRussian() {
		return
			$this->age
			.' '
			.RussianTextUtils::caseForNumber(
				intval($this->age),
				array('год', 'года', 'лет')
			);
	}

}