<?php

/**
 * This is the model class for table "doctor_appointment".
 *
 * The followings are the available columns in table 'doctor_appointment':
 * @property integer $id
 * @property integer $doctor_id
 * @property string $created
 * @property integer $done
 * @property string $name
 * @property string $phone
 *
 * The followings are the available model relations:
 * @property Doctor $doctor
 */
class DoctorAppointment extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return DoctorAppointment the static model class
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
		return 'doctor_appointment';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('doctor_id, name, phone', 'required'),
			array('doctor_id, done', 'numerical', 'integerOnly'=>true),
			array('name, phone', 'length', 'max'=>512),
			array('name, phone', 'filter', 'filter' => 'strip_tags'),
			array('name, phone', 'filter', 'filter' => 'htmlspecialchars'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('doctor, created, done, name, phone', 'safe', 'on'=>'search'),
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
				'condition' => 'done = 0',
			),
			'onlyDone' => array(
				'condition' => 'NOT (done = 0)',
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
			'doctor_id' => 'Врач',
			'doctor' => 'Врач',
			'created' => 'Дата заявки',
			'done' => 'Выполнено',
			'name' => 'Имя клиента',
			'phone' => 'Телефон',
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
		$criteria->with = 'doctor';

		$criteria->compare('doctor.id',$this->doctor);
		$criteria->compare('t.created',$this->created,true);
		$criteria->compare('t.done',$this->done);
		$criteria->compare('t.name',$this->name,true);
		$criteria->compare('t.phone',$this->phone,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'pagination'=>array(
				'pageSize' => 20,
			),
		));
	}
}