<?php

/**
 * This is the model class for table "doctor_request".
 *
 * The followings are the available columns in table 'doctor_request':
 * @property integer $id
 * @property integer $sector_id
 * @property integer $doctor_id
 * @property string $created
 * @property integer $done
 * @property string $name
 * @property string $phone
 *
 * The followings are the available model relations:
 * @property Sector $sector
 * @property Doctor $doctor
 * @property UndergroundStation[] $undergroundStations
 */
class DoctorRequest extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return DoctorRequest the static model class
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
		return 'doctor_request';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name, phone', 'required'),
			array('sector_id, doctor_id, done', 'numerical', 'integerOnly'=>true),
			array('name, phone', 'length', 'max'=>512),
			array('name, phone', 'filter', 'filter' => 'strip_tags'),
			array('name, phone', 'filter', 'filter' => 'htmlspecialchars'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, sector, doctor, created, done, name, phone', 'safe', 'on'=>'search'),
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
			'sector' => array(self::BELONGS_TO, 'Sector', 'sector_id'),
			'doctor' => array(self::BELONGS_TO, 'Doctor', 'doctor_id'),
			'undergroundStations' => array(self::MANY_MANY, 'UndergroundStation', 'doctor_request_address(doctor_request_id, underground_station_id)'),
		);
	}
	
	public function scopes() {
		return array(
			'onlyNew' => array(
				'condition' => '(done = 0 OR done IS NULL)',
			),
			'onlyDone' => array(
				'condition' => '(NOT (done = 0) OR done IS NOT NULL)',
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
			'sector_id' => 'Направление',
			'doctor_id' => 'Врач',
			'doctor' => 'Врач',
			'created' => 'Дата запроса',
			'done' => 'Выполнено',
			'name' => 'Имя клиента',
			'phone' => 'Телефон',
			'departure' => 'Выезд на дом',
		);
	}
	
	public function behaviors()
	{
		return array(
			'CAdvancedArBehavior' => array(
				'class' => 'application.extensions.CAdvancedArBehavior',
			),
			
			'CArIdsBehavior' => array(
				'class' => 'application.extensions.CArIdsBehavior',
			),
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
		$criteria->with = array('doctor', 'sector');

		$criteria->compare('id',$this->id);
		$criteria->compare('sector.id',$this->sector);
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