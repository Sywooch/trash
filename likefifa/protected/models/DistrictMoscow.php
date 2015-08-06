<?php

/**
 * This is the model class for table "district_moscow".
 *
 * The followings are the available columns in table 'district_moscow':
 * @property integer $id
 * @property string $rewrite_name
 * @property string $name
 * @property integer $area_moscow_id
 *
 * The followings are the available model relations:
 * @property AreaMoscow $areaMoscow
 * @property LfMaster[] $masters
 * @property LfMasterDistrict[] $masterDistircts
 */
class DistrictMoscow extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return DistrictMoscow the static model class
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
		return 'district_moscow';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('rewrite_name, name, area_moscow_id', 'required'),
			array('area_moscow_id', 'numerical', 'integerOnly'=>true),
			array('rewrite_name, name', 'length', 'max'=>255),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, rewrite_name, name, area_moscow_id', 'safe', 'on'=>'search'),
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
			'areaMoscow' => array(self::BELONGS_TO, 'AreaMoscow', 'area_moscow_id'),
			'masters' => array(self::HAS_MANY, 'LfMaster', 'district_id'),
			'masterDistircts' => array(self::HAS_MANY, 'LfMasterDistrict', 'district_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'rewrite_name' => 'Rewrite Name',
			'name' => 'Name',
			'area_moscow_id' => 'Area Moscow',
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
		$criteria->compare('rewrite_name',$this->rewrite_name,true);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('area_moscow_id',$this->area_moscow_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
	public function scopes() {
		return array(
			'ordered' => array(
				'order' => 'name ASC',		
			)
		);
	}
	
	public function behaviors()
	{
		return array(
				'CArRewriteBehavior' => array(
						'class' => 'application.extensions.CArRewriteBehavior',
				),
		);
	}
	
	public function getListItems($withEmpty = false) {
		$items = array();
		$model = new self();
		if ($withEmpty) $items[''] = 'Нет района';
		$districts = $model->ordered()->findAll();
		foreach ($districts as $district) {
			$items[$district->id] = $district->name;
		}
	
		return $items;
	}
    
}