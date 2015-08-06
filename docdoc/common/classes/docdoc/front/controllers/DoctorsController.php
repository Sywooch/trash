<?php

namespace dfs\docdoc\front\controllers;

use dfs\docdoc\models\ClinicModel;
use dfs\docdoc\models\DoctorModel;
use dfs\docdoc\models\DoctorClinicModel;
use dfs\docdoc\models\DoctorOpinionModel;

require_once ROOT_PATH . '/back/public/lib/php/models/doctor.class.php';

class DoctorsController extends FrontController
{
	/**
	 * Страница врача
	 *
	 * @param string $alias
	 *
	 * @throws \CHttpException
	 */
	public function actionShow($alias)
	{
		$doctorModel = DoctorModel::model()->inCity(\Yii::app()->city->getCityId());

		if (is_numeric($alias)) {
			$doctor = $doctorModel->findByPk($alias);
			if ($doctor) {
				\Yii::app()->request->redirect('/doctor/' . $doctor->rewrite_name);
			}
		} else {
			$doctor = $doctorModel->byRewriteName($alias)->find();
		}

		if (!$doctor || $doctor->status == DoctorModel::STATUS_ANOTHER_DOCTOR) {
			throw new \CHttpException(404, 'Врач не найден');
		}

		$this->initXslTemplates();

		$clinic = $doctor->getDefaultClinic();

		$this->xmlPageInfo($doctor, $clinic);

		$reviews = DoctorOpinionModel::model()
			->allowed()
			->byDoctor($doctor->id)
			->findAll([
				'order' => 't.created DESC',
				'limit' => 3,
			]);

		$vars = [
			'doctor' => $doctor,
			'schedule' => null,
			'nearestDoctors' => $doctor->nearestDoctors(15),
			'refURL' => $this->getRefUrlFromSession(),
			'reviewsData' => [
				'doctor' => $doctor,
				'avgRatings' => DoctorOpinionModel::model()->getAvgRatingsByReviewsForDoctor($doctor->id),
				'reviews' => $reviews,
				'countReviews' => $doctor->countReviews(),
			],
		];

		if (\Yii::app()->params['doctorScheduleEnabled'] && $clinic->scheduleForDoctors) {
			$days = DoctorClinicModel::COUNT_DAYS_FOR_SCHEDULE;
			$schedule = DoctorClinicModel::model()->getDoctorsSchedule([$doctor->id], $days);
			if ($schedule && !empty($schedule[$doctor->id][$clinic->id])) {
				$vars['schedule'] = DoctorClinicModel::formatScheduleForDoctor($schedule[$doctor->id][$clinic->id], $days);
			}
		}

		$detect = new \MobileDetect();
		$isMobile = ($detect->isMobile() || $detect->isTablet()) ? true : false;

		if (!$isMobile && \Yii::app()->params['doctorScheduleEnabled'] && $clinic && $clinic->scheduleForDoctors) {
			$days = DoctorClinicModel::COUNT_DAYS_FOR_SCHEDULE;
			$schedule = DoctorClinicModel::model()->getDoctorsSchedule([$doctor->id], $days);
			if ($schedule && !empty($schedule[$doctor->id][$clinic->id])) {
				$vars['schedule'] = DoctorClinicModel::formatScheduleForDoctor($schedule[$doctor->id][$clinic->id], $days);
			}
		}

		$this->render('doctor', $vars);
	}

	/**
	 * Установка мета-данных о странице врача (для mixpanel и sociomantic)
	 *
	 * @param DoctorModel $doctor
	 * @param ClinicModel $clinic
	 */
	protected function xmlPageInfo(DoctorModel $doctor, $clinic)
	{
		$city = \Yii::app()->city;
		$host = $city->host();

		$stations = [];
		if ($clinic) {
			foreach ($clinic->stations as $item) {
				$stations[] = $item->name;
			}
		}

		$specialities = \CHtml::listData($doctor->sectors, 'id', 'name');

		$speciality = \Yii::app()->session->get('speciality');

		$eventParams = [
			'Spec'          => array_values($specialities),
			'SearchingSpec' => $speciality ? $speciality['Name'] : implode(', ', $specialities),
			'Clinic'        => $clinic ? $clinic->name : '',
			'Metro'         => implode(', ', $stations),
			'Area'          => $clinic && $clinic->district ? $clinic->district->name : '',
			'Name'          => $doctor->name,
			'Amount'        => $doctor->price,
			'Price'         => $doctor->special_price > 0 ? $doctor->special_price : $doctor->price,
			'Discount'      => $doctor->special_price > 0,
			'Reviews'       => intval($doctor->countReviews()),
			'Rating'        => $doctor->getDoctorRating(),
			'Experience'    => $doctor->getExperience(),
			'Awards'        => $doctor->getAwards(),
			'Photo'         => boolval($doctor->image),
			'City'          => $city->getTitle(),
			'DocID'         => $doctor->id,
			'ClinID'        => $clinic ? $clinic->id : '',
			'Url'           => 'http://' . $host . parse_url($_SERVER['REQUEST_URI'])['path'],
			'PhotoUrl'      => $doctor->getImg('sq'),
		];

		$this->globalTrack = [
			'Name' => 'DoctorPage',
			'Params' => json_encode($eventParams),
		];
	}

	/**
	 * Сформировать ссылку на список докторов
	 *
	 * @return string
	 */
	protected function getRefUrlFromSession()
	{
		$session = \Yii::app()->session;

		$speciality = $session->get('speciality');
		$stations = $session->get('stations');
		$listParams = $session->get('doctorListParams');

		$refUrl = '/doctor';

		if ($speciality) {
			$refUrl .= '/' . $speciality['Alias'];
		}
		if ($stations) {
			$stationIds = [];
			foreach ($stations as $station) {
				$stationIds[] = $station['Id'];
			}
			if ($speciality) {
				$refUrl .= '/stations/' . implode(',', $stationIds);
			} else {
				$refUrl = '/search/stations/' . implode(',', $stationIds);
			}
		}

		if (is_array($listParams)) {
			if (!empty($listParams['order'])) {
				$refUrl .= '/order/' . $listParams['order'];
				if (!empty($listParams['direction'])) {
					$refUrl .= '/direction/' . $listParams['direction'];
				}
			}
			if (!empty($listParams['page'])) {
				$refUrl .= '/page/' . $listParams['page'];
			}
		}
		
		if ($refUrl === '/doctor' && isset(\Yii::app()->session['resultsUrl'])) {
			$refUrl = \Yii::app()->session['resultsUrl'];	
		} 

		return $refUrl;
	}
}
