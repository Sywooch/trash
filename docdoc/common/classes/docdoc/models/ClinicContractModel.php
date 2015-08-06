<?php

namespace dfs\docdoc\models;

/**
 * This is the model class for table "clinic_contract".
 *
 * The followings are the available columns in table 'clinic_contract':
 *
 * @property integer $id
 * @property string $clinic_id
 * @property string $contract_id
 *
 * The followings are the available model relations:
 *
 * @property ClinicModel $clinic
 * @property ContractModel $contract
 * @property ClinicContractCostModel[] $costRules
 * @property ClinicContractCostModel[] $allCostRules
 * @property ContractGroupModel[] contractGroups
 * @property ClinicRequestLimitModel[] requestLimits
 *
 * @method ClinicContractModel findByPk
 * @method ClinicContractModel[] findAll
 */
class ClinicContractModel extends \CActiveRecord
{

	/**
	 * Returns the static model of the specified AR class.
	 *
	 * @param string $className
	 *
	 * @return ClinicContractModel the static model class
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
		return 'clinic_contract';
	}

	/**
	 * Зависимости
	 *
	 * @return array
	 */
	public function relations()
	{
		return array(
			'clinic'   => array(
				self::BELONGS_TO,
				ClinicModel::class,
				'clinic_id'
			),
			'contract' => array(
				self::BELONGS_TO,
				ContractModel::class,
				'contract_id'
			),
			'costRules' => array(
				self::HAS_MANY,
				ClinicContractCostModel::class,
				'clinic_contract_id',
				'condition' => 'costRules.is_active = 1',
				'order' => 'from_num ASC',
			),
			'allCostRules' => array(
				self::HAS_MANY,
				ClinicContractCostModel::class,
				'clinic_contract_id',
				'order' => 'cost ASC'
			),
			'contractGroups' => [
				self::MANY_MANY,
				ContractGroupModel::class,
				'clinic_contract_cost(clinic_contract_id, group_uid)',
				'group' => 'contractGroups.id',
			],
			'requestLimits' => [
				self::HAS_MANY,
				ClinicRequestLimitModel::class,
				'clinic_contract_id',
			],
		);
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
	 * Получение тарифной группы для услуги
	 *
	 * @param $serviceId
	 *
	 * @return ContractGroupModel|null
	 */
	public function getServiceGroup($serviceId)
	{
		if (empty($serviceId)) {
			$serviceId = ContractGroupServiceModel::ANY_SERVICE;
		}

		$defaultGroup = null;

		foreach ($this->costRules as $rule) {
			$services = $rule->contractGroup->getServicesInGroup();

			foreach ($services as $id) {
				if ($id == $serviceId) {
					return $rule->contractGroup;
				}

				if (isset($services[ContractGroupServiceModel::ANY_SERVICE])) {
					$defaultGroup = $rule->contractGroup;
				}
			}
		}

		return $defaultGroup;
	}

	/**
	 * Возвращает массив филиалов, которые работают по данному тарифу
	 *
	 *
	 * @return int[]
	 */
	public function getClinicsForContract()
	{
		$criteria = new \CDbCriteria();
		$criteria->condition = "tariffs.clinic_id IS NULL OR tariffs.clinic_id = :mainClinic";
		$criteria->params = [":mainClinic" => $this->clinic_id];
		$criteria->group = "t.id";
		$clinics = ClinicModel::model()
			->withBranches($this->clinic_id)
			->with([
					'tariffs' => [
						'joinType' => "LEFT JOIN",
					]
				])
			->findAll($criteria);
		$r = [];
		foreach ($clinics as $c) {
			$r[] = $c->id;
		}
		return $r;
	}

	/**
	 * Поиск по контракту
	 *
	 * @param integer|int[] $contract
	 *
	 * @return $this
	 */
	public function byContract($contract)
	{
		$criteria = new \CDbCriteria();
		if (is_array($contract)) {
			$criteria->addInCondition($this->getTableAlias() . '.contract_id', $contract);
		} else {
			$criteria->condition = $this->getTableAlias() . '.contract_id = :contract';
			$criteria->params = [':contract' => $contract];
		}

		$this->getDbCriteria()->mergeWith($criteria);

		return $this;
	}

	/**
	 * Расчет стоимости заявки
	 *
	 * @param RequestModel $request
	 *
	 * @return null|float
	 */
	public function getRequestCost(RequestModel $request)
	{
		if (!$this->isTariffForRequest($request)) {
			return null;
		}

		$serviceId = ($request->kind == RequestModel::KIND_DOCTOR) ? $request->req_sector_id : $request->diagnostics_id;
		$steps = $this->getCostSteps($serviceId);
		if (count($steps) === 0) {
			return null;
		}

		//определяем месяц биллинга
		$billingPeriod = $this->contract->getBillingPeriod($request);
		if ($billingPeriod === null) {
			return null;
		}

		//количество заявок в этом месяце
		$reqCount = $this->getRequestNumInBilling($billingPeriod['from'], $billingPeriod['to'], $serviceId);

		$rule = $this->getCurrentRule($serviceId, $reqCount);

		return ($rule !== null) ? $rule->cost : null;

	}

	/**
	 * определяем в какой-интервал попадают заявки
	 *
	 * @param int $serviceId
	 * @param int $reqCount
	 *
	 * @return ClinicContractCostModel
	 */
	public function getCurrentRule($serviceId, $reqCount)
	{
		$r = null;
		$steps = $this->getCostSteps($serviceId);
		if (count($steps)) {
			//определяем в какой-интервал попадают заявки
			foreach ($steps as $rule) {
				if ($reqCount >= $rule->from_num) {
					$r = $rule;
				}
			}
		}

		return $r;
	}

	/**
	 * определяем какой будет следующий шаг
	 *
	 * @param int $serviceId
	 * @param int $reqCount
	 *
	 * @return ClinicContractCostModel
	 */
	public function getNextRule($serviceId, $reqCount)
	{
		$steps = $this->getCostSteps($serviceId);
		if (count($steps)) {
			//определяем в какой-интервал попадают заявки
			foreach ($steps as $rule) {
			//	echo $reqCount." " . $rule->from_num . "<br>";
				if ($reqCount < $rule->from_num) {
					return $rule;
				}
			}
		}
		return null;
	}

	/**
	 * возвращает флаг находится ли заявка в биллнге или нет
	 *
	 * @param RequestModel $request
	 *
	 * @return bool
	 */
	public function isInBilling(RequestModel $request)
	{
		return is_null($this->contract->getMonthForBilling($request)) ? false : true;
	}

	/**
	 * Шаги лесенки со стоимостью заявок в зависимости от специальности/диагностики
	 *
	 * @param int $serviceId
	 *
	 * @return ClinicContractCostModel[]
	 */
	public function getCostSteps($serviceId) {
		$group = $this->getServiceGroup($serviceId);

		if ($group !== null) {
			$steps = [];

			foreach ($this->costRules as $rule) {
				if ($rule->group_uid == $group->id) {
					$steps[] = $rule;
				}
			}

			return count($steps) ? $steps : null;
		}

		return null;
	}

	/**
	 * Массив идентификаторов услуг, которые находятся в одной группе с данной услугой
	 * (БЕЗ serviceId=0 для всех услуг)
	 *
	 * @param int $serviceId
	 * @return int[]
	 */
	public function getServicesInGroup($serviceId)
	{
		$group = $this->getServiceGroup($serviceId);
		if ($group === null) {
			return [];
		}

		$services = $group->getServicesInGroup();
		if (isset($services[ContractGroupServiceModel::ANY_SERVICE])) {
			unset($services[ContractGroupServiceModel::ANY_SERVICE]);
		}

		return $services;
	}

	/**
	 * Массив идентификаторов всех услуг, для которых есть специальная цена
	 *
	 * @return int[]
	 */
	public function getStepsId()
	{
		$services = [];

		foreach ($this->costRules as $rule) {
			foreach ($rule->contractGroup->getServicesInGroup() as $serviceId) {
				$services[$serviceId] = $serviceId;
			}
		}

		if (isset($services[ContractGroupServiceModel::ANY_SERVICE])) {
			unset($services[ContractGroupServiceModel::ANY_SERVICE]);
		}

		return $services;
	}



	/**
	 * Проверка подходит ли этот тариф для данной заявки
	 *
	 * @param RequestModel $request
	 *
	 * @return bool
	 */
	public function isTariffForRequest(RequestModel $request)
	{
		$kind = intval($request->kind);

		if ($kind === intval($this->contract->kind)) {
			/**
			 * Для клиники может быть только один контракт по врачам и один на диагностику
			 * тариф онлайн-запись - виртуальный тариф, по которому не билятся заявки. Для него берутся ставки из основного тарифа а диагностику
			 **/
			if ($kind == RequestModel::KIND_DIAGNOSTICS && $this->contract->contract_id == ContractModel::TYPE_DIAGNOSTIC_ONLINE) {
				return false;
			}

			return true;
		}

		return false;
	}


	/**
	 * Количество заявок в биллинге за месяц с учетом специальностей
	 *
	 * @param string $from дата и время начала биллинга
	 * @param string $to дата и время окончания биллинга
	 * @param int $serviceId
	 * @param integer|int[] $clinicId для всех филиалов (null) для конкретной клиники
	 *
	 * @return int
	 */
	public function getRequestNumInBilling($from, $to, $serviceId, $clinicId = null)
	{
		$model = $this->getRequestsInBilling($from, $to, $serviceId, false, $clinicId);
		return $model->count();
	}

	/**
	 * Выборка заявок в биллинге за месяц по данному контракту
	 *
	 * @param string $from дата и время начала биллинга
	 * @param string $to дата и время окончания биллинга
	 * @param null|int $serviceId
	 * @param bool $total получать общее количество заявок или в разрезе услуг
	 * @param integer|int[] $clinicId для всех филиалов (null) для конкретной клиники
	 *
	 * @return RequestModel
	 */
	public function getRequestsInBilling($from, $to, $serviceId = null, $total = false, $clinicId = null)
	{
		$model = (new RequestModel)
			->byKind($this->contract->kind)
			->origin() // 112014
			->inBilling();

		($clinicId === null) ? $model->inBranches($this->clinic->id) : $model->inClinic($clinicId);

		if (!$total) {


			$services = $this->getServicesInGroup($serviceId);
			//если для этой услуги есть своя лесенка
			//берем заявки только по этим услугам
			if (isset($services[$serviceId])) {
				($this->contract->kind == RequestModel::KIND_DIAGNOSTICS) ? $model->inDiagnostics($services) : $model->inSectors($services);
			} else {
				//иначе берем все заявки, для которых нет специальных лесенок
				$services = $this->getStepsId();
				($this->contract->kind == RequestModel::KIND_DIAGNOSTICS) ? $model->exceptDiagnostics($services) : $model->exceptSectors($services);
			}
		}

		switch($this->contract->getBillingDate()) {
			case 'date_admission':
				$model->betweenDateAdmission(strtotime($from), strtotime($to));
				break;
			case 'date_record':
				$model->betweenDateRecord($from, $to);
				break;
			case 'req_created':
				$model->createdInInterval(strtotime($from), strtotime($to));
				break;
		}

		//фильтруем заявки по договору
		$model
			->getDbCriteria()
			->mergeWith($this->contract->getContractCriteria());

		return $model;
	}

	/**
	 * Общее количество заявок в биллинге за месяц без учета специальностей
	 *
	 * @param string $from дата и время начала биллинга
	 * @param string $to дата и время окончания биллинга
	 * @param integer|int[] $clinicId для всех филиалов (null) для конкретной клиники
	 *
	 * @return int
	 */
	public function getTotalRequestNumInBilling($from, $to, $clinicId = null)
	{
		$model = $this->getRequestsInBilling($from, $to, null, true, $clinicId);
		return $model->count();
	}

	/**
	 * Стоимость общего количество заявок в биллинге за месяц без учета специальностей
	 *
	 * @param string $from дата и время начала биллинга
	 * @param string $to дата и время окончания биллинга
	 * @param integer|int[] $clinicId для всех филиалов (null) для конкретной клиники
	 *
	 * @return float
	 */
	public function  getTotalRequestCostInBilling($from, $to, $clinicId = null)
	{
		return $this->getRequestCostInBilling($from, $to, null, $clinicId, true);
	}

	/**
	 * Стоимость заявок в биллинге за месяц с учетом специальностей
	 *
	 * @param string $from дата и время начала биллинга
	 * @param string $to дата и время окончания биллинга
	 * @param int $serviceId
	 * @param integer|int[] $clinicId для всех филиалов (null) для конкретной клиники
	 * @param bool $total
	 *
	 * @return int
	 */
	public function getRequestCostInBilling($from, $to, $serviceId, $clinicId = null, $total = false)
	{
		$model = $this->getRequestsInBilling($from, $to, $serviceId, $total, $clinicId);
		$clone = clone $model;
		$criteria = $clone->getDbCriteria();
		$criteria->select =["SUM(request_cost) as request_cost"];

		$result = $clone->query($criteria);
		return ($result instanceof RequestModel) ? $result->request_cost : 0;
	}

	/**
	 * Сохранение лимитов записей для каждой группы услуг
	 *
	 * @param array $groupLimits
	 */
	public function saveRequestLimits($groupLimits)
	{
		foreach ($this->requestLimits as $item) {
			$item->delete();
		}

		foreach ($groupLimits as $item) {
			$limit = new ClinicRequestLimitModel();
			$limit->clinic_contract_id = $this->id;
			$limit->group_uid = $item['groupId'];
			$limit->limit = $item['limit'];
			$limit->save();
		}
	}

	/**
	 * Сброс статуса биллинга в заявках за выбранный период
	 *
	 * @param $status
	 * @param $from
	 * @param $to
	 *
	 * @return bool
	 */
	public function changeBillingStatus($status, $from, $to)
	{
		$from = !is_null($from) ? strtotime($from) : null;
		$to = !is_null($to) ? strtotime($to) : null;

		$criteria = new \CDbCriteria();
		$criteria->addCondition("billing_status = :status AND kind = :kind");
		$criteria->params = [
			':status'   => RequestModel::BILLING_STATUS_YES,
			':kind'     => $this->contract->kind,
		];

		$criteria->addInCondition('clinic_id', $this->getClinicsForContract());

		if (!is_null($from)) {
			$criteria->addCondition("req_created >= :from");
			$criteria->params[':from'] = $from;
		}

		if (!is_null($to)) {
			$criteria->addCondition("req_created <= :to");
			$criteria->params[':to'] = $to;
		}

		RequestModel::model()->updateAll(['billing_status' => $status], $criteria);

		return true;
	}

	/**
	 * Сброс заявок в статус BILLING_STATUS_NO
	 *
	 * @param string $from
	 * @param string|null $to
	 *
	 * @return bool
	 */
	public function resetBilling($from, $to = null)
	{
		return $this->changeBillingStatus(RequestModel::BILLING_STATUS_NO, $from, $to);
	}

	/**
	 * Закрытие счета по заявкам за предыдущие месяцы
	 * Сброс заявок в статус BILLING_STATUS_PAID
	 *
	 * @param string $from
	 * @param string|null $to
	 *
	 * @return bool
	 */
	public function closeBilling($from, $to = null)
	{
		return $this->changeBillingStatus(RequestModel::BILLING_STATUS_PAID, $from, $to);
	}

	/**
	 * Информация о биллинге за период по данному договору
	 *
	 * @param string $date
	 *
	 * @return ClinicBillingModel
	 */
	public function saveBillingByPeriod($date)
	{
		$dateBilling = (new \DateTime($date))->modify('first day of this month' )->format('Y-m-d');
		$dateTo =  (new \DateTime($date))->modify('last day of this month' )->format('Y-m-d')." 23:59:59";

		$billing = ClinicBillingModel::model()
			->byPeriod($date)
			->byClinicContract($this->id)
			->find();
		$clinics = $this->getClinicsForContract();

		if ($billing === null) {
			$billing = new ClinicBillingModel();
			$billing->clinic_id = $this->clinic_id;
			$billing->clinic_contract_id = $this->id;
			$billing->billing_date = $dateBilling;
		}

		if (empty($billing->manager_id)) {
			$billing->manager_id = $this->clinic->manager_id;
		}

		$billing->setStartNum($this->getTotalRequestNumInBilling($dateBilling, $dateTo, $clinics));
		$billing->setStartCost($this->getTotalRequestCostInBilling($dateBilling, $dateTo, $clinics));
		$billing->today_requests = $this->getTotalRequestNumInBilling($dateBilling, $dateTo, $clinics);
		$billing->today_sum = $this->getTotalRequestCostInBilling($dateBilling, $dateTo, $clinics);
		$billing->save();

		return $billing;
	}
}
