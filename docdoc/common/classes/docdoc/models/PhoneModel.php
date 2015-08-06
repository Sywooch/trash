<?php

namespace dfs\docdoc\models;

use dfs\docdoc\extensions\AdvancedCActiveRecord;
use dfs\docdoc\objects\Phone;
use CDbCriteria;
use CActiveDataProvider;
use CActiveRecord;

/**
 * модель для таблицы phone
 *
 * @property integer $id
 * @property string $number
 * @property integer $provider_id
 * @property integer $partner_id
 * @property string $comment
 * @property string $model_name
 * @property string $mtime
 * @property string $muser_id
 *
 * @method PhoneModel findByPk
 * @method PhoneModel find
 * @method PhoneModel[] findAll
 *
 * @property PartnerModel $partner
 * @property PhoneProviderModel $provider
 * @property CityModel[] $cities вызывается не как релейшон а через магический метод getCities()
 * @property ClinicPartnerPhoneModel $clinicPartnerPhones
 * @property ClinicModel[] $clinics
 * @property PartnerPhoneModel[] $partnerPhones
 */
class PhoneModel extends CActiveRecord
{
	public $status;

	const STATUS_FREE = 1;
	const STATUS_BUSY = 2;
	const STATUS_DISABLED = 3;

	/**
	 * Returns the static model of the specified AR class.
	 *
	 * @param string $className
	 *
	 * @return PhoneModel the static model class
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
		return 'phone';
	}

	/**
	 * правила валидации
	 *
	 * @return array
	 */
	public function rules()
	{
		return [
			[
				'number',
				'dfs\docdoc\validators\PhoneValidator',
				'allowEmpty' => false,
			],
			[
				'number',
				'safe',
				'on' => 'insert, update'
			],
			[
				'id, number, status, muser_id, mtime, model_name',
				'safe',
				'on' => 'search'
			],
			[
				'number',
				'unique',
				'message' => 'Такой телефон уже существует.'
			],
			['comment', 'safe'],
			['partner_id', 'exist', 'attributeName' => 'id', 'className' => PartnerModel::class],
			['provider_id', 'exist', 'attributeName' => 'id', 'className' => PhoneProviderModel::class]
		];
	}

