<?php

namespace dfs\docdoc\front\controllers\pk;

use dfs\docdoc\models\RequestModel;
use dfs\docdoc\models\ServiceModel;
use dfs\docdoc\objects\Rejection;
use dfs\docdoc\objects\Phone;
use dfs\docdoc\reports\LkReportRequests;
use CDataProviderIterator;


/**
 * Class PkPatientsController
 *
 * @package dfs\docdoc\front\controllers\pk
 */
class PatientsController extends FrontController
{
	/**
	 * Список периодов для выбора
	 *
	 * @var array
	 */
	protected $_requestPeriod = [
		'today' => [ 'title' => 'сегодня', 'date' => 'now' ],
		'yesterday' => [ 'title' => 'вчера', 'date' => '-1 day', 'dateEnd' => '-1 day' ],
		'week' => [ 'title' => 'за неделю', 'date' => '-1 week' ],
		'month' => [ 'title' => 'за месяц', 'date' => '-1 month' ],
		'quarter' => [ 'title' => 'за квартал', 'date' => '-3 month' ],
	];

	/**
	 * Список типов заявки
	 *
	 * @var array
	 */
	protected $_kinds = [
		RequestModel::KIND_DOCTOR => 'Запись ко врачу',
		RequestModel::KIND_DIAGNOSTICS => 'Диагностика',
	];

	/**
	 * Список статусов заявки
	 *
	 * @var array
	 */
	protected $_requestStates = [
		RequestModel::PARTNER_STATUS_HOLD => [ 'title' => 'Холд', 'name' => 'hold', 'class' => 'transferred' ],
		RequestModel::PARTNER_STATUS_ACCEPT => [ 'title' => 'Подтверждено', 'name' => 'accept', 'class' => 'completed' ],
		RequestModel::PARTNER_STATUS_REJECT => [ 'title' => 'Отклонено', 'name' => 'reject', 'class' => 'decline' ],
	];

	/**
	 * Параметры полей таблицы
	 *
	 * @var array
	 */
	protected $_fields = [
		'id' => [
			'label' => 'Номер',
			'width' => 45,
		],
		'city_title' => [
			'label' => 'Город',
			'width' => 45,
		],
		'created' => [
			'sort' => 'created_',
			'label' => 'Регистрация заявки',
			'width' => 90,
		],
		'req_type' => [
			'label' => 'Способ обращения',
			'width' => 90,
		],
		'kind' => [
			'label' => 'Тип заявки',
			'width' => 90,
		],
		'client_name' => [
			'type' => 'text',
			'label' => 'Пациент',
			'width' => 250,
		],
		'client_phone' => [
			'type' => 'text',
			'label' => 'Телефон',
			'width' => 100,
		],
		'cost' => [
			'label' => 'Цена',
			'width' => 80,
		],
		'speciality' => [
			'label' =>  'Специальность',
		],
		'partner_status' => [
			'sort' => 'partner_status_name',
			'emptyData' => true,
			'label' => 'Статус',
			'width' => 150,
		],
	];

	/**
	 * Какие столбцы отображаем в таблице
	 *
	 * @var array
	 */
	protected $_columns = [
		'id',
		'city_title',
		'created',
		'req_type',
		'kind',
		'client_name',
		'client_phone',
		'cost',
		'speciality',
		'partner_status'
	];

	/**
	 * Какие столбцы показываем в отчёте
	 *
	 * @var array
	 */
	protected $_columnsExport = [
		'id',
		'city_title',
		'created',
		'req_type',
		'kind',
		'client_name',
		'client_phone',
		'cost',
		'speciality',
		'state_title'
	];

	/**
	 * Дефолтные параметры для поиска
	 *
	 * @var array
	 */
	protected $_findParams = [];


	/**
	 * Страница заявок
	 */
	public function actionIndex()
	{
		$vars = [
			'defaultPeriod' => 'week',
			'requestPeriod' => $this->_requestPeriod,
			'requestStates' => $this->_requestStates,
			'requestTypes' => RequestModel::getTypeNames(),
			'kinds' => $this->_kinds,
			'tableConfig' => [
				'url' => '/pk/patients/list',
				'dtDom' => 'lfrtip',
				'fields' => $this->_fields,
				'columns' => $this->_columns,
			],
		];

		$this->render('index', $vars);
	}

