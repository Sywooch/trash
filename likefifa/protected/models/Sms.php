<?php

use dfs\modules\payments\models\PaymentsInvoice;
use dfs\modules\sms\models\Sms as SmsModel;

/**
 * Класс хранит методы для отправки SMS сообщений мастерам
 */
class Sms extends SmsModel
{

	/**
	 * Новая заявка. Мастер.
	 * Если заявка поступила после 21:00, оложить до 9:00
	 * @param int $appointment_id идентификатор заявки
	 *
	 * @return bool результат операции
	 */
	public function makeNewSmsForMasterByAppointmentId($appointment_id)
	{
		if ($appointment_model = LfAppointment::model()->findByPk($appointment_id)) {
			if ($appointment_model->master) {
				$destination_address = $this->getFormatPhone($appointment_model->master->phone_cell);
			} elseif ($appointment_model->salon) {
				$destination_address = $this->getFormatPhone($appointment_model->salon->phone);
			} else {
				$destination_address = false;
			}
			$message =
				"Вам поступила заявка №" .
				$appointment_model->id .
				". " .
				$appointment_model->getServiceName() .
				", " .
				$appointment_model->name .
				", " .
				$appointment_model->phone .
				". Свяжитесь с клиентом в течение 20 мин.";
			$send_time = $this->getDayTime();
			if ($destination_address && $message && $send_time) {
				return $this->saveSms($destination_address, $message, $send_time);
			}
		}
		return false;
	}

	/**
	 * Новая заявка из БО. Мастер.
	 * Если заявка поступила после 21:00, оложить до 9:00
	 * @param int $appointment_id идентификатор заявки
	 *
	 * @return bool результат операции
	 */
	public function makeNewSmsForMasterByAdminAndAppointmentId($appointment_id)
	{
		if ($appointment_model = LfAppointment::model()->findByPk($appointment_id)) {
			if ($appointment_model->master) {
				$destination_address = $this->getFormatPhone($appointment_model->master->phone_cell);
			} elseif ($appointment_model->salon) {
				$destination_address = $this->getFormatPhone($appointment_model->salon->phone);
			} else {
				$destination_address = false;
			}
			$message =
				date("d.m.y", $appointment_model->date) . ' на ' . date("H:i", $appointment_model->date) .
				" к Вам записана " .
				$appointment_model->name .
				", " . $appointment_model->phone .
				". Услуга " .
				$appointment_model->getServiceName() .
				", заявка №" .
				$appointment_model->id;
			$send_time = $this->getDayTime();
			if ($destination_address && $message && $send_time) {
				return $this->saveSms($destination_address, $message, $send_time);
			}
		}
		return false;
	}

	/**
	 * Новая заявка. Клиент.
	 *
	 * @param int     $appointment_id идентификатор заявки
	 * @param boolean $fromHeader     заявки пришла из шапки
	 *
	 * @return bool результат операции
	 */
	public function makeNewSmsForClientByAppointmentId($appointment_id, $fromHeader = false)
	{
		if ($appointment_model = LfAppointment::model()->findByPk($appointment_id)) {
			$who = "Мастер";
			if ($appointment_model->salon) {
				$who = "Салон";
			}

			$destination_address = $this->getFormatPhone($appointment_model->phone);
			$h = date("H", time());
			if (($h > 8) && ($h < 22)) {
				$message =
					'Ваша заявка на услугу "' .
					$appointment_model->getServiceName() .
					'" принята. ';
					if($fromHeader == false) {
						$message .= $who . ' свяжется с Вами в течение 2-х часов.';
					} else {
						$message .= 'Оператор свяжется с Вами в течение 15-ти минут для уточнения информации.';
					}
			} else {
				$message =
					'Ваша заявка на услугу "' .
					$appointment_model->getServiceName() .
					'" принята. ';
				if($fromHeader == false) {
					$message .= 'Время работы нашего сервиса с 9 до 21. Мастер свяжется с Вами завтра в рабочее время.';
				} else {
					$message .= 'Оператор свяжется с Вами завтра для уточнения иноформации в рабочее время.';
				}
			}
			$send_time = time();
			if ($destination_address && $message) {
				return $this->saveSms($destination_address, $message, $send_time);
			}
		}
		return false;
	}

