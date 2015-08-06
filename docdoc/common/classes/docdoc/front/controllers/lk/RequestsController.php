<?php

namespace dfs\docdoc\front\controllers\lk;

use dfs\docdoc\models\ClinicContractModel;
use dfs\docdoc\models\PartnerModel;
use dfs\docdoc\models\RequestModel;
use dfs\docdoc\models\ClinicModel;
use dfs\docdoc\models\ContractModel;
use dfs\docdoc\models\DiagnosticaModel;
use dfs\docdoc\objects\Rejection;
use dfs\docdoc\objects\Phone;
use dfs\docdoc\reports\LkReportRequests;
use dfs\docdoc\extensions\TextUtils;


/**
 * Class RequestsController
 *
 * @package dfs\docdoc\front\controllers\lk
 */
class RequestsController extends FrontController
{
	/**
	 * Список периодов для выбора
	 *
	 * @var array
	 */
	protected $_requestPeriod = [
		'today'     => ['title' => 'сегодня', 'date' => 'now'],
		'yesterday' => ['title' => 'вчера', 'date' => '-1 day', 'dateEnd' => '-1 day'],
		'week'      => ['title' => 'за неделю', 'date' => '-1 week'],
		'month'     => ['title' => 'за месяц', 'date' => '-1 month'],
		'quarter'   => ['title' => 'за квартал', 'date' => '-3 month'],
		'prevMonth' => [
			'title'   => 'предыдущий отчетный период',
			'date'    => 'first day of previous month',
			'dateEnd' => 'last day of previous month'
		],
	];

	/**
	 * Возможные состояния заявок по тарифным группам (фильтры)
	 *
	 * @var array
	 */
	protected $_requestStatesByPayGroup = [
		ContractModel::PAY_GROUP_RECORD => [ RequestModel::BILLING_STATE_RECORD, RequestModel::BILLING_STATE_REFUSED ],
		ContractModel::PAY_GROUP_VISIT  => [ RequestModel::BILLING_STATE_CAME, RequestModel::BILLING_STATE_REFUSED ],
		ContractModel::PAY_GROUP_CALL   => [],
	];

	/**
	 * Список статусов заявки
	 *
	 * @var array
	 */
	protected $_requestStates = [
		RequestModel::BILLING_STATE_NEW     => [
			'title'  => 'Новые',
			'status' => 'Новая',
			'name'   => 'new',
			'class'  => 'transferred',
		],
		RequestModel::BILLING_STATE_RECORD  => [
			'title'  => 'Записанные',
			'status' => 'Запись',
			'name'   => 'record',
			'class'  => 'completed',
		],
		RequestModel::BILLING_STATE_CAME    => [
			'title'  => 'Подтвержденные',
			'status' => 'Подтверждена',
			'name'   => 'came',
			'class'  => 'completed',
		],
		RequestModel::BILLING_STATE_REFUSED => [
			'title'  => 'Отклонённые',
			'status' => 'Отказ',
			'name'   => 'refused',
			'class'  => 'declined',
		],
		RequestModel::BILLING_STATE_REJECT  => [
			'title'  => 'Отклонённые',
			'status' => 'Отказ',
			'name'   => 'reject',
			'class'  => 'declined',
		],
	];

