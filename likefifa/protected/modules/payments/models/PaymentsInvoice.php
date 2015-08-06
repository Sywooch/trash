<?php
namespace dfs\modules\payments\models;

use CActiveRecord;
use RuntimeException;

/**
 * Class PaymentsInvoice
 *
 * Запрос на пополнение баланса. В последствее может быть использованно как запрос на вывод денежных средств.
 *
 * @author  Aleksey Parshukov <parshukovag@gmail.com>
 * @date    24.09.2013
 *
 * @see     https://docdoc.atlassian.net/wiki/pages/viewpage.action?pageId=1310733
 *
 * @property string                                         $id
 * @property string                                         $create_date
 * @property int                                            $amount_real
 * @property int                                            $amount_fake
 * @property int                                            $processor_id
 * @property int                                            $account_to
 * @property string                                         $message
 * @property int                                            $status
 * @property string                                         $status_date
 * @property string                                         $email
 *
 * @property \dfs\modules\payments\models\PaymentsAccount   $account
 * @property \dfs\modules\payments\models\PaymentsProcessor $processor
 *
 * @method \dfs\modules\payments\models\PaymentsInvoice findByPk
 *
 * @package dfs\modules\payments
 */
class PaymentsInvoice extends CActiveRecord
{
	/**
	 * Новый запрос
	 *
	 * @var int
	 */
	const STATUS_NEW = 10;

	/**
	 * Проведённый запрос
	 *
	 * @var int
	 */
	const STATUS_CLOSE = 20;

	/**
	 * Отменённый запрос
	 *
	 * @var int
	 */
	const STATUS_CANCEL = 30;

	/**
	 * Статусы
	 *
	 * @var string[]
	 */
	public static $statusList = array(
		self::STATUS_NEW    => "Новый запрос",
		self::STATUS_CLOSE  => "Проведённый запрос",
		self::STATUS_CANCEL => "Отменённый запрос",
	);

	/**
	 * Получает статус
	 *
	 * @return string
	 */
	public function getStatus()
	{
		if (array_key_exists($this->status, self::$statusList)) {
			return self::$statusList[$this->status];
		}

		return null;
	}

	/**
	 * @param string $className
	 *
	 * @return PaymentsInvoice
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
		return 'payments_invoice';
	}

	/**
	 * @return array[] relational rules.
	 */
	public function relations()
	{
		return array(
			'account' => array(self::BELONGS_TO, '\dfs\modules\payments\models\PaymentsAccount', 'account_to'),
			'processor' => array(self::BELONGS_TO, '\dfs\modules\payments\models\PaymentsProcessor', 'processor_id'),
		);
	}

	/**
	 * Фильтры
	 *
	 */
	protected function beforeSave()
	{
		if ($this->getIsNewRecord()) {
			$this->id = $this->createUuid();
		}

		if (is_null($this->status)) {
			$this->setStatus(PaymentsInvoice::STATUS_NEW);
		}

		return parent::beforeSave();
	}

	/**
	 * Получает новый UUID()
	 *
	 * @return string
	 */
	public function createUuid()
	{
		/**
		 * @var \CDbConnection $connection
		 */
		$connection = \Yii::app()->db;
		$ret = $connection->createCommand('SELECT UUID()')->queryRow(false);
		return $ret[0];
	}

	/**
	 * Изменить статус инвойса
	 *
	 * @param int $newStatus Новый статус
	 *
	 * @return $this
	 */
	public function setStatus($newStatus)
	{
		$this->status_date = date('Y-m-d H:i:s');
		$this->status = $newStatus;
		return $this;
	}

	/**
	 * Получить дату создания заявки
	 *
	 * @return \DateTime|null
	 */
	public function getCreateDate()
	{
		return is_null($this->create_date)
			? null
			: new \DateTime($this->create_date);
	}

	/**
	 * Получить дату изменения статуса
	 *
	 * @return \DateTime|null
	 */
	public function getStatusDate()
	{
		return is_null($this->status_date)
			? null
			: new \DateTime($this->status_date);
	}

	/**
	 * Сумма счёта
	 *
	 * @return int
	 */
	public function getAmount()
	{
		return $this->amount_fake + $this->amount_real;
	}

	/**
	 * Реальный или фейковый
	 *
	 * @return bool
	 */
	public function isReal()
	{
		return $this->amount_real > 0;
	}

	/**
	 * Закрываем инвойс
	 *
	 * @throws RuntimeException
	 * @return bool
	 */
	public function close()
	{
		if (!$this->canClose()) {
			throw new RuntimeException('Already closed');
		}

		$payment = new Payment(
			$this->processor->account,
			$this->account,
			PaymentsOperations::TYPE_TOP_UP,
			"Close invoice #{$this->id}",
			$this->amount_real,
			$this->amount_fake
		);
		$payment->setInvoice($this);


		$transaction = $this->getDbConnection()->beginTransaction();
		$payment->credit(false);
		$this->setStatus(self::STATUS_CLOSE);
		$ret = $this->save();
		$transaction->commit();

		if (!empty(\Yii::app()->modules['payments']['onInvoiceClose'])) {
			foreach (\Yii::app()->modules['payments']['onInvoiceClose'] as $onInvoiceClose) {
				if (is_callable($onInvoiceClose)) {
					call_user_func($onInvoiceClose, $this);
				} else {
					trigger_error(
						E_USER_ERROR,
						"invalid callback in modules.payments.onInvoiceClose" . var_export($onInvoiceClose, 1)
					);
				}
			}
		}

		return $ret;
	}

	/**
	 * Можно делать проводку или нет
	 *
	 * @return bool
	 */
	public function canClose()
	{
		return (int)$this->status === self::STATUS_NEW;
	}

	/**
	 * Получаем ссылку для оплаты
	 *
	 * @return string ссылка для оплаты
	 */
	public function getProcessorUrl()
	{
		$processor = $this->processor;
		return $processor->getProcessor()->buildMerchantUrl($this);
	}
} 