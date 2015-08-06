<?php
namespace dfs\docdoc\models;

use CActiveDataProvider;
use CDbCriteria;
use dfs\docdoc\objects\Phone;

/**
 * Модель для таблицы partner
 *
 * @property integer $id
 * @property string  $name
 * @property string  $login
 * @property string  $password
 * @property string  $contact_name
 * @property string  $contact_phone
 * @property string  $contact_email
 * @property integer $city_id
 * @property string  $password_salt
 * @property integer $offer_accepted
 * @property string  $offer_accepted_timestamp
 * @property string  $offer_accepted_from_addresses
 * @property integer $cost_per_request
 * @property string  $param_client_uid_name
 * @property bool    $use_special_price
 * @property int     $request_kind
 * @property bool    $send_sms
 * @property bool    $send_sms_to_clinic
 * @property bool    $show_clinics_with_contracts
 * @property integer $not_merged_requests
 * @property string $json_params
 *
 * @property PhoneModel $phone
 * @property CityModel  $city
 * @property ClinicPartnerPhoneModel[]  $clinicPhones
 * @property PartnerPhoneModel[] $phones
 *
 * @method PartnerModel find
 * @method PartnerModel[] findAll
 * @method PartnerModel findByPk
 * @method PartnerModel findByAttributes
 * @method PartnerModel ordered()
 * @method PartnerModel cache()
 * @method PartnerModel with
 */
class PartnerModel extends \CActiveRecord
{
	/**
	 * Значение по умолчанию для телефонной очереди
	 *
	 * @var string
	 */
	public $phone_queue = QueueModel::QUEUE_PARTNER;

	/**
	 * Показывать ли ватермарки
	 *
	 * @var bool
	 */
	public $show_watermark = true;

	/**
	 * идентификатор яндекса
	 */
	const YANDEX_ID = 8;

	/**
	 * идентификатор smart media 2
	 * 112014
	 */
	const SMART_MEDIA_2 = 91;

	/**
	 * Сценарии
	 */
	const SCENARIO_ADMIN = 'SCENARIO_ADMIN';
	const SCENARIO_LANDING = 'SCENARIO_LANDING';

	/**
	 * Массив с логинами партнеров,
	 * которые необходимо показывать в отчете клиникам
	 * в личном кабинете
	 *
	 * @var array
	 */
	static public $showInLkRepopt = [
		'yandex' => 'Яндекс'
	];

	/**
	 * Статусы принятия заявок
	 *
	 * @var string[]
	 */
	public static $offerAcceptedFlags = array(
		1 => "Принято"
	);

	/**
	 * Массив с логинами партнеров, за заявки от которых с клиентов деньги не берутся.
	 *
	 * партнер => дата, с которой заявки от партнера стали бесплатными
	 *
	 * @var array
	 */
	static private $_freePartner = [
		'yandex' => '2014-03-01'
	];

	/**
	 * Существующий пароль
	 *
	 * @var string
	 */
	private $_password = "";

	/**
	 * Метод опредеяет являются ли заявки, от партнера $partnerName, созданные на дату $reqCreated бесплатными
	 *
	 * @param string $partnerName логин партнера
	 * @param int $reqCreated unix-метка времени создания заявки
	 *
	 * @return bool
	 */
	public static function isFreePartner($partnerName, $reqCreated)
	{
		return isset(self::$_freePartner[$partnerName])
		&& date('Y-m-d', $reqCreated) >= self::$_freePartner[$partnerName];
	}

	/**
	 * Returns the static model of the specified AR class.
	 *
	 * @param string $className
	 *
	 * @return PartnerModel the static model class
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
		return 'partner';
	}

	/**
	 * Первичный ключ
	 * @return string
	 */
	public function primaryKey()
	{
		return 'id';
	}

	/**
	 * в выборке оставляет все поля без пароля и соли
	 * @return object
	 */
	public function withoutSecureInfo()
	{
		$this->getDbCriteria()->mergeWith([
			'select' => [
				'id',
				'name',
				'login',
				'contact_name',
				'contact_phone',
				'contact_email',
				'city_id',
				'offer_accepted',
				'offer_accepted_timestamp',
				'offer_accepted_from_addresses',
				'cost_per_request',
				'param_client_uid_name',
				'use_special_price',
				'send_sms',
				'json_params',
			]
		]);

		return $this;
	}

	/**
	 * Отношения
	 *
	 * @return array
	 */
	public function relations()
	{
		return [
			'city'         => [ self::BELONGS_TO, CityModel::class, 'city_id' ],
			'clinicPhones' => [ self::HAS_MANY, ClinicPartnerPhoneModel::class, 'partner_id' ],
			'phones'       => [self::HAS_MANY, PartnerPhoneModel::class, 'partner_id'],
		];
	}