	/**
	 * Отношения
	 *
	 * @return array
	 */
	public function relations()
	{
		return [
			'partner'      => [
				self::BELONGS_TO,
				PartnerModel::class,
				'partner_id',
			],
			'provider' => [
				self::BELONGS_TO,
				PhoneProviderModel::class,
				'provider_id',
			],
			'muser' => [
				self::BELONGS_TO,
				UserModel::class,
				'muser_id',
			],
			'clinicPartnerPhones' => [
				self::HAS_MANY,
				ClinicPartnerPhoneModel::class,
				'phone_id'
			],
			'clinics' => [
				self::HAS_MANY,
				ClinicModel::class,
				['asterisk_phone' => 'number'],
			],
			'partnerPhones' => [
				self::HAS_MANY,
				PartnerPhoneModel::class,
				'phone_id',
			]
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
		$criteria->compare($this->getTableAlias() . '.number', $this->number, true);
		$criteria->compare($this->getTableAlias() . '.provider_id', $this->provider_id);
		$criteria->compare($this->getTableAlias() . '.partner_id', $this->partner_id);
		$criteria->compare($this->getTableAlias() . '.comment', $this->comment, true);
		$criteria->compare($this->getTableAlias() . '.mtime', $this->mtime, true);
		$criteria->compare($this->getTableAlias() . '.model_name', $this->model_name);
		$criteria->compare('muser.user_login', $this->muser_id, true);

		switch ($this->status) {
			case self::STATUS_BUSY:
				$criteria->addCondition($this->getTableAlias() . '.model_name is not null and provider.enabled');
				break;
			case self::STATUS_FREE:
				$criteria->addCondition($this->getTableAlias() . '.model_name is null and provider.enabled');
				break;
			case self::STATUS_DISABLED:
				$criteria->addCondition('provider.enabled = 0');
				break;
		}

		$criteria->with = [
			'provider',
			'partner',
			'muser'
		];

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

	/**
	 * Геттер для $this->number
	 *
	 * $this->number = '+7 (912) 123-12-12';
	 *
	 * echo $this->number; //выведет 79121231212 (сработает Phone::__toString())
	 * var_dump($this->number->isValid()); //true
	 * echo $this->number->prettyFormat('8 '); //выведет 8 (912) 123-12-12
	 *
	 * @param $number
	 *
	 * @return Phone
	 */
	public function getNumber($number)
	{
		return new Phone($number);

	}

	/**
	 * Список статусов
	 *
	 * @return array
	 */
	public static function getStatusList()
	{
		return [
			self::STATUS_FREE => 'Не занят',
			self::STATUS_BUSY => 'Занят',
			self::STATUS_DISABLED => 'Не активен'
		];
	}

	/**
	 * Определяет существует такой телефон или нет.
	 * Если не существует, пытается его создать
	 *
	 * @param string $number
	 *
	 * @return PhoneModel|null
	 */
	public function createPhone($number)
	{
		$number = Phone::strToNumber($number);

		$phone = PhoneModel::model()->findByAttributes(['number' => $number]);

		if ($phone !== null) {
			return $phone;
		}

		$phone = new PhoneModel();
		$phone->number = $number;

		return $phone->save() ? $phone : null;
	}

	/**
	 * Находит и получает список отформатированных телефонов по части телефона
	 * Используется для autocomplete в БО
	 *
	 * @param string $term искомое совпадение
	 *
	 * @return string[]
	 */
	public function getFormatListByTerm($term)
	{
		$number = $this->getNumber($term);

		$list = [];

		$criteria = new CDbCriteria;
		$criteria->condition = "t.number LIKE :number";
		$criteria->params["number"] = "{$number}%";

		foreach ($this->findAll($criteria) as $model) {
			$list[] = $model->getPhone()->prettyFormat('+7 ');
		}

		return $list;
	}

	/**
	 * Возвращает объект телефона
	 *
	 * @return Phone
	 */
	public function getPhone()
	{
		return new Phone($this->number);
	}

	/**
	 * Названия меток для атрибутов
	 *
	 * @return string[]
	 */
	public function attributeLabels()
	{
		return [
			'id'     => 'ID',
			'number' => 'Номер',
			'provider_id' => 'Провайдер',
			'partner_id' => 'Партнер',
			'comment' => 'Комментарий',
			'status' => 'Статус',
			'mtime' => 'Время изменения',
			'muser_id' => "Кто изменял",
			'model_name' => 'Тип',
		];
	}

	/**
	 * Получает идентификатор по номеру
	 *
	 * @param string $number номер телефона в формате +Х (ХХХ) ХХХ-ХХ-ХХ
	 *
	 * @return int|null
	 */
	public function getIdByNumber($number)
	{
		if (!$number) {
			return null;
		}

		$number = $this->getNumber($number);

		$criteria = new CDbCriteria;
		$criteria->condition = "t.number = :number";
		$criteria->params["number"] = $number;
		$model = $this->find($criteria);

		if ($model) {
			return $model->id;
		}

		return -1;
	}

	/**
	 * Поиск по номеру
	 *
	 * @param string $number
	 *
	 * @return $this
	 */
	public function byNumber($number)
	{
		$this->getDbCriteria()
			->mergeWith(
				[
					'condition' => 'number = :number',
					'params'    => [':number' => $number]
				]
			);

		return $this;
	}

	/**
	 * @return string
	 */
	public function getStatus()
	{
		$statuses = self::getStatusList();

		if (!$this->provider->enabled) {
			$status = $statuses[self::STATUS_DISABLED];
		} else {
			if ($this->model_name) {
				$status = $statuses[self::STATUS_BUSY];
			} else {
				$status = $statuses[self::STATUS_FREE];
			}
		}

		return $status;
	}

	/**
	 * Перед сохранения
	 */
	public function beforeSave()
	{
		$this->mtime = date('c');

		if (php_sapi_name() != 'cli' && \Yii::app()->session['user']) {
			$this->muser_id = \Yii::app()->session['user']->idUser;
		} else {
			$this->muser_id = 0;
		}

		return parent::beforeSave();
	}

	/**
	 * Не использованные
	 *
	 * @return $this
	 */
	public function notUsed()
	{
		$this->getDbCriteria()
			->mergeWith(
				['condition' => 'model_name is null']
			);

		return $this;
	}

	/**
	 * Активные
	 *
	 * @return $this
	 */
	public function enabled()
	{
		$this->getDbCriteria()
			->mergeWith(
				[
					'condition' => 'provider.enabled = 1',
					'with' => 'provider'
				]
			);

		return $this;
	}

	public function bindObject(CActiveRecord $object)
	{
		if ($this->model_name && $this->model_name == $object->tableName()) {
			if ($object instanceof ClinicPartnerPhoneModel) {
				//если телефон занят но проставлен у родительской клиники - то пропускаю
				if ($object->clinic->parent_clinic_id) {
					if ($cph = ClinicPartnerPhoneModel::model()->byPartnerId($object->partner_id)->byClinicId($object->clinic->parent_clinic_id)->find()) {
						if ($cph->phone_id == $object->phone_id) {
							return true;
						}
					}
				}
			} elseif ($object instanceof ClinicModel) {
				if ($object->parentClinic) {
					$clinics = $object->parentClinic->branches;
					$clinics[] = $object->parentClinic;
				} else {
					$clinics = $object->branches;
				}

				foreach ($clinics as $clinic) {
					if ($clinic->asterisk_phone == $this->number) {
						return true;
					}
				}
			} elseif ($object instanceof CityModel) {
				//может быть у разных городов одни и теже телефоны
				return true;
			} elseif ($object instanceof PartnerPhoneModel) {
				//если нет такого телефона  у других партнеров
				if (!PartnerPhoneModel::model()->byPhoneId($this->id)->excludePartner($object->partner_id)->count()) {
					return true;
				}
			} else {
				return false;
			}
		} elseif ($this->model_name) {
			return false;
		} else {
			$this->model_name = $object->tableName();

			if ($object instanceof ClinicPartnerPhoneModel) {
				$this->partner_id = $object->partner_id;
			}

			return $this->save();
		}

		return false;
	}

	/**
	 * @param CActiveRecord $object
	 *
	 * @return bool
	 */
	public function unbindObject(CActiveRecord $object)
	{
		$unbind = false;

		if ($this->model_name == $object->tableName()) {
			if ($object instanceof ClinicPartnerPhoneModel && !$this->clinicPartnerPhones) {
				//если телефон не нахожу, то обнуляю model_name
				$unbind = true;
			} elseif ($object instanceof ClinicModel && !$this->clinics) {
				$unbind = true;
			} elseif ($object instanceof CityModel && !$this->cities) {
				$unbind = true;
			} elseif ($object instanceof PartnerPhoneModel && !$this->partnerPhones) {
				//убираю если ниодного нет занятого
				$unbind = true;
			}

			if ($unbind) {
				$this->model_name = null;
				$this->partner_id = null;

				return $this->save();
			} else {
				return false;
			}
		}

		return false;
	}

	/**
	 * Получить список городо где используется этот номер телефона
	 *
	 * @return CityModel[]
	 */
	public function getUsedCities()
	{
		$cities = [];

		if ($this->model_name == 'city') {
			$allCities = array_merge(CityModel::model()->bySitePhone($this->number)->findAll(),
				CityModel::model()->byOpinionPhone($this->number)->findAll());

			foreach ($allCities as $city) {
				!isset($cities[$city->id_city]) && $cities[$city->id_city] = $city;
			}
		} elseif ($this->model_name == 'clinic_partner_phone') {
			$clinicPartnerPhones = ClinicPartnerPhoneModel::model()->byPhone($this->number)->findAll();

			foreach ($clinicPartnerPhones as $cpp) {
				!isset($cities[$cpp->clinic->city_id]) && $cities[$cpp->clinic->city_id] = $cpp->clinic->clinicCity;
			}
		} elseif ($this->model_name == 'clinic') {
			$clinics = ClinicModel::model()->byReplacedPhone($this->number)->findAll();

			foreach ($clinics as $c) {
				!isset($cities[$c->city_id]) && $cities[$c->city_id] = $c->clinicCity;
			}
		} elseif ($this->model_name == 'partner_phones') {
			$partnerPhones = PartnerPhoneModel::model()->byPhoneId($this->id)->findAll();

			foreach ($partnerPhones as $pp) {
				!isset($cities[$pp->city_id]) && $cities[$pp->city_id] = $pp->city;
			}
		}

		return $cities;
	}

	/**
	 * Вместо релейшона ->cities
	 *
	 * @return CityModel[]
	 */
	public function getCities()
	{
		$cities = CityModel::model()->byOpinionPhone($this->number)->findAll();

		foreach (CityModel::model()->bySitePhone($this->number)->findAll() as $city) {
			!isset($cities[$city->id_city]) && $cities[$city->id_city] = $city;
		}

		return $cities;
	}

	/**
	 * Урлы на связанные объекты, использующие телефон
	 *
	 * @return array
	 */
	public function getRelatedUrls()
	{
		$host = 'https://' . \Yii::app()->params['hosts']['back'];
		$urls = [];

		foreach ($this->cities as $city) {
			$urls[] = [
				'url' => $host . '/2.0/city/update/' . $city->id_city,
				'text' => $city->title,
			];
		}

		foreach ($this->clinics as $clinic) {
			$urls[] = [
				'url' => $host . '/clinic/index.htm?id=' . $clinic->id,
				'text' => $clinic->short_name ?: ($clinic->rewrite_name ?: $clinic->name),
			];
		}

		foreach ($this->partnerPhones as $pp) {
			$urls[] = [
				'url' => $host . '/2.0/partner/update/' . $pp->partner_id,
				'text' => $pp->partner->login . ' => ' . $pp->city->title,
			];
		}

		foreach ($this->clinicPartnerPhones as $cpp) {
			$urls[] = [
				'url' => $host . '/2.0/clinicPartnerPhone/update/' . $cpp->clinic_id . '/?partner_id=' . $cpp->partner_id,
				'text' => $cpp->partner->login . ' => ' . $cpp->clinic->short_name,
			];
		}

		return $urls;
	}

	/**
	 * Полчить список возможных типов
	 *
	 * @return array
	 */
	public function getAllTypes()
	{
		return [
			ClinicModel::model()->tableName() => 'Клиника',
			ClinicPartnerPhoneModel::model()->tableName() => 'Партнер - Клиника',
			PartnerPhoneModel::model()->tableName() => 'Партнер - Город',
			CityModel::model()->tableName() => 'Город'
		];
	}

	/**
	 * Валидация телефона для  конкретного объекта
	 *
	 * @param AdvancedCActiveRecord $object
	 *
	 * @throws \CException
	 */
	public function validatePhoneForObject(AdvancedCActiveRecord $object)
	{
		if ($object instanceof CityModel) {
			$phones = ['site_phone', 'opinion_phone'];

			foreach ($phones as $phoneAttr) {
				if (!$object->getErrors($phoneAttr) && $object->isChanged($phoneAttr)) {
					if ($phone = self::model()->byNumber($object->$phoneAttr)->find()) {
						if (!$phone->bindObject($object)) {
							$object->addError($phoneAttr, 'Номер уже занят');
						}
					} else {
						$object->addError($phoneAttr, 'Номер не найден');
					}
				}
			}
		} elseif ($object instanceof PartnerPhoneModel) {
			if (!$object->getErrors('phone_id') && $object->isChanged('phone_id')) {
				if ($phone = PhoneModel::model()->findByPk($object->phone_id)) {
					if (!$phone->bindObject($object)) {
						$object->addError('phone_id', 'Телефон уже занят');
					}
				} else {
					$object->addError('phone_id', 'Телефон не найден');
				}
			}
		} elseif ($object instanceof ClinicPartnerPhoneModel) {
			if (!$object->getErrors() && $object->isChanged('phone_id')) {
				if (!$object->phone->bindObject($object)) {
					$object->addError('phone_id', 'Номер уже занят');
				}
			}
		} elseif ($object instanceof ClinicModel) {
			if (!$object->getErrors('asterisk_phone') && $object->isChanged('asterisk_phone')) {
				if ($phone = PhoneModel::model()->byNumber($object->asterisk_phone)->find()) {
					if (!$phone->bindObject($object)) {
						$object->addError('asterisk_phone', 'Подменный номер уже занят');
					}
				} else {
					$object->addError('asterisk_phone', 'Подменный номер не найден');
				}
			}
		} else {
			throw new \CException('Неизветный объект');
		}
	}

	/**
	 * Отвязка телефона от объекта
	 *
	 * @param AdvancedCActiveRecord $object
	 *
	 * @throws \CException
	 */
	public function unbindPhoneFromObject(AdvancedCActiveRecord $object)
	{
		if ($object instanceof CityModel) {
			$phones = ['site_phone', 'opinion_phone'];

			foreach ($phones as $phoneAttr) {
				if ($object->isChanged($phoneAttr) && $object->getOldValue($phoneAttr)) {
					if ($phone = PhoneModel::model()->byNumber($object->getOldValue($phoneAttr))->find()) {
						$phone->unbindObject($object);
					}
				}
			}
		} elseif ($object instanceof PartnerPhoneModel) {
			if ($object->isChanged('phone_id') && $object->getOldValue('phone_id')) {
				if ($oldPhone = PhoneModel::model()->findByPk($object->getOldValue('phone_id'))) {
					$oldPhone->unbindObject($object);
				}
			}
		} elseif ($object instanceof ClinicPartnerPhoneModel) {
			if ($object->isChanged('phone_id') && $object->getOldValue('phone_id')) {
				if ($oldPhone = PhoneModel::model()->findByPk($object->getOldValue('phone_id'))) {
					$oldPhone->unbindObject($object);
				}
			}
		} elseif ($object instanceof ClinicModel) {
			if ($object->isChanged('asterisk_phone') && $object->getOldValue('asterisk_phone')) {
				if ($oldPhone = PhoneModel::model()->byNumber($object->getOldValue('asterisk_phone'))->find()) {
					$oldPhone->unbindObject($object);
				}
			}
		} else {
			throw new \CException('Неизветный объект');
		}
	}
}
