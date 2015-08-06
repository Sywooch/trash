<?php

namespace dfs\docdoc\models;

use CAdvancedArbehavior;
use dfs\docdoc\exceptions\SpamException;
use dfs\docdoc\objects\Phone;
use dfs\docdoc\objects\Rejection;
use dfs\docdoc\extensions\TextUtils;

/**
 * This is the model class for table "request".
 *
 * The followings are the available columns in table 'request':
 *
 * @property integer $req_id
 * @property integer $clientId
 * @property string $id_city
 * @property string $client_name
 * @property string $client_phone
 * @property string $req_comments
 * @property integer $req_created
 * @property integer $req_status
 * @property integer $req_user_id
 * @property integer $req_departure
 * @property integer $req_sector_id
 * @property integer $diagnostics_id
 * @property integer $diagnostics_other
 * @property integer $req_doctor_id
 * @property integer $req_type
 * @property integer $kind
 * @property integer $source_type
 * @property integer $doctor_request_id
 * @property integer $clinic_id
 * @property integer $req_client_stations
 * @property integer $opinion_id
 * @property string $record
 * @property string $record1
 * @property string $record2
 * @property string $url_record
 * @property string $records
 * @property integer $lk_status
 * @property integer $is_transfer
 * @property integer $date_admission  время приема, если в прошлом - значит клиент дошел ??
 * @property integer $appointment_time
 * @property integer $appointment_status
 * @property integer $request_cost
 * @property integer $call_occured
 * @property integer $call_later_time
 * @property integer $clinic_address_id
 * @property integer $status_sms
 * @property integer $actionpay_id
 * @property integer $transferred
 * @property string $age_selector
 * @property string $client_comments
 * @property string $last_call_id
 * @property integer $partner_id
 * @property integer $reject_reason
 * @property integer $destination_phone_id
 * @property string $date_record
 * @property string $add_client_phone
 * @property integer $transferred_clinic_id
 * @property integer $is_hot
 * @property integer $for_listener
 * @property string $enter_point
 * @property integer $partner_cost
 * @property integer $billing_status
 * @property integer $partner_status
 * @property string  $validation_code
 * @property string $date_billing
 * @property integer $processing_time
 * @property string  $token
 * @property string  $expire_time
 * @property string  $queue
 *
 *
 * relations
 *
 * @property DoctorModel $doctor
 * @property RequestPartnerModel $request_partner
 * @property PartnerModel $partner
 * @property ClinicModel $clinic
 * @property RequestHistoryModel[] $request_history
 * @property CityModel $city
 * @property StationModel[] $stations
 * @property PhoneModel $phone
 * @property BookingModel[] $booking
 * @property BookingModel $activeBooking
 * @property RequestRecordModel[] $request_record
 * @property SectorModel $sector
 * @property ClientModel $client
 * @property DiagnosticaModel $diagnostics
 * @property RequestStationModel[] $requestStations
 * @property UserModel $operator
 * @property DiagnosticClinicModel $diagnosticClinic
 *
 * @property string $scenario
 *
 *
 * The followings are the available model relations:
 *
 * @method RequestModel find
 * @method RequestModel findByPk
 * @method RequestModel[] findAll
 * @method RequestModel resetScope
 * @method RequestModel findByAttributes
 * @method int count
 * @method RequestModel with
 * @method RequestModel cache()
 */
class RequestModel extends \CActiveRecord
{
	/**
	 *	первоначальный статус модели
	 * @var int
	 */
	private $_prev_req_status = -1;

	/**
	 * первоначальное значение аттрибутов заявки
	 * @var null
	 */
	private $_prev_attr = null;

	/**
	 * @var int
	 */
	public $partner_id = 0;

	/**
	 * @var int
	 */
	public $req_status = RequestModel::STATUS_NEW;

	/**
	 * @var string
	 */
	public $age_selector = 'multy';

	/**
	 * Сценарии
	 */
	//Запись ко врачу
	const SCENARIO_SITE = 'SCENARIO_SITE';
	//заказать звонок
	const SCENARIO_CALL = 'SCENARIO_CALL';
	//заказать звонок
	const SCENARIO_ASTERISK = 'SCENARIO_ASTERISK';
	//изменение клиники
	const SCENARIO_SAVE_CLINIC = 'SCENARIO_SAVE_CLINIC';
	//обработка изменения состояния isAppointment телефонного разговора
	const SCENARIO_RECORD_APPOINTMENT = "SCENARIO_RECORD_APPOINTMENT";
	//изменение статуса заявки
	const SCENARIO_CHANGE_STATUS = "SCENARIO_CHANGE_STATUS";
	//для парсинга записей и создания заявок
	const SCENARIO_REPLACED_PHONE = 'SCENARIO_REPLACED_PHONE';
	//сохранение заявки оператором в БО
	const SCENARIO_OPERATOR = "SCENARIO_OPERATOR";
	//создание заявки партнером
	const SCENARIO_PARTNER = 'SCENARIO_PARTNER';
	//Запись на диагностику онлайн
	const SCENARIO_DIAGNOSTIC_ONLINE = 'SCENARIO_DIAGNOSTIC_ONLINE';
	//Создания пустой заявки в статусе STATUS_PRE_ORDER
	const SCENARIO_VALIDATE_PHONE = 'SCENARIO_VALIDATE_PHONE';
	//изменение из личного кабинета клиники
	const SCENARIO_LK_CLINIC = 'SCENARIO_LK_CLINIC';

	/**
	 * Сценарий для дублирования модели
	 *
	 * @var string
	 */
	const SCENARIO_DUPLICATE = 'SCENARIO_DUPLICATE';

	/**
	 * Точки входа
	 */
	const ENTER_POINT_SHORT_FORM = 'ShortForm';
	const ENTER_POINT_FULL_FORM = 'FullForm';
	const ENTER_POINT_CALL_ME_BACK = 'CallMeBack';
	const ENTER_POINT_DIRECT_CALL = 'DirectCall';
	const ENTER_POINT_DIAGNOSTICS = 'Diagnostics';
	const ENTER_POINT_OPERATOR = 'Operator';
	const ENTER_POINT_PARTNER_CALL = 'PartnerCall';
   //Запись к врачу через партнера- PartnerDoctor
	const ENTER_POINT_PARTNER_DOCTOR = 'PartnerDoctor';
	//Подбор врача через партнера- PartnerDoctor
	const ENTER_POINT_PARTNER_SEARCH = 'PartnerSearch';
	//Запись к клинику через партнера- PartnerDoctor
	const ENTER_POINT_PARTNER_CLINIC = 'PartnerClinic';
	const ENTER_POINT_MOBILE = 'Mobile';
	const ENTER_POINT_CLINIC_CALL = 'ClinicCall';

	// Статусы
	const STATUS_NEW                = 0;
	const STATUS_PROCESS            = 1;
	const STATUS_RECORD             = 2;
	const STATUS_CAME               = 3;
	const STATUS_REMOVED            = 4;
	const STATUS_REJECT             = 5;
	const STATUS_ACCEPT             = 6;
	const STATUS_CALL_LATER         = 7;
	const STATUS_RECALL             = 10;
	//заявка как-бы не настоящая, ожидает подтверждения
	const STATUS_PRE_CREATED        = 11;
	//запись была, но клиент не пришел на прием
	const STATUS_NOT_CAME           = 12;
	//была запись, но не удалось дозвониться до клиента, не известно, пришел он на прием или нет
	const STATUS_CAME_UNDEFINED     = 13;

	/**
	 * клиника отказывается платить за заявку в ЛК, сейчас логика этого статуса перенесена в BILLING_STATUS
	 * оставлено для обратной совестимости исторических данных
	 *
	 * @deprecated
	 */
	const STATUS_REJECT_BY_PARTNER  = 8;


	// Способы обращения
	const TYPE_WRITE_TO_DOCTOR  = 0;
	const TYPE_PICK_DOCTOR      = 1;
	const TYPE_CALL             = 2;
	const TYPE_CALL_TO_DOCTOR   = 3;
	const TYPE_ONLINE_RECORD = 4;

	/**
	 * Виды заявок
	 */
	const KIND_DOCTOR       = 0;
	const KIND_DIAGNOSTICS  = 1;
	const KIND_ANALYSIS     = 2;

	// Источники
	const SOURCE_SITE       = 1;
	const SOURCE_PHONE      = 2;
	const SOURCE_PARTNER    = 3;
	const SOURCE_YANDEX     = 4;
	const SOURCE_IPHONE     = 5;

	/**
	 * Состояние заявок для ЛК и биллинга
	 */
	const BILLING_STATE_NEW = 0;
	const BILLING_STATE_RECORD = 1;
	const BILLING_STATE_REJECT = 2;
	const BILLING_STATE_CAME = 3;
	const BILLING_STATE_REFUSED = 4;

	//заявка не в билинге
	const BILLING_STATUS_NO = 0;
	//заявка в билинге
	const BILLING_STATUS_YES = 1;
	//@todo при закрытии счета, когда деньги за заявку получены, проставлять таким заявкам этот статус, который нельзя изменять в будущем
	const BILLING_STATUS_PAID = 2;
	const BILLING_STATUS_REFUSED = 3;

	// Статусы для партнеров
	const PARTNER_STATUS_HOLD = 0;
	const PARTNER_STATUS_ACCEPT = 1;
	const PARTNER_STATUS_REJECT = 2;

	// Новые заявки
	const OPERATOR_STREAM_NEW = 1;
	// Заявки для перезвона
	const OPERATOR_STREAM_CALL_LATER = 2;

	// Признак переведенной заявки
	const TRANSFERRED = 1;

	// Время в секундах, при котором заявки считаются одинаковыми - 14 дней
	const DIFF_TIME_FOR_MERGED_REQUEST = 1209600;

	/**
	 * Интервал времени, за который анализируются заявки на спам
	 */
	const SPAM_INTERVAL_TIME = 600;

	/**
	 * Кол-во разрешенных заявок с одним и тем же token за интервал SPAM_INTERVAL_TIME
	 */
	const SPAM_NUM_REQUESTS = 2;

	/**
	 * Статусы для партнеров
	 *
	 * @var array
	 */
	static protected $_partnerStatuses = [
		self::PARTNER_STATUS_HOLD => 'Холд',
		self::PARTNER_STATUS_ACCEPT => 'Подтверждено',
		self::PARTNER_STATUS_REJECT => 'Отклонено',
	];

	/**
	 * Типы источников
	 *
	 * @var array
	 */
	static protected $_sourceTypes = [
		self::SOURCE_SITE       => 'Сайт',
		self::SOURCE_PHONE      => 'Asterisk',
		self::SOURCE_PARTNER    => 'Партнер',
		self::SOURCE_YANDEX     => 'Яндекс',
		self::SOURCE_IPHONE     => 'Мобильное приложение (IPhone)'
	];

	/**
	 * Returns the static model of the specified AR class.
	 *
	 * @param string $className
	 *
	 * @return RequestModel the static model class
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
		return 'request';
	}

	/**
	 * @return string the associated primary key
	 */
	public function primaryKey()
	{
		return 'req_id';
	}


