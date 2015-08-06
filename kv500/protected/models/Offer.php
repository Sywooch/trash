<?php

/**
 * This is the model class for table "offer".
 *
 * The followings are the available columns in table 'offer':
 * @property integer $id
 * @property string $name
 * @property string $email
 * @property string $text
 * @property integer $is_read
 */
class Offer extends CActiveRecord
{

	public $yesNoFlags = array(
		0 => "Нет",
		1 => "Да"
	);

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'offer';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('text', 'required'),
			array('is_read', 'numerical', 'integerOnly'=>true),
			array('name, email', 'length', 'max'=>255),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, name, email, text, is_read', 'safe', 'on'=>'search'),
			array('email', "email"),
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
			'id' => 'ID',
			'name' => 'Имя',
			'email' => 'Электронная почта',
			'text' => 'Предложение',
			'is_read' => 'Прочитано',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * Typical usecase:
	 * - Initialize the model fields with values from filter form.
	 * - Execute this method to get CActiveDataProvider instance which will filter
	 * models according to data in model fields.
	 * - Pass data provider to CGridView, CListView or any similar widget.
	 *
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('email',$this->email,true);
		$criteria->compare('text',$this->text,true);
		$criteria->compare('is_read',$this->is_read);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Offer the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	public function getReadFlag()
	{
		if (empty($this->yesNoFlags[$this->is_read])) {
			return null;
		}

		return $this->yesNoFlags[$this->is_read];
	}

	public function getCountNotRead()
	{
		$criteria = new CDbCriteria;
		$criteria->condition = "t.is_read = :is_read";
		$criteria->params["is_read"] = 0;
		$count = count($this->findAll($criteria));
		if ($count) {
			return " ({$count})";
		}

		return "";
	}
}
