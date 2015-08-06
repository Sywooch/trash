<?php
namespace dfs\docdoc\models;

use dfs\common\config\Environment;
use dfs\docdoc\objects\Phone;
use Yii;
use CLogger;

/**
 * This is the model class for table "SmsQuery".
 *
 * The followings are the available columns in table 'SmsQuery':
 *
 * @property integer $idMessage
 * @property string  $phoneTo
 * @property string  $typeSMS
 * @property string  $message
 * @property string  $crDate
 * @property string  $sendDate
 * @property integer $priority
 * @property string  $status
 * @property string  $systemId
 * @property integer $gateId
 * @property integer $ttl допустимая задержка в минутах в случае сбоя
 *
 * @method SmsQueryModel findByPk
 * @method SmsQueryModel[] findAll
 * @method SmsQueryModel find
 *
 * Relations:
 * @property \dfs\docdoc\models\SmsRequestModel $smsRequest
 */
class SmsQueryModel extends \CActiveRecord
{

	// Приоритет отправки сообщения по умолчанию
	const PRIORITY_DEFAULT = 5;

	// Идентификатор шлюза по умолчанию
	const GATEWAY_DEFAULT = 1;

	const STATUS_NEW = 'new';
	const STATUS_IN_PROCESS = 'in_process';
	const STATUS_SENDED = 'sended';
	const STATUS_DELETED = 'deleted';
	const STATUS_CANCELED = 'canceled';
	const STATUS_ERROR = 'error';
	const STATUS_ERROR_GATE = 'error_gate';
	const STATUS_ERROR_CONNECT = 'error_connect';
	const STATUS_DELIVERED = 'delivered';

	//Системное сообщение
	const TYPE_SYSTEM_MSG = 1;
	//Заявка с сайта (ФО)
	const TYPE_REQUEST = 2;
	//Уведомление о записи к врачу (БО)
	const TYPE_NOTICE = 3;
	//Напоминание о приёме (БО)
	const TYPE_REMIND = 4;
	//Сообщение об ошибке
	const TYPE_ERROR_MSG = 5;
	//Уведомление об изменении приема (БО)
	const TYPE_CHANGE_APPOINTMENT = 6;
	//Уведомление о недозвоне до пациента (БО)
	const TYPE_CLIENT_NOT_AVAILABLE = 7;
	//Валидация телефона
	const TYPE_VALIDATE_PHONE = 8;
	//не задан
	const TYPE_UNKNOWN = 0;

	/**
	 * Хранит в себе значения TTL в минутах для СМС-сообщений
	 *
	 * Массив в виде названиеМетода => значениеTTL
	 * Если в методе несколько сообщений с разными TTL, тогда названиеМетода_комментарий => значениеTTL
	 * Методы преимущественно в модели SmsRequestModel
	 *
	 * @link https://docdoc.atlassian.net/wiki/pages/viewpage.action?pageId=15138872
	 *
	 * @var array
	 */
	private $_ttlForSmsMethods = [
		"requestCreatedMessage_duringWorkingHours"            => 20,
		"requestCreatedMessage_outOfHours"                    => 60,
		"requestProcessedMessage"                             => 300,
		"requestProcessedMessage_doctorDateTimeClinic"        => 120,
		"requestReminder"                                     => 300,
		"opinionClientNotAvailableMessage"                    => 300,
		"requestNewCityMessage"                               => 300,
		"requestClientNotAvailableMessage"                    => 300,
		"requestAppointmentChangedMessage"                    => 300,
		"addMessageToClinic"                                  => 300,
	];

	/**
	 * Название ключа для $this->_ttlForSmsMethods
	 *
	 * @var string
	 */
	public $ttlKey = "";

	/**
	 * Returns the static model of the specified AR class.
	 *
	 * @param string $className
	 *
	 * @return static model class
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
		return 'SMSQuery';
	}

	/**
	 * первичный ключ
	 *
	 * @return string the associated database table name
	 */
	public function primaryKey()
	{
		return 'idMessage';
	}

	/**
	 * правила валидации
	 * @return array
	 */
	public function rules()
	{
		return [
			[
				'phoneTo',
				'dfs\docdoc\validators\PhoneValidator'
			],
			[
				'typeSMS, priority, gateId',
				'numerical',
				'integerOnly' => true,
			],
			[
				'status',
				'in',
				'range' => self::getStatuses(),
			],
			[
				'message, systemId',
				'length',
				'max' => 500,
			],
			[
				'message, phoneTo, gateId',
				'safe',
				'on' => 'insert',
			],
		];
	}