	/**
	 * Мастер принял заявку. Клиент.
	 * Если после 21:00 Оложить до 9:00
	 * @param int $appointment_id идентификатор заявки
	 *
	 * @return bool результат операции
	 */
	public function makeAcceptedSmsForClientByAppointmentId($appointment_id)
	{
		if ($appointment_model = LfAppointment::model()->findByPk($appointment_id)) {
			$destination_address = $this->getFormatPhone($appointment_model->phone);
			$message =
				'Вы записаны на услугу "' .
				$appointment_model->getServiceName() .
				'" к мастеру ' .
				$appointment_model->master->getFullName() .
				' на ' .
				date("d.m.y H:i", $appointment_model->date) .
				'.';
			$send_time = $this->getDayTime();
			if ($destination_address && $message && $send_time) {
				return $this->saveSms($destination_address, $message, $send_time);
			}
		}
		return false;
	}

	/**
	 * Мастер принял заявку. Мастер.
	 * @param int $appointment_id идентификатор заявки
	 *
	 * @return bool результат операции
	 */
	public function makeAcceptedSmsForMasterByAppointmentId($appointment_id)
	{
		if ($appointment_model = LfAppointment::model()->findByPk($appointment_id)) {
			$destination_address = null;
			if($appointment_model->master != null) {
				$destination_address = $this->getFormatPhone($appointment_model->master->phone_cell);
			} elseif($appointment_model->salon != null) {
				$destination_address = $this->getFormatPhone($appointment_model->salon->phone);
			}
			$message =
				'Вы приняли заявку №"' .
				$appointment_model->id . '. ' .
				'Клиент ' . $appointment_model->name . ', ' . $appointment_model->phone . ', '.
				'дата приема ' .
				date("d.m.y H:i", $appointment_model->date) .
				'.';
			$send_time = time();
			if ($destination_address && $message && $send_time) {
				return $this->saveSms($destination_address, $message, $send_time);
			}
		}
		return false;
	}

	/**
	 * За 3 часа до приёма. Мастер.
	 *
	 * Если сеанс назначен до 10:00 - сообщение отправляем в 21:00 за день до приёма.
	 * Если сеанс назначен после 10:00 - сообщение отправляем не раньше 9:00 утра.
	 *
	 * @param int $appointment_id идентификатор заявки
	 *
	 * @return bool результат операции
	 */
	public function makeDelayedAcceptedSmsForMasterByAppointmentId($appointment_id)
	{
		// За 3 часа до приема
		if ($appointment_model = LfAppointment::model()->findByPk($appointment_id)) {
			$destination_address = $this->getFormatPhone($appointment_model->master->phone_cell);
			$time = date("H:i", $appointment_model->date);
			$h = date("H", $appointment_model->date);
			$m = (int)(date("i", $appointment_model->date));
			if ($h < 10) {
				$send_time = $appointment_model->date - ($h + 3) * 3600 - $m * 60;
				$day = "Завтра";
			} else {
				$send_time = $this->getDayTime($appointment_model->date - 3 * 3600);
				$day = "Сегодня";
			}
			$message =
				$day .
				' на ' .
				$time .
				' к Вам на "' .
				$appointment_model->getServiceName() . 
				'" записана ' .
				$appointment_model->name .
				', заявка №' .
				$appointment_model->id .
				'.';
			if ($destination_address && $message && $send_time) {
				return $this->saveSms($destination_address, $message, $send_time);
			}
		}
		return false;
	}

	/**
	 * За 3 часа до приёма. Клиент.
	 *
	 * Если сеанс назначен до 10:00 - сообщение отправляем в 21:00 за день до приёма.
	 * Если сеанс назначен после 10:00 - сообщение отправляем не раньше 9:00 утра.
	 *
	 * @param int $appointment_id идентификатор заявки
	 *
	 * @return bool результат операции
	 */
	public function makeDelayedAcceptedSmsForClientByAppointmentId($appointment_id)
	{
		// За 3 часа до приема
		if ($appointment_model = LfAppointment::model()->findByPk($appointment_id)) {
			$destination_address = $this->getFormatPhone($appointment_model->phone);
			$time = date("H:i", $appointment_model->date);
			$h = date("H", $appointment_model->date);
			$m = (int)(date("i", $appointment_model->date));
			if ($h < 10) {
				$send_time = $appointment_model->date - ($h + 3) * 3600 - $m * 60;
			} else {
				$send_time = $this->getDayTime($appointment_model->date - 3 * 3600);
			}
			$message =
				'Вы записаны в ' .
				$time .
				' на услугу "' .
				$appointment_model->getServiceName() .
				'". Мастер ' .
				$appointment_model->master->getFullName() .
				'.';
			if ($destination_address && $message && $send_time) {
				return $this->saveSms($destination_address, $message, $send_time);
			}
		}
		return false;
	}

