<?php

namespace dfs\docdoc\back\controllers;

use Yii;
use CHttpException;
use dfs\docdoc\models\DoctorClinicModel;
use dfs\docdoc\models\SlotModel;

class ScheduleController extends BackendController
{
	/**
	 * Выводит расписание работы врача в JSON-формате.
	 * Расписание представлено в виде временных интервалов работы врача
	 */
	public function actionPeriod()
	{
		$doctorId = Yii::app()->request->getParam('doctorId', null);
		$clinicId = Yii::app()->request->getParam('clinicId', null);
		$workDate = Yii::app()->request->getParam('doctorWorkDate', null);

		if (!empty($workDate)) {
			$workDate = \CDateTimeParser::parse($workDate, "dd.MM.yyyy");
		}

		if (!$doctorId || !$clinicId) {
			throw new CHttpException(400, 'Некорректные параметры запроса');
		}

		$doctorClinic = DoctorClinicModel::model()->findDoctorClinic($doctorId, $clinicId);

		if (!$doctorClinic) {
			throw new CHttpException(400, 'Запрошенный врач не найден в данной клинике');
		}

		//ищем активные слоты и группируем их в интервалы
		$slots = $doctorClinic->getSlots(date('Y-m-d'), null, true);
		$intervals = SlotModel::groupIntervals($slots);

		$this->renderPartial(
			'period',
			array(
				'intervals' => $intervals,
				'date' => $workDate
			)
		);
	}

	/**
	 * @throws \CHttpException
	 */
	public function actionSlots()
	{
		$doctorId = Yii::app()->request->getParam('doctorId', null);
		$clinicId = Yii::app()->request->getParam('clinicId', null);

		if (!$doctorId || !$clinicId) {
			throw new CHttpException(400, 'Некорректные параметры запроса');
		}

		$dateFrom = new \DateTime();
		$dateTo = null;

		$date = Yii::app()->request->getParam('doctorWorkDate', null);

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
				'id' => $x->external_id,
				'doctor_4_clinic_id' => $x->doctor_4_clinic_id,
				'start_time' => date('H:i', strtotime($x->start_time)),
				'finish_time' => date('H:i', strtotime($x->finish_time)),
				'external_id' => $x->external_id,
			];
		}

		$this->renderJson(['slots' => $slotsArray]);
	}
}
