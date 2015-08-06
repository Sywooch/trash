<?php

namespace dfs\docdoc\models;

use dfs\docdoc\api\clinic\ClinicApiClient;
use dfs\docdoc\api\JsonRpcException;

/**
 * This is the model class for table "booking".
 *
 * The followings are the available columns in table 'booking':
 *
 * @property integer      $id
 * @property integer      $request_id
 * @property integer      $slot_id
 * @property integer      $status
 * @property string       $date_created
 * @property string       $external_id
 * @property string       $start_time
 * @property string       $finish_time
 *
 * The followings are the available model relations:
 * @property RequestModel $request
 * @property SlotModel    $slot
 *
 * @method BookingModel findByPk
 * @method BookingModel find
 * @method int count
 */
class BookingModel extends \CActiveRecord
{
	/**
	 * статусы брони
	 */
	//новая заявка на бронирование
	const STATUS_NEW = 1;
	//Заявка на бронирование принята партнером
	const STATUS_ACCEPTED = 2;
	//заявка на бронирование отменена пользователем
	const STATUS_CANCELED_BY_USER = 5;
	//заявка подтверждена организацией
	const STATUS_APPROVED = 6;
	//заявка отклонена организацией
	const STATUS_CANCELED_BY_ORGANIZATION = 7;
	//человек пришел
	const STATUS_COME = 8;
	//человек не пришел
	const STATUS_DID_NOT_COME = 9;
	//человек пришел по брони но был развернут
	const STATUS_COME_BUT_DENIED = 10;
	//резерв без обращения в апи клиники
	const STATUS_RESERVED = 11;

	/**
	 * Если нужно создать бронь с не валидными данными
	 */
	const SCENARIO_SKIP_VALIDATION = 'SCENARIO_SKIP_VALIDATION';

	/**
	 * Локальные статусы в статусы заявки
	 *
	 * @var array
	 */
	public static $localToRequestStatuses = [
		self::STATUS_ACCEPTED                 => [RequestModel::STATUS_PROCESS, null],
		self::STATUS_CANCELED_BY_USER         => [RequestModel::STATUS_REJECT, null],
		self::STATUS_APPROVED                 => [RequestModel::STATUS_ACCEPT, null],
		self::STATUS_CANCELED_BY_ORGANIZATION => [RequestModel::STATUS_REJECT, null],
		self::STATUS_COME                     => [RequestModel::STATUS_CAME, null],
		self::STATUS_DID_NOT_COME             => [RequestModel::STATUS_CAME, 'Пациент не пришел на прием'],
		self::STATUS_COME_BUT_DENIED          => [RequestModel::STATUS_CAME, 'Пациенту отказали в приеме'],
		self::STATUS_RESERVED                 => [null, 'Резерв'],//если резерв
	];

	/**
	 * Маппинг локальных в удаленные статусы
	 *
	 * @var array
	 */
	public static $localToRemoteStatuses = [
		self::STATUS_NEW                      => 'NEW',
		self::STATUS_ACCEPTED                 => 'ACCEPTED',
		self::STATUS_CANCELED_BY_USER         => 'CANCELED_BY_USER',
		self::STATUS_APPROVED                 => 'STATUS_APPROVED',
		self::STATUS_CANCELED_BY_ORGANIZATION => 'STATUS_CANCELED_BY_ORGANIZATION',
		self::STATUS_COME                     => 'COME',
		self::STATUS_DID_NOT_COME             => 'DID_NOT_COME',
		self::STATUS_COME_BUT_DENIED          => 'COME_BUT_DENIED',
	];

	/**
	 * @param int $localStatus
	 *
	 * @return mixed
	 */
	public function localToRemoteStatus($localStatus)
	{
		if (array_key_exists($localStatus, self::$localToRemoteStatuses)) {
			return self::$localToRemoteStatuses[$localStatus];
		} elseif ($localStatus == self::STATUS_RESERVED) {
			return 'RESERVED'; //только локальный статус
		} else {
			trigger_error('Не могу преобразовать локальный в удаленный статус', E_USER_WARNING);
			return 'UNKNOWN';
		}
	}