	protected $_fields = [
		'id' => [
			'sort' => 'sortByNew',
			'label' => 'Номер',
			'width' => 45,
		],
		'billing_date' => [
			'sort' => 'billing_date_',
			'label' => 'Дата создания записи',
			'width' => 100,
		],
		'date_admission' => [
			'sort' => 'date_admission_',
			'label' => 'Дата приёма',
			'type' => 'text',
			'className' => 'DateTimePicker',
			'width' => 110,
		],
		'client_name' => [
			'editable' => true,
			'type' => 'text',
			'label' => 'Пациент',
			'width' => 250,
		],
		'req_type' => [
			'display' => 'req_type_name',
			'orderable' => false,
			'type' => 'select',
			'label' => 'Тип',
			'width' => 20,
			'values' => 'reqTypes',
		],
		'client_phone' => [
			'editable' => true,
			'type' => 'text',
			'label' => 'Телефон',
			'width' => 100,
		],
		'phones' => [
			'type' => 'text',
			'label' => 'Телефон',
			'width' => 100,
		],
		'req_doctor_id' => [
			'display' => 'req_doctor_name',
			'label' => 'Врач',
			'type' => 'select',
			'values' => 'doctors',
			'width' => 200,
		],
		'req_sector_id' => [
			'display' => 'req_sector_name',
			'label' =>  'Специальность',
			'type' => 'select',
			'values' => 'sectors',
		],
		'diagnostics_id' => [
			'editable' => true,
			'display' => 'diagnostics_name',
			'label' => 'Услуга',
			'type' => 'select',
			'values' => 'diagnostics',
		],
		'cost' => [
			'display' => 'cost_title',
			'sort' => 'cost',
			'label' => 'Цена за заявку',
			'width' => 50,
		],
		'processing_time' => [
			'label' => 'Время обработки',
			'width' => 50,
		],
		'status' => [
			'emptyData' => true,
			'orderable' => false,
			'label' => 'Статус',
			'width' => 170,
		],
		'reject_reason' => [
			'label' => 'Причина отказа',
			'type' => 'select',
			'values' => 'rejectReasons',
		],
		'action_type' => [
			'type' => 'hidden',
		],
		'branch' => [
			'label' => 'Филиал',
			'width' => 170,
		],
	];

	protected $_actions = [
		'change'  => [
			'title'  => 'Изменить',
			'fields' => ['date_admission'],
			'state'  => RequestModel::BILLING_STATE_RECORD,
		],
		'accept'  => [
			'title'  => 'Подтвердить',
			'fields' => ['date_admission'],
			'state'  => RequestModel::BILLING_STATE_RECORD,
		],
		'came'    => [
			'title'  => 'Подтвердить заявку',
			'fields' => ['date_admission'],
			'state'  => RequestModel::BILLING_STATE_CAME,
		],
		'refused' => [
			'title'  => 'Отклонить заявку',
			'fields' => ['reject_reason'],
			'state'  => RequestModel::BILLING_STATE_REFUSED,
		],
	];

	protected $_rejectReasons = [
		[ 'value' => Rejection::REASON_NOT_COME, 'label' => 'Не был на приеме' ],
		[ 'value' => Rejection::REASON_NOT_NEED, 'label' => 'В услуге больше не нуждается' ],
		[ 'value' => Rejection::REASON_OTHER, 'label' => 'Другое' ],
	];

	protected $_rejectReasonsOnline = [
		[ 'value' => Rejection::REASON_CLIENT_NOT_ANSWER, 'label' => 'Клиент не отвечает' ],
		[ 'value' => Rejection::REASON_NO_SUCH_SERVICE, 'label' => 'Нет такой услуги' ],
		[ 'value' => Rejection::REASON_NOT_NEED, 'label' => 'В услуге больше не нуждается' ],
		[ 'value' => Rejection::REASON_PRICE, 'label' => 'Не устроила цена' ],
		[ 'value' => Rejection::REASON_TIME, 'label' => 'Не устроило время/дата' ],
		[ 'value' => Rejection::REASON_REFINE, 'label' => 'Уточнение данных' ],
		[ 'value' => Rejection::REASON_OTHER, 'label' => 'Другое' ],
	];

	protected $_tariffGroups = [
		'doctor' => [
			'title' => 'Пациенты',
			'columns' => [ 'id', 'billing_date', 'date_admission', 'client_name', 'phones', 'req_doctor_id', 'req_sector_id', 'cost', 'status' ],
			'columnsExport' => [ 'id', 'billing_date', 'date_admission', 'client_name', 'phones', 'req_doctor_name', 'req_sector_name', 'cost_title', 'state_title' ],
		],
		'diagnostic' => [
			'title' => 'Заявки на диагностику',
			'columns' => [ 'id', 'billing_date', 'date_admission', 'req_type', 'client_name', 'phones', 'diagnostics_id', 'cost', 'processing_time', 'status' ],
			'columnsExport' => [ 'id', 'billing_date', 'date_admission', 'req_type_name', 'client_name', 'phones', 'diagnostics_name', 'cost_title', 'state_title' ],
		],
	];

	/**
	 * Дефолтные параметры для поиска
	 *
	 * @var array
	 */
	protected $_findParams = [
		'criteria' => [
			'order' => 't.req_id DESC',
		],
	];

