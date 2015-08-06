<?php
namespace dfs\modules\payments\models;


/**
 * Class Payment
 *
 * @author  Aleksey Parshukov <parshukovag@gmail.com>
 * @date    16.10.2013
 *
 * @see     https://docdoc.atlassian.net/wiki/pages/viewpage.action?pageId=1310733
 *
 * @package dfs\modules\payments
 *
 * Платёж
 */
class Payment
{
	/**
	 * @var PaymentsAccount
	 */
	private $accountFrom;

	/**
	 * @var PaymentsAccount
	 */
	private $accountTo;

	/**
	 * @var int
	 */
	private $amountFake;

	/**
	 * @var int
	 */
	private $amountReal;

	/**
	 * @var string
	 */
	private $message;

	/**
	 * @var int
	 */
	private $operationType;

	/**
	 * @var PaymentsInvoice
	 */
	private $invoice;

	/**
	 * Создание платежа
	 *
	 * @param PaymentsAccount $accountFrom
	 * @param PaymentsAccount $accountTo
	 * @param int             $operationType
	 * @param string          $message
	 * @param int|null        $amountReal
	 * @param int|null        $amountFake
	 *
	 * @internal param \dfs\modules\payments\models\PaymentsAccount $this ->accountTo
	 */
	public function __construct(
		PaymentsAccount $accountFrom,
		PaymentsAccount $accountTo,
		$operationType,
		$message,
		$amountReal = null,
		$amountFake = null
	) {
		$this->setAccountFrom($accountFrom);
		$this->setAccountTo($accountTo);
		$this->setAmountFake($amountFake);
		$this->setAmountReal($amountReal);
		$this->setMessage($message);
		$this->setOperationType($operationType);
	}

	/**
	 * @param \dfs\modules\payments\models\PaymentsInvoice $invoice
	 */
	public function setInvoice(PaymentsInvoice $invoice)
	{
		$this->invoice = $invoice;
	}

	/**
	 * @return \dfs\modules\payments\models\PaymentsInvoice
	 */
	public function getInvoice()
	{
		return $this->invoice;
	}

	/**
	 * @param \dfs\modules\payments\models\PaymentsAccount $accountFrom
	 */
	public function setAccountFrom(PaymentsAccount $accountFrom)
	{
		$this->accountFrom = $accountFrom;
	}

	/**
	 * @return \dfs\modules\payments\models\PaymentsAccount
	 */
	public function getAccountFrom()
	{
		return $this->accountFrom;
	}

	/**
	 * @param PaymentsAccount $accountTo
	 */
	public function setAccountTo(PaymentsAccount $accountTo)
	{
		$this->accountTo = $accountTo;
	}

	/**
	 * @return \dfs\modules\payments\models\PaymentsAccount
	 */
	public function getAccountTo()
	{
		return $this->accountTo;
	}

	/**
	 * @param int $amountFake
	 */
	public function setAmountFake($amountFake)
	{
		$this->amountFake = $amountFake;
	}

	/**
	 * @return int
	 */
	public function getAmountFake()
	{
		return (int) $this->amountFake;
	}

	/**
	 * @return int
	 */
	public function getAmount()
	{
		return $this->getAmountReal() + $this->getAmountFake();
	}

	/**
	 * @param int $amountReal
	 */
	public function setAmountReal($amountReal)
	{
		$this->amountReal = $amountReal;
	}

	/**
	 * @return int
	 */
	public function getAmountReal()
	{
		return (int) $this->amountReal;
	}

	/**
	 * @param string $message
	 */
	public function setMessage($message)
	{
		$this->message = $message;
	}

	/**
	 * @return string
	 */
	public function getMessage()
	{
		return $this->message;
	}

	/**
	 * @param int $operationType
	 */
	public function setOperationType($operationType)
	{
		$this->operationType = $operationType;
	}

	/**
	 * @return int
	 */
	public function getOperationType()
	{
		return $this->operationType;
	}

	/**
	 * Вычисляет
	 *
	 * Сумму реальных и виртуальных денег для осуществления перевода
	 *
	 * @param int $amount
	 * @param bool|null $isReal
	 */
	public function addPayment($amount, $isReal)
	{
		$amount = floor($amount);
		if ($isReal === true) {
			$this->amountReal += $amount;
		} elseif ($isReal === false) {
			$this->amountFake += $amount;

		} else {
			$amountFake = $this->accountFrom->amount_fake;
			$amountReal = $this->accountFrom->amount_real;

			$self = $this;
			$creditReal = function($amount) use ($amountReal, $self) {
				if ($amountReal >= $amount) {
					$self->addPayment($amount, true);
				} else {
					$self->addPayment($amount - $amountReal, false);
					$self->addPayment($amountReal, true);
				}
			};

			if ($amountFake > 0 || $amountReal <= 0) {
				if ($amountFake >= $amount || $amountReal <= 0) {
					$this->addPayment($amount, false);
				} else {
					if ($amountFake > 0) {
						$amount -= $amountFake;
						$this->addPayment($amountFake, false);
					}

					call_user_func($creditReal, $amount);
				}
			} else {
				call_user_func($creditReal, $amount);
			}
		}
	}

	/**
	 * Снимает сумму с аккаунтов
	 */
	public function creditAccounts()
	{
		$this->getAccountFrom()->amount_real -= $this->getAmountReal();
		$this->getAccountFrom()->amount_fake -= $this->getAmountFake();

		$this->getAccountTo()->amount_real += $this->getAmountReal();
		$this->getAccountTo()->amount_fake += $this->getAmountFake();
	}

	/**
	 * Сохраняет лог операции
	 */
	public function logOperation()
	{
		$po = new PaymentsOperations();
		$id = $po->createUuid();
		$po->id = $id;
		$po->account_from = $this->getAccountFrom()->id;
		$po->account_to = $this->getAccountTo()->id;
		$po->amount_real = $this->amountReal;
		$po->amount_fake = $this->amountFake;
		$po->income = false;
		$po->message = $this->getMessage();
		$po->type = $this->getOperationType();

		if ($this->invoice) {
			$po->invoice_id = $this->getInvoice()->id;
		}

		$po2 = clone($po);
		$po2->account_from = $this->getAccountTo()->id;
		$po2->account_to = $this->getAccountFrom()->id;
		$po2->amount_real = -$this->amountReal;
		$po2->amount_fake = -$this->amountFake;
		$po2->income = true;

		$po->save();
		$po2->save();
	}

	/**
	 * Провести платёж
	 *
	 * @param bool $useTransaction Использовать или нет транзакцию
	 *
	 * @return bool
	 */
	public function credit($useTransaction = true)
	{
		if ($useTransaction) {
		$transaction = $this
			->getAccountTo()
			->getDbConnection()
			->beginTransaction();
		}

		$this->getAccountTo()->block();
		$this->getAccountFrom()->block();
		$this->logOperation();
		$this->creditAccounts();
		$this->getAccountTo()->save();
		$this->getAccountFrom()->save();

		if (isset($transaction)) {
			$transaction->commit();
		}

		return true;
	}
} 