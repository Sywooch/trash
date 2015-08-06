<?php

namespace dfs\docdoc\back\controllers;

use dfs\docdoc\models\CityModel;
use dfs\docdoc\models\ClinicBillingModel;
use dfs\docdoc\models\ClinicPaymentModel;
use dfs\docdoc\models\ContractModel;
use dfs\docdoc\models\RequestModel;
use dfs\docdoc\models\UserModel;
use Yii;


/**
 * управление биллингом
 */
class ClinicBillingController extends BackendController
{
	/**
	 * Суммарная статистика
	 *
	 * @var int
	 */
	private $_stat = ['totalNum' => 0, 'totalCost' => 0, 'totalTodayCost' => 0, 'totalTodayNum' => 0, 'totalRecieved' => 0, 'totalCredit' => 0];


	/**
	 * Параметры полей таблицы для платежей
	 *
	 * @var array
	 */
	protected $_paymentFields = [
		'id' => [
			'label' => 'ID',
			'width' => 50,
			'type' => 'text',
		],
		'clinic_billing_id' => [
			'label' => 'Дата',
			'width' => 50,
			'editable' => true,
			'type' => 'text',
		],
		'payment_date' => [
			'label' => 'Дата',
			'width' => 250,
			'editable' => true,
			'type' => 'text',
			'className' => 'DatePicker',
		],
		'sum' => [
			'label' => 'Сумма',
			'width' => 150,
			'editable' => true,
			'type' => 'text',
		],
		'remove' => [
			'label' => 'Действие',
			'defaultContent' => '<button class="remove btn btn-danger">Удалить</button>',
			'width' => 50,
		],
	];

	/**
	 * Действия для платежей
	 *
	 * @var array
	 */
	protected $_paymentActions = [
		'create' => [
			'title' => 'Добавление',
			'type' => 'create',
		],
		'edit' => [
			'title' => 'Редатирование',
			'type' => 'edit',
		],
		'remove' => [
			'title' => 'Редатирование',
			'type' => 'remove',
		],
	];

	/**
	 * Параметры страниц с отчётами
	 *
	 * @var array
	 */
	protected $_paymentColumns = ['id', 'payment_date', 'sum', 'remove'];

	/**
	 * Параметры полей таблицы
	 *
	 * @var array
	 */
	protected $_fields = [
		'city' => [
			'label' => 'Город',
			'width' => 50,
		],
		'clinicId' => [
			'label' => 'ID',
			'width' => 30,
		],
		'clinic' => [
			'label' => 'Клиника',
			'width' => 100,
		],
		'tariff' => [
			'label' => 'Тариф',
			'width' => 200,
		],
		'status' => [
			'label' => 'Статус',
			'width' => 100,
		],
		'start_requests' => [
			'label' => 'Заявок на начало периода',
			'width' => 70,
		],
		'start_sum' => [
			'label' => 'Сумма на начало периода',
			'width' => 70,
		],
		'today_requests' => [
			'label' => 'Заявок на сегодня',
			'width' => 70,
		],
		'today_sum' => [
			'label' => 'Сумма на сегодня',
			'width' => 70,
		],
		'agreed_requests' => [
			'label' => 'Согласовано заявок',
			'width' => 70,
		],
		'agreed_sum' => [
			'label' => 'Согласовано на сумму',
			'width' => 70,
		],
		'recieved_sum' => [
			'label' => 'Получено',
			'width' => 100,
		],
		'credit' => [
			'label' => 'Долг',
			'width' => 70,
		],
		'actions' => [
			'label' => 'Действия',
			'width' => 100,
		],
		'action_type' => [
			'type' => 'hidden',
		],
	];

	protected $_statusClasses = [
		ClinicBillingModel::STATUS_OPEN => "",
		ClinicBillingModel::STATUS_WAITING_PAYMENT => "info",
		ClinicBillingModel::STATUS_AGREEMENT => "warning",
		ClinicBillingModel::STATUS_CLOSED => "success",
		ClinicBillingModel::STATUS_PESSIMISATION => 'text-danger',
		ClinicBillingModel::STATUS_DEBTOR => 'danger',
	];




	/**
	 * Параметры страниц с отчётами
	 *
	 * @var array
	 */
	protected $_columns = [
		'city',
		'clinicId',
		'clinic',
		'tariff',
		'status',
		'start_requests',
		'start_sum',
		'today_requests',
		'today_sum',
		'agreed_requests',
		'agreed_sum',
		'recieved_sum',
		'credit',
		'actions',
	];