	/**
	 * Через 4 часа после приёма. Клиент.
	 * Если после 21:00 Оложить до 9:00
	 * @param int $appointment_id идентификатор заявки
	 *
	 * @return bool результат операции
	 */
	public function makeCompletedSmsForClientByAppointmentId($appointment_id)
	{
		if ($appointment_model = LfAppointment::model()->findByPk($appointment_id)) {
			$destination_address = $this->getFormatPhone($appointment_model->phone);
			$message =
				'Спасибо, что воспользовались нашим сервисом. Оставьте отзыв о работе мастера http://likefifa.ru/masters/' .
				$appointment_model->master->rewrite_name .
				'/';
			$send_time = $this->getDayTime(time() + 4 * 3600);
			if ($destination_address && $message && $send_time) {
				return $this->saveSms($destination_address, $message, $send_time);
			}
		}
		return false;
	}

	/**
	 * Поступление ДС. Мастер
	 * @param int $master_id  идентификатор мастера
	 * @param int $add_balance
	 *
	 * @internal param int $add_balans пополнение баланса
	 *
	 * @return bool результат операции
	 */
	public function makeAddBalanceSmsForMasterByMasterId($master_id, $add_balance = 0)
	{
		if ($master_model = LfMaster::model()->findByPk($master_id)) {
			$destination_address = $this->getFormatPhone($master_model->phone_cell);
			$message = 'На Ваш счет послупила сумма ' . $add_balance . ' руб.';
			$send_time = time();
			if ($destination_address && $message && $send_time) {
				return $this->saveSms($destination_address, $message, $send_time);
			}
		}
		return false;
	}

	/**
	 * Списание ДС. Мастер
	 * @param int $appointment_id идентификатор заявки
	 * @param int $minus_balance
	 *
	 * @internal param int $add_balans списание баланса
	 *
	 * @return bool результат операции
	 */
	public function makeMinusBalanceSmsForMasterByAppointmentId($appointment_id, $minus_balance = 0)
	{
		if ($appointment_model = LfAppointment::model()->findByPk($appointment_id)) {
			$destination_address = $this->getFormatPhone($appointment_model->master->phone_cell);
			$message =
				'С вашего счета была списана сумма ' . $minus_balance . ' руб. Заявка №' . $appointment_model->id . '.';
			$send_time = $this->getDayTime();
			if ($destination_address && $message && $send_time) {
				return $this->saveSms($destination_address, $message, $send_time);
			}
		}
		return false;
	}

	/**
	 * По расписанию, когда мастеру пришла первая заявка и он ее "принял" указал дату/время приема.
	 * Отправляется 1 раз спустя час после принятия заявки.
	 *
	 * @param LfAppointment $appointment модель заявки
	 *
	 * @return bool
	 */
	public function makeAcceptedFirstSms(LfAppointment $appointment)
	{
		$destinationAddress = $this->getFormatPhone($appointment->master->phone_cell);
		$message = "На вашем счету недостаточно денежных средств для завершения заявки.
			Не забудьте вовремя пополнить Ваш баланс.";
		$sendTime = $this->getDayTime(time() + 3600);
		if ($destinationAddress && $message && $sendTime) {
			return $this->saveSms($destinationAddress, $message, $sendTime);
		}

		return false;
	}

	/**
	 * По рассписанию, когда баланс становится меньше {@see Yii::app()->params["littelBalance"]} руб.,
	 * но больше 0 рублей. Отсылается 1 раз в день 2 дня, далее через 3 дня. После не отсылается.
	 *
	 * @param LfMaster $master мастер
	 * @param string   $err    Текст ошибки
	 *
	 * @return bool результат операции
	 */
	public function makeLittleBalanceSmsForMaster(LfMaster $master, &$err)
	{
		if ($master->is_published) {
			$err = "Мастер заблокирован";
			return false;
		}
		if ($master->is_popup != LfMaster::IS_POPUP_RECEIVED) {
			$err = "Мастер не принял договор оферты";
			return false;
		}

		$appointment = LfAppointment::model()->getFirstByMasterId($master->id);

		if (!$appointment) {
			$err = "У мастера нет заказов";
			return false;
		}

		if (!$appointment->isOlderThanOneday()) {
			$err = "С момента первой заявки прошло меньше 24 часов";
			return false;
		}

		$destination_address = $this->getFormatPhone($master->phone_cell);
		if (!$destination_address) {
			$err = "Номер не известен";
			return false;
		}

		$message =
			'Не забудьте вовремя пополнить Ваш баланс, чтобы принимать заявки.
			Текущий остаток на счете ' .
			$master->getBalance() .
			' руб. ';

		if ($master->little_balance_count) {
			if ($master->little_balance_count == 1) {
				$send_time = $master->little_balance_time + 24 * 3600;
			} elseif ($master->little_balance_count == 2) {
				$send_time = $master->little_balance_time + 4 * 24 * 3600;
			} else {
				$err = "Слишком много СМС";
				return false;
			}
		} else {
			$send_time = $this->getDayTime();
			$master->little_balance_time = $send_time;
		}

		$master->little_balance_count++;
		if (!$master->save()) {
			return false;
		}

		if ($message && $send_time) {
			return $this->saveSms($destination_address, $message, $send_time);
		}

		$err = "Недостаточно параметров";
		return false;
	}