	/**
	 * Сообщение об ошибки
	 *
	 * @var string
	 */
	protected $errorMsg = null;


	/**
	 * Страница заявок
	 *
	 * @param string $kind
	 *
	 * @throws \CException
	 * @throws \CHttpException
	 */
	public function actionIndex($kind)
	{
		if (!isset($this->_tariffGroups[$kind])) {
			throw new \CHttpException(404, 'Неверный тип страницы заявок');
		}

		$params = $this->_tariffGroups[$kind];

		if ($this->_clinic->branches) {
			$params["columns"][] = "branch";
		}

		$tariffs = $this->_clinic->getContractsByKind($this->kindConstant($kind));

		if ($tariffs) {
			$diagnostics = [];
			if ($kind === 'diagnostic') {
				foreach (DiagnosticaModel::getListDiagnostics() as $id => $name) {
					$diagnostics[] = [ 'label' => $name, 'value' => $id ];
				}
			}

			$states = array_intersect_key($this->_requestStates, $this->billingStatesByTariffs($tariffs));

			if (isset($tariffs[ContractModel::TYPE_DIAGNOSTIC_ONLINE])) {
				$states = [
					'needProcessing' => [
						'title'  => 'Требующие ответа',
						'name'   => 'needProcessing',
						'class'  => 'transferred',
					]
				] + $states;
			}

			if (empty($this->_clinic->parent_clinic_id)) {
				$parentId = $this->_clinic->id;
				$selectedClinic = null;
			} else {
				$parentId = $this->_clinic->parent_clinic_id;
				$selectedClinic = $this->_clinic->id;
			}
			$clinics = ClinicModel::model()->withBranches($parentId)->findAll();

			$vars = [
				'title'          => $params['title'],
				'defaultPeriod'  => 'week',
				'branches'       => \CHtml::listData($clinics, 'id', 'shortName'),
				'selectedClinic' => $selectedClinic,
				'needProcessing' => isset($tariffs[ContractModel::TYPE_DIAGNOSTIC_ONLINE]),
				'requestPeriod'  => $this->_requestPeriod,
				'requestStates'  => $states,
				'tableConfig'    => [
					'url'     => '/lk/requests/list?kind=' . $kind,
					'urlEdit' => '/lk/requests/change?id=_id_',
					'dtDom'   => 'lfrtip',
					'fields'  => $this->_fields,
					'columns' => $params['columns'],
					'actions' => $this->_actions,
					'isInlineEdit' => false,
					'trackingChanges' => 30000, // проверка новых заявок каждые 30 с.
					'values'  => [
						'rejectReasons' => isset($tariffs[ContractModel::TYPE_DIAGNOSTIC_ONLINE]) ? $this->_rejectReasonsOnline : $this->_rejectReasons,
						'diagnostics'   => $diagnostics,
					],
				],
			];

			$this->render('index', $vars);
		} else {
			$this->render('noContract', [ 'title' => $params['title'] ]);
		}
	}

