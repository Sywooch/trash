<?php
/**
 * Created by PhpStorm.
 * User: atyutyunnikov
 * Date: 25.03.15
 * Time: 12:05
 */

namespace dfs\docdoc\models;

use CActiveDataProvider;
use CDbCriteria;

/**
 * Class PhoneProviderModel
 *
 * @package dfs\docdoc\models
 *
 * @property integer $id
 * @property string $name
 * @property boolean $enabled
 *
 * @method PhoneProviderModel[] findAll
 */
class PhoneProviderModel extends \CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 *
	 * @param string $className
	 *
	 * @return static
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
		return 'phone_provider';
	}

	/**
	 * @return array
	 */
	public function rules()
	{
		return [
			['name', 'unique'],
			['enabled', 'boolean'],
			['id, name, enabled', 'safe', 'on' => 'search'],
		];
	}

	/**
	 * @return array
	 */
	public function relations()
	{
		return [
			'phones' => [
				self::HAS_MANY,
				PhoneModel::class,
				'provider_id'
			],
			'usedPhones' => [
				self::HAS_MANY,
				PhoneModel::class,
				'provider_id',
				'condition' => 'usedPhones.model_name is not null',
			],
			'unusedPhones' => [
				self::HAS_MANY,
				PhoneModel::class,
				'provider_id',
				'condition' => 'unusedPhones.model_name is null',
			],
		];
	}

	/**
	 * @return array
	 */
	public function attributeLabels()
	{
		return [
			'name' => 'Имя',
			'enabled' => 'Вкл / Выкл'
		];
	}

	/**
	 * Поиск
	 *
	 * @return CActiveDataProvider
	 */
	public function search()
	{
		$criteria = new CDbCriteria;

		$criteria->compare($this->getTableAlias() . '.id', $this->id);
		$criteria->compare($this->getTableAlias() . '.name', $this->name, true);
		$criteria->compare($this->getTableAlias() . '.enabled', $this->enabled);
		$criteria->with = 'phones';

		return new CActiveDataProvider(
			$this,
			[
				'criteria' => $criteria,
				'pagination' => [
					'pageSize' => 50,
				],
			]
		);
	}
}