<?php
namespace dfs\docdoc\back\controllers;

//костыль для генерации отчета
ini_set('max_execution_time', 0);

use dfs\docdoc\models\ClinicContractModel;
use dfs\docdoc\models\ClinicPhotoModel;
use dfs\docdoc\models\ClinicRequestLimitModel;
use dfs\docdoc\models\DiagnosticClinicModel;
use dfs\docdoc\models\ModerationModel;
use dfs\docdoc\models\RequestModel;
use Yii;
use Exception;
use CHtml;
use CHttpException;
use dfs\docdoc\models\ClinicModel;
use dfs\docdoc\models\ContractModel;
use dfs\docdoc\models\ClinicContractCostModel;
use dfs\docdoc\models\ContractGroupModel;

/**
 * Файл класса ClinicController.
 *
 * Контроллер клиник
 *
 * @package dfs.docdoc.back.controllers
 */
class ClinicController extends BackendController
{

	/**
	 * Пересчет стоимости заявок по тарифу за определенный срок
	 */
	public function actionRecalculate()
	{
		$dateFrom = Yii::app()->request->getQuery('dateFrom');
		$dateTo =Yii::app()->request->getQuery('dateTo');
		$clinicId = Yii::app()->request->getQuery('clinicId');

		$clinic = ClinicModel::model()->findByPk($clinicId);
		$clinicRequests = [];
		foreach ($clinic->getClinicContracts() as $tariff) {
			$requestModel = RequestModel::model()
				->origin()
				->inBranches($clinicId)
				->byContract($tariff->contract)
				->betweenBillingDate($dateFrom, $dateTo." 23:59:59", $tariff->contract);

			if (!$tariff->contract->isPayForCall()) {
				$requestModel->inBillingState(RequestModel::BILLING_STATE_RECORD);
			}

			$clinicRequests[$tariff->contract->title] = $requestModel->findAll(['order' => 'request_cost']);
		}

		foreach ($clinicRequests as $requests) {
			foreach ($requests as $request) {
				$request->saveRequestCost();
			}
		}

		$this->render(
			'recalculate',
			array(
				'clinicRequests' => $clinicRequests,
				'dateFrom' => $dateFrom,
				'dateTo' => $dateTo,
			)
		);
	}


	/**
	 * Список клиник, которые находятся в биллинге в период
	 */
	public function actionList()
	{
		$model = new ClinicModel('search');

		$model->unsetAttributes();

		$modelName = CHtml::modelName(ClinicModel::class);
		if (isset($_GET[$modelName])) {
			$model->attributes = $_GET[$modelName];
		}

		$period = $this->getDateParams();

		$this->render(
			'list',
			array(
				'model' => $model,
				'dateFrom' => $period['dateFrom'],
				'dateTo' => $period['dateTo'],
				'periods' => $period['periods'],
			)
		);
	}

	/**
	 * Список клиник, которые находятся в биллинге в период
	 */
	public function actionListDetails()
	{
		$clinic = new ClinicModel('search');
		$model = new ClinicContractCostModel('search');

		$model->unsetAttributes();

		$modelName = CHtml::modelName(ClinicContractCostModel::class);
		if (isset($_GET[$modelName])) {
			$model->attributes = $_GET[$modelName];
		}

		$model->is_active = 1;

		$period = $this->getDateParams();
		$groupType = Yii::app()->request->getQuery('groupType', 0);
		$params = ($groupType == 1) ? ["order" => "t.clinic_contract_id, t.from_num"] : ['group' => "t.clinic_contract_id, t.group_uid", "order" => "t.clinic_contract_id"];

		$totalNum = RequestModel::model()
			->betweenBillingDate($period['dateFrom'], $period['dateTo']." 23:59:59")
			->inBilling()
			->count(["condition" => "request_cost IS NOT NULL"]);

		$totalSum = RequestModel::model()
			->betweenBillingDate($period['dateFrom'], $period['dateTo']." 23:59:59")
			->inBilling()
			->find(["condition" => "request_cost IS NOT NULL", "select" => "*, SUM(request_cost) as request_cost"]);

		$this->render(
			'listDetails',
			array(
				'model' => $model,
				'dateFrom' => $period['dateFrom'],
				'dateTo' => $period['dateTo']." 23:59:59",
				'periods' => $period['periods'],
				'groupType' => $groupType,
				'params' => $params,
				'clinic' => $clinic,
				'totalNum' => $totalNum,
				'totalSum' => $totalSum->request_cost,
			)
		);
	}

