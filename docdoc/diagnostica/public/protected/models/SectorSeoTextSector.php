<?php

/**
 * This is the model class for table "sector_seo_text_sector".
 *
 * The followings are the available columns in table 'sector_seo_text_sector':
 * @property integer $sector_id
 * @property integer $sector_seo_text_id
 */
class SectorSeoTextSector extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return SectorSeoTextSector the static model class
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
		return 'sector_seo_text_sector';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('sector_id, sector_seo_text_id', 'required'),
			array('sector_id, sector_seo_text_id', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('sector_id, sector_seo_text_id', 'safe', 'on'=>'search'),
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
			'sector_id' => 'Sector',
			'sector_seo_text_id' => 'Sector Seo Text',
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

		$criteria->compare('sector_id',$this->sector_id);
		$criteria->compare('sector_seo_text_id',$this->sector_seo_text_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}