<?php

namespace dfs\docdoc\models;

use dfs\docdoc\objects\Rejection;
use dfs\docdoc\objects\Phone;
use Yii;

/**
 * Class SmsRequestModel
 * @package dfs\docdoc\models
 *
 * @property integer $smsQueryMessageId
 * @property string $request_id
 * @property SmsQueryModel $smsQuery
 *
 * @method SmsRequestModel findByPk
 * @method SmsRequestModel[] findAll
 * @method SmsRequestModel find
 */
class SmsRequestModel extends \CActiveRecord
{

	/**
	 * Статусы СМС
	 */
	const STATUS_SMS_CREATE_REQUEST     = 1;
	const STATUS_SMS_RECORD             = 2;
	const STATUS_SMS_REMINDER           = 3;

	/**
	 * Время до приема, за которое отправляется напоминание - 24 часа
	 */
	const TIME_TO_REMINDER = 86400;
	
	/**
	 * @var RequestModel
	 */
	private $_request;

	/**
	 * Returns the static model of the specified AR class.
	 *
	 * @param string $className
	 *
	 * @return SmsRequestModel the static model class
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
		return 'sms_4_request';
	}

	/**
	 * @return array
	 */
	public function relations() {
		return array(
			'smsQuery' => array(
				self::HAS_ONE, 'dfs\docdoc\models\SmsQueryModel', 'request_id'
			),
		);
	}

	/**
	 * действия перед сохранением модели
	 *
	 * @return bool
	 */
	protected function beforeSave()
	{

		if (empty($this->smsQuery->phoneTo)) {
			$this->smsQuery->phoneTo = $this->_request->add_client_phone ?: $this->_request->client_phone;
		}
		if (!$this->smsQuery->save()) {
			return false;
		}

		$this->request_id = $this->_request->req_id;
		$this->smsQueryMessageId = $this->smsQuery->idMessage;

		return parent::beforeSave();
	}

	/**
	 * После сохранения
	 */
	protected function afterSave()
	{
		$this->_request->addHistory('Отправлено SMS: "' . $this->smsQuery->message. '"');
	}

	/**
	 * обработка события onRequestStatusChange при изменении статуса заявки
	 *
	 * @param \CEvent $event
	 *
	 * @return bool
	 */
	public function requestStatusChanged(\CEvent $event)
	{
		/**
		 * @var RequestModel $request
		 */
		$request = $this->_request = $event->sender;

		//отправка в клинику
		if ($request->isNeedToSendSmsToClinic()) {
			$this->addMessageToClinic();
		}

		//отправка клиентам

		if ($request->isNeedToSendSms() && $request->req_type != RequestModel::TYPE_CALL_TO_DOCTOR) {
			$scenario = [
				RequestModel::SCENARIO_SITE,
				RequestModel::SCENARIO_PARTNER,
				RequestModel::SCENARIO_DIAGNOSTIC_ONLINE,
				RequestModel::SCENARIO_CALL,
				RequestModel::SCENARIO_ASTERISK,
			];
			if ($request->isNew() && in_array($request->scenario, $scenario)) {
				return $this->requestCreatedMessage();
			}
		}
	}

	/**
	 * обработка события onDateAdmissionChange при изменении даты посещения
	 *
	 * @param \CEvent $event
	 *
	 * @return bool
	 */
	public function requestDateAdmissionChanged(\CEvent $event)
	{
		$this->_request = $event->sender;
		if ($this->_request->isNeedToSendSms()) {
			$prevDateAdmission = (int)$this->_request->getOriginalValue('date_admission');
			if (empty($prevDateAdmission)) {
				return $this->requestProcessedMessage();
			} else {
				return $this->requestAppointmentChangedMessage();
			}
		}
	}

	/**
	 * обработка события onRejectReasonChange при изменении причины отказа
	 *
	 * @param \CEvent $event
	 *
	 * @return bool
	 */
	public function requestRejectReasonChanged(\CEvent $event)
	{
		$this->_request = $event->sender;
		if ($this->_request->isNeedToSendSms()) {
			if ($this->_request->reject_reason == Rejection::REASON_CLIENT_NOT_ANSWER) {
				return $this->requestClientNotAvailableMessage($event);
			} elseif ($this->_request->reject_reason == Rejection::REASON_HAVE_CONTACTS
				&& !in_array($this->_request->id_city, array(1, 2))
			) {
				return $this->requestNewCityMessage($event);
			}
		}
	}