	/**
	 * Получает информацию о периоде выборки из запроса
	 *
	 * @return array
	 */
	public function getDateParams()
	{
		$dateFrom = Yii::app()->request->getQuery('dateFrom', date('Y-m-01'));
		$dateTo = Yii::app()->request->getQuery('dateTo', date('Y-m-d', strtotime($dateFrom . 'last day of this month')));

		$firstRequest = RequestModel::model()->find(['order' => 'req_created ASC']);
		$time = $firstRequest->req_created;
		$now = time();
		$periods = [];
		while  ($time < $now) {
			$periods[date('Y-m-d', $time)] = date('m.Y', $time);
			$time = strtotime('next month', $time);
		}

		return ['dateFrom' => $dateFrom, 'dateTo' => $dateTo, 'periods' => $periods];

	}

	/**
	 * Вкладка - Тарифы и реквизиты
	 *
	 * @param integer $id Идентификатор клиники
	 *
	 * @throws \CException
	 */
	public function actionContracts($id)
	{
		$model = ClinicModel::model()->findByPk($id);
		$contractsDict = CHtml::listData(ContractModel::model()->findAll(), 'contract_id', 'title');

		$selectedContracts = [];
		$clinicContracts = [];
		foreach ($model->tariffs as $item) {
			$selectedContracts[$item->contract_id] = ['selected' => 'selected'];
			$clinicContracts[$item->id] = $item->contract->title;
		}

		$this->renderPartial('contracts/index', [
			'model'             => $model,
			'contractsDict'     => $contractsDict,
			'selectedContracts' => $selectedContracts,
			'clinicContracts'   => $clinicContracts,
		]);
	}

	/**
	 * Ставки по выбранному тарифу
	 *
	 * @param integer $id Идентификатор контракта клиники
	 *
	 * @throws \CException
	 */
	public function actionContractCosts($id)
	{
		$clinicContract = ClinicContractModel::model()->findByPk($id);

        if (!$clinicContract) {

            $this->renderPartial('contracts/notfound');
            return;
        }
        
		$costs = $clinicContract->allCostRules;

		$groups = ContractGroupModel::model()->byKind($clinicContract->contract->kind)->findAll();
		$groupDict = !empty($groups) ? CHtml::listData($groups, 'id', 'name') : [];

		$this->renderPartial('contracts/costs', [
			'costs'        => $costs,
			'groupDict'    => $groupDict,
		]);
	}

	/**
	 * Лимиты по выбранному тарифу
	 *
	 * @param int $id Идентификатор контракта клиники
	 */
	public function actionContractGroupLimits($id)
	{
		$clinicContract = ClinicContractModel::model()->findByPk($id);
		$groupLimits = CHtml::listData($clinicContract->requestLimits, 'group_uid', 'limit');

		$groups = [];
		foreach ($clinicContract->contractGroups as $group) {
			$groups[] = [
				'id' => $group->id,
				'name' => $group->name,
				'limit' => isset($groupLimits[$group->id]) ? $groupLimits[$group->id] : 0,
			];
		}

		$this->renderPartial('contracts/groupLimits', [
			'groups' => $groups,
		]);
	}

	/**
	 * Сохранение лимитов по записям клиники
	 */
	public function actionSaveLimits()
	{
		$request = Yii::app()->request;
		$limits = json_decode($request->getParam('limits'), true);

		$clinicContract = ClinicContractModel::model()->findByPk($request->getParam('contract'));
		$clinicContract->saveRequestLimits($limits);

		$this->renderJSON(['status' => 'success']);
	}

