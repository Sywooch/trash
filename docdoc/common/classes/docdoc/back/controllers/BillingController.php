<?php

namespace dfs\docdoc\back\controllers;

use dfs\docdoc\models\ContractModel;
use dfs\docdoc\models\RequestModel;
use dfs\docdoc\models\ClinicModel;
use dfs\docdoc\models\ClinicContractModel;
use dfs\docdoc\models\PartnerModel;
use dfs\docdoc\reports\LkReportRequests;


/**
 * Аналитика
 */
class BillingController extends BackendController
{
	/**
	 * Параметры полей таблицы
	 *
	 * @var array
	 */
	protected $_fields = [
		'id' => [
			'label' => 'Заявка',
			'width' => 50,
		],
		'client_name' => [
			'label' => 'Пациент',
			'width' => 300,
		],
		'online' => [
			'label' => 'Онлайн-запись',
			'width' => 100,
		],
		'billing_status' => [
			'display' => 'billing_status_name',
			'label' => 'Статус в биллинге',
			'type' =>'radio',
			'ipOpts' => [
				[ 'label' => 'В биллинге', 'value' => RequestModel::BILLING_STATUS_YES ],
				[ 'label' => 'Клиника отказывается платить', 'value' => RequestModel::BILLING_STATUS_REFUSED ],
			],
			'width' => 160,
		],
		'billing_date' => [
			'sort' => 'billing_date_',
			'label' => 'Дата попадания в биллинг',
			'type' => 'text',
			'className' => 'DateTimePicker',
			'width' => 100,
		],
		'service' => [
			'label' => 'Услуга',
			'width' => 400,
		],
		'cost' => [
			'display' => 'cost_title',
			'sort' => 'cost',
			'label' => 'Стоимость',
			'width' => 70,
		],
		'actions' => [
			'emptyData' => true,
			// 'orderable' => false,
			'label' => 'Действия',
			'defaultContent' => '<a class="edit" href="javascript:void(0);">Редактировать</a>',
			'width' => 100,
		],
		'action_type' => [
			'type' => 'hidden',
		],
	];

	/**
	 * Параметры страниц с отчётами
	 *
	 * @var array
	 */
	protected $_columns = [ 'id', 'client_name', 'online', 'billing_status', 'billing_date', 'service', 'cost', 'actions' ];

	/**
	 * Какие столбцы показываем в отчёте
	 *
	 * @var array
	 */
	protected $_columnsExport = [ 'id', 'client_name', 'billing_status_name', 'billing_date', 'service', 'online', 'cost_title' ];

	/**
	 * Используемые действия
	 *
	 * @var array
	 */
	protected $_actions = [
		'edit' => [
			'title' => 'Редатирование',
			// 'type' => 'edit',
			'fields' => [ 'billing_status', 'billing_date' ],
		],
	];


	/**
	 * Главная страница
	 */
	public function actionIndex()
	{
		$request = \Yii::app()->request;

		$dateFrom = $request->getQuery('dateFrom', date('Y-m-01'));
		$clinicId = $request->getQuery('clinicId');
		$contractId = $request->getQuery('contractId', ContractModel::TYPE_DOCTOR_VISIT);
		$withBranches = $request->getQuery('withBranches', true);
		$recalculate = $request->getQuery('recalculate');

		$time = strtotime($dateFrom);
		$dateTo = date('Y-m-d', mktime(0, 0, 0, date('n', $time) + 1, 0, date('Y', $time)));

		$data = [];
		$tariff = null;

		$clinic = ClinicModel::model()->findByPk($clinicId);

		if ($clinic) {
			$clinicContracts = $clinic->getClinicContracts();

			if (isset($clinicContracts[$contractId])) {
				$tariff = $clinicContracts[$contractId];
				$data = $this->getRequestInBilling($clinic, $tariff, $dateFrom, $dateTo, $withBranches, $recalculate);
			}
		}

		if ($request->isAjaxRequest) {
			$this->renderJSON([
				'data' => $data,
				'clinicContractIds' => isset($clinicContracts) ? array_keys($clinicContracts) : null,
				'statisticsHtml' => $this->renderPartial('statistics', [
					'dateFrom'     => $dateFrom,
					'dateTo'       => $dateTo,
					'tariff'       => $tariff,
					'stat'         => $tariff ? $this->calculateStatistics($tariff, $dateFrom, $dateTo) : [],
				], true),
			]);
		}
		elseif ($request->getQuery('contentType') === 'xls') {
			$report = new LkReportRequests();
			$this->renderExcel($report->excel($data, $this->_columnsExport));
		} else {
			$vars = [
				'tableConfig'    => [
					'url'               => '/2.0/billing',
					'urlEdit'           => '/2.0/billing/requestChange?id=_id_',
					'dtDom'             => 'lfrtip',
					'fields'            => $this->_fields,
					'columns'           => $this->_columns,
					'actions'           => $this->_actions,
					'order'             => 'billing_date',
					'orderDirection'    => 'asc',
					'rowsData'          => $data,
					'clinicContractIds' => isset($clinicContracts) ? array_keys($clinicContracts) : null
				],
				'clinic'         => $clinic,
				'contractId'     => $contractId,
				'dateFrom'       => $dateFrom,
				'dateTo'         => $dateTo,
				'tariff'         => $tariff,
				'withBranches'   => $withBranches,
				'billingPeriods' => $this->getBillingPeriods(),
				'contracts'      => ContractModel::model()->realContracts()->findAll(['order' => 't.contract_id']),
				'stat'           => $tariff ? $this->calculateStatistics($tariff, $dateFrom, $dateTo) : [],
			];
			$this->render('index', $vars);
		}
	}