	/**
	 * Получение списка заявок
	 * Фильтры:
	 *        - по состоянию в биллинге (reqStatus)
	 *        - по дате (crDateFrom, crDateTill)
	 *        - получение новых заявок (lastId)
	 *
	 * @param string $kind
	 *
	 * @throws \CException
	 */
	public function actionList($kind)
	{
		$request = \Yii::app()->request;

		$lastId = intval($request->getQuery('lastId'));
		$billingState = $request->getQuery('reqStatus');
		$online = $request->getQuery('online');
		$booking = $request->getQuery('booking');
		$dateFrom = $request->getQuery('crDateFrom');
		$dateTill = $request->getQuery('crDateTill');
		$clinicId = $request->getQuery('clinic_id');
		$contentType =  $request->getQuery('type', 'json');

		$checkLast = $lastId > 0;

		$timeFrom = null;
		$timeTill = null;

		if ($dateFrom) {
			$timeFrom = strtotime($dateFrom);
			if ($dateTill) {
				$timeTill = strtotime($dateTill) + 86400;
			}
		}

		$model = RequestModel::model()
			->byKind($this->kindConstant($kind))
			->inStatuses([ RequestModel::STATUS_REMOVED ], true)
			->with([
					'doctor' => [ 'select' => 'doctor.id, doctor.name' ],
					'sector' => [ 'select' => 'sector.id, sector.name' ],
					'clinic' => [ 'select' => 'clinic.id, clinic.name, clinic.parent_clinic_id, clinic.status' ],
					'activeBooking' => [ 'select' => 'activeBooking.id, activeBooking.status' ],
				]);

		if ($billingState !== null && $billingState !== '') {
			if ($billingState === 'needProcessing') {
				$model->needProcessing($this->_clinic->getEndWorkTime(time()));
				$online = 'yes';
			} else {
				$model->inBillingState($billingState, true);
			}
		}
		if ($online) {
			if ($online === 'yes') {
				$model->withTypes([ RequestModel::TYPE_ONLINE_RECORD ]);
			} elseif ($online === 'no') {
				$model->withTypes([ RequestModel::TYPE_ONLINE_RECORD ], true);
			}
		}
		if ($booking) {
			if ($booking === 'yes') {
				$model->isBooking(true);
			} elseif ($booking === 'no') {
				$model->isBooking(false);
			}
		}
		if ($clinicId) {
			$model->inClinic($clinicId);
		} else {
			$model->inBranches(empty($this->_clinic->parent_clinic_id) ? $this->_clinic->id : $this->_clinic->parent_clinic_id);
		}
		if ($checkLast) {
			$model->latest($lastId);
		}
		if ($timeFrom) {
			$model->betweenBillingDate(date('Y-m-d', $timeFrom), date('Y-m-d', $timeTill));
		}

		// 112014
		$model->origin();

		$endWorkTime = $this->_clinic->getEndWorkTime(time());

		$processingTime = 0;
		$processingCount = 0;
		$totalCount = 0;
		$totalCost = 0;
		$onlineCount = 0;
		$onlineSuccessCount = 0;

		$rows = [];
		$dataProvider = new \CActiveDataProvider($model, $this->_findParams);
		$dataIterator = new \CDataProviderIterator($dataProvider, 1000);
		foreach ($dataIterator as $request) {
			$item = $this->buildDataTableRequestData($request, $endWorkTime);
			if (!$item) {
				continue;
			}

			$rows[] = $item;

			if ($lastId < $request->req_id) {
				$lastId = $request->req_id;
			}

			if ($request->processing_time) {
				$processingTime += $request->processing_time;
				$processingCount++;
			}

			if ($request->req_type == RequestModel::TYPE_ONLINE_RECORD) {
				$onlineCount++;
				if ($request->processing_time && $request->processing_time <= 900) {
					$onlineSuccessCount++;
				}
			}

			if ($request->request_cost > 0) {
				$totalCount++;
				$totalCost += $request->request_cost;
			}
		}

		if ($contentType === 'xls') {
			$params = $this->_tariffGroups[$kind];

			if ($this->_clinic->branches) {
				$params['columnsExport'][] = "branch";
			}

			$report = new LkReportRequests();
			$report->title = $params['title'];

			$this->renderExcel($report->excel($rows, $params['columnsExport']));
		} else {
			$vars = [
				'data' => $rows,
				'lastId' => $lastId,
			];

			if (!$checkLast) {
				$vars['avgProcessingTime'] = $processingCount ? TextUtils::timePeriod(intval($processingTime / $processingCount)) : null;
				$vars['totalCount'] = $totalCount;
				$vars['totalCost'] = $totalCost;
				$vars['onlineSuccessCount'] = $onlineSuccessCount;
				$vars['onlineCount'] = $onlineCount;
			}

			$this->renderJSON($vars);
		}
	}

	/**
	 * Изменение свойств и статуса заявки
	 *
	 * @param string $id
	 *
	 * @throws \CException
	 */
	public function actionChange($id)
	{
		$this->errorMsg = null;
		$data = \Yii::app()->request->getPost('data');
		$endWorkTime = $this->_clinic->getEndWorkTime(time());

		$request = RequestModel::model()
			->inBranches($this->_clinic->id)
			->findByPk($id);

		if (!$request) {
			$this->errorMsg = 'Заявка не найдена';
		}
		elseif (!is_array($data) || !$data) {
			$this->errorMsg = 'Неверный запрос';
		}
		else {
			$request->setScenario(RequestModel::SCENARIO_LK_CLINIC);
			$result = null;

			if (!empty($data['action_type'])) {
				$result = $this->changeRequestState($request, $data['action_type'], $data);
			} else {
				$request->setAttributes($data);
				$result = $request->save();
			}

			if ($result === null) {
				$this->errorMsg = 'Не верно переданы данные';
			}
			elseif (!$result && !$this->errorMsg) {
				$this->errorMsg = 'Ошибка сохранения заявки: ' . self::buildErrorMessageByRecord($request);
			}
		}

		$this->renderJSON([
			'success' => $this->errorMsg === null,
			'errorMsg' => $this->errorMsg,
			'row' => $request ? $this->buildDataTableRequestData($request, $endWorkTime) : null,
		]);
	}


