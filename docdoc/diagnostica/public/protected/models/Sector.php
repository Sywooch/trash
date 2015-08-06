<?php

/**
 * This is the model class for table "sector".
 *
 * The followings are the available columns in table 'sector':
 * @property integer $id
 * @property string $name
 * @property integer doctor_count_all
 * @property integer doctor_count_active
 * @property string rewrite_name
 *
 * The followings are the available model relations:
 * @property Doctor[] $doctors
 * @property SectorSeoText[] $sectorSeoTexts
 */
class Sector extends CActiveRecord
{
	const ANY_SECTOR = 'по всем специальностям';
	
	/**
	 * Returns the static model of the specified AR class.
	 * @return Sector the static model class
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
		return 'sector';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name,rewrite_name', 'required'),
			array('name,rewrite_name', 'length', 'max'=>512),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, name,rewrite_name', 'safe', 'on'=>'search'),
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
			'doctors' => array(self::MANY_MANY, 'Doctor', 'doctor_sector(sector_id, doctor_id)'),
			'sectorSeoTexts' => array(self::MANY_MANY, 'SectorSeoText', 'sector_seo_text_sector(sector_id, sector_seo_text_id)'),
		);
	}
	
	public function scopes()
	{
		return array(
			'ordered' => array(
				'order' => 'name ASC',
			),
			'withActiveDoctors' => array(
				'condition' => 'doctor_count_active > 0'
			)
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'name' => 'Название направления',
			'rewrite_name' => 'Алиас для ЧПУ',
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
		$criteria->compare('name',$this->name,true);
		$criteria->compare('rewrite_name',$this->rewrite_name,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'pagination'=>array(
				'pageSize' => 20,
			),
		));
	}
	
	public function getListItems($withEmpty = false) {
		$items = array();
		
		if ($withEmpty) {
			$items[''] = 'нет направления';
		}
		
		$sectors = $this->ordered()->findAll();
		foreach ($sectors as $sector) {
			$items[$sector->id] = $sector->name;
		}

		return $items;
	}
	
	protected function replaceEnding($word, $endings) {
		foreach ($endings as $endingFrom => $endingTo) {
			if (preg_match('/'.$endingFrom.'$/u', $word)) {
				return preg_replace('/'.$endingFrom.'$/u', $endingTo, $word);
			}
		}
	}
	
	protected function wordNominative($word, $many) {
		if (!$many) return $word;
		
		return $this->replaceEnding($word, array(
			'р' => 'ры',
			'г' => 'ги',
			'т' => 'ты',
			'д' => 'ды',
			'ий' => 'ие',
		));
	}
	
	protected function wordGenitive($word, $many) {
		if (!$many) {
			return $this->replaceEnding($word, array(
				'р' => 'ра',
				'г' => 'га',
				'т' => 'та',
				'д' => 'да',
				'ий' => 'ого',
			));
		}
		else {
			return $this->replaceEnding($word, array(
				'р' => 'ров',
				'г' => 'гов',
				'т' => 'тов',
				'д' => 'дов',
				'ий' => 'их',
			));
		} 
	}
	
	protected function wordDative($word, $many) {
		if (!$many) {
			return $this->replaceEnding($word, array(
				'р' => 'ру',
				'г' => 'гу',
				'т' => 'ту',
				'д' => 'ду',
				'ий' => 'ому',
			));
		}
		else {
			return $this->replaceEnding($word, array(
				'р' => 'рам',
				'г' => 'гам',
				'т' => 'там',
				'д' => 'дам',
				'ий' => 'им',
			));
		} 
	}
	
	protected function parseWords($words, $many, $callback) {
		return preg_replace_callback(
			'/([a-zа-яё]+)/u',
			function ($matches) use ($callback, $many) {
				return $this->{$callback}($matches[1], $many);
			},
			$words
		);
	}
	
	public function nameInNominative($many = false) {
		return $this->parseWords($this->name, $many, 'wordNominative');
	}
	
	public function nameInGenitive($many = false) {
		return $this->parseWords($this->name, $many, 'wordGenitive');
	}
	
	public function nameInDative($many = false) {
		return $this->parseWords($this->name, $many, 'wordDative');
	}
	
}