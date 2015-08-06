<?php

namespace dfs\docdoc\models;

use dfs\docdoc\extensions\AdvancedCActiveRecord;
use dfs\docdoc\validators\UniqueAttributesValidator;

/**
 * @package models
 *
 * @property int          $partner_id идентификатор партнера
 * @property int          $city_id    идентификатор города
 * @property int          $phone_id   идентификатор телефона
 *
 * @property PartnerModel $partner
 * @property PhoneModel   $phone
 * @property CityModel    $city
 *
 * @method PartnerPhoneModel[] findAll
 */
class PartnerPhoneModel extends AdvancedCActiveRecord
{
	/**
	 * Возвращает имя связанной таблицы базы данных
	 *
	 * @return string
	 */
	public function tableName()
	{
		return 'partner_phones';
	}

	/**
	 * Возвращает первичный ключ
	 *
	 * @return string[]
	 */
	public function primaryKey()
	{
		return ['partner_id', 'city_id', 'phone_id'];
	}

	/**
	 * Возвращает правила проверки для атрибутов модели
	 *
	 * @return array
	 */
	public function rules()
	{
		return [
			[
				'partner_id, city_id, phone_id',
				'required'
			],
			[
				'partner_id, city_id, phone_id',
				'numerical',
				'integerOnly' => true
			],
			[
				'partner_id, city_id, phone_id',
				'safe',
				'on' => 'search'
			],
			[
				'partner_id',
				UniqueAttributesValidator::class,
				'with'    => 'city_id,phone_id',
				'message' => 'Такая комбинация уже существует'
			],
			[
				'phone_id',
				'exist',
				'attributeName' => 'id',
				'className' => PhoneModel::class,
				'allowEmpty' => false,
				'message' => 'Телефон не найден',
				'skipOnError' => true,
			],
			[
				'phone_id',
				'exist',
				'attributeName' => 'id',
				'className' => PhoneModel::class,
				'allowEmpty' => false,
				'criteria' => ['with' => 'provider', 'condition' => 'provider.enabled'],
				'message' => 'Телефон не активен',
				'skipOnError' => true,
			],
		];
	}

	/**
	 * Возвращает связи между объектами
	 *
	 * @return string[]
	 */
	public function relations()
	{
		return [
			'partner' => [
				self::BELONGS_TO,
				'\dfs\docdoc\models\PartnerModel',
				'partner_id',
			],
			'city'    => [
				self::BELONGS_TO,
				'\dfs\docdoc\models\CityModel',
				'city_id',
			],
			'phone'   => [
				self::BELONGS_TO,
				'\dfs\docdoc\models\PhoneModel',
				'phone_id',
			]
		];
	}

	/**
	 * Возвращает подписей полей
	 *
	 * @return string[]
	 */
	public function attributeLabels()
	{
		return array(
			'partner_id' => 'Партнер',
			'city_id'    => 'Город',
			'phone_id'   => 'Телефон',
		);
	}

	/**
	 * Возвращает статическую модель указанного класса.
	 *
	 * @param string $className название класса
	 *
	 * @return PartnerPhoneModel
	 */
	public static function model($className = __CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * После валидации
	 */
	public function afterValidate()
	{
		\Yii::app()->eventDispatcher->raiseEvent('onPhoneChangeAfterValidate', $this);

		parent::afterValidate();
	}

	/**
	 * После сохранения
	 */
	public function afterSave()
	{
		\Yii::app()->eventDispatcher->raiseEvent('onPhoneChangeAfterSave', $this);

		parent::afterSave();
	}

	/**
	 * Поиск по id телефона
	 *
	 * @param integer $id
	 *
	 * @return $this
	 */
	public function byPhoneId($id)
	{
		$this->getDbCriteria()
			->mergeWith(
				[
					'condition' => 'phone_id = :phone_id',
					'params' => [':phone_id' => $id]
				]
			);

		return $this;
	}

	/**
	 * Исключить из выборки партнера
	 *
	 * @param int $id
	 *
	 * @return $this
	 */
	public function excludePartner($id)
	{
		$this->getDbCriteria()
			->mergeWith(
				[
					'condition' => 'partner_id != :partner_id',
					'params' => [':partner_id' => $id]
				]
			);

		return $this;
	}
}