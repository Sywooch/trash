<?php

namespace dfs\docdoc\models;

/**
 * This is the model class for table "clinic_billing".
 *
 * The followings are the available columns in table 'clinic_billing':
 *
 * @property integer  $id
 * @property string   $clinic_id
 * @property string   $clinic_contract_id
 * @property string   $billing_date
 * @property integer  $status
 * @property integer  $start_sum
 * @property integer  $start_requests
 * @property integer  $agreed_sum
 * @property integer  $agreed_requests
 * @property integer  $today_sum
 * @property integer  $today_requests
 * @property float    $recieved_sum
 * @property string   $changedata_date
 * @property integer  $manager_id
 *
 * The followings are the available model relations:
 *
 * @property ClinicModel $clinic
 * @property ClinicContractModel $tariff
 * @property ClinicPaymentModel[] $payments
 * @property UserModel $user
 *
 * @method ClinicBillingModel findByPk
 */
class ClinicBillingModel extends \CActiveRecord
{

	const SCENARIO_UPDATE_SUM = "SCENARIO_UPDATE_SUM";

	const STATUS_OPEN            = 1; //период открыт
	const STATUS_AGREEMENT       = 2; //согласонвание
	const STATUS_WAITING_PAYMENT = 3; //ожидание оплаты
	const STATUS_CLOSED          = 4; //деньги получены
	const STATUS_PESSIMISATION   = 5; //пессимизирована
	const STATUS_DEBTOR          = 6; //должник


	/**
	 * Returns the static model of the specified AR class.
	 *
	 * @param string $className
	 *
	 * @return ClinicBillingModel the static model class
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
		return 'clinic_billing';
	}

	/**
	 * Зависимости
	 *
	 * @return array
	 */
	public function relations()
	{
		return [
			'clinic'   => [
				self::BELONGS_TO,
				ClinicModel::class,
				'clinic_id'
			],
			'tariff' => [
				self::BELONGS_TO,
				ClinicContractModel::class,
				'clinic_contract_id'
			],
			'payments' => [
				self::HAS_MANY,
				ClinicPaymentModel::class,
				'clinic_billing_id'
			],
			'user' => [
				self::BELONGS_TO,
				UserModel::class,
				'manager_id'
			],
		];
	}

