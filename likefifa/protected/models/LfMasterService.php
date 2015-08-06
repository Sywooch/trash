<?php

/**
 * This is the model class for table "lf_master_service".
 *
 * The followings are the available columns in table 'lf_master_service':
 * @property integer $master_id
 * @property string $user_id
 * @property string $service
 *
 * The followings are the available model relations:
 * @property LfMaster $master
 */
class LfMasterService extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return LfMasterService the static model class
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
		return 'lf_master_service';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('master_id, user_id, service', 'required'),
			array('master_id', 'numerical', 'integerOnly'=>true),
			array('user_id', 'length', 'max'=>32),
			array('service', 'length', 'max'=>256),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('master_id, user_id, service', 'safe', 'on'=>'search'),
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
			'master' => array(self::BELONGS_TO, 'LfMaster', 'master_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'master_id' => 'Master',
			'user_id' => 'User',
			'service' => 'Service',
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

		$criteria->compare('master_id',$this->master_id);
		$criteria->compare('user_id',$this->user_id,true);
		$criteria->compare('service',$this->service,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}