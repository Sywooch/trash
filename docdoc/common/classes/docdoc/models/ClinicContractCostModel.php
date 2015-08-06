<?php

namespace dfs\docdoc\models;

/**
 * This is the model class for table "clinic_contract_cost".
 *
 * The followings are the available columns in table 'clinic_contract_cost':
 *
 * @property integer $id
 * @property integer $service_id
 * @property integer $clinic_contract_id
 * @property integer $from_num
 * @property integer $is_active
 * @property float   $cost
 * @property integer  $group_uid
 *
 * The followings are the available model relations:
 *
 * @property ClinicContractModel $tariff
 * @property DiagnosticaModel $diagnostica
 * @property ContractGroupModel $contractGroup
 *
 * @method ClinicContractCostModel findByPk
 * @method ClinicContractCostModel with
 * @method ClinicContractCostModel[] findAll
 */
class ClinicContractCostModel extends \CActiveRecord
{

	/**
	 * Сумма в биллинге для статистики
	 * @var int
	 */
	static $billingSum = 0;

	/**
	 * Количество заявок в биллинге для статистики
	 * @var int
	 */
	static $billingNum = 0;

	/**
	 * кеш для статистики
	 * @var null
	 */
	private $_stat = [];

	/**
	 * Returns the static model of the specified AR class.
	 *
	 * @param string $className
	 *
	 * @return ClinicContractCostModel the static model class
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
		return 'clinic_contract_cost';
	}

	/**
	 * @return string the associated primary key
	 */
	public function primaryKey()
	{
		return 'id';
	}

	/**
	 * Зависимости
	 *
	 * @return array
	 */
	public function relations()
	{
		return array(
			'tariff' => array(
				self::BELONGS_TO,
				'dfs\docdoc\models\ClinicContractModel',
				'clinic_contract_id',
			),
			'diagnostic' => array(
				self::BELONGS_TO,
				DiagnosticaModel::class,
				'service_id'
			),
			'contractGroup' => array(
				self::BELONGS_TO,
				ContractGroupModel::class,
				'group_uid'
			),
		);
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array();
	}

	/**
	 * Поиск по ид контракта клиники
	 *
	 * @param integer $clinicContractId
	 *
	 * @return $this
	 */
	public function byClinicContract($clinicContractId)
	{
		$this->getDbCriteria()->mergeWith([
			'condition' => 'clinic_contract_id = :contractId',
			'params' => [':contractId' => $clinicContractId]
		]);

		return $this;
	}

	/**
	 * поиск по идентификатору группы
	 *
	 * @param $groupUid
	 * @return $this
	 */
	public function byGroup($groupUid)
	{
		$this->getDbCriteria()->mergeWith([
				'condition' => $this->getTableAlias() . '.group_uid = :group_uid',
				'params' => [':group_uid' => $groupUid]
			]);

		return $this;
	}

	/**
	 * Поис клиник, имеющих тарифы
	 *
	 * @param array $params
	 * @return \CActiveDataProvider
	 */
	public function searchClinicsForBilling($params = [])
	{
		$criteria = new \CDbCriteria();

		if (!empty($this->clinic_id)) {
			$criteria->compare('t.clinic_id', $this->clinic_id);
		}

		$criteria->together = true;
		$criteria->with = [
			'tariff' => [
				'joinType' => 'INNER JOIN',
			],
			'contractGroup' => [
				'joinType' => 'INNER JOIN',
			],
			'tariff.clinic' => [
				'joinType' => 'INNER JOIN',
			],
			'tariff.contract' => [
				'joinType' => 'INNER JOIN',
				'scopes' => [
					'realContracts' => []
				]
			],
			'tariff.clinic.clinicCity' => [
				'joinType' => 'INNER JOIN',
			]
		];

		if (!empty($this->is_active)) {
			$criteria->compare('t.is_active', $this->is_active);
		}

		foreach ($params as $k => $v) {
			$criteria->$k = $v;
		}


		return new \CActiveDataProvider(
			new ClinicContractCostModel(),
			array(
				'criteria' => $criteria,
				'pagination' => array(
					'pageSize' => 1000
				),
			)
		);
	}

	/**
	 * Статистика
	 *
	 * @param $parameter
	 * @param $from
	 * @param $to
	 *
	 * @return null
	 */
	public function getContractStatistics($parameter, $from, $to) {
		if (isset($this->_stat["_" . $this->group_uid])) {
			return  isset($this->_stat["_" . $this->group_uid][$parameter]) ? $this->_stat["_" . $this->group_uid][$parameter] : null;
		}

		$stat = [
			'totalNum' => null,
			'totalCost' => null,
			'numForService' => null,
			'costForService' => null,
			'currentStepNum' => null,
			'currentStepCost' => null,
			'nextStepNum' => null,
			'nextStepCost' => null,
			'leftToNextStep' => null,
		];

		$services = $this->contractGroup->getServicesInGroup();
		$serviceId  = (count($services)) ? current($services) : null;

		$stat['totalNum'] = $this->tariff->getTotalRequestNumInBilling($from, $to);
		$stat['totalCost'] = $this->tariff->getTotalRequestCostInBilling($from, $to);

		$clinics = $this->tariff->getClinicsForContract();

		$stat['numForService'] = $this->tariff->getRequestNumInBilling($from, $to, $serviceId, $clinics);
		$stat['costForService'] = $this->tariff->getRequestCostInBilling($from, $to, $serviceId, $clinics);

		self::$billingSum += $stat['costForService'];
		self::$billingNum += $stat['numForService'];

		$currentRule = $this->tariff->getCurrentRule($serviceId, $stat['numForService']);
		if ($currentRule) {
			$stat['currentStepNum'] = $currentRule->from_num;
			$stat['currentStepCost'] = $currentRule->cost;
		}

		$nextRule = $this->tariff->getNextRule($serviceId, $stat['numForService']);
		if ($nextRule) {
			$stat['nextStepNum'] = $nextRule->from_num;
			$stat['nextStepCost'] = $nextRule->cost;
			$stat['leftToNextStep'] = $nextRule->from_num -$stat['numForService'];
			$stat['profit'] = $stat['nextStepNum'] * $stat['nextStepCost'] - $stat['currentStepCost'] * $stat['nextStepNum'];
		} else {
			$stat['nextStepNum'] = null;
			$stat['nextStepCost'] = null;
			$stat['leftToNextStep'] = null;
			$stat['profit'] = null;
		}

		$this->_stat["_" . $this->group_uid] = $stat;

		return isset($stat[$parameter]) ? $stat[$parameter] : null;
	}


}
