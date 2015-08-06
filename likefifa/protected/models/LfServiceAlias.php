<?php


namespace likefifa\models;

use CActiveDataProvider;
use LfService;
use LfSpecialization;
use likefifa\components\system\ActiveRecord;
use likefifa\components\system\DbCriteria;

/**
 * This is the model class for table "lf_service_aliases".
 *
 * Модель алиасов для услуг и специализаций
 *
 * @property string           $id
 * @property integer          $specialization_id
 * @property integer          $service_id
 * @property string           $alias
 *
 * The followings are the available model relations:
 * @property LfService        $service
 * @property LfSpecialization $specialization
 */
class LfServiceAlias extends ActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'lf_service_aliases';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return [
			['alias', 'required'],
			['specialization_id, service_id', 'numerical', 'integerOnly' => true],
			['alias', 'length', 'max' => 255],
			['id, specialization_id, service_id, alias', 'safe', 'on' => 'search'],
		];
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return [
			'service'        => [self::BELONGS_TO, 'LfService', 'service_id'],
			'specialization' => [self::BELONGS_TO, 'LfSpecialization', 'specialization_id'],
		];
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return [
			'id'                => 'ID',
			'specialization_id' => 'Специализация',
			'service_id'        => 'Услуга',
			'alias'             => 'Алиас',
		];
	}

	/**
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search()
	{
		$criteria = new DbCriteria;

		$criteria->compare('id', $this->id, true);
		$criteria->compare('specialization_id', $this->specialization_id);
		$criteria->compare('service_id', $this->service_id);
		$criteria->compare('alias', $this->alias, true);

		return new CActiveDataProvider(
			$this,
			[
				'criteria' => $criteria,
			]
		);
	}

	/**
	 * @param string $className active record class name.
	 *
	 * @return LfServiceAlias the static model class
	 */
	public static function model($className = __CLASS__)
	{
		return parent::model($className);
	}
}