<?php

namespace dfs\modules\payments\models;

use CActiveRecord;
use dfs\modules\payments\models\PaymentsOperations;
use dfs\modules\payments\models\PaymentsAccount;

/**
 * Class PaymentsAccount
 *
 * @author  Aleksey Parshukov <parshukovag@gmail.com>
 * @date    19.09.2013
 *
 * @see     https://docdoc.atlassian.net/wiki/pages/viewpage.action?pageId=1310733
 *
 * @package dfs\modules\payments
 *
 * @property int                   id
 * @property int                   amount_real
 * @property int                   amount_fake
 * @property string                comment
 *
 * @property PaymentsOperations[]  paymentsOperations
 *
 * @method PaymentsAccount findByPk
 *
 * Аккаунт. Может содержать баланс.
 */
class PaymentsAccount extends CActiveRecord
{
	/**
	 * Минимальный идентификатор пользовательского аккаунта
	 *
	 * @var int
	 */
	const MIN_USER_ID = 10000;

	/**
	 * Заданный системный идентификатор
	 *
	 * @var int
	 */
	const SYSTEM_ID = 1;

	/**
	 * Заданный бонусный идентификатор
	 *
	 * @var int
	 */
	const BONUS_ID = 2;

	/**
	 * @param string $className
	 *
	 * @return PaymentsAccount
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
		return 'payments_account';
	}

	/**
	 * Правила валидации для атрибутов модели
	 *
	 * @return string[]
	 */
	public function relations()
	{
		return array(
			'paymentsOperations' => array(
				self::HAS_MANY,
				'\dfs\modules\payments\models\PaymentsOperations',
				'account_to',
				"order" => "paymentsOperations.create_date DESC"
			),
		);
	}

	/**
	 * Получить сумму на счету пользователя
	 *
	 * @var int
	 */
	public function getAmount()
	{
		return $this->amount;
	}

	/**
	 * Подсчитывает остаток суммы
	 *
	 * @return bool
	 */
	public function beforeSave()
	{
		if (!$this->isNewRecord) {
			$this->amount = $this->amount_fake + $this->amount_real;
			$this->saveAttributes(array('amount'));
		}

		return parent::beforeSave();
	}

	/**
	 * Создаёт в базе новый инвойс
	 *
	 * @param int               $amount    Сумма к зачеслению
	 * @param PaymentsProcessor $processor Процессор
	 * @param boolean           $isReal    Реальная сумма или нет
	 * @param string            $message   Комментарий
	 * @param string            $eMail
	 *
	 * @throws \RuntimeException
	 * @return PaymentsInvoice
	 */
	public function createInvoice($amount, PaymentsProcessor $processor, $isReal, $message, $eMail)
	{
		$ret = new PaymentsInvoice();
		$ret->{$isReal ? 'amount_real' : 'amount_fake'} = (int)$amount;
		$ret->account_to = $this->id;
		$ret->processor_id = $processor->id;
		$ret->message = $message;
		$ret->email = $eMail;
		if (!$ret->save()) {
			throw new \RuntimeException("Invoice not created");
		}

		return $ret;
	}

	/**
	 * Блокировка счёта для обновления
	 *
	 * @return PaymentsAccount|null
	 */
	public function block()
	{
		$table = $this->getTableSchema();

		return static::model()->findBySql(
			"
			SELECT *
			FROM {$table->rawName} WHERE {$table->primaryKey}={$this->getPrimaryKey()}
			FOR UPDATE
		"
		);
	}

	/**
	 * Перевести деньги со счёта на счёт
	 *
	 * @param PaymentsAccount $accountTo
	 * @param int             $amount
	 * @param bool|null       $isReal
	 * @param int             $operationType
	 * @param string          $message
	 *
	 * @return bool
	 */
	public function creditAmount(PaymentsAccount $accountTo, $amount, $isReal, $operationType, $message)
	{
		$payment = new Payment($this, $accountTo, $operationType, $message);
		$payment->addPayment($amount, $isReal);
		return $payment->credit();
	}
}