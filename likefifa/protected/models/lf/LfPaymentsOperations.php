<?php

namespace likefifa\models\lf;

use dfs\modules\payments\models\PaymentsOperations;
use LfAppointment;

/**
 * Перегруженная модель @see PaymentsOperations
 *
 * Class LfPaymentsOperations
 *
 * @package likefifa\models\lf
 */
class LfPaymentsOperations extends PaymentsOperations
{
	/**
	 * @var LfAppointment
	 */
	private $_appointment;

	/**
	 * @param string $className
	 *
	 * @return \dfs\modules\payments\models\PaymentsOperations
	 */
	public static function model($className = __CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * Определяет, был ли платеж входящий
	 *
	 * @return bool
	 */
	public function isIncoming()
	{
		$value = $this->amount_real != 0 ? $this->amount_real : $this->amount_fake;
		if ($value >= 0) {
			return false;
		}
		return true;
	}

	/**
	 * Возвращает заявку, по которой был проведен платеж
	 *
	 * @return LfAppointment
	 */
	public function getAppointment()
	{
		if ($this->_appointment != null) {
			return $this->_appointment;
		}

		if ($this->type != PaymentsOperations::TYPE_COMMISSION || !strstr($this->message, 'Завершен заказ номер №')) {
			return null;
		}

		preg_match('|№(\d+)|iu', $this->message, $matches);
		if (!isset($matches[1])) {
			return null;
		}

		$this->_appointment = LfAppointment::model()->findByPk($matches[1]);
		return $this->_appointment;
	}

	/**
	 * Возвращает размер платежа
	 *
	 * @return int
	 */
	public function getPriceFormatted() {
		return ($this->amount_real != 0 ? $this->amount_real : $this->amount_fake) * -1;
	}
}