	/**
	 * Зависимости
	 *
	 * @return array
	 */
	public function relations()
	{
		return [
			'smsRequest' => [
				self::HAS_ONE,
				'dfs\docdoc\models\SmsRequestModel',
				'smsQueryMessageId'
			],
		];
	}

	/**
	 * Получение массива статусов
	 *
	 * @return array
	 */
	public static function getStatuses()
	{
		return [
			self::STATUS_NEW,
			self::STATUS_IN_PROCESS,
			self::STATUS_SENDED,
			self::STATUS_DELETED,
			self::STATUS_CANCELED,
			self::STATUS_ERROR,
			self::STATUS_ERROR_GATE,
			self::STATUS_ERROR_CONNECT,
			self::STATUS_DELIVERED,
		];
	}

	/**
	 * Действия перед валидацией
	 *
	 * @return bool
	 */
	public function beforeValidate()
	{
		if ($this->getIsNewRecord()) {
			$this->crDate = date("Y-m-d H:i:s");
			$this->priority = self::PRIORITY_DEFAULT;
			$this->status = self::STATUS_NEW;
			$this->gateId = self::GATEWAY_DEFAULT;
		}

		return parent::beforeValidate();
	}

	/**
	 * Послать смс через сохранение в очереди
	 *
	 * @param string number
	 * @param string $text
	 * @param int $type
	 * @param bool $online
	 *
	 * @return bool
	 */
	public static function sendSmsToNumber($number, $text, $type = self::TYPE_UNKNOWN, $online = false)
	{
		$sms = new self();
		$sms->phoneTo = $number;
		$sms->message = $text;
		$sms->typeSMS = $type;

		if($sms->save()){
			if($online){
				return $sms->sendSms();
			} else {
				return true;
			}
		} else {
			return false;
		}
	}

	/**
	 * Отправка смс
	 *
	 * @throws \CException
	 * @return bool
	 */
	public function sendSms()
	{
		$smsRequest = $this->smsRequest;

		if(Environment::isTest()){
			return true;
		}

		if ($this->ttl) {
			$time = time() - strtotime($this->crDate);
			if ($time > $this->ttl * 60) {
				$this->status = self::STATUS_CANCELED;
				return $this->save();
			}
		}

		$res = \Yii::app()->sms->send_sms($this->phoneTo, $this->message);
		\Yii::app()->newRelic->customMetric('Custom/Sms/Send', 1);

		if ($res && isset($res[0])) {
			$this->status = self::STATUS_SENDED;
			$this->sendDate = date("Y-m-d H:i:s");
			$this->systemId = $res[0];

			if($this->save()){
				if ($smsRequest) {
					Yii::log(
						"Сообщение #{$this->idMessage} по заявке #{$smsRequest->request_id} на номер {$this->phoneTo} успешно отправлено",
						CLogger::LEVEL_INFO,
						"sms.requests"
					);
				}

				return true;
			} else {
				//не должно сюда попасть
				throw new \CException("Смс {$this->idMessage} отправлено, но в базе статус не поменялся");
			}
		} else {
			if ($smsRequest) {
				Yii::log(
					"Не удалось отправить сообщение #{$this->idMessage} по заявке #{$smsRequest->request_id} на номер {$this->phoneTo}",
					CLogger::LEVEL_ERROR,
					"sms.requests"
				);
			}

			return false;
		}
	}

	/**
	 * Проверить статус
	 *
	 * @return array|bool
	 */
	public function checkStatus()
	{
		$status = false;

		if($this->systemId && is_numeric($this->systemId)){
			$res = \Yii::app()->sms->get_status($this->systemId, $this->phoneTo);

			if(is_array($res) && isset($res[0])){
				$status = $res[0];
			}
		}

		return $status;
	}

	/**
	 * Сохранение статуса
	 *
	 * @param $status
	 *
	 * @return bool
	 */
	public function saveStatus($status)
	{
		$this->status = $status;
		return $this->save();
	}

	/**
	 * Получение баланса
	 *
	 * @return bool
	 */
	public static function getBalance()
	{
		return \Yii::app()->sms->get_balance();
	}

	/**
	 * Выполняется перед сохранением модели
	 *
	 * @return bool
	 */
	protected function beforeSave()
	{
		if ($this->ttlKey && array_key_exists($this->ttlKey, $this->_ttlForSmsMethods)) {
			$this->ttl = $this->_ttlForSmsMethods[$this->ttlKey];
		}

		return parent::beforeSave();
	}
}
