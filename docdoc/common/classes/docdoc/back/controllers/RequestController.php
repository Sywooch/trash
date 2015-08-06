<?php

namespace dfs\docdoc\back\controllers;

use dfs\docdoc\models\ClinicModel;
use dfs\docdoc\models\UserModel;
use CHttpException;
use dfs\docdoc\models\RequestModel;
use dfs\docdoc\models\RequestRecordModel;
use dfs\docdoc\models\SmsRequestModel;
use Yii;
use dfs\docdoc\models\RequestStationModel;
use dfs\docdoc\models\RequestHistoryModel;

/**
 * Файл класса RequestController.
 *
 * Контроллер заявок
 *
 * @package dfs.docdoc.back.controllers
 */
class RequestController extends BackendController
{
	/**
	 * Филиалы клиник для выбранной аудиозаписи
	 *
	 * @throws \CException
	 * @throws \CHttpException
	 */
	public function actionGetBranches()
	{
		if (!Yii::app()->request->isAjaxRequest) {
			throw new CHttpException(400, 'Invalid request. Please do not repeat this request again.');
		}

		$recordId = Yii::app()->request->getParam('id');
		if (empty($recordId)) {
			throw new CHttpException(400, 'Invalid request. Please do not repeat this request again.');
		}
		$record = RequestRecordModel::model()->findByPk($recordId);

		$clinic = null;

		if (!is_null($record)) {
			$clinic = ClinicModel::model()->findByPk($record->clinic_id);
		}

		if (is_null($clinic)) {
			throw new CHttpException(400, 'Invalid request. Please do not repeat this request again.');
		}

		$parentClinicId = $clinic->parent_clinic_id ?: $clinic->id;
		$clinics = ClinicModel::model()->withBranches($parentClinicId)->findAll();

		$this->renderPartial('branches', compact(
			'clinic',
			'clinics',
			'record'
		));
	}

	/**
	 * Отправка СМС-уведомлений
	 *
	 * @throws \CHttpException
	 */
	public function actionSendSms()
	{
		if (!Yii::app()->request->isAjaxRequest) {
			throw new CHttpException(400, 'Invalid request. Please do not repeat this request again.');
		}

		$requestId = Yii::app()->request->getParam('request');
		$action = Yii::app()->request->getParam('action');
		if (empty($requestId) || empty($action)) {
			throw new CHttpException(400, 'Invalid request. Please do not repeat this request again.');
		}

		$request = RequestModel::model()->findByPk($requestId);

		if ((new SmsRequestModel())->opinionClientNotAvailableMessage($request)) {
			$result = array('status' => 'success');
		} else {
			$result = array(
				'status'  => 'error',
				'message' => 'Не удалось отправить сообщение',
			);
		}

		echo json_encode($result);
	}

	/**
	 * Проверка на изменение статуса и оператора заявки
	 *
	 * @param int $id
	 *
	 * @throws \CHttpException
	 */
	public function actionSwitchToRequest($id)
	{
		$req = \Yii::app()->request;
		$user = \Yii::app()->session['user'];

		if (!$req->isAjaxRequest || !$user) {
			throw new CHttpException(400, 'Invalid request. Please do not repeat this request again.');
		}

		$nameSession = $req->getParam('nameSession', 'params');
		$filters = $req->getParam('filters');
		\Yii::app()->session[$nameSession] = is_array($filters) ? $filters : [];

		$result = null;
		$request = RequestModel::model()->findByPk($id);

		if ($request) {
			$requestUser = UserModel::model()->findByPk($request->req_user_id);

			$result = [
				'id'           => $request->req_id,
				'status'       => $request->req_status,
				'owner'        => $request->req_user_id,
				'ownerName'    => $requestUser ? $requestUser->user_lname . ' ' . $requestUser->user_fname : '',
				'isAssignToMe' => $request->req_user_id == $user->idUser,
			];
		}

		$this->renderJSON($result);
	}