	/**
	 * По рассписанию. От нуля и меньше. Отсылается 1 раз в день 2 дня, далее через 3 дня. После не отсылается.
	 *
	 * @param LfMaster $master мастер
	 * @param string   $err    Текст ошибки
	 *
	 * @return bool результат операции
	 */
	public function makeNullBalanceSmsForMaster(LfMaster $master, &$err)
	{
		if ($master->is_popup == LfMaster::IS_POPUP_RECEIVED) {
			$destination_address = $this->getFormatPhone($master->phone_cell);
			if (!$destination_address) {
				$err = "Номер не известен";
				return false;
			}

			$message =
				'Ваша анкета на сайте временно заблокирована. Для того чтобы принимать заявки Вам необходимо пополнить баланс.';

			if ($master->null_balance_count) {
				if ($master->null_balance_count == 1) {
					$send_time = $master->null_balance_time + 24 * 3600;
				} elseif ($master->null_balance_count == 2) {
					$send_time = $master->null_balance_time + 4 * 24 * 3600;
				} else {
					$err = "Слишком много СМС";
					return false;
				}
			} else {
				$send_time = $this->getDayTime();
				$master->null_balance_time = $send_time;
			}

			$master->null_balance_count++;
			if (!$master->save()) {
				return false;
			}

			if ($message && $send_time) {
				return $this->saveSms($destination_address, $message, $send_time);
			}
			$err = "Недостаточно параметров";
			return false;

		}

		if ($master->is_popup != LfMaster::IS_POPUP_RECEIVED) {
			$err = "Мастер не принял договор оферты";
			return false;
		}

		return false;
	}

	/**
	 * Отправляет смс для просточенных заявок
	 *
	 * @param LfAppointment $model модель заявки
	 *
	 * @return bool
	 */
	public function makeSmsForOverdue(LfAppointment $model)
	{
		if ($model->master) {
			$destinationAddress = $this->getFormatPhone($model->master->phone_cell);
			$message = "Вам необходимо завершить заявку №{$model->id} в вашем личном кабинете.";
			$sendTime = $this->getDayTime();
			if ($destinationAddress && $message && $sendTime) {
				return $this->saveSms($destinationAddress, $message, $sendTime);
			}
		}

		return false;
	}

	/**
	 * Сообщение о завершени заявки
	 *
	 * @param LfAppointment $model модель заявки
	 *
	 * @return bool
	 */
	public function makeSmsForOverdueLast(LfAppointment $model)
	{
		if ($model->master) {
			$destinationAddress = $this->getFormatPhone($model->master->phone_cell);
			$message = "Заявка №{$model->id} была автоматически завершенна в вашем личном кабинете.";
			$sendTime = $this->getDayTime();
			if ($destinationAddress && $message && $sendTime) {
				return $this->saveSms($destinationAddress, $message, $sendTime);
			}
		}

		return false;
	}

	/**
	 * Событие на проведение инвойса
	 *
	 * @param PaymentsInvoice $invoice
	 *
	 * @return bool
	 */
	public static function onPaymentInvoiceClose(PaymentsInvoice $invoice)
	{
		$master_model = LfMaster::model()->findByAttributes(
			array(
				'account_id' => $invoice->account_to,
			)
		);
		if ($master_model) {
			$sms = new self();

			$master_model->little_balance_time = 0;
			$master_model->little_balance_count = 0;
			$master_model->null_balance_time = 0;
			$master_model->null_balance_count = 0;
			if (!$master_model->save()) {
				return false;
			}

			$master_model->checkAndUnblock();

			return $sms->makeAddBalanceSmsForMasterByMasterId($master_model->id, $invoice->getAmount());
		}
		return false;
	}
}