<?php
/**
 * Created by PhpStorm.
 * User: a.tyutyunnikov
 * Date: 14.07.14
 * Time: 17:49
 */

namespace dfs\docdoc\models;

use dfs\docdoc\extensions\AdvancedCActiveRecord;
use dfs\docdoc\validators\UniqueAttributesValidator;
use CActiveDataProvider;
use CDbCriteria;

/**
 * Class ClinicPartnerPhone
 *
 * Телефоны партнеров для клиник
 *
 * @property int $partner_id
 * @property int $clinic_id
 * @property int $phone_id
 *
 * @property \dfs\docdoc\models\PhoneModel $phone
 * @property \dfs\docdoc\models\ClinicModel $clinic
 * @property \dfs\docdoc\models\PartnerModel $partner
 *
 * @method ClinicPartnerPhoneModel find
 * @method ClinicPartnerPhoneModel[] findAll
 * @method ClinicPartnerPhoneModel findByPk
 * @method ClinicPartnerPhoneModel cache
 * @method ClinicPartnerPhoneModel with
 */
class ClinicPartnerPhoneModel extends AdvancedCActiveRecord
{
	/**
	 * Телефонный номер, вводимый с клавиатуры
	 *
	 * @var string
	 */
	public $phoneNumber = "";

	/**
	 * @param string $className
	 *
	 * @return ClinicPartnerPhoneModel
	 */
	public static function model($className = __CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string
	 */
	public function tableName()
	{
		return 'clinic_partner_phone';
	}

	/**
	 * @return string
	 */
	public function primaryKey()
	{
		return ['partner_id', 'clinic_id'];
	}

	/**
	 * @return array
	 */
	public function relations()
	{
		return [
			'phone' => [
				self::BELONGS_TO,
				PhoneModel::class,
				'phone_id'
			],
			'clinic' => [
				self::BELONGS_TO,
				ClinicModel::class,
				'clinic_id',
			],
			'partner' => [
				self::BELONGS_TO,
				PartnerModel::class,
				'partner_id',
			],
		];
	}

	/**
	 * @param $phone
	 *
	 * @return $this
	 */
	public function byPhone($phone)
	{
		$this->getDbCriteria()
			->mergeWith(
				[
					'with' => [
						'phone' => [
							'select' => false,
							'joinType' => 'INNER JOIN',
							'condition' => "phone.number = :phone",
							'params'    => [':phone' => $phone]
						],
					]
				]
			);

		return $this;
	}

	/**
	 * Правила валидации для атрибутов модели
	 *
	 * @return array
	 */
	public function rules()
	{
		return [
			['clinic_id, partner_id, phone_id', 'required'],
			['clinic_id, partner_id, phone_id', 'numerical', 'integerOnly' => true],
			['clinic_id, partner_id, phoneNumber', 'safe', 'on' => 'search'],
			[
				'clinic_id',
				UniqueAttributesValidator::class,
				'with' => 'partner_id',
				'message' => 'Такая пара "клиника-партнер" уже существует'
			],
			['clinic_id', 'exist', 'attributeName' => 'id', 'className' => ClinicModel::class],
			['partner_id', 'exist', 'attributeName' => 'id', 'className' => PartnerModel::class],
			[
				'phone_id',
				'exist',
				'attributeName' => 'id',
				'className' => PhoneModel::class,
				'allowEmpty' => false,
				'message' => 'Телефон не найден'
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
	 * Поиск
	 *
	 * @return CActiveDataProvider
	 */
	public function search()
	{
		$criteria = new CDbCriteria;

		$criteria->with = ["phone", "partner", "clinic"];
		$criteria->together = true;

		$criteria->compare('t.clinic_id', $this->clinic_id);
		$criteria->compare('t.partner_id', $this->partner_id);
		$criteria->compare('phone.number', $this->phoneNumber, true);

		return new CActiveDataProvider(
			$this,
			[
				'criteria' => $criteria,
				'pagination' => [
					'pageSize' => 50,
				],
				'sort' => [
					'attributes' => [
						'clinic_id' => [
							'asc' => 'clinic.name',
							'desc' => 'clinic.name DESC',
						],
						'partner_id' => [
							'asc' => 'partner.name',
							'desc' => 'partner.name DESC',
						],
						'phoneNumber' => [
							'asc' => 'phone.number',
							'desc' => 'phone.number DESC',
						],
					],
				],
			]
		);
	}

	/**
	 * Названия меток для атрибутов
	 *
	 * @return string[]
	 */
	public function attributeLabels()
	{
		return [
			'clinic_id' => 'Клиника',
			'partner_id' => 'Партнер',
			'phoneNumber' => 'Подменный телефон',
		];
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
	 * Добавляет в критерии выборки выбор по идентификатору слиники
	 *
	 * @param integer $clinicId идентификатор клиники
	 *
	 * @return $this
	 */
	public function byClinicId($clinicId)
	{

		$criteria = new CDbCriteria;
		$criteria->condition = $this->getTableAlias() . ".clinic_id = :clinic_id";
		$criteria->params["clinic_id"] = $clinicId;

		$this->getDbCriteria()->mergeWith($criteria);

		return $this;
	}

	/**
	 * Добавляет в критерии выборки выбор по идентификатору партнера
	 *
	 * @param integer $partnerId идентификатор партнера
	 *
	 * @return $this
	 */
	public function byPartnerId($partnerId)
	{

		$criteria = new CDbCriteria;
		$criteria->condition = $this->getTableAlias() . ".partner_id = :partner_id";
		$criteria->params["partner_id"] = $partnerId;

		$this->getDbCriteria()->mergeWith($criteria);

		return $this;
	}

	/**
	 * Выборка с моделью телефона
	 *
	 * @return $this
	 */
	public function withPhone()
	{

		$criteria = new CDbCriteria();
		$criteria->with = [
			'phone' => [
				'joinType' => 'INNER JOIN',
			],
		];

		$this->getDbCriteria()->mergeWith($criteria);

		return $this;
	}
} 
