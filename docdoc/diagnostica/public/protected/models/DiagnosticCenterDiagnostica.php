<?php

/**
 * This is the model class for table "diagnostic_center_diagnostica".
 *
 * The followings are the available columns in table 'diagnostic_center_diagnostica':
 * @property integer $diagnostic_center_id
 * @property integer $diagnostica_id
 * @property integer $price
 */
class DiagnosticCenterDiagnostica extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return DiagnosticCenterDiagnostica the static model class
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
		return 'diagnostic_center_diagnostica';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('diagnostic_center_id, diagnostica_id', 'required'),
			array('diagnostic_center_id, diagnostica_id, price', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('diagnostic_center_id, diagnostica_id, price', 'safe', 'on'=>'search'),
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
		//	'diagnosticCenter' => array(self::BELONG_TO, 'DiagnosticCenter', 'id'),
		//	'diagnostic' => array(self::BELONG_TO, 'Diagnostica', 'id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'diagnostic_center_id' => 'Diagnostic Center',
			'diagnostica_id' => 'Diagnostica',
			'price' => 'Price',
		);
	}
	
	public function getListPrices($cid)
	{
		$diagnostics = array();
		$items = self::model()->findAll("diagnostic_center_id=:cid", compact('cid'));
		foreach($items as $item){
			$diagnostics[$item['diagnostica_id']] = $item['price'];
		}
 
		return $diagnostics;
	}
    
    public function getListSpecialPrices($cid)
	{
		$diagnostics = array();
		$items = self::model()->findAll("diagnostic_center_id=:cid", compact('cid'));
		foreach($items as $item){
			$diagnostics[$item['diagnostica_id']] = $item['special_price'];
		}
 
		return $diagnostics;
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

		$criteria->compare('diagnostic_center_id',$this->diagnostic_center_id);
		$criteria->compare('diagnostica_id',$this->diagnostica_id);
		$criteria->compare('price',$this->price);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}