	/**
	 * Сохранение тарифов клиники
	 */
	public function actionSaveContracts()
	{
		$request = Yii::app()->request;

		$clinic = ClinicModel::model()->findByPk($request->getParam('clinicId'));
		$clinic->saveTariffs($request->getParam('contracts', []));

		$this->renderJSON(array('status' => 'success'));
	}

	/**
	 * Сохранение тарифных ставок
	 */
	public function actionSaveContractCosts()
	{
		$clinicContractId = Yii::app()->request->getParam('contract');
		$costs = json_decode(Yii::app()->request->getParam('costs'));

		$transaction = Yii::app()->getDb()->beginTransaction();

		try {
			$clinicContract = ClinicContractModel::model()->findByPk($clinicContractId);
			if (is_null($clinicContract)) {
				throw new Exception('Нет такого тарифа!');
			}

			$defaultGroupId = null;
			if ($clinicContract->contract->kind == ContractModel::KIND_DOCTOR) {
				$defaultGroupId = ContractGroupModel::ALL_SECTORS;
			} elseif ($clinicContract->contract->kind == ContractModel::KIND_DIAGNOSTICS) {
				$defaultGroupId = ContractGroupModel::ALL_DIAGNOSTICS;
			}
			ClinicContractCostModel::model()->deleteAllByAttributes(['clinic_contract_id' => $clinicContractId]);
			foreach ($costs as $cost) {
				$costModel = new ClinicContractCostModel();
				$costModel->group_uid = $cost->serviceId ?: $defaultGroupId;
				$costModel->cost = $cost->cost;
				$costModel->clinic_contract_id = $clinicContractId;
				$costModel->from_num = $cost->fromNum;
				$costModel->is_active = $cost->isActive;
				if (!$costModel->save()) {
					throw new Exception("Не удалось сохранить ставку. (ServiceId: {$cost->serviceId}. FromNum: {$cost->fromNum}.");
				}
			}
			$transaction->commit();
			$result = ['status' => 'success'];
		} catch (Exception $e) {
			$transaction->rollback();
			$result = [
				'status' => 'error',
				'message' => $e->getMessage()
			];
		}

		$this->renderJSON($result);
	}

	/**
	 * Сохранение данных о реквизитах и контрактах клиники
	 */
	public function actionSaveDetails()
	{
		$data = Yii::app()->request->getParam('dfs_docdoc_models_ClinicModel');

		$result = ['status' => 'success'];

		$clinicModel = ClinicModel::model()->findByPk($data['id']);
		$clinicModel->contract_signed = $data['contract_signed'];
		if (!$clinicModel->save(false)) {
			$result = [
				'status' => 'error',
				'message' => 'Ошибка при сохранении данных'
			];
		}

		$this->renderJSON($result);
	}

	/**
	 * Автодополнение для поиска по названию клиники
	 */
	public function actionClinicAutocomplete()
	{
		$term = \Yii::app()->request->getQuery('term');

		$items = ClinicModel::model()
			->active()
			->searchByName($term)
			->findAll([ 'order' => 'name', 'limit' => 30 ]);

		$data = [];
		foreach ($items as $item) {
			$data[] = [
				'id' => $item->id,
				'value' => $item->short_name ? $item->short_name : $item->name,
			];
		}

		$this->renderJSON($data);
	}

	/**
	 * Окно с добавлением фотографий для клиники
	 */
	public function actionAddImage()
	{
		$this->renderPartial('addImage');
	}