	/**
	 * Сообщение клинике о новой заявке
	 *
	 * @return bool
	 */
	private function addMessageToClinic()
	{
		$sms = new self();
		$request = $this->_request;
		// Отправляем СМС клинике только если есть мобильной номер
		if (is_null($request->clinic)
			|| !$request->clinic->getNotifyPhones()
		) {
			return false;
		}

		// Отправляем СМС, если тип заявки - онлайн-запись, статус заявки - обработана и вид - диагностика
		if ($request->req_type != RequestModel::TYPE_ONLINE_RECORD
			|| $request->req_status != RequestModel::STATUS_RECORD
			|| $request->kind != RequestModel::KIND_DIAGNOSTICS
		) {
			return false;
		}

		$sms->_request = $request;
		$diagnostic = $request->diagnostics ? $request->diagnostics->getFullName() : '';

		$message = 'Поступила заявка №' .
			$request->req_id .
			($diagnostic ? ': ' . $diagnostic : '') .
			'. Зайдите в ЛК DocDoc.';

		foreach($request->clinic->getNotifyPhones() as $phone){
			$sms->smsQuery = new SmsQueryModel();
			$sms->smsQuery->ttlKey = "addMessageToClinic";
			$sms->smsQuery->message = $message;
			$sms->smsQuery->typeSMS = SmsQueryModel::TYPE_REQUEST;
			$sms->smsQuery->phoneTo = $phone;
			$sms->save();
		}

		return true;
	}

	/**
	 * Сообщение при создании заявки с сайта
	 *
	 * @return bool
	 */
	private function requestCreatedMessage()
	{
		$request = $this->_request;
		if (
			($request->kind == RequestModel::KIND_DOCTOR && empty($request->req_doctor_id)) ||
			($request->kind != RequestModel::KIND_DOCTOR && empty($request->clinic_id))
		) {
			return false;
		}

		$time = is_numeric($request->req_created) ? $request->req_created : time();
		$currentTime = \CTimestamp::getDate($time);
		$time = '';
		if (!empty($request->date_admission)) {
			$date = \CTimestamp::getDate($request->date_admission);
			$time = ($date['hours'] == 0 && $date['minutes'] == 0)
				? date('d.m.Y', $request->date_admission)
				: date('d.m.Y H:i', $request->date_admission);
			$time = " на " . $time;
		}

		if ($request->kind == RequestModel::KIND_DOCTOR) {

			if ($currentTime['hours'] >= 8 && $currentTime['hours'] < 21) {
				$ttlKey = "requestCreatedMessage_duringWorkingHours";
				$message = "Ваша заявка о записи на прием к врачу принята. " .
					"Наши консультанты свяжутся с вами в течение 15 минут с 8:00 до 21:00 и запишут Вас на прием. Ваш DocDoc.ru";
			} else {
				$ttlKey = "requestCreatedMessage_outOfHours";
				$message = "Ваша заявка на подбор врача принята. Сервис работает с 8:00 до 21:00 Пн-Вс. " .
					"Наш администратор свяжется с Вами до 09:00. Ваш DocDoc.ru";
			}

		} else {

			$ttlKey = 'requestCreatedMessage_duringWorkingHours';
			if (!is_null($request->diagnostics)) {

				if ($request->req_type == RequestModel::TYPE_ONLINE_RECORD) {
					$clinic = $request->clinic;
					$priceOnline = $request->diagnosticClinic ? $request->diagnosticClinic->price_for_online : null;
					$discount = ($priceOnline > 0 && $clinic->discount_online_diag > 0)
						? " Дополнительная скидка на услугу составит {$request->diagnosticClinic->getDiscountForOnline()}%."
						: '';
					$message = "Вы записаны на {$request->diagnostics->getFullName()} в клинику \"{$request->clinic->name}\"{$time}. " .
						"Заявка №{$request->req_id}. Оператор свяжется с Вами для подтверждения даты и времени посещения.{$discount}";
				} else {
					$message ="Вы записаны на услугу {$request->diagnostics->getFullName()} в клинику \"{$request->clinic->name}\"{$time}." .
						" Оператор свяжется с Вами для подтверждения даты и времени посещения.";
				}

			} else {
				$message = "Вы записались в клинику \"{$request->clinic->name}\"{$time}. Оператор свяжется с Вами для подтверждения даты и времени посещения.";
			}

		}

		$this->smsQuery = new SmsQueryModel();
		$this->smsQuery->ttlKey = $ttlKey;
		$this->smsQuery->message = $message;
		$this->smsQuery->typeSMS = SmsQueryModel::TYPE_REQUEST;

		if (!$this->save()) {
			return false;
		}

		/** @todo Нужно выпилить сохранение статуса смс в заявке  */
		$request->updateByPk($request->req_id, array('status_sms' => self::STATUS_SMS_CREATE_REQUEST));

		return true;
	}