	/**
	 * @param string $remoteStatus
	 *
	 * @return mixed
	 */
	public function remoteToLocalStatus($remoteStatus)
	{
		if (in_array($remoteStatus, self::$localToRemoteStatuses)) {
			return array_search($remoteStatus, self::$localToRemoteStatuses);
		} else {
			trigger_error('Не могу преобразовать удаленный в локальный статус', E_USER_WARNING);
			return -1;
		}
	}

	/**
	 * Возможные переходы по по статусам
	 *
	 * @var array
	 */
	public $_statusMap = [
		null                                  => [self::STATUS_RESERVED, self::STATUS_NEW, self::STATUS_ACCEPTED],
		self::STATUS_RESERVED                 => [self::STATUS_NEW, self::STATUS_CANCELED_BY_ORGANIZATION],
		self::STATUS_NEW                      => [
			self::STATUS_ACCEPTED,
			self::STATUS_CANCELED_BY_ORGANIZATION,
			self::STATUS_CANCELED_BY_USER,
		],
		self::STATUS_ACCEPTED                 => [
			self::STATUS_CANCELED_BY_USER,
			self::STATUS_CANCELED_BY_ORGANIZATION,
			self::STATUS_APPROVED
		],
		self::STATUS_CANCELED_BY_USER         => [],
		self::STATUS_APPROVED                 => [
			self::STATUS_COME,
			self::STATUS_DID_NOT_COME,
			self::STATUS_COME_BUT_DENIED
		],
		self::STATUS_CANCELED_BY_ORGANIZATION => [],
		self::STATUS_COME                     => [],
		self::STATUS_DID_NOT_COME             => [],
		self::STATUS_COME_BUT_DENIED          => [],
	];

	/**
	 * Статусы, в которых заявка должны быть уникальна
	 *
	 * @return int[]
	 */
	public function getSuccessStatuses()
	{
		return [
			self::STATUS_RESERVED,
			self::STATUS_NEW,
			self::STATUS_ACCEPTED,
			self::STATUS_APPROVED,
			self::STATUS_COME,
			self::STATUS_DID_NOT_COME,
			self::STATUS_COME_BUT_DENIED,
		];
	}

	/**
	 * Для отслеживания смены статуса
	 *
	 * @var null
	 */
	protected $_status = null;

	/**
	 * Слежу за слотом
	 *
	 * @var null
	 */
	protected $_slotId = null;

	/**
	 * Слежу за заявкой
	 *
	 * @var null
	 */
	protected $_requestId = null;

	/**
	 * Returns the static model of the specified AR class.
	 *
	 * @param string $className
	 *
	 * @return BookingModel the static model class
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
		return 'booking';
	}

	/**
	 * @return string имя первичного ключа
	 */
	public function primaryKey()
	{
		return 'id';
	}

	/**
	 * rules
	 *
	 * @return array
	 */
	public function rules()
	{
		return [
			[
				'id, request_id, slot_id, status, date_created, external_id',
				'safe',
				'on' => ['test'],
			]
		];
	}