	/**
	 * Изменение статуса заявки
	 *
	 * @param string $id
	 */
	public function actionRequestChange($id)
	{
		$errorMsg = null;
		$data = \Yii::app()->request->getPost('data');

		$request = RequestModel::model()->findByPk($id);

		if (!$request) {
			$errorMsg = 'Заявка не найдена';
		}
		elseif (!is_array($data) || empty($data['billing_status'])) {
			$errorMsg = 'Неверный запрос';
		}
		else {
			$billingDate = isset($data['billing_date']) ? $data['billing_date'] : '';

			if (!$request->saveBillingStatus($data['billing_status'], $billingDate)) {
				$errorMsg = 'Ошибка сохранения заявки: ' . self::buildErrorMessageByRecord($request);
			} else {
				if ($request->clinic) {
					$contract = $request->clinic->getRequestContract($request);
					$contract !== null && $contract->saveBillingByPeriod($request->date_billing);
				}
			}
		}

		$this->renderJSON([
			'success' => $errorMsg === null,
			'errorMsg' => $errorMsg,
			'row' => $request ? $this->buildDataTableRequestData($request) : null,
		]);
	}

	/**
	 * Поиск заявок клиники для биллинга
	 *
	 * @param ClinicModel         $clinic
	 * @param ClinicContractModel $tariff
	 * @param string              $dateFrom
	 * @param string              $dateTo
	 * @param bool                $withBranches
	 * @param bool                $recalculate
	 *
	 * @return array
	 */
	public function getRequestInBilling(ClinicModel $clinic, ClinicContractModel $tariff, $dateFrom, $dateTo, $withBranches, $recalculate = false)
	{
		$contract = $tariff->contract;

		$scopes = [
			'origin' => [],
			'byContract' => [ $contract ],
			'betweenBillingDate' => [ $dateFrom, $dateTo . " 23:59:59", $contract ],
		];

		if ($withBranches) {
			$scopes['inBranches'] = [ $clinic->id ];
		} else {
			$scopes['inClinic'] = [ $clinic->id ];
		}

		if (!$contract->isPayForCall()) {
			$scopes['inBillingState'] = [ RequestModel::BILLING_STATE_RECORD ];
		}

		$dataProvider = new \CActiveDataProvider(RequestModel::class);
		$dataProvider->setCriteria([
			'scopes' => $scopes,
			'order' => 't.req_id ASC',
		]);

		$data = [];
		$dataIterator = new \CDataProviderIterator($dataProvider, 1000);
		foreach ($dataIterator as $request) {
			if ($recalculate) {
				$request->saveRequestCost();
			}
			$data[] = $this->buildDataTableRequestData($request);
		}

		return $data;
	}

	/**
	 * @param RequestModel $request
	 *
	 * @return array
	 */
	protected function buildDataTableRequestData(RequestModel $request)
	{
		$cost = $request->request_cost !== null ? intval($request->request_cost) : null;

		return [
			'id'                  => $request->req_id,
			'created'             => date('d.m.Y H:i', $request->req_created),
			'created_'            => date('Y.m.d H:i', $request->req_created),
			'client_name'         => $request->client_name,
			'online'              => ($request->req_type == RequestModel::TYPE_ONLINE_RECORD) ? 'онлайн-запись' : '',
			'billing_status'      => $request->billing_status,
			'billing_status_name' => $request->getBillingStatusName(),
			'billing_date'        => $request->getBillingDate('d.m.Y H:i:s'),
			'billing_date_'       => $request->getBillingDate('Y.m.d H:i:s'),
			'service'             => $request->getServiceName(),
			'cost'                => $cost,
			'cost_title'          => $request->partner_id == PartnerModel::YANDEX_ID ? '0 (Яндекс)' : $cost,
		];
	}

	/**
	 * Статистика по тарифу для клиники
	 *
	 * @param ClinicContractModel $tariff
	 * @param string $dateFrom
	 * @param string $dateTo
	 *
	 * @return array
	 */
	protected function calculateStatistics(ClinicContractModel $tariff, $dateFrom, $dateTo)
	{
		$stat = [];

		foreach ($tariff->contractGroups as $item) {
			if (!empty($item->services)) {
				$service = $item->services[0]->service_id;
				$cost = $tariff->getRequestCostInBilling($dateFrom, $dateTo . " 23:59:59", $service);
				$requests = $tariff->getRequestNumInBilling($dateFrom, $dateTo . " 23:59:59", $service);
				$stat[] = [
					'groupName' => $item->name,
					'requests'  => $requests,
					'cost'      => $cost,
					'requestCost' => $requests ? $cost / $requests : 0,
				];
			}
		}

		return $stat;
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
}
