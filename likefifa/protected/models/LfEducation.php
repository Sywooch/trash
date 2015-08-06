<?php

/**
 * This is the model class for table "lf_education".
 *
 * The followings are the available columns in table 'lf_education':
 * @property integer $id
 * @property integer $master_id
 * @property string $organization
 * @property string $course
 * @property string $specialization
 * @property integer $graduation_year
 *
 * The followings are the available model relations:
 * @property LfMaster $master
 */
class LfEducation extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return LfEducation the static model class
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
		return 'lf_education';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('master_id, organization', 'required'),
			array('master_id, graduation_year', 'numerical', 'integerOnly'=>true),
			array('organization, course, specialization', 'length', 'max'=>512),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, master_id, organization, course, specialization, graduation_year', 'safe', 'on'=>'search'),
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
			'id' => 'ID',
			'master_id' => 'Master',
			'organization' => 'Organization',
			'course' => 'Course',
			'specialization' => 'Specialization',
			'graduation_year' => 'Graduation Year',
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
		$criteria->compare('master_id',$this->master_id);
		$criteria->compare('organization',$this->organization,true);
		$criteria->compare('course',$this->course,true);
		$criteria->compare('specialization',$this->specialization,true);
		$criteria->compare('graduation_year',$this->graduation_year);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Вызывается после сохранения модели
	 *
	 * @return void
	 */
	protected function afterSave()
	{
		if ($this->master) {
			$this->master->updateRating();
		}

		return parent::afterSave();
	}

	/**
	 * Вызывается после удаления модели
	 *
	 * @return void
	 */
	protected function afterDelete()
	{
		if ($this->master) {
			$this->master->updateRating();
		}

		return parent::afterDelete();
	}
}