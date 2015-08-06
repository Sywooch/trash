<?php

namespace dfs\docdoc\front\controllers;

use dfs\docdoc\components\AppController;
use dfs\docdoc\extensions\ClinicServiceTrait;
use dfs\docdoc\models\ClinicModel;
use dfs\docdoc\models\DiagnosticaModel;
use dfs\docdoc\models\DoctorModel;
use dfs\docdoc\models\SectorModel;
use dfs\docdoc\models\RequestModel;
use Yii;

/**
 * Виджет записи на прием для 2GIS
 *
 * Class AppointmentController
 * @package dfs\docdoc\front\controllers
 */
class AppointmentController extends AppController
{
	use ClinicServiceTrait;

	public $layout = 'appointment';

	/**
	 * Страница с виджетом записи на прием
	 */
	public function actionIndex()
	{
		$vars = [];

		$clinicId = Yii::app()->request->getParam('clinicId');
		$vars['clinic'] = $clinic = ClinicModel::model()
			->active()
			->cache(3600)
			->findByPk($clinicId);

		if (is_null($clinic)) {
			$this->actionError();
			exit();
		}

		$vars['services'] = $this->getServices($clinic->id);

		$this->render('index', $vars);
	}

	/**
	 * Страница со списком врачей
	 */
	public function actionDoctors()
	{
		$vars = [];

		$clinicId = Yii::app()->request->getParam('clinicId');
		$specId = Yii::app()->request->getParam('specId');

		$doctors = DoctorModel::model()
			->inClinics([$clinicId])
			->active();

		if ($specId <> 0) {
			$doctors = $doctors->bySpeciality($specId);
		}

		$vars['doctors'] = $doctors
			->cache(3600)
			->findAll([
			'order' => 't.rating_internal DESC'
		]);

		$this->renderPartial('doctors', $vars);
	}

	/**
	 * Сохранение заявки
	 *
	 * @return bool
	 */
	public function actionCreateRequest()
	{
		$name = Yii::app()->request->getPost("requestName");
		$phone = Yii::app()->request->getPost("requestPhone");
		$comments = Yii::app()->request->getPost("requestComments");
		$doctorId = Yii::app()->request->getParam('doctorId');
		$clinicId = Yii::app()->request->getParam('clinicId');
		$specId = Yii::app()->request->getParam('specId');
		$diagnosticId = Yii::app()->request->getParam('diagnosticId');

		$request = new RequestModel(RequestModel::SCENARIO_PARTNER);
		$attributes = array(
			'client_name'     => $name,
			'client_phone'    => $phone,
			'client_comments' => $comments,
			'clinic_id'       => $clinicId,
			'partner_id'      => 145, // 2Gis
			'enter_point'     => RequestModel::ENTER_POINT_PARTNER_SEARCH,
		);
		$request->attributes = $attributes;

		if (!empty($specId)) {
			$request->req_sector_id = $specId;
		}

		if (!empty($doctorId)) {
			$request->req_doctor_id = $doctorId;
		}

		if (!empty($diagnosticId)) {
			$request->diagnostics_id = $diagnosticId;
		}

		echo $request->save();
	}


	/**
	 * Страница с сообщением об ошибке
	 */
	public function actionError()
	{
		$this->render('error');
	}
}
