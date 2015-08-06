<?php

namespace dfs\docdoc\models;

/**
 * This is the model class for table "contract_dict".
 *
 * The followings are the available columns in table 'contract_dict':
 *
 * @property integer $contract_id
 * @property string $title
 * @property string $description
 * @property string $isClinic
 * @property string $isDiagnostic
 * @property integer $kind
 *
 * The followings are the available model relations:
 *
 *
 * @method ContractModel findByPk
 */
class ContractModel extends \CActiveRecord
{

	//Оплата за дошедших
	const TYPE_DOCTOR_VISIT      = 1;
	//Оплата за записанных
	const TYPE_DOCTOR_RECORD     = 2;
	//Оплата за звонки
	const TYPE_DIAGNOSTIC_CALL   = 3;
	//Оплата за запись на диагностику
	const TYPE_DIAGNOSTIC_RECORD = 4;
	//Плата за дошедших на диагностику
	const TYPE_DIAGNOSTIC_VISIT  = 5;
	//Оплата за звонки на врачей по записи
	//@todo сделать этот тариф. Сейчас он не описан здесь @link https://docdoc.atlassian.net/wiki/pages/viewpage.action?pageId=15138901
	const TYPE_DOCTOR_CALL       = 6;
	//онлайн-запись на диагностику
	const TYPE_DIAGNOSTIC_ONLINE = 7;

	//время разгоовора (сек), которое мы считаем, что звонок на диагностику состоялся
	const DIAGNOSTIC_CALL_LENGTH = 30;

	// Тариф по договору на врачей
	const KIND_DOCTOR       = 0;
	// Тариф по договору на диагностику
	const KIND_DIAGNOSTICS  = 1;

	// Группы контрактов
	const PAY_GROUP_RECORD = 1;
	const PAY_GROUP_VISIT = 2;
	const PAY_GROUP_CALL = 3;


	/**
	 * Returns the static model of the specified AR class.
	 *
	 * @param string $className
	 *
	 * @return ContractModel the static model class
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
		return 'contract_dict';
	}

	/**
	 * @return string the associated primary key
	 */
	public function primaryKey()
	{
		return 'contract_id';
	}