	/**
	 * Сообщение о записи на прием
	 *
	 * @return bool
	 */
	private function requestProcessedMessage()
	{
		$request = $this->_request;

		if (is_null($request->doctor) && $request->kind == RequestModel::KIND_DOCTOR) {
			return false;
		}
		if (is_null($request->diagnostics) && $request->kind == RequestModel::KIND_DIAGNOSTICS) {
			return false;
		}

		$message = "";
		$ttlKey = "requestProcessedMessage";

		$date = \CTimestamp::getDate($request->date_admission);
		$time = ($date['hours'] == 0 && $date['minutes'] == 0)
			? date('d.m.Y', $request->date_admission)
			: date('d.m.Y H:i', $request->date_admission);
		if ($request->kind == RequestModel::KIND_DOCTOR) {
			if (!empty($request->doctor)) {
				$ttlKey = "requestProcessedMessage_doctorDateTimeClinic";
				$message .= "Вы записались к врачу. Врач: {$request->doctor->name}, ";
				$message .= ($date['hours'] == 0 && $date['minutes'] == 0)
					? "дата приема: {$time}. "
					: "дата и время приема: {$time}. ";
			} else {
				$message .= "Вы записались к врачу на: {$time}. ";
			}
		} elseif ($request->kind == RequestModel::KIND_DIAGNOSTICS) {

			if (!is_null($request->diagnostics)) {
				$message .= "Вы записались на диагностику: {$request->diagnostics->getFullName()}, дата и время приема: {$time}. ";
			} else {
				$message .= "Вы записались в клинику, дата и время приема: {$time}. ";
			}
		}

		if (!empty($request->clinic) && $request->clinic->getAddress() && !$request->req_departure) {
			$message .= "Адрес клиники: {$request->clinic->getAddress()}. ";
		}

		$message .= "Ваш DocDoc.ru";

		$this->smsQuery = new SmsQueryModel();
		$this->smsQuery->ttlKey = $ttlKey;
		$this->smsQuery->message = $message;
		$this->smsQuery->typeSMS = SmsQueryModel::TYPE_NOTICE;

		if (!$this->save()) {
			return false;
		}

		/** @todo Нужно выпилить сохранение статуса смс в заявке  */
		$request->updateByPk($request->req_id, array('status_sms' => self::STATUS_SMS_RECORD));

		return true;
	}

	/**
	 * Сообщение при изменении времени приема в заявке
	 *
	 * @return bool
	 */
	public function requestAppointmentChangedMessage()
	{
		$request = $this->_request;

		if (is_null($request->doctor) && $request->kind == RequestModel::KIND_DOCTOR) {
			return false;
		}
		if (is_null($request->diagnostics) && $request->kind == RequestModel::KIND_DIAGNOSTICS) {
			return false;
		}
		if ($request->date_admission <= time()) {
			return false;
		}

		$time = \Yii::app()->dateFormatter->format("dd MMMM HH:mm", $request->date_admission);
		$message = '';
		$message .= "Вы изменили время записи на {$time}. ";
		$message .= $request->kind == RequestModel::KIND_DOCTOR
			? "Врач: {$request->doctor->name}. "
			: "Диагностика: {$request->diagnostics->getFullName()}. ";
		if (!empty($request->clinic) && $request->clinic->getAddress() && !$request->req_departure) {
			$message .= "Адрес клиники: {$request->clinic->getAddress()}. ";
		}
		$message .= "Ваш DocDoc.ru";

		$this->smsQuery = new SmsQueryModel();
		$this->smsQuery->ttlKey = "requestAppointmentChangedMessage";
		$this->smsQuery->message = $message;

		return $this->save();
	}

	/**
	 * Сообщение в случае недозвона до пациента
	 *
	 * @return bool
	 */
	public function requestClientNotAvailableMessage()
	{
		$request = $this->_request;

		$this->smsQuery = new SmsQueryModel();
		$phone = (new Phone($request->city->site_phone))->formatPrefix()->getNumber();

		/**
		 * @var string $str
		 */
		$str = ($request->kind == RequestModel::KIND_DOCTOR) ? 'на подбор врача' : 'о записи в клинику';
		$this->smsQuery->ttlKey = "requestClientNotAvailableMessage";
		$this->smsQuery->message =
			"Вы оставляли заявку {$str} на портале DocDoc.ru. К сожалению, мы не смогли с Вами связаться. " .
			"Перезвоните нам по тел. {$phone} для записи к специалисту. Ваш DocDoc.ru";
		$this->smsQuery->typeSMS = SmsQueryModel::TYPE_CLIENT_NOT_AVAILABLE;

		return $this->save();
	}