	/**
	 * Главная страница
	 */
	public function actionIndex()
	{
		$request = Yii::app()->request;

		$dateFrom   = $request->getQuery('dateFrom', date('Y-m-01'));
		$cityId     = $request->getQuery('cityId');
		$contractId = $request->getQuery('contractId');
		$status     = $request->getQuery('status');
		$managerId  = $request->getQuery('managerId');


		if ($request->isAjaxRequest) {
			$this->renderJSON([
				'data' => $this->getRequestInBilling($dateFrom, $contractId, $cityId, $status, $managerId),
				'statisticsHtml' => $this->renderPartial('statistics', [
					'dateFrom'     => $dateFrom,
					'stat'         => $this->calculateStatistics($dateFrom),
				], true),
			]);
		} else {

			$vars = [
				'tableConfig'    => [
					'url'               => '/2.0/clinicBilling',
					'urlEdit'           => '/2.0/clinicBilling/update?id=_id_',
					'dtDom'             => 'TC<"clear">lfrtip',
					'fields'            => $this->_fields,
					'columns'           => $this->_columns,
					'order'             => 'clinicId',
					'orderDirection'    => 'asc',
					'rowsData'          =>  $this->getRequestInBilling($dateFrom, $contractId, $cityId, $status, $managerId),
				],
				'paymentsTableConfig' => [
					'url'               => '/2.0/clinicBilling/payments',
					'urlEdit'           => '/2.0/clinicBilling/editPayment',
					'dtDom'             => '',
					'fields'            => $this->_paymentFields,
					'columns'           => $this->_paymentColumns,
					'order'             => 'payment_date',
					'orderDirection'    => 'asc',
					'actions'           => $this->_paymentActions,
					'rowsData'          => [],
				],
				'dateFrom'       => $dateFrom,
				'billingPeriods' => $this->getBillingPeriods(),
				'stat'           => $this->calculateStatistics($dateFrom),
				'cityId'         => $cityId,
				'contractId'     => $contractId,
				'contracts'      => ContractModel::model()->realContracts()->findAll(),
				'cities'         => CityModel::model()->findAll(),
				'statuses'       => ClinicBillingModel::getStatuses(),
				'status'         => $status,
				'managerId'      => $managerId,
				'managers'       => UserModel::model()->enabled()->withRoles(['ACN'])->findAll(),
			];
			$this->render('index', $vars);
		}
	}

	/**
	 * Поиск заявок клиники для биллинга
	 *
	 * @param string              $dateFrom
	 * @param int                 $contractId
	 * @param int                 $cityId
	 * @param int                 $status
	 * @param int                 $managerId
	 * @return array
	 */
	public function getRequestInBilling($dateFrom, $contractId, $cityId, $status, $managerId)
	{
		$criteria = new \CDbCriteria();

		$scopes = [
			'byPeriod' => [$dateFrom],
		];

		if (!empty($contractId)) {
			$scopes['byContract'] = [$contractId];
		}

		if (!empty($status)) {
			$scopes['byStatus'] = [$status];
		}

		if (!empty($managerId)) {
			$scopes['byManager'] = [$managerId];
		}


		$criteria->scopes = $scopes;
		$criteria->order = 't.clinic_id ASC';

		if (!empty($cityId)) {
			$with = ['clinic' => [
				'joinType' => "INNER JOIN",
				'scopes' => [
					'inCity' => [$cityId]
				]
			]];
		} else {
			$with = ['clinic'];
		}
		$with[] = 'tariff';
		$with[] = 'clinic.clinicCity';
		$criteria->with = $with;

		$dataProvider = new \CActiveDataProvider(ClinicBillingModel::class);
		$dataProvider->setCriteria($criteria);

		$data = [];
		$dataIterator = new \CDataProviderIterator($dataProvider, 100);
		foreach ($dataIterator as $billing) {
			$data[] = $this->buildDataTable($billing);
		}

		return $data;
	}

	/**
	 * @param ClinicBillingModel $billing
	 *
	 * @return array
	 */
	protected function buildDataTable(ClinicBillingModel $billing)
	{
		$tariffTitle = "";
		$contractId = null;
		if (isset($billing->tariff)) {
			$tariffTitle = $billing->tariff->contract ? $billing->tariff->contract->title : "Не известен!!!!";
			$contractId  = $billing->tariff->contract_id;
		}

		$rowClass = $this->_statusClasses[$billing->status];

		$data = [
			'DT_RowClass' => $rowClass,
			'id'                  => $billing->id,
			'city'                => $billing->clinic->clinicCity->title,
			'clinicId'            => $billing->clinic->id,
			'clinic'              => !empty($billing->clinic->short_name) ? $billing->clinic->short_name : $billing->clinic->name,
			'tariff'              => '<a href="/2.0/billing/?dateFrom=' .
				$billing->billing_date . '&clinicId=' . $billing->clinic_id. '&contractId=' . $contractId . '">' .
				$tariffTitle . '</a>',
			'start_requests'      => $billing->start_requests,
			'start_sum'           => $billing->start_sum,
			'today_requests'      => $billing->today_requests,
			'today_sum'           => $billing->today_sum,
			'agreed_requests'     => $billing->agreed_requests,
			'agreed_sum'          => $billing->agreed_sum,
			'recieved_sum'        => '<a href="#" class="billingPayments" data-billing="' . $billing->id . '">' . $billing->recieved_sum . '</a>',
			'actions'             => $billing->isNeedAgree() ? '<a class="btn btn-default actionAgree" href="" data-billing="' . $billing->id . '">Согласовать</a>' : '',
			'credit'              => $billing->agreed_sum - $billing->recieved_sum,
			'status'              => $billing->getStatusTitle(),
			'action_type'         => '',
		];

		$this->_stat['totalCost']         += $billing->start_sum;
		$this->_stat['totalNum']          += $billing->start_requests;
		$this->_stat['totalTodayCost']    += $billing->today_sum;
		$this->_stat['totalTodayNum']     += $billing->today_requests;
		$this->_stat['totalRecieved']     += $billing->recieved_sum;
		$this->_stat['totalCredit']       += $data['credit'];

		return $data;
	}

