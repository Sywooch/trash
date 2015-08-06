<?php
namespace dfs\modules\payments\models;

use CActiveRecord;
use CDbCriteria;
use CActiveDataProvider;
use CDateTimeParser;
use dfs\modules\payments\models\PaymentsAccount;
use dfs\modules\payments\models\PaymentsInvoice;

/**
 * Class PaymentsOpetarions
 *
 * Операции перевода денег
 *
 * @author  Aleksey Parshukov <parshukovag@gmail.com>
 * @date    30.09.2013
 *
 * @see     https://docdoc.atlassian.net/wiki/pages/viewpage.action?pageId=1310733
 *
 * @package dfs\modules\payments\models
 *
 *
 * @property string          $id           Идентификатор перевода
 * @property string          $create_date  Датаперевода
 * @property int             $amount_real  Реальные деньги
 * @property int             $amount_fake  Фековые
 * @property int             $account_from От кого
 * @property int             $account_to   Кому
 * @property int             $type         Тип услуги
 * @property string          $message      Примечание
 * @property int             $income       Входящая или исходящая операция
 * @property int             $invoice_id   Если есть
 *
 * @property PaymentsAccount accountFrom
 * @property PaymentsAccount accountTo
 * @property PaymentsInvoice invoice
 *
 * @method PaymentsOperations[] findAll
 */
class PaymentsOperations extends CActiveRecord
{
	/**
	 * Пополнение счёта
	 *
	 * @var int
	 */
	const TYPE_TOP_UP = 1;

	/**
	 * Комиссия системы
	 *
	 * @var int
	 */
	const TYPE_COMMISSION = 2;

	/**
	 * Бонусное пополнение
	 *
	 * @var int
	 */
	const TYPE_BONUS = 3;

	/**
	 * Пополнение или списание оператором в БО
	 *
	 * @var int
	 */
	const TYPE_BO = 4;

	/**
	 * Типы
	 *
	 * @var string[]
	 */
	public static $types = array(
		self::TYPE_TOP_UP     => "Пополнение счёта",
		self::TYPE_COMMISSION => "Комиссия системы",
		self::TYPE_BONUS      => "Бонусное пополнение",
		self::TYPE_BO         => "Оператор в БО",
	);

	/**
	 * @param string $className
	 *
	 * @return PaymentsOperations
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
		return 'payments_operation';
	}

	/**
	 * @return array[] relational rules.
	 */
	public function relations()
	{
		return array(
			'accountFrom' => array(self::BELONGS_TO, '\dfs\modules\payments\models\PaymentsAccount', 'account_from'),
			'accountTo'   => array(self::BELONGS_TO, '\dfs\modules\payments\models\PaymentsAccount', 'account_to'),
			'invoice'     => array(self::BELONGS_TO, '\dfs\modules\payments\models\PaymentsInvoice', 'invoice_id'),
		);
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
	 * Получает отформатированную дату создания
	 *
	 * @return string
	 */
	public function getFormatDate()
	{
		if ($this->create_date) {
			$timestamp = CDateTimeParser::parse($this->create_date, "yyyy-MM-dd HH:mm:ss");
			if ($timestamp) {
				return date("d.m.Y H:i:s", $timestamp);
			}
		}

		return null;
	}

	/**
	 * Получает тип строкой
	 *
	 * @return string
	 */
	public function getType()
	{
		if (array_key_exists($this->type, self::$types)) {
			return self::$types[$this->type];
		}

		return null;
	}

	/**
	 * Получает статус
	 *
	 * @return string
	 */
	public function getInvoiceStatus()
	{
		if ($this->invoice) {
			return $this->invoice->getStatus();
		}

		return null;
	}

	/**
	 * Получает e-mail платильщика
	 *
	 * @return string
	 */
	public function getInvoiceEmail()
	{
		if ($this->invoice) {
			return $this->invoice->email;
		}

		return null;
	}

	/**
	 * Поиск в списке операций
	 *
	 * @return CActiveDataProvider
	 */
	public function search()
	{
		$criteria = new CDbCriteria;
		$criteria->condition = "t.account_to > :account_to";
		$criteria->params[":account_to"] = PaymentsAccount::MIN_USER_ID;

		return new CActiveDataProvider($this, array(
				'criteria'   => $criteria,
				'pagination' => array(
					'pageSize' => 50,
				),
			));
	}

	/**
	 * Названия меток для атрибутов
	 *
	 * @return string[]
	 */
	public function attributeLabels()
	{
		return array(
			'id'             => 'ID',
			'create_date'    => 'Дата перевода',
			'amount_real'    => 'Сумма Реал',
			'amount_fake'    => 'Сумма Фейк',
			'type'           => 'Тип',
			'message'        => 'Сообщение',
			'invoice_id'     => 'Идентификатор платежа',
			'invoice_status' => 'Статус платежа',
			'invoice_email'  => 'E-mail',
		);
	}

	/**
	 * Получает идентификатор аккаунта пользователя
	 *
	 * @return int
	 */
	public function getUserAccountId()
	{
		if ($this->accountTo && $this->accountTo->id > PaymentsAccount::MIN_USER_ID) {
			return $this->accountTo->id;
		}
		if ($this->accountFrom && $this->accountFrom->id > PaymentsAccount::MIN_USER_ID) {
			return $this->accountFrom->id;
		}

		return 0;
	}
}