	/**
	 * Конструктор
	 *
	 * @param string $scenario
	 */
	public function __construct($scenario = 'insert')
	{
		parent::__construct($scenario);

		//нужно чтобы проставлять в setKind()
		$this->kind = null;
	}
	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return [
			[
				'client_phone',
				'filter',
				'filter' => ['dfs\docdoc\objects\Phone', 'strToNumber']
			],
			['req_type, req_status, req_user_id, req_departure, req_sector_id,
				diagnostics_id, req_doctor_id, kind, source_type, doctor_request_id, clinic_id
				req_client_stations, opinion_id, partner_id, appointment_status',
				'numerical',
				'integerOnly' => true,
			],
			[
				'client_name',
				'required',
				'on' => [
					self::SCENARIO_SITE,
					self::SCENARIO_PARTNER,
					self::SCENARIO_DIAGNOSTIC_ONLINE,
				],
				'message' => 'Необходимо ввести имя',
			],
			[
				'enter_point',
				'dfs\docdoc\validators\StringValidator',
				'type' => "latinCharacters"
			],
			['client_comments, client_name, diagnostics_other',
				'filter',
				'filter' => 'strip_tags'
			],
			[
				'client_name, client_phone, req_sector_id, req_doctor_id, partner_id,
					clinic_id, req_departure, id_city, client_comments, age_selector, is_hot,
					enter_point, token',
				'safe',
				'on' => [
					self::SCENARIO_SITE,
					self::SCENARIO_CALL
				]
			],
			[
				'id_city, clinic_id, client_name, date_admission, date_record, req_status, req_doctor_id
				diagnostics_other, is_transfer, req_departure, reject_reason, req_user_id, client_phone,
				add_client_phone, call_later_time, is_hot, for_listener, req_type, enter_point, appointment_status',
				'safe',
				'on' => [
					self::SCENARIO_OPERATOR
				]
			],
			[
				'client_name, client_phone, clinic_id, diagnostics_id, id_city,
					client_comments, is_hot, enter_point',
				'safe',
				'on' => [
					self::SCENARIO_PARTNER,
					self::SCENARIO_DIAGNOSTIC_ONLINE
				]
			],
			[
				'req_type, client_name, client_phone, id_city, last_call_id,
					clinic_id, req_status, enter_point, queue',
				'safe',
				'on' => [
					self::SCENARIO_ASTERISK,
				]
			],
			[
				'clinic_id',
				'safe',
				'on' => [
					self::SCENARIO_SAVE_CLINIC,
				]
			],
			[
				'req_status',
				'safe',
				'on' => [
					self::SCENARIO_CHANGE_STATUS,
				],
			],
			[
				array_diff($this->attributeNames(), [ 'client_name', 'client_phone', 'diagnostics_id' ]),
				'unsafe',
				'on' => [
					self::SCENARIO_LK_CLINIC,
				]
			],
			[
				'client_name, client_phone, diagnostics_id',
				'safe',
				'on' => [
					self::SCENARIO_LK_CLINIC,
				]
			],
			[
				'partner_cost',
				'numerical',
				'allowEmpty' => false,
			],
			[
				'date_admission',
				'numerical',
				'allowEmpty' => true,
				'integerOnly' => true,
				'message' => 'Время записи в неверном формате'
			],
			[
				'date_admission',
				'required',
				'on' => [self::SCENARIO_DIAGNOSTIC_ONLINE],
				'message' => 'Укажите время записи'
			],
			[
				'clinic_id',
				'exist',
				'on' => self::SCENARIO_DIAGNOSTIC_ONLINE,
				'message' => 'Клиника не найдена',
				'attributeName' => 'id',
				'className' => ClinicModel::class,
				'allowEmpty' => false,
			],
			[
				'client_phone',

				'required',
				'on' => [
					self::SCENARIO_PARTNER,
					self::SCENARIO_DIAGNOSTIC_ONLINE,
					self::SCENARIO_VALIDATE_PHONE,
				],
				'message' => 'Введите номер телефона'
			],
			[
				'client_phone',
				'dfs\docdoc\validators\PhoneValidator',
				'allowEmpty' => false,
				'skipOnError' => true,
				'except' => [
					self::SCENARIO_ASTERISK,
					self::SCENARIO_REPLACED_PHONE,
					self::SCENARIO_OPERATOR], //с астериска у нас может прийти некорректный номер телефона
			],
			[
				'date_admission',
				'unsafe',
				'on' => self::SCENARIO_VALIDATE_PHONE,
			],
			[
				"is_transfer",
				"numerical",
				'integerOnly' => true,
				'max' => 4
			]
		];
	}

	/**
	 * Выполнение действий после выборки
	 *
	 * 1. запоминаем текущий статус заявки
	 *
	 */
	protected function afterFind()
	{
		$this->_prev_req_status = (int)$this->req_status;
		$this->resetChanges();

		parent::afterFind();
	}

	/**
	 * Получение предыдущего статуса заявки
	 *
	 * @return int
	 */
	public function getOldStatus()
	{
		return $this->_prev_req_status;
	}

	/**
	 * @return array
	 */
	public function relations()
	{
		return [
			'request_partner' => [
				self::HAS_ONE, 'dfs\docdoc\models\RequestPartnerModel', 'request_id'
			],
			'doctor' => [
				self::BELONGS_TO, 'dfs\docdoc\models\DoctorModel', 'req_doctor_id'
			],
			'clinic' => [
				self::BELONGS_TO, 'dfs\docdoc\models\ClinicModel', 'clinic_id'
			],
			'partner' => [
				self::BELONGS_TO, 'dfs\docdoc\models\PartnerModel', 'partner_id'
			],
			'request_history' => [
				self::HAS_MANY, 'dfs\docdoc\models\RequestHistoryModel', 'request_id'
			],
			'city' => [
				self::BELONGS_TO, 'dfs\docdoc\models\CityModel', 'id_city'
			],
			'stations' => [
				self::MANY_MANY, 'dfs\docdoc\models\StationModel', 'request_station(request_id, station_id)'
			],
			'phone' => [
				self::BELONGS_TO, 'dfs\docdoc\models\PhoneModel', 'destination_phone_id'
			],
			'booking' => [
				self::HAS_MANY, BookingModel::class, 'request_id'
			],
			'activeBooking' => [
				self::HAS_ONE,
				BookingModel::class,
				'request_id',
				'on' => 'activeBooking.status in (' . implode(',', BookingModel::model()->getSuccessStatuses()) . ')',
			],
			'request_record' => [
				self::HAS_MANY, 'dfs\docdoc\models\RequestRecordModel', 'request_id', 'order' => 'crDate ASC'
			],
			'sector' => [
				self::BELONGS_TO, 'dfs\docdoc\models\SectorModel', 'req_sector_id',
			],
			'client' => [
				self::BELONGS_TO, 'dfs\docdoc\models\ClientModel', 'clientId',
			],
			'diagnostics' => [
				self::BELONGS_TO, 'dfs\docdoc\models\DiagnosticaModel', 'diagnostics_id'
			],
			'requestStations' => [
				self::HAS_MANY,
				'dfs\docdoc\models\RequestStationModel',
				'request_id'
			],
			'operator' => [
				self::BELONGS_TO,
				UserModel::class,
				'req_user_id'
			],
			'diagnosticClinic' => [
				self::BELONGS_TO,
				DiagnosticClinicModel::class,
				[
					'diagnostics_id' => 'diagnostica_id',
					'clinic_id' => 'clinic_id'
				]
			]
		];
	}

	/**
	 * Поведения
	 *
	 * CAdvancedArBehavior - класс реализующий автоматическое сохранение, удаление отношений MANY_MANY
	 *
	 * @return array
	 */
	public function behaviors()
	{
		return [
			'CAdvancedArBehavior' => [
				'class' => 'CAdvancedArBehavior',
			],
		];
	}

	/**
	 * выполнить перед валидацией
	 * @return bool
	 */
	public function beforeValidate()
	{
		if($this->date_admission && !is_numeric($this->date_admission)){
			if($date = strtotime($this->date_admission)){
				$this->date_admission = $date;
			}
		}

		$this->client_name = ucwords($this->client_name);

		//если это новая заявка, создваемая из астериска
		if ($this->getIsNewRecord() && $this->scenario === self::SCENARIO_ASTERISK) {
			$this->client_name = 'asterisk';
			$this->enter_point = self::ENTER_POINT_DIRECT_CALL;
		}

		//если нет такого доктора
		if (!empty($this->req_doctor_id) && $this->doctor === null) {
			$this->req_doctor_id = 0;
		}

		//если нет диагностики
		$this->diagnostics_id = (int)$this->diagnostics_id;

		if($this->diagnostics_id && !$this->diagnostics){
			$this->diagnostics_id = 0;//todo переделать на ключ и избавиться от нулей
		}

		//если нет клиники
		if(($this->clinic_id && $this->clinic === null) || !$this->clinic_id){
			$this->clinic_id = null; //todo ключ на клинику и избавиться от нулей
		}


		return true;
	}

	/**
	 * После основной валидации
	 */
	public function afterValidate()
	{
		if (!$this->getErrors() && $this->scenario == self::SCENARIO_DIAGNOSTIC_ONLINE) {
			$this->setBillingState(self::BILLING_STATE_RECORD, [ 'date_admission' => $this->date_admission]);
		}
	}

	/**
	 * выполнить перед сохранением
	 *
	 * @return bool
	 * @throws \dfs\docdoc\exceptions\SpamException
	 */
	public function beforeSave()
	{
		parent::beforeSave();

		//@todo КОСТЫЛЬ
		// так как изменение статусов сейчас полностью переведено на RequestModel,
		// чтобы всегда у нас сохранялся клиент, добавил возможность сохранения clientId сюда
		// когда сохранение заявки будет целиком переведено на модель, убрать ниже условия
		// if ($this->getScenario() === self::SCENARIO_CHANGE_STATUS)
		$this->setRequestClient();

		//если нам нужно только изменить статус, никаких доп. действий не делаем
		if ($this->getScenario() === self::SCENARIO_CHANGE_STATUS) {
			return true;
		}

		if ($this->getIsNewRecord()) {
			empty($this->req_created) && $this->req_created = time();

			if($this->getScenario() == self::SCENARIO_VALIDATE_PHONE){
				$this->req_status = self::STATUS_PRE_CREATED;
				$this->validation_code = $this->generateValidationCode();
				$this->is_hot = 0;
			} else if ($this->getScenario() != self::SCENARIO_DUPLICATE) {
				//при создании заявки всегда ставим подсветку этой заявки
				$this->is_hot = 1;
			}

			if (empty($this->req_status)) {
				$this->req_status = RequestModel::STATUS_NEW;
			}

			// проверяем заявку, оставленную с сайта на спам
			// логгируем IP+UserAgent клиента
			// отправляем событие в new relic
			$scenarioList = [
				self::SCENARIO_SITE,
				self::SCENARIO_CALL,
				self::SCENARIO_VALIDATE_PHONE,
			];

			if (in_array($this->getScenario(), $scenarioList) && \Yii::app()->getParams()['antispamEnabled']) {
				$this->setToken();
				if ($this->isSpam()) {
					(new RequestSpamModel())->saveFromRequest($this);
					throw new SpamException('Заявка будет помещена в спам');
				}
			}

			// отправляем событие о новой заявке в new relic
			\Yii::app()->newRelic->customMetric('Custom/Request/New', 1);
		}

		if($this->getScenario() == self::SCENARIO_DIAGNOSTIC_ONLINE) {
			//для диагностики онлайн не должны быть красмыми заявки
			$this->is_hot = 0;
		}

		//если не задан сектор и известен доктор берем первый сектор доктора
		if (empty($this->req_sector_id) && $this->doctor !== null) {
			$sector = $this->doctor->getDefaultSector();
			$this->req_sector_id = ($sector !== null) ? $sector->id : null;
		}

		//если не задана клиника, но известен доктор берем основную клинику доктора
		if (empty($this->clinic_id) && $this->doctor !== null) {
			$this->clinic = $this->doctor->getDefaultClinic();
			$this->clinic_id = ($this->clinic !== null) ? $this->clinic->id : null;
		}

		//устанавливаем город
		if ($this->clinic !== null) {
			$this->id_city = $this->clinic->city_id;
		}

		$this->setKind();
		$this->setReqType();

		if($this->isNew()){
			if($this->kind == self::KIND_DIAGNOSTICS && $this->req_type == RequestModel::TYPE_ONLINE_RECORD){
				$this->expire_time = $this->calculateExpireTime(time());
			}
		}

		if ($this->partner) {
			if ($this->partner->isYandex()) {
				$this->source_type = self::SOURCE_YANDEX;
			} elseif($this->partner->isMobileApi($this->partner_id)) {
				$this->source_type = self::SOURCE_IPHONE;
			} else {
				$this->source_type = self::SOURCE_PARTNER;
			}
		}

		//если установлена дата приема первый раз, проставляем date_record
		if ($this->isChanged('date_admission') && (int)$this->date_record === 0) {
			$this->date_record = date('Y-m-d H:i:s');
		}

		if ($this->req_doctor_id) {
			DoctorModel::model()->updateByPk($this->req_doctor_id, [ 'update_tips' => 1 ]);
		}

		return true;
	}

	/**
	 *  выполнить после сохранения
	 */
	public function afterSave()
	{
		parent::afterSave();

		if($this->getScenario() == self::SCENARIO_VALIDATE_PHONE){
			$text = "Код подтверждения: $this->validation_code";

			if (!SmsQueryModel::sendSmsToNumber($this->client_phone, $text, SmsQueryModel::TYPE_VALIDATE_PHONE, true)) {
				$this->addHistory('Сбой при отправке смс с кодом валидации');
			}
		}

		//если это новая партнерская заявка и такой партнер существует сохраняем в request_partner
		if (!empty($this->partner_id) && $this->getIsNewRecord() && $this->partner !== null) {
			$this->request_partner = new RequestPartnerModel();
			$this->request_partner->request_id = $this->req_id;
			$this->request_partner->partner_id = $this->partner_id;
			$this->request_partner->save();
		}

		/** @var \dfs\docdoc\components\EventDispatcher $eventDispatcher */
		$eventDispatcher = \Yii::app()->eventDispatcher;
		// Вызываем событие, что заявка создана
		if ($this->getIsNewRecord()) {
			$eventDispatcher->raiseEvent('onRequestCreated', new \CEvent($this));
		}

		// Вызываем  событие, что создана/изменена заявка
		$eventDispatcher->raiseEvent('onRequestSave', new \CEvent($this));

		//если изменила тип и заявка не новая
		if (!$this->getIsNewRecord() && $this->isChanged('kind')) {
			$eventDispatcher->raiseEvent('onRequestKindChange', new \CEvent($this));
		}

		//если изменилась причина отказа, зажигаем событие
		if ($this->isChanged('reject_reason')) {
			$eventDispatcher->raiseEvent('onRejectReasonChange', new \CEvent($this));
		}

		//если изменилась клиника
		if ($this->isChanged('clinic_id')) {
			$this->clinic = ClinicModel::model()->findByPk($this->clinic_id);
		}

		//если изменилась дата записи, зажигаем событие
		//не срабатывает, если указана причина отказа
		if (!$this->isNew() && $this->isChanged('date_admission') && !$this->reject_reason) {
			$eventDispatcher->raiseEvent('onDateAdmissionChange', new \CEvent($this));
		}

		//проверяем был ли изменен статус заявки
		if ($this->req_status != $this->_prev_req_status) {
			// Вызываем  событие, что был изменен статус заявки
			$eventDispatcher->raiseEvent('onRequestStatusChange', new \CEvent($this));
		}

		//предыдущий статус заявки приравниваем текущему
		$this->_prev_req_status = $this->req_status;

		//пытаемся посчитать стоимость заявки
		$this->saveRequestCost();

		//сбрасываем все измеения
		$this->resetChanges();
	}


	/**
	 * установка значения $this->req_type в зависимости от сценария
	 *
	 */
	public function setReqType() {

		switch ($this->scenario) {
			case self::SCENARIO_SITE:
			case self::SCENARIO_PARTNER:
				$this->req_type = (empty($this->req_doctor_id) && empty($this->diagnostics_id) ) ? self::TYPE_PICK_DOCTOR : self::TYPE_WRITE_TO_DOCTOR;
				break;
			case self::SCENARIO_CALL:
				$this->req_type = self::TYPE_PICK_DOCTOR;
				break;
			case self::SCENARIO_ASTERISK:
				$this->req_type = self::TYPE_CALL;
				break;
			case self::SCENARIO_DIAGNOSTIC_ONLINE:
				//проверка есть ли у клиники тариф на запись
				if ($this->kind == self::KIND_DIAGNOSTICS && $this->clinic && $this->clinic->getClinicContract(ContractModel::TYPE_DIAGNOSTIC_ONLINE)) {
					$this->req_type = self::TYPE_ONLINE_RECORD;
				} else {
					$this->req_type =(empty($this->req_doctor_id) && empty($this->diagnostics_id) ) ? self::TYPE_PICK_DOCTOR : self::TYPE_WRITE_TO_DOCTOR;
				}
				break;
			case self::SCENARIO_REPLACED_PHONE:
				$this->req_type = self::TYPE_CALL_TO_DOCTOR;
				break;
		}
	}

	/**
	 * Установка значения $this->kind в зависимости от разных факторов
	 *
	 */
	public function setKind() {

		//если идет пустое, знач надо вычислить
		if($this->kind === null){
			$this->kind = $this->calculateKind();
		}
	}

	/**
	 * Простановка партнера для заявки, созданной с партнерского телефона
	 */
	public function setPartnerFromDestinationPhone()
	{
		//если заявка принята с партнерского телефона, заявке нужно проставить партнера
		if (!empty($this->destination_phone_id) && $this->phone->partner !== null) {
			$this->partner_id = $this->phone->partner->id;
		}
	}

	/**
	 * Высчитывает значение поля kind
	 *
	 * @param null|string $replacedPhone
	 * @return int
	 */
	public function calculateKind($replacedPhone = null)
	{
		//по умолчанию тип доктора
		$kind = RequestModel::KIND_DOCTOR;

		if ($this->scenario == self::SCENARIO_DIAGNOSTIC_ONLINE){
			//сценарий превыше всего!
			$kind = self::KIND_DIAGNOSTICS;
		} elseif ($this->clinic) {
			if ($replacedPhone && $this->clinic->asterisk_phone && $this->clinic->asterisk_phone === $replacedPhone) {
				//диагностика
				$kind = RequestModel::KIND_DIAGNOSTICS;
			} elseif ($this->clinic->isDiagnostic() && !$this->clinic->isClinic()) { //и не клиника
				$kind = self::KIND_DIAGNOSTICS;
			} elseif (!$this->clinic->isDiagnostic() && $this->clinic->isClinic()) { //и не диагн
				$kind = self::KIND_DOCTOR;
			} elseif ($this->partner) {
				$kind = $this->partner->request_kind;
			}
		} elseif ($this->partner) {
			$kind = $this->partner->request_kind;
		} elseif (!empty($this->diagnostics_id)) {
			$kind = RequestModel::KIND_DIAGNOSTICS;
		}

		return $kind;
	}

	/**
	 * Получение заявок с таким же номером телефона клиента
	 * относительно даты
	 *
	 * @param string|null $phone
	 * @param string      $time   Unix timestamp
	 *
	 * @return RequestModel $this
	 */
	public function sameRequestByPhone($phone, $time = null)
	{
		$phoneObject = new Phone($phone);

		is_null($time) && $time = time();
		$time = $time - self::DIFF_TIME_FOR_MERGED_REQUEST;

		$this->getDbCriteria()->mergeWith(
			[
				'condition' => "(t.client_phone=:phone OR t.add_client_phone=:phone AND t.client_phone<>'')
					AND t.req_created > :time",
				'params' => [
					':phone'  => $phoneObject->getNumber(),
					':time'   => $time
				],
				'order' => 't.req_created DESC',
			]
		);

		return $this;
	}

	/**
	 * Выборка заявок в клинике
	 *
	 * @param integer|int[] $clinicId
	 *
	 * @return RequestModel $this
	 */
	public function inClinic($clinicId)
	{
		$criteria = new \CDbCriteria();
		if (is_array($clinicId)) {
			$criteria->addInCondition($this->getTableAlias() . ".clinic_id", $clinicId);
		} else {
			$criteria->condition = $this->getTableAlias() . ".clinic_id = :clinic_id";
			$criteria->params = [":clinic_id" => $clinicId];
		}

		$this->getDbCriteria()->mergeWith($criteria);

		return $this;
	}

	/**
	 * Выборка заявок в родительской клинике и во всех клиниках этого филиала
	 *
	 * @param integer $clinicId
	 *
	 * @return RequestModel $this
	 */
	public function inBranches($clinicId)
	{
		$criteria = new \CDbCriteria();

		$criteria->with = [
			'clinic' => [
				'joinType' => 'INNER JOIN',
				'condition' => "clinic.id = :clinic_id OR clinic.parent_clinic_id = :clinic_id",
				'params' => [
					':clinic_id' => $clinicId,
				],
			]
		];

		$criteria->together = true;

		$this->getDbCriteria()->mergeWith($criteria);
		return $this;
	}

	/**
	 * Выборка заявок по статусу в биллинге (по-умолчанию которые находятся в биллинге)
	 *
	 * @param int | array | null $status
	 *
	 * @return RequestModel $this
	 */
	public function inBilling($status = null)
	{
		$statuses = $status === null ?
			[ self::BILLING_STATUS_YES, self::BILLING_STATUS_PAID ] :
			(is_array($status) ? $status : [ $status ]);

		$this->getDbCriteria()->addInCondition($this->getTableAlias() . '.billing_status', $statuses);

		return $this;
	}

	/**
	 * Выборка заявок cо специальностями, кроме указаных
	 * @param int[] $sectorIds
	 *
	 * @return RequestModel $this
	 */
	public function exceptSectors(array $sectorIds)
	{
		if (!count($sectorIds)) {
			return $this;
		}

		$criteria = new \CDbCriteria();
		$criteria->addNotInCondition('req_sector_id', $sectorIds);

		$this->getDbCriteria()->mergeWith($criteria);
		return $this;
	}

	/**
	 * Выборка заявок c диагностиками, кроме указаных
	 * @param int[] $diagnosticIds
	 *
	 * @return RequestModel $this
	 */
	public function exceptDiagnostics(array $diagnosticIds)
	{
		if (!count($diagnosticIds)) {
			return $this;
		}

		$criteria = new \CDbCriteria();
		$criteria->addNotInCondition('diagnostics_id', $diagnosticIds);

		$this->getDbCriteria()->mergeWith($criteria);
		return $this;
	}

	/**
	 * Выборка заявок с врачом определенных специальностей
	 * @param int[] $sectorIds
	 *
	 * @return RequestModel $this
	 */
	public function inSectors(array $sectorIds)
	{
		if (!count($sectorIds)) {
			return $this;
		}

		$criteria = new \CDbCriteria();
		$criteria->addInCondition($this->getTableAlias() . ".req_sector_id", $sectorIds);

		$this->getDbCriteria()->mergeWith($criteria);
		return $this;
	}

	/**
	 * Выборка заявок на определенные диагностики
	 * @param int[] $diagnosticIds
	 *
	 * @return RequestModel $this
	 */
	public function inDiagnostics(array $diagnosticIds)
	{
		$criteria = new \CDbCriteria();

		if (empty($diagnosticIds)) {
			$criteria->condition = $this->getTableAlias() . ".diagnostics_id IS NULL OR  " . $this->getTableAlias() . ".diagnostics_id = 0";
		} else {
			$criteria->addInCondition($this->getTableAlias() . ".diagnostics_id", $diagnosticIds);
		}

		$this->getDbCriteria()->mergeWith($criteria);
		return $this;
	}

	/**
	 * Выборка заявок, созданных в интевале времени
	 *
	 * @param int $from
	 * @param int|null $to
	 *
	 * @return $this
	 */
	public function createdInInterval($from, $to = null)
	{
		if (!is_null($from)) {
			$this->getDbCriteria()->mergeWith(
				[
					'condition' => "req_created >=:from_time",
					'params'    => [
						":from_time" => $from,
					]
				]
			);
		}

		if (!is_null($to)) {
			$this->getDbCriteria()->mergeWith(
				[
					'condition' => "req_created <= :to_time",
					'params'    => [
						":to_time" => $to,
					]
				]
			);
		}

		return $this;
	}


	/**
	 * Выборка заявок, с датой визита в интервале
	 *
	 * @param int $from
	 * @param int $to
	 *
	 * @return $this
	 */
	public function betweenDateAdmission($from, $to = null)
	{
		$params = [ ':from_time' => $from ];
		if ($to !== null) {
			$params[':to_time'] = $to;
		}

		$this->getDbCriteria()->mergeWith([
				'condition' => 'date_admission >=:from_time' . ($to !== null ? ' AND date_admission <= :to_time' : ''),
				'params'    => $params,
			]);

		return $this;
	}

	/**
	 * Выборка заявок, с датой установки даты визита
	 *
	 * @param string $from
	 * @param string $to
	 *
	 * @return $this
	 */
	public function betweenDateRecord($from, $to)
	{
		$this->getDbCriteria()->mergeWith(
			[
				'condition' => "date_record >=:from_time AND date_record <= :to_time",
				'params'    => [
					":from_time" => $from,
					":to_time" => $to,
				]
			]
		);

		return $this;
	}

	/**
	 * Выборка заявок, с датой биллинга в определенном интервале в зависимости от контракта
	 *
	 * @param string $from
	 * @param string $to
	 * @param ContractModel $contract
	 *
	 * @return $this
	 */
	public function betweenBillingDate($from, $to, $contract = null)
	{
		if (!$contract) {
			$this->getDbCriteria()->mergeWith([
				'condition' => 'date_billing >=:from_time AND date_billing <= :to_time',
				'params'    => [
					':from_time' => $from,
					':to_time' => $to,
				],
			]);
			return $this;
		}

		// Todo: contract можно будет вообще убрать

		$billingDate = $contract->getBillingDate();

		if ($billingDate ===  "date_admission") {
			return $this->betweenDateAdmission(strtotime($from), strtotime($to));
		}

		if ($billingDate ===  "req_created") {
			return $this->createdInInterval(strtotime($from), strtotime($to));
		}

		if ($billingDate ===  "date_record") {
			return $this->betweenDateRecord($from, $to);
		}

		return $this;
	}

	/**
	 * Выборка заявок, созданных по сайту DD и партнерам dd.*
	 *
	 * @return $this
	 */
	public function onlyDocDoc()
	{
		$docdocPartners = PartnerModel::model()->getDocDocPartners();
		//добавляем сам ДокДок
		$docdocPartners[] = 0;

		$criteria = new \CDbCriteria();
		$criteria->addInCondition($this->getTableAlias() . ".partner_id", $docdocPartners);
		$criteria->addCondition($this->getTableAlias() . ".partner_id IS NULL", 'OR');
		$this->getDbCriteria()->mergeWith($criteria);
		return $this;
	}

	/**
	 * Выборка заявок с заданными типами
	 *
	 * @param array $types
	 * @param bool  $isNot
	 *
	 * @return RequestModel $this
	 */
	public function withTypes($types, $isNot = false)
	{
		$criteria = new \CDbCriteria();
		if ($isNot) {
			$criteria->addNotInCondition('t.req_type', $types);
		} else {
			$criteria->addInCondition('t.req_type', $types);
		}

		$this->getDbCriteria()->mergeWith($criteria);

		return $this;
	}

	/**
	 * Выборка без указанных телефонов
	 *
	 * @param array $phones
	 *
	 * @return RequestModel $this
	 */
	public function withoutPhones($phones)
	{
		$criteria = new \CDbCriteria();
		$criteria->addNotInCondition('t.client_phone', $phones);

		$this->getDbCriteria()->mergeWith($criteria);

		return $this;
	}


	/**
	 * Установить статус повторный вызов
	 *
	 * @return RequestModel $this
	 */
	public function setRecall()
	{
		if ($this->req_status == self::STATUS_REJECT
			|| $this->req_status == self::STATUS_REMOVED
		) {
			$this->req_status = self::STATUS_RECALL;
			$this->reject_reason = 0;
		}

		//ставим, что нужно перезвонить
		$this->is_hot = 1;

		$this->addHistory("Поступил повторный звонок.");

		return $this;
	}

	/**
	 * Изменение клиники в заявке
	 *
	 * @param int $clinic_id
	 *
	 * @return bool
	 */
	public function saveClinic($clinic_id)
	{
		//если в заявке известен доктор, то изменить клинику можно только изменив доктора
		if (!empty($this->req_doctor_id)) {
			return true;
		}

		//на пустые значения не реагируем
		if (empty($clinic_id)) {
			return true;
		}

		if ($this->clinic_id === $clinic_id) {
			return true;
		}

		//если заявка без доктора - перезаписываем клинику
		$this->setScenario(self::SCENARIO_SAVE_CLINIC);
		$this->clinic_id = $clinic_id;

		if (!$this->save()) {
			$this->addHistory('Ошибка при сохранении новой клиники '. print_r($this->getErrors(), 1));
			$this->clinic_id = $this->getOriginalValue('clinic_id');
			return false;
		}

		if ($this->getScenario() == self::SCENARIO_DUPLICATE) {
			return false;
		}

		return true;
	}

	/**
	 * Обработка измененеия состояния isAppointment телефонного разговора для этой заявки
	 * @param RequestRecordModel $record
	 *
	 * @return bool
	 */
	public function appointmentByRecord(RequestRecordModel $record)
	{
		$this->setScenario(self::SCENARIO_RECORD_APPOINTMENT);

		//если флаг записи снят, логируем и уходим
		if (!$record->wasAppointment()) {
			$this->addHistory("Удалён признак приёма в аудиозаписи({$record->record})");
			return true;
		}

		$this->addHistory("Установлен признак приёма в аудиозаписи ({$record->record})");

		$clinicId = $record->clinic_id;

		//часто операторы ставят что была запись у request_record с clinic_id = 0
		//поэтому, если была запись и нет клиники, то ищем клинику среди предыдущих записей
		if (empty($record->clinic_id)) {
			for ($i = count($this->request_record)-1; $i >= 0; $i--) {
				if ($this->request_record[$i]->crDate < $record->crDate && !empty($this->request_record[$i]->clinic_id)) {

					$clinicId = $this->request_record[$i]->clinic_id;
					$i = -1;
				}
			}
		}

		$clinic = ClinicModel::model()->findByPk($this->clinic_id);
		//сохраняем клинику в которой была зафиксирована запись в том случае, если в заявке нет клиники или
		//выбранная клиника не является филиалом
		if (is_null($clinic) || !$clinic->relatedWithClinic($clinicId)) {
			$this->saveClinic($clinicId);
		}

		//ставим сценарий простой update, чтобы история по заявке не писалась
		$this->setScenario('update');
		if ((int)$this->date_record === 0) {
			$this->date_record = $record->crDate;
		}

		return $this->save();
	}

	/**
	 * Сохранение флагов isAppointment у всех телефонных записей заявки
	 *
	 * $records = array(
	 *		record_id1 => yes
	 *   	record_id2 => yes
	 *  	record_id3 => yes
	 *  	record_id4 => yes
	 * )
	 *
	 * @param array $records
	 */
	public function saveAppointmentByRecords($records)
	{
		foreach ($this->request_record as $record) {

			//если запись раньше была, но в новом запросе не отмечено, что есть запись
			//делаем запись неактивной
			if (!isset($records[$record->record_id]) && $record->wasAppointment()) {
				$record->saveAppointment('no');
				$this->appointmentByRecord($record);
				continue;
			}

			//если запись не передана и она у нас не отмечена
			if (!isset($records[$record->record_id])) {
				continue;
			}

			//если запись стала активной
			if (!$record->wasAppointment() && $record->isAppointment !== $records[$record->record_id]) {
				$record->saveAppointment('yes');
				$this->appointmentByRecord($record);
			}
		}
	}

	/**
	 * Cохранение истории модели
	 *
	 * ВНИМАНИЕ!!! ИСТОРИЯ БУДЕТ СОХРАНЯТЬСЯ ТОЛЬКО ЕСЛИ У ЗАЯВКИ УСТАНОВЛЕНО ЗНАЧЕНИЕ СООТВЕТСТВУЮЩЕГО СЦЕНАРИЯ,
	 * НА КОТОРЫЕ РЕАГИРУЕТ RequestHistoryModel
	 *
	 * @param string $text
	 * @param int | null $action
	 */
	public function addHistory($text, $action = null)
	{
		$history = new RequestHistoryModel();
		$history->request = $this;
		if ($action !== null) {
			$history->action = $action;
		}
		$history->addLog($text);
	}

	/**
	 * Изменение статуса заявки
	 *
	 * @param int $status
	 *
	 * @return bool
	 */
	public function saveStatus($status)
	{
		if ($this->req_status === (int)$status) {
			return true;
		}

		$this->setScenario(self::SCENARIO_CHANGE_STATUS);
		$this->req_status = $status;
		//@todo КОСТЫЛЬ
		// так как изменение статусов сейчас полностью переведено на RequestModel,
		// чтобы всегда у нас сохранялся клиент, добавил возможность сохранения clientId сюда
		// когда сохранение заявки будет целиком переведено на модель, clientId нужно убрать

		//валидируем и изменяем только столбец req_status
		return $this->save(true, ['req_status', 'clientId']);
	}

	/**
	 * возвращает стоимость заявки
	 *
	 * @return float
	 */
	public function getCost()
	{
		//пока что для mixpanel стоимость возвращается постоянная
		//в последствии, наверно будет переделана на динамику
		$cost = 700.00;
		return $cost;
	}

	/**
	 * сохранение только clientId
	 *
	 * @return bool
	 */
	public function saveClientId()
	{
		$this->setRequestClient();
		return $this->updateByPk($this->req_id, ['clientId' => $this->clientId]);

	}

	/**
	 * Сохранение клиента
	 */
	public function setRequestClient()
	{
		if ($this->client === null) {
			$this->client = new ClientModel();
		}

		//если изменился номер телефона, то может вернуться совсем другой клиент
		//поэтому переприсваиваем $this->client
		$this->client = $this->client->saveFromRequest($this);

		$this->clientId = !is_null($this->client) ? $this->client->clientId : null;
	}

	/**
	 * Сохранение req_user_id
	 *
	 * @param int $userId
	 *
	 * @return int
	 */
	public function saveRequestUser($userId)
	{
		return $this->updateByPk($this->req_id, ['req_user_id' => $userId]);
	}

	/**
	 * Добавляет запись к заявке
	 *
	 * @param RequestRecordModel $r
	 *
	 * @return $this
	 */
	public function addRecord(RequestRecordModel $r)
	{
		$r->request_id = $this->req_id;
		$r->save();

		return $this;
	}

	/**
	 * Поиск по партнеру
	 *
	 * @param int $partnerId
	 *
	 * @return RequestModel $this
	 */
	public function byPartner($partnerId)
	{
		if(is_null($partnerId)){
			$cr = [
				'condition' => 'partner_id is null or partner_id = 0'
			];
		} else {
			$cr = [
				'condition' => 'partner_id = :partner_id',
				'params' => [':partner_id' => $partnerId],
			];
		}
		$this->getDbCriteria()
			->mergeWith($cr);

		return $this;
	}

	/**
	 * Поиск по партнёрскому статусу
	 *
	 * @param $status
	 *
	 * @return $this
	 */
	public function byPartnerStatus($status)
	{
		$name = TextUtils::getUniqueValueName();

		$this->getDbCriteria()
			->mergeWith(
				[
					'condition' => 'partner_status = :' . $name,
					'params' => [$name => $status],
				]
			);

		return $this;
	}

	/**
	 * Поиск по kind
	 *
	 * @param int $kind
	 *
	 * @return $this
	 */
	public function byKind($kind)
	{
		$name = TextUtils::getUniqueValueName();

		$this->getDbCriteria()
			->mergeWith(
				[
					'condition' => 'kind = :' . $name,
					'params' => [$name => $kind],
				]
			);

		return $this;
	}

	/**
	 * Поиск по городу
	 *
	 * @param $cityId
	 *
	 * @return $this
	 */
	public function byCity($cityId)
	{
		$this->getDbCriteria()
			->mergeWith(
				[
					'condition' => 'id_city = :id_city',
					'params' => [':id_city' => $cityId],
				]
			);

		return $this;
	}


	/**
	 * Поиск по типам источника
	 *
	 * @param int[] $sources
	 *
	 * @return $this
	 * @throws \CException
	 */
	public function inSourceTypes(array $sources)
	{
		$criteria = new \CDbCriteria();
		$criteria->addInCondition('t.source_type', $sources);

		$this->getDbCriteria()->mergeWith($criteria);

		return $this;
	}


	/**
	 * Ищет на основании параметров из записи похожую заявку
	 * и если не находит создает новую, иначе возвращает найденную
	 *
	 * @param RequestRecordModel $record
	 *
	 * @return RequestModel
	 */
	public static function saveByRecord(RequestRecordModel $record)
	{
		$clinicId = $record->clinic_id;
		$partnerId = null;
		$replacedPhone = $record->replaced_phone;

		if($record->getPartnerId()){
			$partnerId = $record->getPartnerId();
		} elseif($replacedPhone) {
			$clinicPartnerPhone = ClinicPartnerPhoneModel::model()
				->byPhone($replacedPhone)
				->find();

			if ($clinicPartnerPhone) {
				$partnerId = $clinicPartnerPhone->partner_id;
				$clinicId = $clinicPartnerPhone->clinic_id;
			}
		}

		$clientPhone = $record->getCallerPhone();

		$request = new RequestModel(RequestModel::SCENARIO_REPLACED_PHONE);
		$request->client_phone = $clientPhone;
		$request->client_name = 'Звонок на врача';
		$request->req_created = strtotime($record->crDate);
		$request->is_transfer = RequestModel::TRANSFERRED;
		$request->clinic_id = $record->clinic_id;
		$request->for_listener = 1;
		$request->req_status = RequestModel::STATUS_PROCESS;
		$request->partner_id = $partnerId;
		$request->clinic_id = $clinicId;
		$request->kind = $request->calculateKind($replacedPhone);
		$request->enter_point = $partnerId === null ? RequestModel::ENTER_POINT_CLINIC_CALL : RequestModel::ENTER_POINT_PARTNER_CALL;

		if (!is_null($replacedPhone)) {
			$phone = PhoneModel::model()->createPhone($replacedPhone);
			if ($phone instanceof PhoneModel) {
				$request->destination_phone_id = $phone->id;
			}
		}

		$request = RequestModel::isSameRequest($request, [self::TYPE_CALL_TO_DOCTOR]);

		$ftpServer = $record->getSourceProvider()->getName();

		if (!$request->getIsNewRecord()) {
			$history[] = "Автоматический импорт с FTP сервера $ftpServer. Добавлен аудиофайл";
			$history[] = "Повторный звонок";
		} else {
			$history[] = "Автоматический импорт с FTP сервера $ftpServer. Создана заявка";
			$history[] = "Новый звонок";
		}

		if ($request->save()) {
			$request->addRecord($record);

			foreach ($history as $h) {
				$request->addHistory($h);
			}
		}

		return $request;
	}

	/**
	 * Поиск по клиентскому телефону
	 *
	 * @param string $phone
	 *
	 * @return $this
	 */
	public function byClientPhone($phone)
	{
		$this->getDbCriteria()
			->mergeWith(
				[
					'condition' => 'client_phone=:phone',
					'params' => [':phone' => $phone],
				]
			);

		return $this;
	}

	/**
	 * Поиск по ФИО клиента
	 *
	 * @param string $name
	 *
	 * @return RequestModel
	 */
	public function byClientName($name)
	{
		$this->getDbCriteria()
			->mergeWith(
				[
					'condition' => 'client_name=:name',
					'params' => [':name' => $name],
				]
			);

		return $this;
	}

	/**
	 * Возвращает список статусов
	 *
	 * @return string[]
	 */
	public static function getStatusList()
	{
		$status_list = [
			self::STATUS_NEW => 'Новая',
			self::STATUS_PROCESS => 'В обработке',
			self::STATUS_RECORD => 'Обработана',
			self::STATUS_CAME => 'Завершена',
			self::STATUS_REMOVED => 'Удалена',
			self::STATUS_REJECT => 'Отказ',
			self::STATUS_ACCEPT => 'Принята',
			self::STATUS_CALL_LATER => 'Перезвонить',
			self::STATUS_REJECT_BY_PARTNER => 'Отклонена партнёром',
			self::STATUS_RECALL => 'Повторный звонок',
			self::STATUS_PRE_CREATED => 'Предзаявка',
			self::STATUS_CAME_UNDEFINED => 'Условно завершена',
			self::STATUS_NOT_CAME => 'Не дошел',
		];

		return $status_list;
	}


	/**
	 * Возвращает список статусов биллинга
	 *
	 * @return string[]
	 */
	public static function getBillingStatusList()
	{
		$status_list = [
			self::BILLING_STATUS_YES => 'В биллинге',
			self::BILLING_STATUS_NO => 'Не в биллинге',
			self::BILLING_STATUS_PAID => 'Деньги за заявку получены',
			self::BILLING_STATUS_REFUSED => 'Клиника отказывается платить',
		];

		return $status_list;
	}

	/**
	 * Возвращает название статуса биллинга
	 *
	 * @return string
	 */
	public function getBillingStatusName()
	{
		$statusList = self::getBillingStatusList();
		return isset($statusList[$this->billing_status]) ? $statusList[$this->billing_status] : null;
	}

	/**
	 * Статусы для партнера
	 *
	 * @return array
	 */
	public static function getPartnerStatuses()
	{
		return self::$_partnerStatuses;
	}

	/**
	 * Возвращает название статуса для партнера
	 *
	 * @return string
	 */
	public function getPartnerStatusName()
	{
		return isset(self::$_partnerStatuses[$this->partner_status]) ? self::$_partnerStatuses[$this->partner_status] : null;
	}

	/**
	 * Поиск за период по дате обращения
	 *
	 * @param int $startDate
	 * @param int $endDate
	 *
	 * @return $this
	 */
	public function forPeriodOfDateAdmission($startDate, $endDate)
	{
		$this->getDbCriteria()
			->mergeWith(
				[
					'condition' => 'date_admission IS NOT NULL AND date_admission between :startDate and :endDate',
					'params' => [':startDate' => $startDate, ':endDate' => $endDate]
				]
			);

		return $this;
	}

	/**
	 * Ищет по статусам
	 *
	 * @param int[] $statuses
	 * @param bool  $isNot
	 *
	 * @return $this
	 */
	public function inStatuses(array $statuses, $isNot = false)
	{
		$criteria = new \CDbCriteria();
		if ($isNot) {
			$criteria->addNotInCondition('t.req_status', $statuses);
		} else {
			$criteria->addInCondition('t.req_status', $statuses);
		}

		$this->getDbCriteria()->mergeWith($criteria);

		return $this;
	}

	/**
	 * Возвращает сумму по колонке partner_cost и количество успешных заявок
	 *
	 * @param \CDbCriteria $criteria
	 * @param int | null $serviceId
	 *
	 * @return array
	 */
	public function getPartnerSumAndCount(\CDbCriteria $criteria, $serviceId = null)
	{
		$c = new \CDbCriteria();

		$c->scopes['byPartnerStatus'] = [ self::PARTNER_STATUS_ACCEPT ];

		if ($serviceId !== null) {
			$c->scopes['byServiceId'] = [ $serviceId ];
		}

		$c->mergeWith($criteria);

		$this->applyScopes($c);

		$c->select = 'COUNT(*) as count, TRUNCATE(SUM(t.partner_cost), 0) as cost';

		$result = $this->getCommandBuilder()
			->createFindCommand($this->getTableSchema(), $c, $this->getTableAlias())
			->queryRow();

		$this->resetScope();

		$result['cost'] = intval($result['cost']);

		return $result;
	}

	/**
	 * Поиск по сервису из модели ServiceModel
	 *
	 * @param int $serviceId
	 *
	 * @throws \CException
	 * @return $this
	 */
	public function byServiceId($serviceId)
	{
		$isDiagnostic = 0;

		switch ($serviceId) {
			case ServiceModel::TYPE_SUCCESSFUL_DOCTOR_REQUEST:
				$this->byKind(self::KIND_DOCTOR);
				break;
			case ServiceModel::TYPE_SUCCESSFUL_DIAGNOSTICS_MRT_OR_KT:
				$this->byKind(self::KIND_DIAGNOSTICS);
				$isDiagnostic = 1;
				break;
			case ServiceModel::TYPE_SUCCESSFUL_DIAGNOSTICS_OTHER:
				$this->byKind(self::KIND_DIAGNOSTICS);
				$isDiagnostic = 1;
				break;
			default:
				throw new \CException('Неверный сервис');
				break;
		}

		if ($isDiagnostic) {
			$group = ContractGroupModel::model()->findByPk(ContractGroupModel::MRT_KT);
			$diagnostics = $group->getServicesInGroup();

			$criteria = new \CDbCriteria;
			if ($serviceId == ServiceModel::TYPE_SUCCESSFUL_DIAGNOSTICS_MRT_OR_KT) {
				$criteria->addInCondition('t.diagnostics_id', $diagnostics);
			} else {
				$criteria->addCondition("t.diagnostics_id = 0");
				$criteria->addNotInCondition('t.diagnostics_id', $diagnostics, 'OR');
			}
			$this->getDbCriteria()->mergeWith($criteria);
		}

		return $this;
	}

	/**
	 * Поиск по доктору
	 *
	 * @param int $doctorId
	 *
	 * @return $this
	 */
	public function byDoctor($doctorId)
	{
		$this->getDbCriteria()
			->mergeWith([
				'condition' => 'req_doctor_id = :doctor',
				'params'    => [':doctor' => $doctorId],
			]);

		return $this;
	}

	/**
	 * Возвращает первоначальное значение аттрибута на момент создания объекта
	 *
	 * @param $attr
	 *
	 * @return mixed
	 */
	public function getOriginalValue($attr) {
		return (isset($this->_prev_attr[$attr])) ? $this->_prev_attr[$attr] : null;
	}

	/**
	 * Изменилось ли значение аттрибута при работе с заявкой
	 *
	 * @param string $attr
	 *
	 * @return bool
	 */
	public function isChanged($attr) {

		//для дат, когда 0000-00-00 меняется на пусто '' или наоборот
		if (($attr === 'date_record' || $attr === 'date_admission') && !intval($this->_prev_attr[$attr]) && empty($this->$attr)) {
			return false;
		}

		return ($this->_prev_attr[$attr] != $this->$attr) ? true : false;
	}

	/**
	 * Сброс флагов изменения свойств модели
	 */
	public function resetChanges()
	{
		$this->_prev_attr = $this->getAttributes();
	}

	/**
	 * Отправлять СМС сообщения пациентам или нет
	 *
	 * @param bool $logHistory
	 *
	 * @return bool
	 */
	public function isNeedToSendSms($logHistory = true)
	{
		$result = false;
		$message = null;

		if ($this->getScenario() == self::SCENARIO_DUPLICATE) {
			return false;
		}

		if (empty($this->clinic)) {
			$message = 'SMS не отправлена, пустая клиника';
		}
		elseif ($this->clinic->sendSMS != 'yes') {
			$message = "SMS не отправлена, СМС отключена в клинике: {$this->clinic->name}";
		}
		elseif (!empty($this->partner) && !$this->partner->send_sms) {
			$message = "SMS не отправлена пациенту, СМС отключена для партнёра: {$this->partner->name}";
		} else {
			$result = true;
		}

		if ($message && $logHistory) {
			$this->addHistory($message);
		}

		return $result;
	}

	/**
	 * Отправлять СМС сообщения в клинику или нет
	 *
	 * @param bool $logHistory
	 *
	 * @return bool
	 */
	public function isNeedToSendSmsToClinic($logHistory = true)
	{
		$result = true;
		$message = null;

		if (!empty($this->partner) && !$this->partner->send_sms_to_clinic) {
			$message = "SMS не отправлена в клинику, СМС отключена для партнёра: {$this->partner->name}";
			$result = false;
		}

		if ($message && $logHistory) {
			$this->addHistory($message);
		}

		return $result;
	}

	/**
	 * Получение названий способов обращений
	 *
	 * @return string[]
	 */
	static public function getTypeNames()
	{
		return [
			self::TYPE_WRITE_TO_DOCTOR      => 'Запись к врачу',
			self::TYPE_PICK_DOCTOR          => 'Подбор врача',
			self::TYPE_CALL                 => 'Телефонное обращение',
			self::TYPE_CALL_TO_DOCTOR       => 'Звонок в клинику',
			self::TYPE_ONLINE_RECORD        => 'Онлайн - запись',
		];
	}

	/**
	 * Получение названий источников
	 *
	 * @return string[]
	 */
	static public function getSourceNames()
	{
		return self::$_sourceTypes;
	}

	/**
	 * Получение названия источника
	 *
	 * @return null
	 */
	public function getSourceName()
	{
		return isset(self::$_sourceTypes[$this->source_type]) ? self::$_sourceTypes[$this->source_type] : null;
	}

	/**
	 * Заявка в биллинге?
	 * @return bool
	 */
	public function isInBilling()
	{
		return $this->billing_status == self::BILLING_STATUS_YES || $this->billing_status == self::BILLING_STATUS_PAID;

	}

	/**
	 * Сохранение стоимости заявки
	 *
	 * @return bool
	 */
	public function saveRequestCost()
	{
		if ($this->billing_status == self::BILLING_STATUS_PAID) {
			return true;
		}

		$this->date_billing = $this->getBillingDate();

		//если мы не работаем с клиникой, то мы не трогаем биллинг по таким заявкам
		if ($this->clinic && $this->clinic->isActive()) {
			$this->request_cost = null;

			if ($this->billing_status != self::BILLING_STATUS_REFUSED) {
				$clinicContract = $this->clinic ? $this->clinic->getRequestContract($this) : null;

				if ($clinicContract) {
					$this->billing_status = $clinicContract->isInBilling($this) ? self::BILLING_STATUS_YES : self::BILLING_STATUS_NO;

					//если признак биллинга изменился - сразу пишем это в базу
					//так как при расчете стоимости заявки нам нужно знать количество заявок в биллинге,
					//в том числе нужно учесть и эту заявку
					if ($this->isChanged('billing_status')) {
						$this->updateByPk($this->req_id, [ 'billing_status' => $this->billing_status ]);
					}
					//заявки от яндекса по нулям, для остальных считаем стоимость
					if ($this->partner && $this->partner->isYandex()) {
						$this->request_cost = 0;
						$this->billing_status = self::BILLING_STATUS_NO;
					} else {
						$this->request_cost = $clinicContract->getRequestCost($this);
					}
				} else {
					$this->billing_status = self::BILLING_STATUS_NO;
				}
			}
		}

		// автоматическая смена пратнёрского статуса, если он ранее не был проставлен
		if ($this->partner_status == self::PARTNER_STATUS_HOLD) {
			if ($this->req_status == self::STATUS_REJECT) {
				if ($this->reject_reason == Rejection::REASON_SPAM || $this->reject_reason == Rejection::REASON_TEST) {
					$this->partner_status = self::PARTNER_STATUS_REJECT;
				}
			} elseif ($this->req_status == self::STATUS_REMOVED) {
				$this->partner_status = self::PARTNER_STATUS_REJECT;
			} elseif ($this->isInBilling()) {
				$this->partner_status = self::PARTNER_STATUS_ACCEPT;
			}
		}

		//пересчитываем стоимость для партнера
		$this->partner_cost = PartnerCostModel::model()->getRequestCost($this);

		return $this->updateByPk(
			$this->req_id,
			[
				'partner_status' => $this->partner_status,
				'date_billing'   => $this->date_billing,
				'billing_status' => $this->billing_status,
				'partner_cost'   => $this->partner_cost,
				'request_cost'   => $this->request_cost,
			]
		);
	}

	/**
	 * Поиск новых заявок
	 *
	 * @param int $lastId
	 *
	 * @return $this
	 */
	public function latest($lastId)
	{
		$this->getDbCriteria()
			->mergeWith([
					'condition' => 'req_id > :last_id',
					'params'    => [':last_id' => $lastId],
				]);

		return $this;
	}

	/**
	 * Дошел или нет клиент по этой заявке
	 *
	 * @return bool
	 */
	public function isCame()
	{
		return (
			intval($this->date_admission) > 0 && intval($this->date_admission) <= time()
			&&
			$this->req_status != self::STATUS_REMOVED
			&&
			$this->billing_status != self::BILLING_STATUS_REFUSED
		);
	}

	/**
	 * Был запись по этой заявке или нет
	 *
	 * @return bool
	 */
	public function isRecord()
	{
		return (
			intval($this->date_admission)
			&&
			$this->req_status != self::STATUS_REMOVED
		);
	}


	/**
	 * Проверка является ли заявка логически новой, потому что c появлением
	 * статуса PRE_CREATED метод getIsNewRecord() не является показателем
	 *
	 * @return bool
	 */
	public function isNew()
	{
		$isNew = false;

		if ($this->getIsNewRecord() && $this->req_status !== self::STATUS_PRE_CREATED) {
			$isNew = true;
		} elseif ($this->isChanged('req_status') && $this->getOldStatus() == RequestModel::STATUS_PRE_CREATED) {
			$isNew = true;
		}

		return $isNew;
	}

	/**
	 * Был отказ клиента прийти на прием для этой клиники или нет
	 *
	 * @return bool
	 */
	public function isReject()
	{
		return (
			!intval($this->date_admission)
			&&
			$this->req_status == self::STATUS_REJECT
		);
	}

	/**
	 * Была отменена клиникой
	 *
	 * @return bool
	 */
	public function isRefused()
	{
		return (
			intval($this->date_admission)
			&&
			$this->req_status != self::STATUS_REMOVED
			&&
			$this->billing_status == self::BILLING_STATUS_REFUSED
		);
	}

	/**
	 * Установка КЛИНИКОЙ в заявке в одного из состояний, влияющих на биллинг заявки
	 *
	 * @param int $billingState
	 * @param array $attr
	 *
	 * @return bool
	 */
	public function setBillingState($billingState, $attr = [])
	{
		switch ($billingState) {
			case self::BILLING_STATE_RECORD:
				//нужно ли заворачивать заявки, по которым пришли?

				$this->req_status = self::STATUS_RECORD;
				$this->billing_status = self::BILLING_STATUS_NO;

				if (!isset($attr['date_admission'])) {
					$this->addError('date_admission', 'Не указана дата записи');
				} else {
					$this->date_admission = $attr['date_admission'];
				}
				break;

			case self::BILLING_STATE_REJECT:
				//проверяем, что можно отклонить эту заявку
				$tariff = $this->clinic->getRequestContract($this);
				if ($tariff !== null && $tariff->contract->isPayForRecord() && $this->isRecord()) {
					$this->addError('req_status', 'Нельзя зафиксировать отказ по заявке с записью');
				} else {
					$this->req_status = self::STATUS_REJECT;
					if (isset($attr['reject_reason'])) {
						$this->reject_reason = $attr['reject_reason'];
					}
				}
				break;

			case self::BILLING_STATE_CAME:
				// При сохранении заявки должен автоматом проставиться BILLING_STATUS_YES
				$this->billing_status = self::BILLING_STATUS_NO;
				if (isset($attr['date_admission'])) {
					$this->date_admission = $attr['date_admission'];
				}
				break;

			case self::BILLING_STATE_REFUSED:
				$this->req_status = self::STATUS_REJECT;
				$this->billing_status = self::BILLING_STATUS_REFUSED;
				if (isset($attr['reject_reason'])) {
					$this->reject_reason = $attr['reject_reason'];
				}
				break;
		}

		return !$this->hasErrors();
	}

	/**
	 * Сохранение КЛИНИКОЙ в заявке в одного из состояний, влияющих на биллинг заявки
	 *
	 * @param int $billingState
	 * @param array $attr
	 *
	 * @return bool
	 */
	public function saveBillingState($billingState, $attr = [])
	{
		if (!$this->setBillingState($billingState, $attr)) {
			return false;
		}

		return $this->save(false);
	}

	/**
	 * Сохранение биллинг статуса с датой
	 *
	 * @param int $billingStatus
	 * @param string $date
	 * @return bool
	 */
	public function saveBillingStatus($billingStatus, $date)
	{
		if (empty($this->setBillingDate($date))) {
			return false;
		}

		$this->billing_status = $billingStatus;

		return $this->save(false);
	}

	/**
	 * Фильтр по состоянию в биллинге
	 *
	 * @param int|array $state
	 * @param bool      $extra
	 *
	 * @return $this
	 */
	public function inBillingState($state, $extra = false)
	{
		$criteria = new \CDbCriteria();

		$states = (is_array($state)) ? $state : [$state];

		foreach ($states as $billingState) {
			$criteria_part = new \CDbCriteria();

			switch ($billingState) {
				case self::BILLING_STATE_NEW:
					$criteria_part
						->addNotInCondition('t.req_status', [self::STATUS_REMOVED, self::STATUS_REJECT])
						->addCondition('t.date_admission IS NULL OR t.date_admission = 0');
					break;

				case self::BILLING_STATE_RECORD:
					$criteria_part->addNotInCondition('t.req_status', [ self::STATUS_REMOVED ]);
					$criteria_part->addCondition('t.date_admission IS NOT NULL AND t.date_admission > 0');
					if ($extra) {
						$criteria_part->addNotInCondition('t.billing_status', [ self::BILLING_STATUS_REFUSED ]);
					}
					break;

				case self::BILLING_STATE_REJECT:
					$criteria_part->addInCondition('t.req_status', [ self::STATUS_REJECT ]);
					$criteria_part->addCondition('t.date_admission IS NULL OR t.date_admission = 0');
					break;

				case self::BILLING_STATE_CAME:
					$criteria_part->addNotInCondition('t.req_status', [ self::STATUS_REMOVED ]);
					$criteria_part->addCondition('t.date_admission IS NOT NULL AND t.date_admission > 0 AND t.date_admission <= UNIX_TIMESTAMP(NOW())');
					$criteria_part->addNotInCondition('t.billing_status', [ self::BILLING_STATUS_REFUSED ]);
					break;

				case self::BILLING_STATE_REFUSED:
					$criteria_part->addNotInCondition('t.req_status', [ self::STATUS_REMOVED ]);
					$criteria_part->addCondition('t.date_admission IS NOT NULL AND t.date_admission > 0');
					$criteria_part->addInCondition('t.billing_status', [ self::BILLING_STATUS_REFUSED ]);
					break;
			}
			$criteria->mergeWith($criteria_part, 'OR');
		}

		$this->getDbCriteria()->mergeWith($criteria);
		return $this;
	}
	/**
	 * Бронирует заявку/Отменяет бронь
	 *
	 * @param int|null $slotId
	 * @param bool $reserve
	 * @throws \CException
	 *
	 * @return boolean
	 */
	public function book($slotId, $reserve = false)
	{

		if(is_null($slotId)){
			//unbook
			if($this->activeBooking){
				return $this->activeBooking->cancelBook();
			} else {
				throw new \CException('Бронь/резерв отсутствует для заявки');
			}
		} else {
			if($this->activeBooking){
				throw new \CException("Бронь/резерв уже существует");
			}

			if ($slot = SlotModel::model()->byExternalId($slotId)->find()) {
				$this->req_doctor_id = $slot->doctorClinic->doctor_id;
				$this->clinic_id = $slot->doctorClinic->clinic_id;

				$this->getRelated('doctor', true);
				$this->getRelated('clinic', true);

				//если у клиники нет онлайн записи - время записи не проставляем
				if ($this->clinic && $this->clinic->online_booking) {
					$this->date_admission = strtotime($slot->start_time);
				} else {
					$this->addHistory("При создании заявки клиентом выбрано время ". date("H:i d.m.Y", strtotime($slot->start_time)), RequestHistoryModel::LOG_TYPE_COMMENT);
				}
				$this->save();
			}

			$this->activeBooking = new BookingModel();
			$this->activeBooking->request_id = $this->req_id;

			if($reserve){
				$this->activeBooking->reserve($slotId);
			} else {
				$this->activeBooking->book($slotId);
			}

			if($this->activeBooking->getErrors()){
				$this->addErrors($this->activeBooking->getErrors());
				return false;
			} else {
				return true;
			}
		}
	}

	/**
	 * Выбрать все заявки с онлайн бронированием
	 *
	 * @param bool $isBook
	 *
	 * @return $this
	 */
	public function isBooking($isBook = true)
	{
		$this->getDbCriteria()->mergeWith([
			'with' => [ 'activeBooking' ],
			'condition' => 'activeBooking.id IS ' . ($isBook ? 'NOT ' : '') . 'NULL',
		]);

		return $this;
	}

	/**
	 * Выборка по тарифу
	 *
	 * @param ContractModel | int $contract
	 *
	 * @return $this
	 * @throws \CException
	 */
	public function byContract($contract)
	{
		if (!is_object($contract)) {
			$contract = ContractModel::model()->findByPk($contract);
		}
		if (!$contract) {
			throw new \CException('Unknown contract');
		}

		$this->getDbCriteria()->mergeWith($contract->getContractCriteria());

		return $this;
	}

	/**
	 * Состояние отоброжаемое для клиники
	 *
	 * @return int
	 */
	public function getClinicBillingState()
	{
		$contract = $this->getContract();
		if (!$contract) {
			return null;
		}

		if ($this->isRefused()) {
			return self::BILLING_STATE_REFUSED;
		}

		if ($contract->isPayForCall()) {
			return self::BILLING_STATE_RECORD;
		}

		if ($contract->isPayForVisit()) {
			if ($this->isCame()) {
				return self::BILLING_STATE_CAME;
			}
		}
		elseif ($this->isReject()) {
			return self::BILLING_STATE_REJECT;
		}

		if ($this->isRecord()) {
			return self::BILLING_STATE_RECORD;
		}

		return $contract->isPayForVisit() ? null : self::BILLING_STATE_NEW;
	}

	/**
	 * Получить контракт заявки
	 *
	 * @return ContractModel | null
	 */
	public function getContract() {
		$clinicContract = $this->clinic ? $this->clinic->getRequestContract($this) : null;

		return $clinicContract ? $clinicContract->contract : null;
	}

	/**
	 * Определяет по какой дате считается биллинг и возвращает соответствующую дату в формате YYYY-mm-dd H:i:s
	 *
	 * @param string $format
	 *
	 * @return string | null
	 */
	public function getBillingDate($format = 'Y-m-d H:i:s')
	{
		if ($this->clinic && $clinicContract = $this->clinic->getRequestContract($this)) {
			$billingDate = $clinicContract->contract->getBillingDate();
			$date = $this->$billingDate;

			if ($date && $date !== '0000-00-00 00:00:00') {
				return ($billingDate === 'date_record') ? date($format, strtotime($date)) : date($format, $date);
			}
		}

		return null;
	}


	/**
	 * Определяет по какой дате считается биллинг и устанавливает е значение
	 *
	 * @param string $date
	 *
	 * @return string | null
	 */
	public function setBillingDate($date)
	{
		if (!$date = strtotime($date)) {
			return null;
		}

		if ($this->clinic && $clinicContract = $this->clinic->getRequestContract($this)) {
			$billingDate = $clinicContract->contract->getBillingDate();

			if ($billingDate === "date_record") {
				$date = date('Y-m-d H:i:s', $date);
			}

			return $this->$billingDate = $date;
		}

		return null;
	}

	/**
	 * Возвращает массив с датой начала и окончания периода биллинга для этой заявки
	 *
	 * return array('from'=> '2014-01-01', 'to' => '2014-02-01')
	 *
	 * @return array|null
	 */
	public function getBillingPeriod()
	{
		if ($this->clinic && $clinicContract = $this->clinic->getRequestContract($this)) {
			return $clinicContract->contract->getBillingPeriod($this);
		}

		return null;
	}

	/**
	 * Открытие заявки в БО
	 *
	 */
	public function openRequest()
	{
		$this->is_hot = 0;
		$this->updateByPk($this->req_id, ['is_hot' => $this->is_hot]);
	}

	/**
	 * Поиск по коду валидации телефона
	 *
	 * @param string $code
	 * @return $this
	 */
	public function byValidationCode($code)
	{
		$this->getDbCriteria()
			->mergeWith(
				[
					'condition' => 'validation_code = :code',
					'params' => [':code' => $code]
				]
			);

		return $this;
	}

	/**
	 * Генерируется код
	 *
	 * @param int $min
	 * @param int $max
	 * @return int
	 */
	public function generateValidationCode($min = 1000, $max = 9999)
	{
		$code = rand($min, $max);

		return $code;
	}

	/**
	 * Тип заявки
	 *
	 * @return string
	 */
	public function getTypeName()
	{
		$types = self::getTypeNames();

		return isset($types[$this->req_type]) ? $types[$this->req_type] : null;
	}

	/**
	 * Получение навзания услуги
	 *
	 * @return string
	 */
	public function getServiceName()
	{
		$service = '';
		if ($this->kind == self::KIND_DIAGNOSTICS) {
			if (!empty($this->diagnostics_other)) {
				$service = $this->diagnostics_other;
			} else {
				$service = !is_null($this->diagnostics) ? $this->diagnostics->getFullName() : $service;
			}
		} elseif ($this->kind == self::KIND_DOCTOR) {
			$service = !is_null($this->sector) ? $this->sector->name : $service;
		}

		return $service;
	}

	/**
	 * Список типов заявок
	 *
	 * @return array
	 */
	public function getKindList()
	{
		return [
			self::KIND_DOCTOR      => 'Доктор',
			self::KIND_DIAGNOSTICS => 'Диагностика',
			self::KIND_ANALYSIS    => 'Анализы'
		];
	}

	/**
	 * Ищет похожую заявку за указанный промежуток времени
	 *
	 * @param RequestModel $baseRequest
	 * @param string[] $types
	 * @param string[] $withoutPhones
	 * @return RequestModel
	 */
	public static function isSameRequest(RequestModel $baseRequest, $types, $withoutPhones = [])
	{
		if ($baseRequest->partner && $baseRequest->partner->not_merged_requests) {
			return $baseRequest;
		}

		$request = RequestModel::model()
			->sameRequestByPhone($baseRequest->client_phone, $baseRequest->req_created)
			->inStatuses([self::STATUS_CAME, self::STATUS_NOT_CAME, self::STATUS_CAME_UNDEFINED], true)
			->withTypes($types)
			->byPartner($baseRequest->partner_id);

		if (count($withoutPhones)) {
			$request->withoutPhones($withoutPhones);
		}

		$baseRequest->clinic_id && $request->inClinic($baseRequest->clinic_id);

		!is_null($baseRequest->kind) && $request->byKind($baseRequest->kind);

		$request = $request->find(['limit' => 1]);

		if ($request !== null) {
			$request->setRecall();
		}

		return $request ?: $baseRequest;
	}

	/**
	 * Максимальная длительность записи у заявки
	 *
	 * @return bool
	 */
	public function getMaxDurationRecord()
	{
		$duration = 0;

		foreach ($this->request_record as $rec) {
			if ($rec->duration > $duration) {
				$duration = $rec->duration;
			}
		}

		return $duration;
	}

	/**
	 * Выборка заявок требующих обработки
	 *
	 * @param bool $hot
	 *
	 * @return $this
	 */
	public function isHot($hot = true)
	{
		$this->getDbCriteria()->mergeWith([
			'condition' => 't.is_hot = ' . ($hot ? 1 : 0),
		]);

		return $this;
	}

	/**
	 * Не обработанные
	 *
	 * @return $this
	 */
	public function notProcessed()
	{
		$this->getDbCriteria()
			->mergeWith(
				['condition' => 'processing_time = 0']
			);

		return $this;
	}

	/**
	 * Выборка заявок требующих обработки (для онлайн заявок)
	 *
	 * @param int $limitTime
	 *
	 * @return $this
	 */
	public function needProcessing($limitTime)
	{
		$this->getDbCriteria()->mergeWith([
			'condition' => 't.req_created > :created AND t.processing_time = 0',
			'params' => [
				'created' => $limitTime,
			],
		]);

		return $this;
	}

	/**
	 * Требует ли заявка обработки (для онлайн заявок)
	 *
	 * @param int $limitTime
	 *
	 * @return bool
	 */
	public function isNeedProcessing($limitTime)
	{
		return !$this->processing_time && $this->req_created > $limitTime;
	}

	/**
	 * Доступные к показу
	 *
	 * 112014
	 *
	 * @return RequestModel $this
	 */
	public function origin()
	{
		$this->getDbCriteria()->mergeWith(
			[
				'condition' => "partner_id IS NULL OR partner_id <> :partner",
				'params' => [
					':partner' => PartnerModel::SMART_MEDIA_2,
				],
			]
		);

		return $this;
	}

	/**
	 * Выборка по токену
	 *
	 * @param $token
	 *
	 * @return $this
	 */
	public function byToken($token)
	{
		$this->getDbCriteria()->mergeWith(
			array(
				'condition' => "token = :token",
				'params' => [
					':token' => $token,
				],
			)
		);

		return $this;
	}

	/**
	 * Проверка на спам
	 *
	 * @return bool
	 */
	public function isSpam()
	{
		if (empty($this->token)) {
			return false;
		}

		$isSpam = (bool)RequestSpamModel::model()
			->byToken($this->token)
			->count();
		if ($isSpam) {
			return true;
		}

		$count = RequestModel::model()
			->byToken($this->token)
			->createdInInterval(time() - self::SPAM_INTERVAL_TIME)
			->count();
		if ($count >= self::SPAM_NUM_REQUESTS) {
			return true;
		}

		return false;
	}

	/**
	 * Установка токена
	 */
	public function setToken()
	{
		$request = \Yii::app()->request;
		$this->token = md5($request->getUserAgent() . $request->getUserHostAddress());
	}

	/**
	 * Исключаем из поиска заданные заявки
	 *
	 * @param int|int[] $requests
	 *
	 * @return $this
	 */
	public function except($requests)
	{
		if (empty($requests)) {
			return $this;
		}

		$criteria = new \CDbCriteria();
		$criteria->addNotInCondition('t.req_id', is_array($requests) ? $requests : [$requests]);

		$this->getDbCriteria()->mergeWith($criteria);

		return $this;
	}

	/**
	 * Проверка, что данная заявка - повторная
	 *
	 * Если пациент в рамках текущего месяца повторно записывается в ту же клинику и с той же специальностью,
	 * такая заявка считается повторной и не тарифицируется
	 * Возвращается id заявки, для которой текущая является повторной
	 *
	 * @return RequestModel|null
	 */
	public function isRepeated()
	{
		$from = strtotime(date("Y-m", $this->date_admission)."-01");

		$requestModel = new RequestModel();
		$requestModel->betweenDateAdmission($from)
			->inBilling()
			->byClientName($this->client_name)
			->byClientPhone($this->client_phone)
			->inClinic($this->clinic_id)
			->except($this->req_id)
			->byKind($this->kind);

		if ($this->kind == self::KIND_DOCTOR) {
			$requestModel->inSectors([$this->req_sector_id]);
		} else {
			$requestModel->inDiagnostics([$this->diagnostics_id]);
		}

		return $requestModel->find();
	}

	/**
	 * @param int $reason
	 * @return bool
	 */
	public function setRejectStatus($reason)
	{
		$reason = (int)$reason;
		if ($reason <= 0) {
			$this->addError('reject_reason', 'Выберите причину отказа');
			return false;
		}

		$this->reject_reason = $reason;
		$this->req_status = $reason == Rejection::REASON_NOT_COME ? self::STATUS_NOT_CAME : self::STATUS_REJECT;
		return true;
	}

	/**
	 * Установка статуса заявки оператором
	 *
	 * @param array $params
	 *
	 * @return bool
	 */
	public function getStatusForOperatorAction(array $params = [])
	{
		//оператор не может изменить статус удаленной, отказной заявки, в статусе Завершена или Не пришел
		if ($this->_prev_req_status == self::STATUS_REMOVED
			|| $this->_prev_req_status == self::STATUS_REJECT
			|| $this->_prev_req_status == self::STATUS_CAME
			|| $this->_prev_req_status == self::STATUS_NOT_CAME
		) {
			return $this->_prev_req_status;
		}

		//новая заявка, если ее сохраняет оператор, только в принята
		if ($this->req_status == self::STATUS_NEW) {
			//если есть такой параметр то идет множественное создание заявок и статус всем новым "обработана"
			if(isset($params['multiply_create']) && $params['multiply_create']){
				return self::STATUS_RECORD;
			} else {
				return self::STATUS_ACCEPT;
			}
		}

		//установлен статус отказа
		if ($this->isChanged('req_status') && $this->req_status == self::STATUS_REJECT) {
			//нельзя отменить заявку, по которой нужно перезвонить
			$st = ($this->_prev_req_status == self::STATUS_CALL_LATER && $this->isCallLater())
				? self::STATUS_CALL_LATER
				: self::STATUS_REJECT;

			return $st;
		}

		//если нужно перезвонить позже, для всех, кроме заявки перезвонить ставим статус перезвонить
		if ($this->req_status != self::STATUS_RECALL && $this->isCallLater()) {
			return self::STATUS_CALL_LATER;
		}

		//Принята
		if ($this->req_status == self::STATUS_ACCEPT) {
			return $this->isTransfer() ? self::STATUS_PROCESS : self::STATUS_ACCEPT;
		}

		if ($this->req_status == self::STATUS_PROCESS) {
			//был перевод в клинику и есть дата записи
			if ($this->isRecord() && $this->isTransfer()) {
				return self::STATUS_RECORD;
			}

			//не было перевода в клинику - оставляем в работе
			return !$this->isTransfer() ? self::STATUS_ACCEPT : self::STATUS_PROCESS;
		}

		if (
			$this->req_status == self::STATUS_CAME_UNDEFINED
			|| $this->req_status == self::STATUS_RECORD
			//если статус заявки перезвонить позже, но даты перезвона нет
			|| $this->req_status == self::STATUS_CALL_LATER && !$this->isCallLater()
			|| $this->req_status == self::STATUS_RECALL
		) {
			//был перевод в клинику и есть дата записи и клиент пришел
			if ($this->isRecord() && $this->isTransfer() && $this->isAppointment()) {
				return self::STATUS_CAME;
			}

			//был перевод в клинику и есть дата записи и клиент пришел
			if ($this->isRecord() && $this->isTransfer() && !$this->isAppointment()) {
				return self::STATUS_RECORD;
			}

			//был только перевод в клинику
			if (!$this->isRecord() && $this->isTransfer()) {
				return self::STATUS_PROCESS;
			}

			//не было перевода в клинику - оставляем в работе
			if (!$this->isTransfer()) {
				return self::STATUS_ACCEPT;
			}

			return $this->req_status;
		}

		return $this->req_status;

	}

	/**
	 * Требуется ли перезвонить позже по заявке
	 *
	 * @return bool
	 */
	public function isCallLater()
	{
		return !empty($this->call_later_time);
	}


	/**
	 * Был звонок в заявке переведен в клинику
	 *
	 * @return bool
	 */
	public function isTransfer()
	{
		return !empty($this->is_transfer);
	}

	/**
	 * Оператор подтвердил, что клиент пришел на прием или нет
	 *
	 * @return bool
	 */
	public function isAppointment()
	{
		return !empty($this->appointment_status);
	}

	/**
	 * Астериск еще не звонил по поводу поступившей онлайн заявки
	 *
	 * @return $this
	 */
	public function notNotifiedByAsterisk()
	{
		$this->getDbCriteria()
			->mergeWith(
				[
					'with' => [
						'request_history' => [
							'select' => false,
							'condition' => 'request_history.request_id is null',
							'joinType' => 'left join',
							'on' => 'request_history.action = ' . RequestHistoryModel::LOG_TYPE_NOTIFY_BY_ASTERISK,
						]
					]
				]
			);

		return $this;
	}

	/**
	 * Надо ли уведомлять астериском клинику
	 *
	 * @param string $datetime
	 * @param int $duration период на протяжени которого надо уведомить
	 *
	 * @return $this
	 */
	public function needToNotifyByAsterisk($datetime, $duration)
	{
		$expireTimeLimit = \Yii::app()->params['RequestProcessingTimeLimit'];
		$notifyInterval = \Yii::app()->params['DOnlineClinicNotifyInterval'];

		$time1 = date('c', strtotime($datetime) + $expireTimeLimit - $notifyInterval);
		$time2 = date('c', strtotime($time1) - $duration);

		$criteria = new \CDbCriteria();
		$criteria->addBetweenCondition('expire_time', $time2, $time1);
		$this->getDbCriteria()->mergeWith($criteria);


		$this->notNotifiedByAsterisk();

		return $this;
	}
	/**
	 * Высчитывает время до которого клиника должна обработать заявку на онлайн диагностику
	 *
	 * @param int $dateTime
	 * @param null $interval
	 *
	 * @return bool|string
	 */
	public function calculateExpireTime($dateTime, $interval = null)
	{
		if ($interval === null) {
			$interval = \Yii::app()->params['RequestProcessingTimeLimit'];
		}

		$clinicWorkDateTime = $this->clinic->getWorkTime($dateTime + $interval);

		if(($dateTime + $interval) != $clinicWorkDateTime){
			$expireTime = date('c', $clinicWorkDateTime + $interval);
		} else {
			$expireTime = date('c', $clinicWorkDateTime);
		}

		return $expireTime;
	}

	/**
	 * Выборка заявок с учётом времени региона
	 *
	 * @return $this
	 */
	public function activeCityWorkTime()
	{
		$hour = date('G');

		$this->getDbCriteria()->mergeWith([
			'with' => [
				'city' => [
					'condition' => 'city.time_zone BETWEEN :timeZoneBegin AND :timeZoneEnd',
					'params' => [
						'timeZoneBegin' => 8 - $hour,
						'timeZoneEnd' => 22 - $hour,
					],
				],
			],
		]);

		return $this;
	}

	/**
	 * Заявки с временем перезвона меньше, чем текущее
	 *
	 * @return $this
	 */
	public function needCallLater()
	{
		$this->getDbCriteria()->mergeWith([
			'condition' => $this->getTableAlias() . '.call_later_time < UNIX_TIMESTAMP(NOW())',
		]);

		return $this;
	}

	/**
	 * Поиск следующей заявки для оператора
	 *
	 * @param int $stream
	 *
	 * @return RequestModel | null
	 * @throws \CException
	 */
	public function findRequestByOperatorStream($stream)
	{
		$scopes = [
			'activeCityWorkTime' => null,
		];
		$order = null;

		switch ($stream) {
			case self::OPERATOR_STREAM_NEW:
				$scopes['inStatuses'] = [[RequestModel::STATUS_NEW]];
				$scopes['withTypes'] = [[RequestModel::TYPE_WRITE_TO_DOCTOR, RequestModel::TYPE_PICK_DOCTOR]];
				$order = 't.req_created DESC';
				break;

			case self::OPERATOR_STREAM_CALL_LATER:
				$scopes['inStatuses'] = [[RequestModel::STATUS_CALL_LATER]];
				$scopes['needCallLater'] = [];
				$order = 't.call_later_time DESC';
				break;

			default:
				throw new \CException('Unknown operator stream');
				break;
		}

		return $this->find([
			'scopes' => $scopes,
			'order' => $order,
			'limit' => 1,
		]);
	}
}