	/**
	 * Производит дублирование заявки
	 *
	 * @param int $id идентификатор заявки для дублирования
	 *
	 * @throws CHttpException
	 *
	 * @return void
	 */
	public function actionDuplicate($id)
	{
		$oldRequest = RequestModel::model()->findByPk($id);
		if (!$oldRequest) {
			throw new CHttpException(404, "Неправильно указана заявка для дублирования");
		}

		$transaction = Yii::app()->getDb()->beginTransaction();

		$newRequest = new RequestModel;
		$newRequest->setScenario(RequestModel::SCENARIO_DUPLICATE);
		$newRequest->attributes = $oldRequest->attributes;
		$newRequest->date_record = null;
		$newRequest->date_admission = null;
		$newRequest->req_status = RequestModel::STATUS_ACCEPT;
		$user = Yii::app()->session["user"];
		if (!$user) {
			$transaction->rollback();
			throw new CHttpException(500, "Не определен оператор");
		}
		$newRequest->req_user_id = $user->idUser;
		$newRequest->is_hot = 0;

		if (!$newRequest->validate()) {
			$errors = [];
			foreach ($newRequest->getErrors() as $err) {
				$errors[] = $err[0];
			}
			exit(json_encode(['error' => implode(', ', $errors)]));
		}

		if (!$newRequest->save()) {
			$transaction->rollback();
			throw new CHttpException(500, "Не удалось сохранить новую заявку");
		}

		foreach ($oldRequest->request_record as $record) {
			$newRecord = new RequestRecordModel;
			$newRecord->setScenario('copy');
			$newRecord->attributes = $record->attributes;
			$newRecord->request_id = $newRequest->req_id;
			if (!$newRecord->save()) {
				$transaction->rollback();
				throw new CHttpException(500, "Не удалось сохранить запись к заявке");
			}
		}

		foreach ($oldRequest->requestStations as $oldRequestStation) {
			$newRequestStation = new RequestStationModel;
			$newRequestStation->attributes = $oldRequestStation->attributes;
			$newRequestStation->request_id = $newRequest->req_id;
			if (!$newRequestStation->save()) {
				$transaction->rollback();
				throw new CHttpException(500, "Не удалось сохранить станции метро к заявке");
			}
		}

		$requestHistory = new RequestHistoryModel;
		$requestHistory->request_id = $newRequest->req_id;
		$requestHistory->action = RequestHistoryModel::LOG_TYPE_COMMENT;
		$requestHistory->user_id = $user->idUser;
		$requestHistory->text = "Скопирована из заявки № {$oldRequest->req_id}";
		if (!$requestHistory->save()) {
			$transaction->rollback();
			throw new CHttpException(500, "Не удалось сохранить комментарий к заявке");
		}

		$transaction->commit();

		$type = Yii::app()->request->getQuery("type");
		if (!$type) {
			$type = "default";
		}

		$url = json_encode([
			'redirectUrl' => "/request/request.htm?type={$type}&id={$newRequest->req_id}"
		]);
		exit($url);
	}

	/**
	 * Возврат заявки обратно в поток
	 *
	 * @param int $id идентификатор заявки
	 */
	public function actionBackToStream($id)
	{
		$req = Yii::app()->request;
		$user = Yii::app()->session['user'];

		$type = $req->getQuery('type');

		$request = RequestModel::model()->findByPk($id);

		if ($request) {
			$status = null;

			$request->setScenario(RequestModel::SCENARIO_OPERATOR);

			switch ($user->operator_stream) {
				case RequestModel::OPERATOR_STREAM_NEW:
					$status = RequestModel::STATUS_NEW;
					break;
				case RequestModel::OPERATOR_STREAM_CALL_LATER:
					$status  = RequestModel::STATUS_CALL_LATER;
					break;
				default: break;
			}

			if ($status !== null) {
				$request->req_status = $status;
				$request->req_user_id = 0;

				if ($request->save()) {
					$request->addHistory('Оператор освободил заявку', RequestHistoryModel::LOG_TYPE_CHANGE_STATUS);
				}
			}
		}

		$this->redirect('/request/index.htm' . ($type ? '?type=' . $type : ''));
	}
}
