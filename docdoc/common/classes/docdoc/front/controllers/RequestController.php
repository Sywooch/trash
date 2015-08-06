<?php

namespace dfs\docdoc\front\controllers;

use dfs\docdoc\exceptions\SpamException;
use dfs\docdoc\models\ClinicModel;
use dfs\docdoc\models\DoctorClinicModel;
use dfs\docdoc\models\DoctorModel;
use dfs\docdoc\models\RequestModel;
use dfs\docdoc\components\AppController;
use dfs\docdoc\models\SectorModel;
use dfs\docdoc\models\UndergroundStationModel;
use dfs\docdoc\objects\Phone;
use Yii;

/**
 * Файл класса RequestController.
 *
 * Контроллер для работы с запросами
 *
 * @author  Mikhail Vasilyev <mvasilyev@docdoc.ru>
 * @link    https://docdoc.atlassian.net/browse/DD-111
 * @package dfs.docdoc.front.controllers
 */
class RequestController extends AppController
{

	public function actionSave()
	{
		$name = Yii::app()->request->getPost("requestName");
		if (!$name) {
			$name = "Перезвонить мне";
		}

		$phone = Yii::app()->request->getPost("requestPhone");
		$sector = Yii::app()->request->getPost("sector");

		$ageSelector = Yii::app()->request->getPost('requestAgeSelector','adult');
		if (!$ageSelector) {
			$ageSelector = "adult";
		}

		$comments = Yii::app()->request->getPost("requestComments");
		$departure = Yii::app()->request->getPost("departure");

		$city = Yii::app()->request->getPost("requestCityId");
		if (!$city) {
			$city = Yii::app()->city->getCityId();
		}

		$doctor = Yii::app()->request->getPost("doctor");
		$clinic = Yii::app()->request->getPost("clinic");

		//когда заполняют перезвоните мне, requestBtnType в посте не приходит
		if (Yii::app()->request->getPost("requestBtnType")) {
			$enter_point = Yii::app()->request->getPost("formType");
			if (!$enter_point) {
				$enter_point = 'Unknown';
			}
		} else {
			$enter_point = RequestModel::ENTER_POINT_CALL_ME_BACK;
		}

		if (!$doctor && !$clinic) {
			$scenario = RequestModel::SCENARIO_CALL;
		} else {
			$scenario = RequestModel::SCENARIO_SITE;
		}

		$request = new RequestModel($scenario);
		$attributes = array(
			'client_name'     => $name,
			'client_phone'    => $phone,
			'req_sector_id'   => $sector,
			'age_selector'    => $ageSelector,
			'client_comments' => $comments,
			'req_departure'   => $departure,
			'req_doctor_id'   => $doctor,
			'id_city'         => $city,
			'clinic_id'       => $clinic,
			'partner_id'      => Yii::app()->referral->id,
			'enter_point'     => $enter_point,
		);
		$request->attributes = $attributes;

		if (Yii::app()->request->getPost("stations")) {
			$stations = Yii::app()->request->getPost("stations");
			if (is_array($stations)) {
				$request->stations = $stations;
			} else {
				$request->stations = explode(',', $stations);
			}
		}

		try {
			if (!$request->save()) {
				$errors = array();
				foreach ($request->getErrors() as $field => $error) {
					$errors[$field] = $error;
				}
				$this->renderJSON($errors);
			}
		} catch (SpamException $e) {
			// В случае спама отправляем успешный ответ
			$resp['status'] = 'success';
			$resp['url'] = isset($_POST['redirectToThanks']) ? '/request/thanks' : false;
			$resp['success'] = $this->renderPartial("/client/request_email", null, true);
			$this->renderJSON($resp);
			exit();
		}

		//если заявка создалась, смотрим может ее хотят забукать
		if(Yii::app()->request->getPost('slotId')) {
			$slotId = Yii::app()->request->getPost('slotId');
			$bookingErrors = [];
			try{
				if(!$request->book($slotId, true)){
					foreach ($request->getErrors() as $errors) {
						foreach($errors as $error){
							$bookingErrors[] = $error;
						}
					}
				}
			} catch (\Exception $e){
				$bookingErrors[] = $e->getMessage();
			}

			if (count($bookingErrors)) {
				$request->addHistory(
					"При резервировании слота #" . $slotId . " произошли ошибки: " . var_export($bookingErrors, true)
				);
			}
		}

		$resp['status'] = 'success';
		$resp['url'] = isset($_POST['redirectToThanks']) ? '/request/thanks' : false;
		$resp['req_id'] = $request->req_id;
		$resp['cl_id'] = $request->clientId;
		$resp['created'] = $request->req_created;
		$resp['success'] = $this->renderPartial("/client/request_email", null, true);

		$this->renderJSON($resp);
	}

	/**
	 * Форма записи к врачу
	 *
	 * @throws \CHttpException
	 */
	public function actionForm()
	{
		$request = Yii::app()->request;

		$clinic = ClinicModel::model()->findByPk($request->getParam('clinic'));
		$sector = SectorModel::model()->findByPk($request->getParam('speciality'));

		$dc = DoctorClinicModel::model()
			->findDoctorClinic($request->getParam('doctor'), $request->getParam('clinic'));
		$date = strtotime($request->getParam('date'));
		$date = !empty($date) ? date('d-m-Y', $date) : null;

		$this->layout = 'widget';

		$widget = $this->widget('\dfs\docdoc\front\widgets\RequestFormWidget', [
				'doctorInClinic'    => $dc,
				'bookDate'          => $date,
				'clinic'            => $clinic,
				'sector'            => $sector,
			], true);


		if (Yii::app()->request->isAjaxRequest) {
			echo $widget;
			Yii::app()->end();
		}

		$this->render('form', ['widget'  => $widget]);
	}
}
