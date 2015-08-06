<?php

use dfs\docdoc\extensions\Controller;
use dfs\docdoc\models\ClinicModel;


class ScheduleController extends Controller
{
	/**
	 * Слоты для клиники
	 *
	 * @throws \CHttpException
	 */
	public function actionSlots()
	{
		$request = Yii::app()->request;

		$clinicId = $request->getParam('clinicId');
		$date = $request->getParam('workDate');

		$clinic = ClinicModel::model()->findByPk($clinicId);

		if (!$clinic) {
			throw new CHttpException(400, 'Некорректные параметры запроса');
		}

		$this->renderJson([
			'slots' => $clinic->getSlots($date, 30, 3600),
		]);
	}
}