	static public function getContractList()
	{
		return [
			self::TYPE_DOCTOR_VISIT,
			self::TYPE_DOCTOR_RECORD,
			self::TYPE_DIAGNOSTIC_CALL,
			self::TYPE_DIAGNOSTIC_RECORD,
			self::TYPE_DIAGNOSTIC_VISIT,
			self::TYPE_DOCTOR_CALL,
			self::TYPE_DIAGNOSTIC_ONLINE
		];
	}


	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
		);
	}

	/**
	 * Возвращает месяц (yyyy-mm), в который попадет заявка в биллинг
	 * @link https://docdoc.atlassian.net/wiki/pages/viewpage.action?pageId=15138901
	 *
	 * @param RequestModel $request
	 *
	 * @return string|null
	 */
	public function getMonthForBilling(RequestModel $request)
	{
		//заявки по дошедшим попадают в биллинг в месяц, в котором будет визит в клинику
		if ($this->isPayForVisit()) {
			return $request->isCame() ? date("Y-m",  $request->date_admission) : null;
		}

		//звонки на диагностику билятся по дате создания, в биллинг попадают только заявки с продолжительностью больше 30 секунд
		if ($this->isPayForCall()) {
			return $request->getMaxDurationRecord() > self::DIAGNOSTIC_CALL_LENGTH ? date("Y-m", $request->req_created) : null;
		}

		//для записанных
		if ($this->isPayForRecord()) {
			return $request->isRecord() ? date("Y-m", strtotime($request->date_record)) : null;
		}

		return null;
	}

	/**
	 * Возвращает массив с датой начала и окончания периода биллинга для этой заявки
	 *
	 * return array('from'=> '2014-01-01', 'to' => '2014-01-31 23:59:59')
	 *
	 * @param RequestModel $request
	 * @return array|null
	 */
	public function getBillingPeriod(RequestModel $request)
	{
		//определяем месяц биллинга
		$month = $this->getMonthForBilling($request);
		if ($month === null) {
			return null;
		}

		$lastDay = strtotime($month . "  next month - 1 second");

		//количество заявок в этом месяце
		return ['from' => $month . "-01", 'to' => date('Y-m-d H:i:s', $lastDay)];
	}


	/**
	 * Возвращает имя даты, по которой заявка попадает в биллинг по этому контракту
	 *
	 * @link https://docdoc.atlassian.net/wiki/pages/viewpage.action?pageId=15138901
	 *
	 * @return string
	 */
	public function getBillingDate()
	{
		//заявки по дошедшим попадают в биллинг в месяц, в котором будет визит в клинику
		if ($this->isPayForVisit()) {
			return 'date_admission';
		}

		//звонки на диагностику билятся по дате создания, в биллинг попадают только заявки с продолжительностью больше 30 минут
		if ($this->isPayForCall()) {
			return 'req_created';
		}

		//для записанных
		if ($this->isPayForRecord()) {
			return 'date_record';
		}

		return null;
	}

	/**
	 * Добавляет в критерий условие для отбора заявок только этого договора
	 *
	 * @return \CDbCriteria
	 */
	public function getContractCriteria()
	{
		$criteria = new \CDbCriteria();
		switch ($this->contract_id) {
			case self::TYPE_DOCTOR_CALL:
			case self::TYPE_DOCTOR_VISIT:
			case self::TYPE_DOCTOR_RECORD:
				$criteria->condition = " kind = " . RequestModel::KIND_DOCTOR . " AND req_status != " . RequestModel::STATUS_REMOVED;
				break;
			case self::TYPE_DIAGNOSTIC_CALL:
			case self::TYPE_DIAGNOSTIC_VISIT:
			case self::TYPE_DIAGNOSTIC_RECORD:
				$criteria->condition = " kind = " . RequestModel::KIND_DIAGNOSTICS . " AND req_status != " . RequestModel::STATUS_REMOVED;
				break;
			//дианостика онлайн - это виртуальнй тариф, для него заявки отдельно не выбираются
			case self::TYPE_DIAGNOSTIC_ONLINE:
				$criteria->condition = " 1 = 0 ";
				break;
		}
		return $criteria;
	}

	/**
	 * оплата за визит true/false
	 *
	 * @return bool
	 */
	public function isPayForVisit()
	{
		return ($this->contract_id == self::TYPE_DOCTOR_VISIT || $this->contract_id == self::TYPE_DIAGNOSTIC_VISIT);
	}

	/**
	 * оплата за звонки true/false
	 *
	 * @return bool
	 */
	public function isPayForCall()
	{
		return  ($this->contract_id == self::TYPE_DIAGNOSTIC_CALL);
	}

	/**
	 * оплата за запись true/false
	 *
	 * @return bool
	 */
	public function isPayForRecord()
	{
		return (
			$this->contract_id == self::TYPE_DOCTOR_RECORD
			||
			$this->contract_id == self::TYPE_DIAGNOSTIC_RECORD
			||
			$this->contract_id == self::TYPE_DIAGNOSTIC_ONLINE
		);

	}

	/**
	 * Вид контракта
	 *
	 * @return int
	 */
	public function getPayGroup()
	{
		$group = null;
		switch ($this->contract_id) {
			case self::TYPE_DOCTOR_CALL:
			case self::TYPE_DIAGNOSTIC_CALL:
				$group = self::PAY_GROUP_CALL;
				break;
			case self::TYPE_DOCTOR_VISIT:
			case self::TYPE_DIAGNOSTIC_VISIT:
				$group = self::PAY_GROUP_VISIT;
				break;
			case self::TYPE_DOCTOR_RECORD:
			case self::TYPE_DIAGNOSTIC_RECORD:
			case self::TYPE_DIAGNOSTIC_ONLINE:
				$group = self::PAY_GROUP_RECORD;
				break;
		}
		return $group;
	}

	/**
	 * Исключает из выборки виртуальный контракт онлайн-записи на дианостику
	 *
	 * @return ClinicContractModel $this
	 */
	public function realContracts()
	{
		$this->getDbCriteria()
			->mergeWith(
				[
					'condition' => $this->getTableAlias() . '.contract_id <> :onlineContract' ,
					'params' => [
						':onlineContract' => self::TYPE_DIAGNOSTIC_ONLINE,
					]
				]
			);
		return $this;
	}

	/**
	 * Поиск тарифов по договорам на врачей
	 *
	 * @return ClinicContractModel $this
	 */
	public function onlyOnDoctor()
	{
		$this->getDbCriteria()
			->mergeWith(
				[
					'condition' => 'kind = :kind',
					'params' => [
						':kind' => self::KIND_DOCTOR,
					]
				]
			);

		return $this;
	}

	/**
	 * Поиск тарифов по договорам на диагностику
	 *
	 * @return ClinicContractModel $this
	 */
	public function onlyOnDiagnostics()
	{
		$this->getDbCriteria()
			->mergeWith(
				[
					'condition' => 'kind = :kind',
					'params' => [
						':kind' => self::KIND_DIAGNOSTICS,
					]
				]
			);

		return $this;
	}

}