	/**
	 * Загрузка фотографии для клиники
	 */
	public function actionSaveImage()
	{
		$clinicId = \Yii::app()->request->getQuery('clinicId');

		$photo = null;
		$success = false;

		if ($clinicId && !empty($_FILES)) {
			$photo = new ClinicPhotoModel();

			$uploadFile = \CUploadedFile::getInstanceByName('file');

			$photo->clinic_id = $clinicId;
			$photo->imgPath = $uploadFile;

			if ($photo->save()) {
				$photo->imgPath = $photo->clinic_id . '_' . $photo->img_id . '.' . $uploadFile->getExtensionName();
				if ($uploadFile->saveAs($photo->getFilePath())) {
					$success = $photo->save();
				}
			}
		}

		$this->renderJSON([
			'success' => $success,
			'imgId' => $photo ? $photo->img_id : null,
			'url' => $photo ? $photo->getUrl() : null,
			'errors' => $photo ? $photo->getErrors() : [],
		]);
	}

	/**
	 * Удаление фотографии клиники
	 */
	public function actionDeleteImage()
	{
		$imgId = \Yii::app()->request->getQuery('imgId');

		$photo = ClinicPhotoModel::model()->findByPk($imgId);

		$success = false;

		if ($photo) {
			$filename = $photo->getFilePath();

			$photo->delete();

			if (file_exists($filename)) {
				unlink($filename);
			}

			$success = true;
		}

		$this->renderJSON(['success' => $success]);
	}

	/**
	 * Модерация измененных полей
	 *
	 * @throws \CHttpException
	 */
	public function actionModeration()
	{
		if (!Yii::app()->request->isAjaxRequest) {
			throw new CHttpException(400, 'Invalid request. Please do not repeat this request again.');
		}

		$id = Yii::app()->request->getParam('id');
		$clinic = $id ? ClinicModel::model()->findByPk($id) : null;
		if (!$clinic) {
			throw new CHttpException(400, 'Invalid request. Please do not repeat this request again.');
		}

		$diagnosticClinics = DiagnosticClinicModel::model()
			->with('moderation')
			->inClinics([$id])
			->findAll();

		$diagnosticChanges = [];

		foreach ($diagnosticClinics as $dc) {
			$moderation = ModerationModel::getForRecord($dc);
			$data = $moderation->data;

			$fields = [];

			if (array_key_exists('price', $data)) {
				$fields['price'] = [
					'name' => 'Цена',
					'old'  => $dc->price,
					'new'  => $data['price'],
				];
			}
			if (array_key_exists('special_price', $data)) {
				$fields['special_price'] = [
					'name' => 'Спеццена',
					'old'  => $dc->special_price,
					'new'  => $data['special_price'],
				];
			}

			$diagnosticChanges[$dc->id] = [
				'title' => $dc->diagnostic->getFullName(),
				'fields' => $fields,
				'delete' => $moderation->is_delete,
				'new' => $moderation->is_new,
			];
		}

		$this->renderPartial('moderation', [
			'diagnosticChanges' => $diagnosticChanges,
		]);
	}

	/**
	 * Применение измененний
	 *
	 * @throws \CHttpException
	 */
	public function actionModerationApply()
	{
		$request = Yii::app()->request;
		if (!$request->isAjaxRequest) {
			throw new CHttpException(400, 'Invalid request. Please do not repeat this request again.');
		}

		$errorMsg = null;

		$reset = $request->getParam('reset');
		$apply = $request->getParam('apply');
		$reset = is_array($reset) ? $reset : [];
		$apply = is_array($apply) ? $apply : [];

		if ($reset || $apply) {
			foreach (array_keys($reset + $apply) as $id) {
				$itemApply = isset($apply[$id]) && is_array($apply[$id])? array_keys($apply[$id]) : [];
				$itemReset = isset($reset[$id]) && is_array($reset[$id]) ? array_keys($reset[$id]) : [];

				$dc = DiagnosticClinicModel::model()->findByPk($id);
				$moderation = ModerationModel::getForRecord($dc);

				if (in_array('delete', $itemApply)) {
					$moderation->delete();
					$dc->delete();
				} else {
					$moderation->resetFields($itemReset);
					$moderation->saveWithRecordChanges($dc, $itemApply);
				}
			}
		} else {
			$errorMsg = 'Не найдены изменения';
		}

		$this->renderJSON([
			'success' => $errorMsg === null,
			'errorMsg' => $errorMsg,
		]);
	}

}
