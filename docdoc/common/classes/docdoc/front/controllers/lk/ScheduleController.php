<?php

namespace dfs\docdoc\front\controllers\lk;

use dfs\docdoc\models\DoctorClinicModel;
use dfs\docdoc\models\SlotModel;


/**
 * Class ScheduleController
 *
 * @package dfs\docdoc\front\controllers\lk
 */
class ScheduleController extends FrontController
{
	/**
	 * @param $doctorId
	 * @param $clinicId
	 *
	 * @throws \CHttpException
	 */
	public function actionIndex($doctorId, $clinicId)
	{
		$doctorClinic = DoctorClinicModel::model()->findDoctorClinic($doctorId, $clinicId);

		if (!$doctorClinic || !$doctorClinic->doctor) {
			throw new \CHttpException(400, 'Не найден доктор в клинике');
		}

		$this->render('index', [
			'doctor' => $doctorClinic->doctor,
			'doctorClinic' => $doctorClinic,
		]);
	}

	/**
	 * Получение информации о расписании врача
	 *
	 * @param $doctorId
	 * @param $clinicId
	 *
	 * @throws \CHttpException
	 */
	public function actionCalendar($doctorId, $clinicId)
	{
		$doctorClinic = DoctorClinicModel::model()->findDoctorClinic($doctorId, $clinicId);

		if (empty($doctorClinic)) {
			throw new \CHttpException(400, 'Не найден доктор в клинике');
		}

		//ищем расписание в интервале от двух полных недель назад до конца
		$slots = $doctorClinic->getSlots(date('Y-m-d', strtotime("last Monday") - 1209600), null, false);
		$intervals = SlotModel::groupIntervals($slots);

		$this->renderJSON([
			'events' => $intervals,
			'maxEventDate' => empty($intervals) ? null : date('Y-m-d', strtotime($intervals[count($intervals)-1]['end'])),
		]);
	}

	/**
	 * Сохранение расписания врача
	 *
	 * @param $doctorId
	 * @param $clinicId
	 *
	 * @throws \CHttpException
	 */
	public function actionSave($doctorId, $clinicId)
	{
		$doctorClinic = DoctorClinicModel::model()->findDoctorClinic($doctorId, $clinicId);

		if (empty($doctorClinic)) {
			throw new \CHttpException(400, "Empty clinic id for doctor: {$doctorId}");
		}

		//получаем и сохраняем правила отображения расписания
		$rules =  \Yii::app()->request->getPost('rules', null);
		if (!isset($rules['schedule_step']) || $rules['schedule_step'] < 15) {
			throw new \CHttpException(400, "Undefined schedule_step: {$rules['schedule_step']}");
		}

		$doctorClinic->saveScheduleRules($rules);

		$events = \Yii::app()->request->getPost('events', null);

		//если есть события
		if (is_array($events)) {
			foreach ($events as $i => $e) {
				//очищаем от некорректных запросов
				if (!isset($e['start']) || !isset($e['end'])) {
					unset($events[$i]);
				}
			}

			$doctorClinic->saveSlotsFromSchedule($events);
		}

		$this->renderJSON([ 'success' => true ]);
	}
}