	/**
	 * Является партнер Яндексом или нет
	 *
	 * @return bool
	 */
	public function isYandex()
	{
		return ($this->login === 'yandex');
	}

	/**
	 * Является ли партнер мобильным приложением или нет
	 *
	 * @param int $partner_id
	 *
	 * @return bool
	 */
	public function isMobileApi($partner_id)
	{
		$partner = PartnerModel::model()->findByPk($partner_id);

		if ($partner === null) {
			return false;
		}

		if ($partner->name === 'iphone') {
			return true;
		}

		return false;

	}

	/**
	 * Правила валидации для атрибутов модели
	 *
	 * @return string[]
	 */
	public function rules()
	{
		return [
			['name, login, password, city_id, contact_email', 'required', 'except' => [ self::SCENARIO_LANDING ]],
			[
				'city_id, offer_accepted, cost_per_request, use_special_price, request_kind,
					send_sms, send_sms_to_clinic, show_clinics_with_contracts, not_merged_requests',
				'numerical',
				'integerOnly' => true
			],
			['show_watermark', 'boolean'],
			['name, contact_name, contact_phone, contact_email, phone_queue', 'length', 'max' => 64],
			['login, password_salt', 'length', 'max' => 16],
			['offer_accepted_from_addresses', 'length', 'max' => 45],
			['offer_accepted_timestamp', 'date', 'format' => 'yyyy-MM-dd hh:mm:ss'],
			[
				'id, name, login, contact_name, contact_phone, contact_email, city_id, offer_accepted,
					offer_accepted_timestamp, offer_accepted_from_addresses, cost_per_request,
					use_special_price, send_sms, send_sms_to_clinic, show_clinics_with_contracts, phone_queue',
				'safe',
				'on' => 'search'
			],
			['contact_email', "email"],
			[
				'contact_email',
				'unique',
				'message' => 'Пользователь с таким E-mail адресом уже существует.',
				'except' => [ self::SCENARIO_LANDING ],
			],
			[
				'contact_email',
				'unique',
				'message' => 'Ваш email-адрес уже зарегистрирован в нашей базе. Введите, пожалуйста, другой email-адрес',
				'on' => [ self::SCENARIO_LANDING ],
			],
			[
				'name, contact_name, contact_phone, offer_accepted_from_addresses',
				'filter',
				'filter' => 'strip_tags',
			],
			['json_params', 'safe'],
			['login', 'filter', 'filter' => 'strip_tags', 'except' => [ self::SCENARIO_LANDING ]],
		];
	}

	/**
	 * @param string $login
	 *
	 * @return $this
	 */
	public function byLogin($login)
	{
		$this->getDbCriteria()
			->mergeWith(
				[
					'condition' => 'login = :login',
					'params'    => [':login' => $login],
				]
			);

		return $this;
	}

	/**
	 * Проверяет пароль на соответствие текущему
	 *
	 * @param string $password
	 *
	 * @return bool
	 */
	public function checkPasswordForEquals($password)
	{
		return md5(md5($password . $this->password_salt)) === $this->password;
	}

