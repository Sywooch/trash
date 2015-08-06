<?php

namespace dfs\docdoc\front\controllers\lk;

use dfs\docdoc\models\ClinicModel;
use dfs\docdoc\models\DoctorClinicModel;
use dfs\docdoc\models\DoctorModel;
use dfs\docdoc\models\ModerationModel;
use dfs\docdoc\models\MailQueryModel;
use dfs\docdoc\models\SectorModel;
use dfs\docdoc\reports\LkReportDoctors;

/**
 * Class DoctorsController
 *
 * @package dfs\docdoc\front\controllers\lk
 */
class DoctorsController extends FrontController
{
	/**
	 * Дефолтные параметры для поиска
	 *
	 * @var array
	 */
	protected $_findParams = [];

	protected $_fields = [
		'link' => [
			'editable' => false,
			'orderable' => false,
			'label' => '',
			'width' => 50,
		],
		'image' => [
			'editable' => true,
			'label' => 'Фото',
			'type' => 'image',
			'height' => 120,
		],
		'name' => [
			'editable' => true,
			'type' => 'text',
			'label' => 'Имя врача',
			'width' => 250,
		],
		'clinics' => [
			'display' => 'clinics_text',
			'editable' => true,
			'editBubble' => true,
			'label' => 'Клиники',
			'type' => 'chosen',
			'width' => 170,
			'values' => 'branches',
		],
		'sectors' => [
			'display' => 'sectors_text',
			'editable' => true,
			'editBubble' => true,
			'label' => 'Специальность',
			'type' => 'chosen',
			'width' => 200,
			'values' => 'sectors',
			'max_selected_options' => 3,
		],
		'price' => [
			'editable' => true,
			'label' => 'Стоимость приема',
			'type' => 'text',
			'width' => 90,
		],
		'special_price' => [
			'editable' => true,
			'label' => 'Спеццена приема',
			'type' => 'text',
			'width' => 90,
		],
		'departure' => [
			'display' => 'departure_title',
			'editable' => true,
			'editBubble' => true,
			'label' => 'Прием на дому',
			'type' =>'radio',
			'ipOpts' => [
				[ 'label' => 'Да', 'value' => 1 ],
				[ 'label' => 'Нет', 'value' => 0 ],
			],
			'width' => 50,
		],
		'kids_reception' => [
			'display' => 'kids_reception_title',
			'editable' => true,
			'editBubble' => true,
			'label' => 'Прием детей',
			'type' => 'radio',
			'ipOpts' => [
				[ 'label' => 'Да', 'value' => 1 ],
				[ 'label' => 'Нет', 'value' => 0 ],
			],
			'width' => 50,
		],
		'status' => [
			'display' => 'status_title',
			'editBubble' => true,
			'label' => 'Статус врача',
			'type' => 'radio',
			'ipOpts' => [
				[ 'label' => 'Активен', 'value' => 3 ],
				[ 'label' => 'Скрыт', 'value' => 4 ],
			],
			'width' => 60,
		],
		'schedule' => [
			'emptyData' => true,
			'orderable' => false,
			'label' => 'Расписание',
			'width' => 100,
		],
		'actions' => [
			'emptyData' => true,
			'orderable' => false,
			'label' => 'Действия',
			'defaultContent' => '<a class="button_lk state_change__approve edit" href="javascript:void(0);">Редактировать</a>',
			'width' => 100,
		],
	];

	protected $_columns = [ 'clinics', 'link', 'name', 'sectors', 'price', 'special_price', 'departure', 'kids_reception', 'status', 'schedule', 'actions' ];

	protected $_actions = [
		'create' => [
			'title' => 'Добавление',
			'type' => 'create',
		],
		'edit' => [
			'title' => 'Редатирование',
			'type' => 'edit',
		],
	];

	/**
	 * Страница докторов клиники
	 */
	public function actionIndex()
	{
		$clinics = ClinicModel::model()->withBranches($this->getMainClinicId())->findAll();
		$branches =\CHtml::listData($clinics, 'id', 'shortName');

		$sectors = SectorModel::model()->findAll(['order' => 't.name']);

		$statuses = [];
		foreach ($this->_fields['status']['ipOpts'] as $st) {
			$statuses[$st['value']] = $st['label'];
		}

		$vars = [
			'tableConfig' => [
				'url' => '/lk/doctors/list',
				'urlEdit' => '/lk/doctors/change?id=_id_',
				'fields' => $this->_fields,
				'columns' => $this->_columns,
				'actions' => $this->_actions,
				'orderDirection' => 'asc',
				'dtDom' => 'lfrtip',
				'values'  => [
					'branches' => $branches,
					'sectors' => \CHtml::listData($sectors, 'id', 'name'),
				],
			],
			'branches' => $branches,
			'selectedClinic' => $this->_clinic->parent_clinic_id ? $this->_clinic->id : null,
			'statuses' => $statuses,
		];
		$this->render('index', $vars);
	}

	/**
	 * Получение списка докторов
	 */
	public function actionList()
	{
		$r = \Yii::app()->request;

		$contentType =  $r->getQuery('type', 'json');
		$status = $r->getQuery('status');
		$clinicId = $r->getQuery('clinic_id');

		$statuses = !empty($status) ? [$status] : [ DoctorModel::STATUS_NEW, DoctorModel::STATUS_ACTIVE, DoctorModel::STATUS_BLOCKED, DoctorModel::STATUS_MODERATED ];

		$model = DoctorModel::model()
			->inClinics($this->getClinicBranchIds($clinicId))
			->inStatuses($statuses);

		$data = [];
		foreach ($model->findAll($this->_findParams) as $item) {
			if ($item->moderation) {
				$item->moderation->applyChanges($item);
			}
			$data[] = $this->buildDataTableDoctorData($item, $clinicId ?: $this->_clinic->id);
		}

		if ($contentType === 'xls') {
			$report = new LkReportDoctors();
			$this->renderExcel($report->excel($data));
		} else {
			$this->renderJSON([
				'data' => $data,
			]);
		}
	}