	/**
	 * Сообщения о записи в клинику для новых городов
	 *
	 * @return bool
	 */
	public function requestNewCityMessage()
	{
		$request = $this->_request;

		if (is_null($request->clinic)) {
			return false;
		}

		$this->smsQuery = new SmsQueryModel();
		$phones = explode("; ", $request->clinic->phone_appointment);
		$phone = (new Phone($phones[0]))->formatPrefix()->getNumber();
		$this->smsQuery->ttlKey = "requestNewCityMessage";
		$this->smsQuery->message = "Благодарим Вас за использование сервиса DocDoc.ru. " .
			"На данный момент услуга в Вашем городе предоставляется в тестовом режиме. " .
			"Вы можете обратиться напрямую в клинику по телефону {$phone}. Ваш DocDoc.ru";
		$this->smsQuery->typeSMS = SmsQueryModel::TYPE_CLIENT_NOT_AVAILABLE;

		return $this->save();
	}

	/**
	 * Напоминание о приеме
	 *
	 * @param RequestModel $request
	 *
	 * @return bool
	 */
	public function requestReminder(RequestModel $request)
	{
		$this->_request = $request;

		if (is_null($request->doctor) && $request->kind == RequestModel::KIND_DOCTOR) {
			return false;
		}

		if (is_null($request->diagnostics) && $request->kind == RequestModel::KIND_DIAGNOSTICS) {
			return false;
		}
		
		if (!$request->isNeedToSendSms()) {
			return false;
		}

		$time = \Yii::app()->dateFormatter->format("dd MMMM HH:mm", $request->date_admission);
		$message = '';
		if ($request->kind == RequestModel::KIND_DOCTOR) {
			$message .= "Напоминаем, что Вы записаны на приём к врачу на {$time}. ";
			$message .= "Врач: {$request->doctor->name}. ";
		} elseif ($request->kind == RequestModel::KIND_DIAGNOSTICS) {
			$message .= "Напоминаем, что Вы записаны на диагностику на {$time}. ";
			$message .= "Диагностика: {$request->diagnostics->getFullName()}. ";
		}
		if (!empty($request->clinic) && $request->clinic->getAddress()) {
			$message .= "Адрес клиники: {$request->clinic->getAddress()}. ";
		}

		if ($request->kind == RequestModel::KIND_DIAGNOSTICS) {
			$phones = explode("; ", $request->clinic->phone_appointment);
			$phone = (new Phone($phones[0]))->formatPrefix()->getNumber();
		} else {
			$phone = (new Phone($request->city->site_phone))->formatPrefix()->getNumber();
		}

		$message .= "При необходимости перенести приём звоните {$phone}. Ваш DocDoc.ru";

		$this->smsQuery = new SmsQueryModel();
		$this->smsQuery->ttlKey = "requestReminder";
		$this->smsQuery->message = $message;
		$this->smsQuery->typeSMS = SmsQueryModel::TYPE_REMIND;
		if (!$this->save()) {
			return false;
		}

		/** @todo Нужно выпилить сохранение статуса смс в заявке  */
		$request->updateByPk($request->req_id, array('status_sms' => self::STATUS_SMS_REMINDER));

		return true;
	}

	/**
	 * Сообщение о недозвоне при сборе отзывов
	 *
	 * @param RequestModel $request
	 *
	 * @return bool
	 */
	public function opinionClientNotAvailableMessage(RequestModel $request)
	{
		$this->_request = $request;
		
		if ($request->req_status != RequestModel::STATUS_RECORD) {
			return false;
		}

		$date = \Yii::app()->dateFormatter->format("dd.MM.yyyy", $request->date_admission);
		$this->smsQuery = new SmsQueryModel();
		$this->smsQuery->ttlKey = "opinionClientNotAvailableMessage";
		$this->smsQuery->message = "Благодарим за использование сервиса DocDoc.ru для записи к врачу на {$date}. " .
			"Перезвоните, пожалуйста, нам по телефону +{$request->city->opinion_phone} для оценки качества сервиса. Ваш DocDoc.ru";
		$this->smsQuery->typeSMS = SmsQueryModel::TYPE_NOTICE;

		return $this->save();
	}

} 
