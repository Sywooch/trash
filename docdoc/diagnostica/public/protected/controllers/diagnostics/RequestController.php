<?php

use dfs\docdoc\models\RequestModel;
use dfs\docdoc\models\ClientModel;
use dfs\docdoc\models\ClinicModel;
use dfs\docdoc\models\DiagnosticaModel;
use dfs\docdoc\objects\Phone;
use dfs\docdoc\exceptions\SpamException;
use dfs\docdoc\models\PartnerModel;


/**
 * Class RequestController
 */
class RequestController extends FrontendController
{
	/**
	 * Сохранение заявки
	 *
	 * @throws CHttpException
	 */
	public function actionSave()
	{
		if (!Yii::app()->request->isAjaxRequest) {
			throw new CHttpException(400, 'Invalid request. Please do not repeat this request again.');
		}

		$attributes = Yii::app()->request->getParam('requestForm');

		if (!is_array($attributes)) {
			throw new CHttpException(400, 'Invalid request');
		}

		$validationCode = isset($attributes['validation_code']) ? $attributes['validation_code'] : null;
		$phone = isset($attributes['client_phone']) ? new Phone($attributes['client_phone']) : null;

		$request = null;
		$errors = [];
		$result = [
			'success' => false,
			'errors' => [],
			'req_id' => null,
			'cl_id' => null,
		];

		/*
		$clinic = empty($attributes['clinic_id']) ? null : ClinicModel::model()->findByPk($attributes['clinic_id']);
		$validatePhone = $clinic && $clinic->validate_phone;
		*/
		$validatePhone = true; // TODO: сейчас форма записи не поддерживает создание заявки без валидации

		if ($validatePhone) {
			$reqId = isset($attributes['req_id']) ? intval($attributes['req_id']) : 0;
			if (!$reqId) {
				throw new CHttpException(400, 'Invalid request');
			}

			if (!$validationCode) {
				$errors['validation_code'][] = 'Введите код валидации';
			}
			elseif ($phone && $phone->isValid()) {
				$request = RequestModel::model()
					->byClientPhone($phone)
					->byValidationCode($validationCode)
					->inStatuses([ RequestModel::STATUS_PRE_CREATED ])
					->findByPk($reqId);

				if (!$request) {
					$errors['validation_code'][] = 'Неправильный код';
				} else {
					$time = empty($attributes['date_admission']) ? 0 : strtotime($attributes['date_admission']);
					$now = time();
					$attributes['date_admission'] = $time < $now ? $now : $time;
					$result = $this->createRequest($attributes, RequestModel::SCENARIO_DIAGNOSTIC_ONLINE);
				}
			} else {
				$errors['validation_code'][] = 'Неверный телефон';
			}
		} else {
			$result = $this->createRequest($attributes, RequestModel::SCENARIO_DIAGNOSTIC_ONLINE);
		}

		if (!$result['success']) {
			$result['errors'] = array_merge($errors, $result['errors']);
		}

		$this->renderJSON($result);
	}

	/**
	 * Валидация телефона
	 *
	 * @throws CHttpException
	 */
	public function actionValidate()
	{
		if (!Yii::app()->request->isAjaxRequest) {
			throw new CHttpException(400, 'Invalid request');
		}

		$attributes = Yii::app()->request->getParam('requestForm');

		if (!is_array($attributes) || !isset($attributes['client_phone'])) {
			throw new CHttpException(400, 'Invalid request');
		}

		$phone = new Phone($attributes['client_phone']);

		if (!$phone->isValid()) {
			throw new CHttpException(400, 'Invalid request(phone format)');
		}

		$result = $this->createRequest($attributes, RequestModel::SCENARIO_VALIDATE_PHONE);

		$this->renderJSON($result);
	}

	/**
	 * Сохраняет электронную почту клиента
	 *
	 * @throws CHttpException
	 *
	 * @return void
	 */
	public function actionSaveEmail()
	{
		$request = Yii::app()->request;

		if (!$request->getIsAjaxRequest()) {
			throw new CHttpException(400, 'Некорректный запрос');
		}

		$id = $request->getPost('client_id');
		$email = $request->getPost('client_email');

		$client = $id ? ClientModel::model()->findByPk($id) : null;

		if (!$client || !$email) {
			throw new CHttpException(400, 'Некорректный запрос');
		}

		$client->setScenario('SAVE_EMAIL');
		$client->email = $email;

		$result = $client->save();

		$this->renderJSON([
			'success' => $result,
			'errors' => $client->getErrors(),
		]);
	}

	/**
	 * Создание заявки
	 *
	 * @param $attributes
	 * @param $scenario
	 *
	 * @return array
	 */
	protected function createRequest($attributes, $scenario)
	{
		$this->layout = "";
		$attributes['id_city'] = Yii::app()->city->getCityId();
		$attributes['enter_point'] = RequestModel::ENTER_POINT_DIAGNOSTICS;
		$attributes['kind'] = RequestModel::KIND_DIAGNOSTICS;
		$attributes['req_type'] = RequestModel::TYPE_ONLINE_RECORD;

		$request = !empty($attributes['req_id']) ? RequestModel::model()->findByPk($attributes['req_id']) : new RequestModel($scenario);
		if (!$request) {
			$request = new RequestModel();
		}
		$request->setScenario($scenario);

		$request->attributes = $attributes;
		try {
			$result = $request->save();
		} catch (SpamException $e) {
			// В случае спама выдаем. что заявка успешно создана
			$result =  true;
		}

		return [
			'success'     => $result,
			'errors'      => $request->getErrors(),
			'req_id'      => !empty($request->req_id) ? $request->req_id : null,
			'cl_id'       => !empty($request->clientId) ? $request->clientId : null,
			'successText' => $this->renderPartial(
				"request_success",
				[
					"model"       => $request,
					"withNewline" => true
				],
				true
			),
		];
	}

	/**
	 *
	 */
	public function actionRequestForm()
	{
		$request = Yii::app()->request;

		$pid = $request->getQuery('pid', 0);
		$partner = $pid > 0 ? PartnerModel::model()->findByPk($pid) : null;

		$this->layout = "//layouts/requestForm";
		$clinicId = $request->getQuery('clinicId');
		$diagnosticId = $request->getQuery('diagnosticId');

		$clinic = ClinicModel::model()->findByPk($clinicId);
		if ($clinic === null) {
			throw new CHttpException(400, 'Некорректный запрос');
		}

		$diagnostic = DiagnosticaModel::model()->findByPk($diagnosticId);

		$params = [];
		$params['clinic'] = [
			'id' => $clinic->id,
			'name' => $clinic->name,
			'address' => $clinic->getAddress(),
			'discountOnline' => $clinic->discount_online_diag,
		];
		foreach ($clinic->stations as $s) {
			$params['clinic']['metro'] = [
				'dist' => $s->getDistanceToClinic($clinic->id),
				'lineId' => $s->underground_line_id,
				'title' => $s->name
			];
		}
		$params['contactPhone'] = null;

		$params['specialities'] = $request->getQuery('specialities');

		$this->render('requestForm', [
			'diagnostic' => $diagnostic,
			'parentDiagnostic' => $diagnostic ? $diagnostic->parent : null,
			'params' => $params,
			'partner' => $partner,
		]);
	}
}
