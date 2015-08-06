<?php

/**
 * This is the model class for table "messages".
 *
 * The followings are the available columns in table 'messages':
 *
 * @property integer $id
 * @property string  $message
 * @property string  $send_time
 * @property integer $type
 * @property string  $email
 * @property integer $master_id
 */
class Messages extends CActiveRecord
{

	/**
	 * Тип сообщения о подарке в 2000 рублей
	 */
	const TYPE_2000 = 1;

	/**
	 * Returns the static model of the specified AR class.
	 *
	 * @param string $className active record class name.
	 *
	 * @return Messages the static model class
	 */
	public static function model($className = __CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'messages';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('send_time', 'required'),
			array('type, master_id', 'numerical', 'integerOnly' => true),
			array('email', 'length', 'max' => 512),
			array('message', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, message, send_time, type, email, master_id', 'safe', 'on' => 'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array();
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id'        => 'ID',
			'message'   => 'Message',
			'send_time' => 'Send Time',
			'type'      => 'Type',
			'email'     => 'Email',
			'master_id' => 'Master',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria = new CDbCriteria;

		$criteria->compare('id', $this->id);
		$criteria->compare('message', $this->message, true);
		$criteria->compare('send_time', $this->send_time, true);
		$criteria->compare('type', $this->type);
		$criteria->compare('email', $this->email, true);
		$criteria->compare('master_id', $this->master_id);

		return new CActiveDataProvider($this, array(
			'criteria' => $criteria,
		));
	}
}