	public static function getStatuses()
	{
		return [
			ClinicBillingModel::STATUS_OPEN => "Открыт",
			ClinicBillingModel::STATUS_WAITING_PAYMENT => "Ожидание оплаты",
			ClinicBillingModel::STATUS_AGREEMENT => "Согласование",
			ClinicBillingModel::STATUS_CLOSED => "Период закрыт",
			ClinicBillingModel::STATUS_PESSIMISATION => 'Пессимизация',
			ClinicBillingModel::STATUS_DEBTOR => 'Должник',
		];
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
				"recieved_sum, start_sum, agreed_sum", 'numerical'
			],
			[
				"recieved_sum", "safe", "on" => self::SCENARIO_UPDATE_SUM
			]
		];
	}

	/**
	 * Выборка по дате отчетного периода
	 *
	 * @param $date
	 *
	 * @return $this
	 */
	public function byPeriod($date) {
		$dateFrom = (new \DateTime($date))->modify('first day of this month' )->format('Y-m-d');

		$this->getDbCriteria()->mergeWith(
			[
				'condition' => $this->getTableAlias() . ".billing_date = :billingDate",
				'params' => [
					':billingDate' => $dateFrom
				]
			]
		);

		return $this;
	}

	/**
	 * Выборка по клинике
	 * @param int $id
	 *
	 * @return $this
	 */
	public function inClinic($id) {
		$this->getDbCriteria()->mergeWith(
			[
				'condition' => $this->getTableAlias() . ".clinic_id = :clinic_id",
				'params' => [
					':clinic_id' => $id
				]
			]
		);

		return $this;
	}

	/**
	 * Выборка по менеджеру
	 * @param int $id
	 *
	 * @return $this
	 */
	public function byManager($id) {
		$this->getDbCriteria()->mergeWith(
			[
				'condition' => $this->getTableAlias() . ".manager_id = :manager_id",
				'params' => [
					':manager_id' => $id
				]
			]
		);

		return $this;
	}

	/**
	 * Выборка по статусу
	 * @param int $status
	 *
	 * @return $this
	 */
	public function byStatus($status) {
		$this->getDbCriteria()->mergeWith(
			[
				'condition' => $this->getTableAlias() . ".status = :billingStatus",
				'params' => [
					':billingStatus' => $status
				]
			]
		);
		return $this;
	}

	/**
	 * Выборка по типу контракта
	 *
	 * @param int $contractId
	 *
	 * @return $this
	 */
	public function byContract($contractId) {
		$criteria = new \CDbCriteria();
		$criteria->with = [
			'tariff' => [
				'joinType' => "INNER JOIN",
				'scopes' => [
					'byContract' => [$contractId]
				]
			]
		];

		$this->getDbCriteria()->mergeWith($criteria);
		return $this;
	}

	/**
	 * Выборка по контракту клиники
	 *
	 * @param int $id
	 *
	 * @return $this
	 */
	public function byClinicContract($id) {
		$this->getDbCriteria()->mergeWith(
			[
				'condition' => $this->getTableAlias() . ".clinic_contract_id = :clinicContractId",
				'params' => [
					':clinicContractId' => $id
				]
			]
		);

		return $this;
	}

	/**
	 * установка начального количества заявок
	 *
	 * @param int $num
	 */
	public function setStartNum($num)
	{
		if (empty($this->status) || $this->status == self::STATUS_OPEN) {
			$this->start_requests = $num;
		}
	}

	/**
	 * уставнока конечного количества заявок
	 *
	 * @param int $cost
	 */
	public function setStartCost($cost)
	{
		if (empty($this->status) || $this->status == self::STATUS_OPEN) {
			$this->start_sum = $cost;
		}
	}

	/**
	 * перед сохранением
	 */
	protected function beforeSave()
	{
		//закрываем открытые периоды
		if ($this->status == self::STATUS_OPEN) {
			//если настал первый день следующего месяца, закрываем предыдущий месяц
			$nextMonth = new \DateTime($this->billing_date);

			if (date('Y-m-d') >= $nextMonth->modify("first day +1 month")->format('Y-m-d')) {
				$this->status = self::STATUS_AGREEMENT;
			}
		}

		return true;
	}

	/**
	 * Пересчет биллинга при изменении поступлений
	 *
	 * @return bool
	 */
	public function onPaymentChange()
	{
		$_recieved_sum = $this->recieved_sum;

		$this->recieved_sum = 0;
		$this->getRelated('payments', true);

		foreach ($this->payments as $p) {
			$this->recieved_sum += $p->sum;
		}

		$this->recieved_sum = round($this->recieved_sum, 2);

		if ($this->recieved_sum > 0) {
			if ($this->recieved_sum >= $this->agreed_sum) {
				$this->status = self::STATUS_CLOSED;
			} else {
				$this->status = self::STATUS_WAITING_PAYMENT;
			}
		} elseif ($_recieved_sum != $this->recieved_sum) { //если сумма была, но удалили какой-то платеж
			$this->status = self::STATUS_WAITING_PAYMENT;
		}

		return $this->save();
	}

	/**
	 * Требуется ли согласование по заявке
	 *
	 * @return bool
	 */
	public function isNeedAgree()
	{
		if ($this->today_sum == 0 || $this->agreed_sum == $this->today_sum) {
			return false;
		}

		return true;
	}

	/**
	 * согласовать количество заявок в биллинге
	 */
	public function agree()
	{
		if (!$this->isNeedAgree()) {
			return false;
		}

		$startStatus = $this->status;
		$startSum = $this->agreed_sum;

		$this->agreed_requests = $this->today_requests;
		$this->agreed_sum = $this->today_sum;
		$this->status = self::STATUS_WAITING_PAYMENT;

		if ($this->save()) {
			$period = date('m.Y', strtotime($this->billing_date));

			//повторное нажатие
			$tpl = $startStatus == self::STATUS_WAITING_PAYMENT ?  'clinic_billing_agree_repeat' : 'clinic_billing_agree';

			$to = [\Yii::app()->params['email']['bookkeeping']];
			if ($this->user) {
				$to[] = $this->user->user_email;
			}

			MailQueryModel::model()->createMail($tpl, $to, [
				'billing' => $this,
				'period' => $period,
				'startSum' => $startSum,
			]);
			return true;
		}
		return false;
	}

	/**
	 * название статуса
	 *
	 * @return string
	 */
	public function getStatusTitle()
	{
		$statuses = self::getStatuses();
		return isset($statuses[$this->status]) ? $statuses[$this->status] : null;
	}
}