	/**
	 * Действие для изменения статуса заявки
	 */
	public function actionChangeState()
	{
		$appRequest = \Yii::app()->request;

		$action = $appRequest->getQuery('action');

		if (!isset($this->_actions[$action])) {
			throw new \CHttpException(404, 'Неверное действие');
		}

		$request = RequestModel::model()
			->inBranches($this->_clinic->id)
			->findByPk($appRequest->getQuery('requestId'));

		if (!$request) {
			throw new \CHttpException(404, 'Заявка не найдена');
		}

		$request->setScenario(RequestModel::SCENARIO_LK_CLINIC);

		$data = [
			'reject_reason' => $appRequest->getPost('reject_reason'),
			'date_admission' => $appRequest->getPost('date_admission'),
		];

		if ($data['reject_reason'] || $data['date_admission']) {
			if ($this->changeRequestState($request, $action, $data)) {
				$url = $request->kind == RequestModel::KIND_DIAGNOSTICS ? '/lk/drequest' : '/lk/patients';
				if ($request->req_type == RequestModel::TYPE_ONLINE_RECORD) {
					$url .= '?online=yes';
				}
				$this->redirect($url);
			}
		} elseif (!$request->processing_time) {
			if ($action === 'refused' && !$data['reject_reason']) {
				$data['reject_reason'] = Rejection::REASON_OTHER;
			}
			$this->changeRequestState($request, $action, $data);
		} else {
			if ($action === 'refused' && $request->req_created <= $this->_clinic->getEndWorkTime(time())) {
				$this->errorMsg = 'Истекло время обработки заявки';
			}
		}

		$this->render('request', [
			'request' => $request,
			'action' => $action,
		]);
	}


	/**
	 * Изменение статуса заявки
	 *
	 * @param RequestModel $request
	 * @param string       $action
	 * @param array        $data
	 *
	 * @return bool|null
	 */
	protected function changeRequestState(RequestModel $request, $action, array $data)
	{
		$result = null;

		if (isset($this->_actions[$action])) {
			$data['date_admission'] = empty($data['date_admission'])
				? ($request->date_admission ?: strtotime('now'))
				: strtotime($data['date_admission']);

			if ($request->req_type == RequestModel::TYPE_ONLINE_RECORD) {
				if ($request->req_created > $this->_clinic->getEndWorkTime(time())) {
					if (!$request->processing_time) {
						$limit = \Yii::app()->params['RequestProcessingTimeLimit'];
						$time = time() - $request->req_created;
						if ($time > $limit) {
							$time = $limit;
						}
						$request->processing_time = $time < 1 ? 1 : $time;
					}
				}
				elseif ($action === 'refused') {
					$this->errorMsg = 'Истекло время обработки заявки';
					$result = false;
				}
			}

			if ($result === null) {
				$result = $request->saveBillingState($this->_actions[$action]['state'], $data);
			}
		}

		return $result;
	}

	/**
	 * Получить константу по названию
	 *
	 * @param $kind
	 *
	 * @return int
	 */
	protected function kindConstant($kind)
	{
		return $kind === 'diagnostic' ? RequestModel::KIND_DIAGNOSTICS : RequestModel::KIND_DOCTOR;
	}

	/**
	 * @param ClinicContractModel[] $tariffs
	 *
	 * @return array
	 */
	protected function billingStatesByTariffs($tariffs)
	{
		$states = [];

		foreach ($tariffs as $tariff) {
			$payGroup = $tariff->contract->getPayGroup();

			foreach ($this->_requestStatesByPayGroup[$payGroup] as $state) {
				$states[$state] = $state;
			}
		}

		return $states;
	}