	/**
	 * Поиск
	 *
	 * @return CActiveDataProvider
	 */
	public function search()
	{
		$criteria = new CDbCriteria;

		$criteria->compare('t.id', $this->id);
		$criteria->compare('t.name', $this->name, true);
		$criteria->compare('t.login', $this->login, true);
		$criteria->compare('t.contact_name', $this->contact_name, true);
		$criteria->compare('t.contact_phone', $this->contact_phone, true);
		$criteria->compare('t.contact_email', $this->contact_email, true);
		$criteria->compare('t.city_id', $this->city_id);
		$criteria->compare('t.offer_accepted', $this->offer_accepted);
		$criteria->compare('t.offer_accepted_timestamp', $this->offer_accepted_timestamp, true);
		$criteria->compare('t.offer_accepted_from_addresses', $this->offer_accepted_from_addresses, true);
		$criteria->compare('t.cost_per_request', $this->cost_per_request);
		$criteria->compare('t.use_special_price', $this->use_special_price);
		$criteria->compare('t.send_sms', $this->send_sms);
		$criteria->compare('t.send_sms_to_clinic', $this->send_sms_to_clinic);
		$criteria->compare('t.not_merged_requests', $this->not_merged_requests);
		$criteria->compare($this->getTableAlias() . '.phone_queue', $this->phone_queue);

		$criteria->with = ['phones', 'phones.phone'];
		$criteria->together = true;

		return new CActiveDataProvider(
			$this,
			[
				'criteria'   => $criteria,
				'pagination' => [
					'pageSize' => 50,
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
			'id'                            => 'ID',
			'name'                          => 'Название',
			'login'                         => 'Логин',
			'password'                      => 'Пароль',
			'contact_name'                  => 'Контактное лицо',
			'contact_phone'                 => 'Телефон контактного лица',
			'contact_email'                 => 'E-mail контактного лица',
			'city_id'                       => 'Город',
			'password_salt'                 => 'Соль для пароля',
			'offer_accepted'                => 'Предложение принято',
			'offer_accepted_timestamp'      => 'Время принятия предложения',
			'offer_accepted_from_addresses' => 'Адрес, с которого принято предложение',
			'cost_per_request'              => 'Стоимость каждого запроса',
			'phones_for_clinic'             => 'Телефоны для клиник',
			'use_special_price'             => 'Трансляция скидок',
			'request_kind'                  => 'Тип заявок',
			'send_sms'                      => 'Отправка СМС пациентам',
			'send_sms_to_clinic'            => 'Отправка СМС в клинику',
			'show_clinics_with_contracts'   => 'Выводить клиники с договорами',
			'not_merged_requests'           => 'Не склеивать заявки',
			'json_params'                   => 'Параметры (JSON)',
			'phone_queue'                   => 'Тел. очередь',
			'phoneNumbers'                  => 'Телефоны',
			'show_watermark'                => 'Показывать ватермарки',
		];
	}

	/**
	 * Получает номер телефона для партнера
	 *
	 * Выбираются все телефоны для партнера
	 * Если город не Москва, берется телефон города, если не нашлось берется Московский номер
	 *
	 * @param $cityId
	 * @return Phone
	 */
	public function getPhoneNumber($cityId = CityModel::MOSCOW_ID)
	{
		$phoneMoscow = null;

		foreach ($this->phones as $partnerPhone) {
			if ($partnerPhone->city_id == $cityId) {
				return new Phone($partnerPhone->phone->number);
			}
			if ($partnerPhone->city_id == CityModel::MOSCOW_ID) {
				$phoneMoscow = new Phone($partnerPhone->phone->number);
			}
		}

		return $phoneMoscow;
	}

	/**
	 * Получает номера телефонов
	 *
	 * @return Phone
	 */
	public function getPhoneNumbers()
	{
		$numbers = [];

		foreach ($this->phones as $partnerPhone) {
			$numbers[] = (new Phone($partnerPhone->phone->number))->prettyFormat("+7 ");
		}

		return implode(", ", $numbers); // временная заглушка
	}

	/**
	 * Получает название города
	 *
	 * @return string
	 */
	public function getCityTitle()
	{
		$model = $this->city;

		if ($model) {
			return $model->title;
		}

		return null;
	}

	/**
	 * Выполняется перед валидацией модели
	 *
	 * @return bool
	 */
	protected function beforeValidate()
	{
		if (!$this->password) {
			$this->password = $this->_password;
		}

		if ($this->json_params) {
			$error = $this->validateJsonParams($this->json_params);
			if ($error) {
				$this->addError('json_params', $error);
			}
		}

		return !$this->errors && parent::beforeValidate();
	}

	/**
	 * Проверка json_params
	 *
	 * @param $json
	 *
	 * @return null|string
	 */
	protected function validateJsonParams($json)
	{
		$error = null;
		$json = trim($json);

		if ($json) {
			$params = json_decode($json, true);
			if (!is_array($params)) {
				$error = 'Невалидный формат JSON';
			} else {
				foreach ($params as $item) {
					if (!is_array($item)) {
						$error = 'Ошибка данных';
					}
					elseif (!isset($item['GroupId']) || !isset(ServiceModel::$service_types[$item['GroupId']])) {
						$error = 'Неверный идентификатор группы (GroupId)';
					}
					elseif (!isset($item['Price']) || intval($item['Price']) <= 0) {
						$error = 'Не задана цена (Price)';
					}
					if ($error) {
						break;
					}
				}
			}
		}

		return $error;
	}

	/**
	 * Вызывается перед сохранением модели
	 *
	 * @return bool
	 */
	protected function beforeSave()
	{
		if ($this->login) {
			$this->login = trim($this->login);
		}

		if ($this->isNewRecord || $this->password !== $this->_password) {
			$this->password = self::makePassword($this->password, $this->password_salt);
		}

		return parent::beforeSave();
	}

	/**
	 * Вызывается после создания экземпляра модели
	 *
	 * @return void
	 */
	protected function afterFind()
	{
		$this->_password = $this->password;
	}

	/**
	 * Возвращает хэш пароля
	 *
	 * @param string $password пароль
	 * @param string $salt соль
	 *
	 * @return string
	 */
	public static function makePassword($password, $salt)
	{
		return md5(md5($password . $salt));
	}

	/**
	 * установка пароля
	 *
	 * @param string $password
	 *
	 * @return $this
	 */
	public function setPassword($password)
	{
		$this->password = self::makePassword($password, $this->password_salt);
		$this->_password = $this->password;

		return $this;
	}

	/**
	 * Проверка пароля
	 *
	 * @param string $password
	 *
	 * @return bool
	 */
	public function checkPassword($password)
	{
		return $this->password === self::makePassword($password, $this->password_salt);
	}

	/**
	 * Найти по логину или email
	 *
	 * @param string $login
	 *
	 * @return $this
	 */
	public function byLoginOrEmail($login)
	{
		$this->getDbCriteria()->mergeWith([
			'condition' => 't.login = :login OR t.contact_email = :login',
			'params' => [ 'login' => $login ],
		]);

		return $this;
	}

	/**
	 * Области выбора
	 *
	 * @return array
	 */
	public function scopes()
	{
		return array_merge(
			parent::scopes(),
			[
				'ordered' => [
					'order' => $this->getTableAlias() . '.name ASC',
				],
			]
		);
	}

	/**
	 * Получает массив из клиник и телефонов для партнера
	 *
	 * @return \dfs\docdoc\objects\Phone[]
	 */
	public function getClinicPhones()
	{
		$list = [];

		$clinicPartnerPhoneModels = ClinicPartnerPhoneModel::model()
			->cache(3600)
			->withPhone()
			->byPartnerId($this->id)
			->findAll();

		foreach ($clinicPartnerPhoneModels as $clinicPartnerPhoneModel) {
			$phoneModel = $clinicPartnerPhoneModel->phone;
			$list[$clinicPartnerPhoneModel->clinic_id] = $phoneModel->getNumber($phoneModel->number);
		}

		return $list;
	}

	/**
	 * Получение конфига виджета $widgetName с action $action
	 *
	 * @param string $widgetName
	 *
	 * @return array
	 */
	public function getWidgetConfig($widgetName)
	{
		$partnerWidget = PartnerWidgetModel::model()
			->byPartner($this->id)
			->byWidget($widgetName)
			->find();

		return $partnerWidget !== null ? $partnerWidget->getConfig() : null;
	}

	/**
	 * Массив цен по группам услуг
	 *
	 * @param string $filter
	 *
	 * @return array
	 */
	public function getGroupPrices($filter = null)
	{
		$prices = [
			0 => [],
		];

		if ($this->json_params) {
			$params = json_decode($this->json_params, true);
			foreach ($params as $item) {
				if (isset($item[$filter])) {
					$prices[intval($item[$filter])][$item['GroupId']] = [
						'GroupId' => $item['GroupId'],
						'Price' => $item['Price'],
					];
				} elseif (count($item) == 2 && is_null($filter)) {
					$prices[0][$item['GroupId']] = [
						'GroupId' => $item['GroupId'],
						'Price' => $item['Price'],
					];
				}
			}
		}

		return $prices;
	}

	/**
	 * Идентификаторы сайтов DocDoc'a
	 *
	 * @return int[]
	 */
	public function getDocDocPartners()
	{
		return \Yii::app()->db->createCommand("
			SELECT id FROM partner WHERE name LIKE 'dd.%'
		")->queryColumn();
	}

	/**
	 * Обновляет телефон для партнера
	 *
	 * @param array $list (ID города => номер телефона)
	 *
	 * @throws \CException
	 * @return void
	 */
	public function updatePartnerPhones($list)
	{
		foreach ($this->phones as $partnerPhone) {
			$partnerPhone->delete();
		}

		foreach ($list as $cityId => $phoneNumber) {
			$model = new PartnerPhoneModel();
			$model->partner_id = $this->id;
			$model->city_id = $cityId;
			$model->phone_id = PhoneModel::model()->getIdByNumber($phoneNumber);

			if (!$model->save()) {
				throw new \CException(var_export($model->getErrors(), true));
			}
		}
	}

	/**
	 * Выбрать с телефонами
	 *
	 * @return $this
	 */
	public function withPhones()
	{
		$this->getDbCriteria()
			->mergeWith(
				[
					'with' => [
						'phones' => [
							'joinType' => 'inner join',
							'with' => [
								'phone'
							]
						]
					]
				]
			);

		return $this;
	}
}