	/**
	 * Получение списка заявок
	 */
	public function actionList()
	{
		$request = \Yii::app()->request;

		$lastId = intval($request->getQuery('lastId'));
		$partnerStatus = $request->getQuery('reqStatus');
		$kind = $request->getQuery('reqKind');
		$reqType = $request->getQuery('reqType');
		$dateFrom = $request->getQuery('crDateFrom');
		$dateTill = $request->getQuery('crDateTill');
		$contentType =  $request->getQuery('type', 'json');

		$timeFrom = null;
		$timeTill = null;

		if ($dateFrom) {
			$timeFrom = strtotime($dateFrom);
			if ($dateTill) {
				$timeTill = strtotime($dateTill) + 86400;
			}
		}

		$scopes = [];

		$model = new RequestModel();
		$model
			->with([
					'sector' => [
						'select' => 'sector.id, sector.name',
					],
					'diagnostics' => [
						'select' => 'diagnostics.id, diagnostics.name',
						'with' => [
							'parent' => [
								'select' => 'diagnostics.id, diagnostics.name',
							]
						],
					],
					'city' => [
						'select' => 'city.title',
					],
				]);

		$scopes['byPartner'] = [ $this->_partner->id ];

		if ($partnerStatus != '') {
			$scopes['byPartnerStatus'] = [ $partnerStatus ];
		}
		if ($kind != '') {
			$scopes['byKind'] = [ $kind ];
		}
		if ($reqType != '') {
			$scopes['withTypes'] = [[ $reqType ]];
		}
		if ($lastId > 0) {
			$model->latest($lastId);
		}
		if ($timeFrom) {
			$scopes['createdInInterval'] = [ $timeFrom, $timeTill ];
		}

		$data = [];
		$dataProvider = new \CActiveDataProvider($model, $this->_findParams);
		$dataProvider->setCriteria([ 'scopes' => $scopes ]);
		$dataIterator = new \CDataProviderIterator($dataProvider, 1000);
		foreach ($dataIterator as $item) {
			$data[] = $this->buildDataTableRequestData($item);

			if ($lastId < $item->req_id) {
				$lastId = $item->req_id;
			}
		}

		if ($contentType === 'xls') {
			$report = new LkReportRequests();

			$this->renderExcel($report->excel($data, $this->_columnsExport));
		} else {
			$vars = [
				'data' => $data,
				'lastId' => $lastId,
			];

			$vars += $this->calculateStatistics($timeFrom, $timeTill, $scopes);

			$this->renderJSON($vars);
		}
	}

	/**
	 * @param RequestModel $request
	 *
	 * @return array
	 */
	protected function buildDataTableRequestData(RequestModel $request)
	{
		return [
			'id'            => $request->req_id,
			'created'       => date('d.m.Y H:i', $request->req_created),
			'created_'      => date('Y.m.d H:i', $request->req_created),
			'client_name'   => $request->client_name,
			'client_phone'  => (new Phone($request->client_phone))->prettyFormat('+7 '),
			'speciality'    => $request->getServiceName(),
			'cost'          => $request->partner_cost !== null ? intval($request->partner_cost) : null,
			'parent_status' => $request->partner_status,
			'state'         => isset($this->_requestStates[$request->partner_status])
				? $this->_requestStates[$request->partner_status] : null,
			'reject_reason' => Rejection::getReason($request->reject_reason),
			'kind'          => isset($this->_kinds[$request->kind]) ? $this->_kinds[$request->kind] : null,
			'req_type'      => $request->getTypeName(),
			'city_title'    => $request->city->title,
		];
	}

	/**
	 * Подсчет статистики (количества и стоимости заявок)
	 *
	 * @param string $timeFrom
	 * @param string $timeTill
	 * @param array  $scopes
	 *
	 * @return array
	 */
	protected function calculateStatistics($timeFrom, $timeTill, $scopes)
	{
		$statistic = [
			'from' => date('d.m.Y', $timeFrom),
			'to' => date('d.m.Y', $timeTill - 86400),
		];

		$criteria = new \CDbCriteria([ 'scopes' => $scopes ]);

		$statistic['allCount'] = RequestModel::model()->count($criteria);

		$statistic['total'] = RequestModel::model()->getPartnerSumAndCount($criteria);

		$statistic['totalDoctor'] = RequestModel::model()
			->getPartnerSumAndCount($criteria, ServiceModel::TYPE_SUCCESSFUL_DOCTOR_REQUEST);

		$statistic['totalDiagnosticsMrtKt'] = RequestModel::model()
			->getPartnerSumAndCount($criteria, ServiceModel::TYPE_SUCCESSFUL_DIAGNOSTICS_MRT_OR_KT);

		$statistic['totalDiagnosticsOther'] = RequestModel::model()
			->getPartnerSumAndCount($criteria, ServiceModel::TYPE_SUCCESSFUL_DIAGNOSTICS_OTHER);

		return $statistic;
	}
}