	/**
	 * Формирование данных для таблицы
	 *
	 * @param RequestModel $request
	 *
	 * @return array
	 */
	protected function buildDataTableRequestData(RequestModel $request, $endWorkTime)
	{
		$contract = $request->getContract();
		if (!$contract) {
			return null;
		}
		$payGroup = $contract->getPayGroup();
		$state = $request->getClinicBillingState();
		$isRefused = $request->isRefused();

		$records = [];
		foreach ($request->request_record as $record) {
			if (
				($contract->isPayForCall()) ||
				($contract->isPayForRecord() && $record->wasAppointment()) ||
				($isRefused && $record->wasVisit())
			) {
				$records[] = [
					'url' => $record->getUrl(),
					'duration' => $contract->isPayForCall() ? $record->duration : ''
				];
			}
		}

		$cost = intval($request->request_cost);

		$clientPhone = (new Phone($request->client_phone))->prettyFormat('+7 ');
		$addClientPhone = (new Phone($request->add_client_phone))->prettyFormat('+7 ');
		if ($addClientPhone === $clientPhone) {
			$addClientPhone = '';
		}

		if ($contract->isPayForVisit() && $request->date_record) {
			//для дошедших показываем дату записи
			$billingDate = date('d.m.Y H:i', strtotime($request->date_record));
			$billingDateForSort = date('Y.m.d H:i', strtotime($request->date_record));
		} else {
			$billingDate = $request->getBillingDate('d.m.Y H:i');
			$billingDateForSort = $request->getBillingDate('Y.m.d H:i');
		}

		$isOnline = $request->req_type == RequestModel::TYPE_ONLINE_RECORD;
		$needProcessing = $isOnline && $request->isNeedProcessing($endWorkTime);

		if ($needProcessing) {
			$state = RequestModel::BILLING_STATE_NEW;
		}

		$stateData = isset($this->_requestStates[$state]) ? $this->_requestStates[$state] : null;

		return [
			'id'               => $request->req_id,
			'sortByNew'        => ($needProcessing ? '1.' : '0.') . $request->req_id,
			'stateNum'         => $state,
			'billing_date'     => $billingDate,
			'billing_date_'    => $billingDateForSort,
			'client_name'      => $request->client_name,
			'client_phone'     => $clientPhone,
			'phones'           => $clientPhone . ($clientPhone && $addClientPhone ? ', ' : '') . $addClientPhone,
			'req_type'         => $request->req_type,
			'req_type_name'    => $request->getTypeName(),
			'req_doctor_id'    => $request->req_doctor_id,
			'req_doctor_name'  => $request->doctor ? $request->doctor->name : '',
			'req_sector_id'    => $request->req_sector_id,
			'req_sector_name'  => $request->sector ? $request->sector->name : '',
			'diagnostics_id'   => $request->diagnostics_id,
			'diagnostics_name' => $request->diagnostics ? $request->diagnostics->getFullName() : null,
			'cost'             => $cost,
			'cost_title'       => $request->partner_id == PartnerModel::YANDEX_ID ? '0 (Яндекс)' : $cost,
			'state'            => $stateData,
			'date_admission'   => $request->date_admission ? date('d.m.Y H:i', $request->date_admission) : '',
			'date_admission_'  => date('Y.m.d H:i', $request->date_admission),
			'reject_reason'    => $request->reject_reason,
			'records'          => $records,
			'payGroup'         => $payGroup,
			'processing_time'  => $isOnline ?
				($request->processing_time ?
					TextUtils::timePeriod($request->processing_time) :
					($needProcessing ? null : '<span class="warn">больше 20 минут</span>')
				) :
				null,
			'booking_id'       =>
				$request->activeBooking && !$request->activeBooking->isReserve() ? $request->activeBooking->id : null,
			'actions'          => [
				'change'  => $isOnline && !$needProcessing,
				'accept'  => $needProcessing,
				'came'    => $payGroup == ContractModel::PAY_GROUP_VISIT && !$request->isCame(),
				'refused' => $needProcessing || ($payGroup == ContractModel::PAY_GROUP_VISIT && $request->isCame()),
			],
			'DT_RowClass'      => $needProcessing ? 'new' : null,
			'branch'           => $request->clinic->getShortName(),
		];
	}
}