	/**
	 * Изменение свойств и статуса заявки
	 *
	 * @param int $id
	 */
	public function actionChange($id)
	{
		$errorMsg = null;
		$data = \Yii::app()->request->getPost('data');

		$id = intval($id);

		$doctor = DoctorModel::model()
			->inClinics($this->getClinicBranchIds())
			->findByPk($id);

		if (!$doctor && $id) {
			$errorMsg = 'Врач не найден';
		}
		elseif (!is_array($data)) {
			$errorMsg = 'Не установленны данные для изменения';
		} else {
			$data = array_intersect_key($data, array_flip([ 'name', 'price', 'special_price', 'departure', 'kids_reception', 'status', 'sectors', 'clinics' ]));

			if (isset($data['departure']) && $data['departure'] === '') {
				unset($data['departure']);
			}
			if (isset($data['kids_reception']) && $data['kids_reception'] === '') {
				unset($data['kids_reception']);
			}
			if (isset($data['status']) && $data['status'] === '') {
				unset($data['status']);
			}

			$isNew = false;

			if (!$doctor) {
				$doctor = new DoctorModel();
				$doctor->status = DoctorModel::STATUS_NEW;
				$doctor->name = empty($data['name']) ? null : $data['name'];
				$isNew = true;
				$doctor->save();
			}

			$clinics = $this->checkDoctorClinics($doctor, isset($data['clinics']) ? $data['clinics'] : null);

			if ($clinics === null) {
				unset($data['clinics']);
			} else {
				$data['clinics'] = array_keys($clinics);
			}

			if ($isNew) {
				$doctor->saveRelationClinics($clinics ?: [$this->_clinic]);
			}

			$moderation = ModerationModel::getForRecord($doctor);

			if ($isNew) {
				$moderation->is_new = true;
			}

			if ($moderation->saveChangeData($doctor, $data)) {
				MailQueryModel::model()->sendEmailChangeDoctor($this->_clinic, $doctor);
			} else {
				$errorMsg = 'Ошибка сохранения: ' . $this->buildErrorMessageByRecord($doctor);
			}
		}

		$this->renderJSON([
			'success' => $errorMsg === null,
			'errorMsg' => $errorMsg,
			'row' => $doctor ? $this->buildDataTableDoctorData($doctor, $this->_clinic->id) : null,
		]);
	}

	/**
	 * Проверка редактируемых клиник для доктора
	 *
	 * @param DoctorModel $doctor
	 * @param array $data
	 *
	 * @return array
	 */
	protected function checkDoctorClinics(DoctorModel $doctor, $data)
	{
		$mainClinicId = $this->getMainClinicId();

		$select = [];
		$current = [];
		$another = [];

		foreach ($doctor->clinics as $clinic) {
			if ($clinic->id == $mainClinicId || $clinic->parent_clinic_id == $mainClinicId) {
				$current[$clinic->id] = $clinic;
			} else {
				$another[$clinic->id] = $clinic;
			}
		}

		if ($data && is_array($data)) {
			$selectClinics = ClinicModel::model()->findAllByPk($data);

			foreach ($selectClinics as $clinic) {
				if ($clinic->id == $mainClinicId || $clinic->parent_clinic_id == $mainClinicId) {
					$select[$clinic->id] = $clinic;
				}
			}
		}

		if (!$select) {
			$select[$this->_clinic->id]	= $this->_clinic;
		}

		if (!array_diff_key($select, $current)) {
			return null;
		}

		return $select + $another;
	}

	/**
	 * Данные доктора отправляемые в datatables
	 *
	 * @param DoctorModel $doctor
	 * @param int $clinicId
	 *
	 * @return array
	 */
	protected function buildDataTableDoctorData($doctor, $clinicId)
	{
		$mainClinicId = $this->getMainClinicId();

		$specialities = [];
		if ($doctor->sectors) {
			foreach ($doctor->sectors as $sector) {
				$specialities[$sector->id] = $sector->name;
			}
		}

		$clinics = [];
		foreach ($doctor->clinics as $clinic) {
			if ($clinic->id == $mainClinicId || $clinic->parent_clinic_id == $mainClinicId) {
				$clinics[$clinic->id] = $clinic->getShortName();
			}
		}

		return [
			'id' => $doctor->id,
			'clinicId' => $clinicId,
			'link' => '<a href="http://' . \Yii::app()->params['hosts']['front'] . '/doctor/' . $doctor->rewrite_name . '" target="_blank"><img src="'.$doctor->getImg('small').'" height="50"/></a>',
			'name' => $doctor->name,
			'sectors' => array_keys($specialities),
			'sectors_text' => implode(', ', $specialities),
			'price' => $doctor->price,
			'special_price' => $doctor->special_price,
			'departure' => $doctor->departure,
			'departure_title' => $doctor->departure ? 'Да' : 'Нет',
			'kids_reception' => $doctor->kids_reception,
			'kids_reception_title' => $doctor->kids_reception ? 'Да' : 'Нет',
			'status' => $doctor->status,
			'status_title' => $doctor->status == DoctorModel::STATUS_ACTIVE ? 'Активен' : 'Скрыт',
			'schedule' => true,
			'image' => $doctor->getImg('small'),
			'clinics' => array_keys($clinics),
			'clinics_text' => implode(', <br/>', $clinics),
		];
	}
}