	/**
	 * Статистика по тарифу для клиники
	 *
	 * @param string $dateFrom
	 *
	 * @return array
	 */
	protected function calculateStatistics($dateFrom)
	{
		$stat = $this->_stat;
		$dateTo = (new \DateTime($dateFrom))->modify('last day of this month' )->format('Y-m-d')." 23:59:59";
		$model = RequestModel::model()
			->betweenBillingDate($dateFrom, $dateTo)
			->inBilling();


		$numModel = clone $model;
		$stat['totalRequestNumInBillingControl']  = $numModel->count();

		$totalCost = $model->find([ "select" => "*, SUM(request_cost) as request_cost"]);
		$stat['totalRequestCostInBillingControl'] = $totalCost->request_cost;
		return $stat;
	}

	/**
	 * Изменение поступлений
	 */
	public function actionEdit()
	{
		$billing = ClinicBillingModel::model()->findByPk(
			Yii::app()->request->getPost("id")
		);
		$errorMsg = null;

		if ($billing !== null) {
			$billing->setScenario(ClinicBillingModel::SCENARIO_UPDATE_SUM);
			$billing->attributes = Yii::app()->request->getPost('data');
			$billing->save();
		}

		$this->getRowJsonResponse($billing);
	}

	/**
	 * Согласование периода
	 */
	public function actionUpdate()
	{
		$billingId = Yii::app()->request->getQuery('id');
		$billing  = ClinicBillingModel::model()->findByPk($billingId);

		$data = Yii::app()->request->getPost('data', []);

		$msg = null;
		if ($billing !== null && isset($data['action_type'])) {
			if ($data['action_type'] == 'agree') {
				if ($billing->agree()) {
					$msg = "Письмо о выставлении счета отправлено в бухгалтерию";
				}
			}
		}
		$this->getRowJsonResponse($billing, $msg);
	}

	/**
	 * Вывод Json ответа после изменения записи по биллингу
	 *
	 * @param ClinicBillingModel|null $billing
	 * @param string|null $msg
	 *
	 */
	private function getRowJsonResponse($billing, $msg = null)
	{
		if ($billing == null) {
			$msg = "Не найдена запись";
		} else {
			if ($billing->hasErrors()) {
				foreach ($billing->getErrors() as $errors) {
					$msg = implode('<br>', $errors);
				}
			}
		}

		$this->renderJSON([
				'success' => $msg === null,
				'errorMsg' => $msg,
				'row' => $billing ? $this->buildDataTable($billing) : null,
			]);
	}



	/**
	 * Список периодов биллинга
	 *
	 * @return array
	 */
	public function getBillingPeriods()
	{
		$periods = [];

		$firstRequest = RequestModel::model()->find(['order' => 'req_created ASC']);
		$time = $firstRequest->req_created;
		$now = time();

		while ($time < $now) {
			$periods[date('Y-m-d', $time)] = date('m.Y', $time);
			$time = strtotime('next month', $time);
		}

		return $periods;
	}

	/**
	 * список платежей за период
	 *
	 */
	public function actionPayments($billingId)
	{
		$billing = ClinicBillingModel::model()->findByPk($billingId);

		$payments = [];

		foreach ($billing->payments as $p) {
			$pd = $p->getAttributes();
			$pd['payment_date'] = date('d.m.Y', strtotime($pd['payment_date']));
			$payments[] = $pd;
		}

		$this->renderJSON([
			'data' => $payments,
		]);
	}

	/**
	 * список платежей за период
	 *
	 */
	public function actionEditPayment()
	{
		$errorMsg = null;

		$data = Yii::app()->request->getPost('data');

		$id = empty($data['id']) ? null: (int)$data['id'];

		$payment = null;
		if (!empty($id)) {
			$payment = ClinicPaymentModel::model()->findByPk($id);
		}

		if ($payment === null) {
			$payment = new ClinicPaymentModel();
		}

		$data['payment_date'] = date('Y-m-d', strtotime($data['payment_date']));
		$payment->attributes = $data;
		if (!$payment->save()) {
			foreach ($payment->getErrors() as $e) {
				$errorMsg .= implode("<br>", $e);
			}
		}
		$pd = $payment->getAttributes();
		$pd['payment_date'] = date('d.m.Y', strtotime($pd['payment_date']));

		$this->renderJSON([
				'success' => $errorMsg === null,
				'errorMsg' => $errorMsg,
				'row' => $pd,
			]);
	}

	/**
	 * удаление платежа
	 */
	public function actionDeletePayment()
	{
		$idPayment = Yii::app()->request->getPost('paymentId');
		$payment = ClinicPaymentModel::model()->findByPk($idPayment);
		if ($payment) {
			$payment->delete();
		}
	}
}