	/**
	 * relations
	 *
	 * @return array relations.
	 */
	public function relations()
	{
		return [
			'request' => [
				self::BELONGS_TO,
				RequestModel::class,
				'request_id'
			],
			'slot'    => [
				self::BELONGS_TO,
				SlotModel::class,
				['slot_id'=>'external_id']
			],
		];
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id'           => 'ID',
			'request_id'   => 'Заявка',
			'slot_id'      => 'Слот',
			'status'       => 'Статус',
			'date_created' => 'Дата бронирования',
			'external_id'  => 'ID в МИС',
		);
	}

	/**
	 * До стандартной валидации
	 *
	 * @return bool
	 */
	public function beforeValidate()
	{
		if ($this->status === null && $this->isNewRecord) {
			$this->status = self::STATUS_NEW;
		}

		if ($this->getScenario() !== self::SCENARIO_SKIP_VALIDATION) {
			//если не инсерт то нельзя менять слот и заявку
			if (!$this->isNewRecord) {
				if (!is_null($this->_slotId) && $this->_slotId != $this->slot_id) {
					$this->addError('slot_id', 'Нельзя менять слот для брони');
				}

				if (!is_null($this->_requestId) && $this->_requestId != $this->request_id) {
					$this->addError('request_id', 'Нельзя менять заявку для брони');
				}
			}

			//смена статуса только на разрешенный
			if (!$this->canChangeStatus($this->status)) {
				$this->addError(
					'status',
					'Запрещено бронь переводить в статус ' . $this->localToRemoteStatus($this->status)
				);
			}

			if (!$this->request) {
				$this->addError('request_id', 'Заявка не найдена');
			} elseif(!$this->request->client){
				$this->addError('request_id', 'Введите имя и телефон клиента');
			}

			if ($this->getIsNewRecord() && !$this->slot) {
				$this->addError('slot_id', 'Расписание не найдено');
			}

			if (in_array($this->status, $this->getSuccessStatuses())) {
				//одну заявку нельзя бронировать на разные слоты в успешном статусе
				if (!$this->hasErrors() &&
					$this->request->activeBooking &&
					$this->request->activeBooking->id != $this->id
				) {
					$this->addError(
						'request_id',
						sprintf('Заявка #%s уже имеет бронь/резерв', $this->request_id)
					);
				}

				//на один слот нельзя бронировать несколько заявок в статусе STATUS_NEW
				if (!$this->hasErrors()) {

					if(!$slot = $this->slot){//ушло из кеша
						$slot = new SlotModel();
						$slot->external_id = $this->slot_id;
					}

					if(!$slot->isAvailable($this->request_id)){
						$this->addError('slot_id', sprintf('Слот #%s уже занят', $this->slot_id));
					}

				}
			}
		}

		return !count($this->errors) && parent::beforeValidate();
	}

	/**
	 * Перед сохранением
	 *
	 * @return bool
	 */
	public function beforeSave()
	{
		if(!$this->date_created){
			$this->date_created = date('Y-m-d H:i:s');
		}

		if($this->getIsNewRecord() && $this->slot){
			$this->start_time = $this->slot->start_time;
			$this->finish_time = $this->slot->finish_time;
		}

		return parent::beforeSave();
	}

	/**
	 * После селекта
	 */
	public function afterFind()
	{
		parent::afterFind();

		$this->_status = $this->status;
		$this->_slotId = $this->slot_id;
		$this->_requestId = $this->request_id;
	}

	/**
	 * После сохранения
	 */
	public function afterSave()
	{
		parent::afterSave();

		//если был апдейт и сменился статус
		if ($this->_status != $this->status) {
			if ($this->isNewRecord) {
				if($this->status == self::STATUS_RESERVED){
					$history = "Создан резерв #{$this->id} для слота #{$this->slot_id}";
				} else {
					$history = "Создана бронь #{$this->id} для слота #{$this->slot_id}";
				}

				$this->request->addHistory($history);
			} else {
				$history = "Бронь #{$this->id}:
				изменение статуса
				 {$this->localToRemoteStatus($this->_status)} => {$this->localToRemoteStatus($this->status)}";
				$this->request->addHistory($history);
			}

			MailQueryModel::model()->addMessage(
				\Yii::app()->params['email']['support'],
				"Создание/изменение онлайн записи для заявки {$this->request_id}",
				$history
			);

			if($this->status == self::STATUS_CANCELED_BY_ORGANIZATION){
				$this->request->setBillingState(RequestModel::BILLING_STATE_REFUSED, ['reject_reason' => '']);
			}

			if (array_key_exists($this->status, self::$localToRequestStatuses)) {
				list($status, $comment) = BookingModel::$localToRequestStatuses[$this->status];
				!is_null($status) && $this->request->saveStatus($status);
				$comment && $this->request->addHistory($comment);
			}
		}

		$this->_status = $this->status;
	}

	/**
	 * Бронирую в клинике
	 */
	public function bookInClinic()
	{
		$params['slotId'] = $this->slot_id;
		$params["fullname"] = $this->request->client->name;
		$params["phone"] = $this->request->client->phone;
		$params["email"] = $this->request->client->email;
		$params["comment"] = $this->request->client_comments;

		try {
			$response = ClinicApiClient::createClient()->book($params);
			$this->external_id = $response->bookId;
			$this->status = $this->remoteToLocalStatus($response->status);
			$this->save();

			$result = true;
		} catch (\Exception $e) {
			$result = false;

			$message = $e instanceof JsonRpcException ? $e->getErrorMessage() : $e->getMessage();

			$this->addError('request_id', $message);

			MailQueryModel::model()->addMessage(
				\Yii::app()->params['email']['support'],
				"Ошибка при создании брони в клинике",
				"Заявка: {$this->request_id}, Ошибка:{$message}"
			);

			trigger_error($e->getMessage(), E_USER_WARNING);
		}

		return $result;
	}

	/**
	 * Проверяю слот
	 *
	 * @return bool
	 */
	public function checkSlot()
	{
		if(!$this->slot){
			$this->addError('slot_id', 'Слот недоступен');
			return false;
		}

		//если стол резеврируется, то в клинику лезть не надо
		if ($this->status == self::STATUS_RESERVED) {
			return true;
		}

		$available = false;
		try{
			$slots = $this->slot->doctorClinic->loadSlots(
				date('Y-m-d 00:00', strtotime($this->slot->start_time)),
				date('Y-m-d 23:59', strtotime($this->slot->finish_time))
			);
		} catch (\Exception $e){
			$slots = [];
			$this->addError('slot_id', 'Ошибка при синхронизации слота');
			trigger_error($e->getMessage(), E_USER_WARNING);
		}

		foreach($slots as $slot){
			if($slot->slotId == $this->slot_id){
				$available = true;
				break;
			}
		}

		if(!$available){
			$this->addError('slot_id', 'Слот не найден при проверке');
		}

		return $available;
	}

	/**
	 * Признак разрешен ли букинг в клинике или нет
	 *
	 * @return bool
	 */
	public function clinicHasBooking()
	{
		return (bool)$this->request->clinic->online_booking;
	}

	/**
	 * Бронь
	 *
	 * @param int $slotId
	 *
	 * @return boolean
	 */
	public function book($slotId)
	{
		$result = false;
		$this->status = self::STATUS_NEW;
		$this->slot_id = $slotId;

		if ($this->clinicHasBooking() && $this->validate() && $this->checkSlot()) {
			$result = $this->bookInClinic();
		}

		return $result;
	}

	/**
	 * Резерв
	 *
	 * @param int $slotId
	 *
	 * @return bool
	 */
	public function reserve($slotId)
	{
		$this->slot_id = $slotId;
		$this->status = self::STATUS_RESERVED;

		if($this->validate() && $this->checkSlot()){
			return $this->save();
		}

		return false;
	}

	/**
	 * Поиск по статусам
	 *
	 * @param int|int[] $status
	 *
	 * @return $this
	 */
	public function inStatus($status)
	{
		!is_array($status) && $status = [$status];

		$criteria = new \CDbCriteria();
		$criteria->addInCondition($this->getTableAlias() . '.status', $status);
		$this->getDbCriteria()->mergeWith($criteria);

		return $this;
	}

	/**
	 * Поиск по слоту
	 *
	 * @param int $slotId
	 *
	 * @return $this
	 */
	public function bySlot($slotId)
	{
		$this->getDbCriteria()
			->mergeWith(
				[
					'condition' => 'slot_id = :slot_id',
					'params'    => [':slot_id' => $slotId]
				]
			);

		return $this;
	}

	/**
	 * Можно ли менять статус
	 *
	 * @param int $status
	 *
	 * @return bool
	 */
	public function canChangeStatus($status)
	{
		$canChange =
			$this->_status == $status
			||
			(
				array_key_exists($this->_status, $this->_statusMap)
				&&
				in_array($status, $this->_statusMap[$this->_status])
			);

		return $canChange;
	}

	/**
	 * Исключить из поиска заявки
	 *
	 * @param int[] $requestIds
	 *
	 * @return $this
	 */
	public function requestNotIn($requestIds)
	{
		$cr = new \CDbCriteria();
		$cr->addNotInCondition('request_id', $requestIds);

		$this->getDbCriteria()
			->mergeWith($cr);

		return $this;
	}

	/**
	 * Отмена брони
	 *
	 * @return \StdClass
	 * @throws \CException
	 */
	public function cancelBook()
	{
		if (in_array($this->status, [self::STATUS_NEW, self::STATUS_ACCEPTED, self::STATUS_APPROVED])) {
			//обновляю статус перед отменой
			$this->reloadFromApi();

			if ($res = ClinicApiClient::createClient()->cancelBook($this->external_id)) {
				$this->status = self::STATUS_CANCELED_BY_USER;
				$this->external_id = null;
				$this->save();

				$this->request->addHistory("Отмена брони #{$this->id}");
			}
		} elseif($this->status == self::STATUS_RESERVED){
			//если резерв то можно просто удалить?
			return $this->delete();
		} else {
			throw new \CException('Бронь в текущем статусе отменить нельзя');
		}

		return $res;
	}

	/**
	 * Поиск по внешнему идентификатору
	 *
	 * @param string $externalId
	 *
	 * @return $this
	 */
	public function byExternalId($externalId)
	{
		$this->getDbCriteria()
			->mergeWith(
				[
					'condition' => 'external_id = :ex_id',
					'params' => [':ex_id' => $externalId]
				]
			);

		return $this;
	}

	/**
	 *
	 * @throws \CException
	 * @return bool
	 */
	public function confirm()
	{
		if($this->status != self::STATUS_RESERVED){
			throw new \CException('Бронь должна быть зарезервированна');
		}

		return $this->bookInClinic();
	}

	/**
	 * Обновить статус брони из апи
	 *
	 * @return boolean
	 * @throws \CException
	 */
	public function reloadFromApi()
	{
		$api = ClinicApiClient::createClient();
		$result = $api->getBookStatus($this->external_id);

		if (isset($result->status)) {
			$this->status = BookingModel::model()->remoteToLocalStatus($result->status);

			if (isset($result->start_time) && isset($result->finish_time)) {
				$this->start_time = $result->start_time;
				$this->finish_time = $result->finish_time;
			}

			return $this->save();
		} else {
			throw new \CException('Неожиданный ответ от шлюза');
		}
	}

	/**
	 * @param $clinicId
	 *
	 * @return $this
	 */
	public function byClinic($clinicId)
	{
		$this->getDbCriteria()
			->mergeWith(
				[
					'with' => [
						'request' =>[
							'scopes' => [
								'inClinic' => $clinicId
							]
						]
					],
					'together' => true,
				]
			);

		return $this;
	}

	/**
	 * Созданных в интевале времени
	 *
	 * @param int      $from
	 * @param int|null $to
	 *
	 * @return $this
	 */
	public function createdInInterval($from, $to = null)
	{
		$this->getDbCriteria()->mergeWith(
			array(
				'condition' => "date_created >=:from_time",
				'params'    => array(
					":from_time" => $from,
				)
			)
		);

		if (!is_null($to)) {
			$this->getDbCriteria()->mergeWith(
				array(
					'condition' => "req_created <= :to_time",
					'params'    => array(
						":to_time" => $to,
					)
				)
			);
		}

		return $this;
	}

	/**
	 * Является ли резервной
	 *
	 * @return bool
	 */
	public function isReserve()
	{
		return $this->status == self::STATUS_RESERVED;
	}
}
