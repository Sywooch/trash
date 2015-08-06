<?php

namespace dfs\docdoc\front\controllers\lk;

use dfs\docdoc\models\ClinicModel;
use dfs\docdoc\models\DiagnosticaModel;
use dfs\docdoc\models\DiagnosticClinicModel;
use dfs\docdoc\models\ModerationModel;
use dfs\docdoc\models\MailQueryModel;
use dfs\docdoc\reports\LkReportDiagnostics;

/**
 * Class DiagnosticsController
 *
 * @package dfs\docdoc\front\controllers\lk
 */
class DiagnosticsController extends FrontController
{
	/**
	 * Дефолтные параметры для поиска
	 *
	 * @var array
	 */
	protected $_findParams = [];

	protected $_fields = [
		'clinic' => [
			'display' => 'clinic_name',
			'type' => 'select',
			'values' => 'branches',
			'label' => 'Филиал',
			'width' => 250,
		],
		'diagnostic' => [
			'display' => 'diagnostic_name',
			'type' => 'select',
			'values' => 'diagnostics',
			'label' => 'Исследование',
			'width' => 250,
		],
		'price' => [
			'editable' => true,
			'label' => 'Цена',
			'type' => 'text',
			'width' => 90,
		],
		'special_price' => [
			'editable' => true,
			'label' => 'Спеццена',
			'type' => 'text',
			'width' => 90,
		],
		'actions' => [
			'emptyData' => true,
			'orderable' => false,
			'label' => 'Действия',
			'defaultContent' => '
				<a class="button_lk state_change__approve edit" href="javascript:void(0);">Редактировать</a>
				<a class="button_lk state_change__approve delete" href="javascript:void(0);">Удалить</a>
			',
			'width' => 150,
		],
	];

	protected $_columns = [ 'clinic', 'diagnostic', 'price', 'special_price', 'actions' ];

	protected $_actions = [
		'create' => [
			'title' => 'Добавление',
			'type' => 'create',
		],
		'edit' => [
			'title' => 'Редатирование',
			'type' => 'edit',
		],
		'delete' => [
			'title' => 'Удаление',
			'type' => 'delete',
		],
	];


	/**
	 * Страница диагностик клиники
	 */
	public function actionIndex()
	{
		$clinics = ClinicModel::model()->withBranches($this->getMainClinicId())->findAll();
		$branches = \CHtml::listData($clinics, 'id', 'shortName');

		$diagnostics = DiagnosticaModel::model()
			->withoutParents()
			->with('parent')
			->findAll(['order' => 'parent.name, t.name']);

		$vars = [
			'tableConfig' => [
				'url' => '/lk/diagnostics/list',
				'urlEdit' => '/lk/diagnostics/change?id=_id_',
				'fields' => $this->_fields,
				'columns' => $this->_columns,
				'actions' => $this->_actions,
				'orderDirection' => 'asc',
				'dtDom' => 'lfrtip',
				'values'  => [
					'diagnostics' => $this->recordsAsLabelValues($diagnostics, 'fullName', 'id'),
					'branches' => $this->recordsAsLabelValues($clinics, 'shortName', 'id'),
				],
			],
			'branches' => $branches,
			'selectedClinic' => $this->_clinic->parent_clinic_id ? $this->_clinic->id : null,
		];
		$this->render('index', $vars);
	}

	/**
	 * Получение списка диагностик
	 */
	public function actionList()
	{
		$r = \Yii::app()->request;
		$contentType = $r->getQuery('type', 'json');
		$clinicId = $r->getQuery('clinic_id');

		$model = DiagnosticClinicModel::model()
			->with('clinic', 'diagnostic')
			->inClinics($this->getClinicBranchIds($clinicId));

		$data = [];
		foreach ($model->findAll($this->_findParams) as $item) {
			if ($item->moderation) {
				$item->moderation->applyChanges($item);
				if ($item->moderation->is_delete) {
					continue;
				}
			}
			$data[] = $this->buildDataTableDiagnosticData($item);
		}

		if ($contentType === 'xls') {
			$report = new LkReportDiagnostics();
			$this->renderExcel($report->excel($data));
		} else {
			$this->renderJSON(['data' => $data]);
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
		$clinicId = isset($data['clinic']) ? $data['clinic'] : null;
		$diagnosticId = isset($data['diagnostic']) ? $data['diagnostic'] : null;

		$item = DiagnosticClinicModel::model()->findByAttributes([
			'clinic_id' => $clinicId,
			'diagnostica_id' => $diagnosticId,
		]);

		$moderation = $item ? ModerationModel::getForRecord($item) : null;

		$diagnostic = DiagnosticaModel::model()->findByPk($diagnosticId);

		if (!$diagnostic || !$this->_clinic->hasBranch($clinicId)) {
			$errorMsg = 'Диагностика не найдена';
		}
		elseif ($item && (($id && $item->id != $id) || (!$id && !$moderation->is_delete))) {
			$item = null;
			$errorMsg = 'Диагностика для клиники уже существует';
		}
		elseif (!$item && $id) {
			$errorMsg = 'Невозможно изменить диагностику или клинику для существующей записи';
		} else {
			if (!$item) {
				$item = new DiagnosticClinicModel();
				$item->clinic_id = $clinicId;
				$item->diagnostica_id = $diagnosticId;
				$item->save();
				$moderation = ModerationModel::getForRecord($item);
				$moderation->is_new = 1;
			}
			$data = array_intersect_key($data, array_flip(['price', 'special_price']));
			$moderation->is_delete = empty(\Yii::app()->request->getPost('delete')) ? 0 : 1;
			if ($moderation->saveChangeData($item, $data)) {
				MailQueryModel::model()->sendEmailChangeClinicDiagnostic($this->_clinic, $item);
			} else {
				$errorMsg = 'Ошибка сохранения: ' . $this->buildErrorMessageByRecord($item);
			}
		}

		$this->renderJSON([
			'success' => $errorMsg === null,
			'errorMsg' => $errorMsg,
			'row' => $item ? $this->buildDataTableDiagnosticData($item) : null,
		]);
	}

	/**
	 * Данные доктора отправляемые в datatables
	 *
	 * @param DiagnosticClinicModel $diagnostic
	 *
	 * @return array
	 */
	protected function buildDataTableDiagnosticData($diagnostic)
	{
		return [
			'id' => $diagnostic->id,
			'clinic' => $diagnostic->clinic_id,
			'clinic_name' => $diagnostic->clinic->getShortName(),
			'diagnostic' => $diagnostic->diagnostica_id,
			'diagnostic_name' => $diagnostic->diagnostic->getFullName(),
			'price' => intval($diagnostic->price),
			'special_price' => intval($diagnostic->special_price),
		];
	}
}
