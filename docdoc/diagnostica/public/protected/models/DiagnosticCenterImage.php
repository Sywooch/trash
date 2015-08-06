<?php

/**
 * This is the model class for table "diagnostic_center_image".
 *
 * The followings are the available columns in table 'diagnostic_center_image':
 * @property integer $id
 * @property integer $diagnostic_center_id
 * @property string $image
 * @property string $image_description
 */
class DiagnosticCenterImage extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return DiagnosticCenterImage the static model class
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
		return 'diagnostic_center_image';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('diagnostic_center_id, image', 'required'),
			array('diagnostic_center_id', 'numerical', 'integerOnly'=>true),
			array('image', 'file', 'types'=>'jpg, gif, png', 'allowEmpty' => true),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, diagnostic_center_id, image, image_description', 'safe', 'on'=>'search'),
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
			'diagnostic_center_id' => 'Diagnostic Center',
			'image' => 'Image',
			'image_description' => 'Image Description',
		);
	}
	
	public function getPhotos($cid)
	{
		$photos = array();
		$crit = new CDbCriteria;
		$crit->distinct = true;
		$crit->addCondition("diagnostic_center_id=$cid");
		$data = self::model()->findAll($crit);
		foreach($data as $item)
			$photos[] = $item->image;
			
		return $photos;
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
		$criteria->compare('diagnostic_center_id',$this->diagnostic_center_id);
		$criteria->compare('image',$this->image,true);
		$criteria->compare('image_description',$this->image_description,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
	public function behaviors()
	{
		return array(
			'CarFileUploadBehavior' => array(
				'class' => 'application.extensions.CArFileUploadBehavior',
			)
		);
	}
}