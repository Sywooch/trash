<?php

namespace dfs\docdoc\front\controllers;

use dfs\docdoc\models\ClinicModel;
use Yii;
use CHttpException;
use dfs\docdoc\extensions\Controller;
use dfs\docdoc\models\DoctorClinicModel;
use dfs\docdoc\models\SlotModel;

class ScheduleController extends Controller
{
	/**
	 * @throws \CHttpException
	 */
	public function actionSlots()
	{
		$doctorId = Yii::app()->request->getParam('doctorId', null);
		$clinicId = Yii::app()->request->getParam('clinicId', null);
		$date = Yii::app()->request->getParam('workDate', null);

		if (!$doctorId || !$clinicId) {
			throw new CHttpException(400, 'Некорректные параметры запроса');
		}

		$dateFrom = new \DateTime();
		$dateTo = null;

		if (!empty($date)) {
			try {
				//дата начала
				$doctorWorkHour = sprintf('%02d', Yii::app()->request->getParam('doctorWorkHour'));
				$doctorWorkMin = sprintf('%02d', Yii::app()->request->getParam('doctorWorkMin'));
				$_dateFrom = new \DateTime("$date {$doctorWorkHour}:{$doctorWorkMin}");

				if ($_dateFrom >= $dateFrom) {
					//не пропускаю слоты в прошлом
					$dateFrom = $_dateFrom;
				}

				//дата конца
				$doctorWorkToHour = Yii::app()->request->getParam('doctorWorkToHour');

				if ($doctorWorkToHour) {
					$doctorWorkToHour = sprintf('%02d', $doctorWorkToHour);
					$doctorWorkToMin = sprintf('%02d', Yii::app()->request->getParam('doctorWorkToMin'));
					$dateTo = new \DateTime("{$date} {$doctorWorkToHour}:{$doctorWorkToMin}");
				}
			} catch (\Exception $e) {
				//при неправильном формате даты будет ексепшон
				throw new CHttpException(400, 'Некорректные параметры запроса');
			}
		}

		$doctorClinic = DoctorClinicModel::model()->findDoctorClinic($doctorId, $clinicId);

		if (!$doctorClinic) {
			throw new CHttpException(400, 'Некорректные параметры запроса');
		}

		$slots = $doctorClinic->getSlots(
			$dateFrom->format('Y-m-d H:i'),
			$dateTo ? $dateTo->format('Y-m-d H:i') : null,
			true
		);

		$slotsArray = [];

		foreach ($slots as $x) {
			$slotsArray[date('d-m-Y', strtotime($x->start_time))][] = [
				'id'                 => $x->external_id,
				'doctor_4_clinic_id' => $x->doctor_4_clinic_id,
				'start_time'         => date('H:i', strtotime($x->start_time)),
				'finish_time'        => date('H:i', strtotime($x->finish_time)),
				'external_id'        => $x->external_id,
				'active' => true,
			];
		}

		$this->renderJson([
			'slots' => $slotsArray,
			'html'  => $this->renderPartial('slots', ['data' => $slotsArray], true),
		]);
	}


	/**
	 * Слоты для клиники
	 *
	 * @throws \CHttpException
	 */
	public function actionDiagnosticSlots()
	{
		$request = Yii::app()->request;

		$clinicId = $request->getParam('clinicId');
		$date = $request->getParam('workDate');

		$clinic = ClinicModel::model()->findByPk($clinicId);

		if (!$clinic) {
			throw new CHttpException(400, 'Некорректные параметры запроса');
		}

		$this->renderJson([
			'slots' => $clinic->getSlots($date),
		]);
	}
}
