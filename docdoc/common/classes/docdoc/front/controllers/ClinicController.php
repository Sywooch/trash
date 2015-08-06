<?php

namespace dfs\docdoc\front\controllers;

use dfs\docdoc\listInterface\ClinicList;
use dfs\docdoc\models\ClinicModel;
use dfs\docdoc\models\DoctorModel;
use dfs\docdoc\models\DoctorOpinionModel;
use dfs\docdoc\models\SectorModel;
use dfs\docdoc\models\StationModel;
use RussianTextUtils;


class ClinicController extends FrontController
{
	protected $limitReviewsFirst = 2;
	protected $limitDoctorsFirst = 4;
	protected $limitReviewsMore = 10;
	protected $limitDoctorsMore = 10;


	/**
	 * Страница со списком клиник
	 */
	public function actionIndex()
	{
		$request = \Yii::app()->request;

		$clinicList = new ClinicList();

		$params = [
			'isClinic' => 'yes',
			'withNearest' => true,
			'speciality' => $request->getParam('spec'),
			'station' => $request->getParam('station'),
			'street' => $request->getParam('street'),
			'regCity' => $request->getParam('city'),
			'district' => $request->getParam('district'),
			'area' => $request->getParam('area'),
		];

		$params['isMain'] = !$params['speciality'] && !$params['station'] && !$params['street'] && !$params['district'] && !$params['area'];

		$clinicList
			->setSort($request->getParam('order'))
			->setSortDirection($request->getParam('direction'))
			->setPage($request->getParam('page'))
			->setParams($params)
			->buildParams();

		if ($clinicList->hasErrors()) {
			throw new \CHttpException(404, 'Неправильный запрос');
		}

		$clinicList->loadData();

		$this->setSessionData($clinicList);

		$this->initXslTemplates();

		$this->render('list', [
			'clinicList' => $clinicList,
			'bestClinics' => $clinicList->findBestClinics($clinicList->getLimit() - $clinicList->getCount()),
			'sectors' => SectorModel::model()
				->cache(3600)
				->simple()
				->visible()
				->selectCountClinics()
				->findAll(['order' => 't.spec_name']),
		]);
	}

	/**
	 * Страница для печати информации о клинике
	 *
	 * @param string $alias
	 *
	 * @throws \CHttpException
	 */
	public function actionPrint($alias)
	{
		$clinic = ClinicModel::model()
			->inCity(\Yii::app()->city->getCityId())
			->searchByAlias($alias)
			->find();

		if (!$clinic) {
			throw new \CHttpException(404, 'Врач не найден');
		}

		$vars = [
			'clinic' => $clinic,
		];

		$this->layout = 'print';
		$this->render('print', $vars);
	}

	/**
	 * Страница клиники
	 *
	 * @param string $alias
	 *
	 * @throws \CHttpException
	 */
	public function actionShow($alias)
	{
		$city = \Yii::app()->city;

		$clinicModel = ClinicModel::model()->inCity($city->getCityId());

		if (is_numeric($alias)) {
			$clinic = $clinicModel->findByPk($alias);
			if ($clinic) {
				\Yii::app()->request->redirect('/clinic/' . $clinic->rewrite_name);
			}
		} else {
			$clinic = $clinicModel->searchByAlias($alias)->find();
		}

		if (!$clinic) {
			throw new \CHttpException(404, 'Врач не найден');
		}

		$this->initXslTemplates();

		$reviewsData = $this->findReviews($clinic->id, $this->limitReviewsFirst);
		$doctorsData = $this->findDoctors($clinic, $this->limitDoctorsFirst);

		$sectors = SectorModel::model()->visible()->findAllForClinic($clinic->id);

		$vars = [
			'clinic' => $clinic,
			'sectors' => $sectors,
			'nearestClinics' => $clinic->nearestClinics(15),
			'refURL' => $this->getRefUrlFromSession(),
			'reviewsData' => [
				'clinic' => $clinic,
				'avgRatings' => DoctorOpinionModel::model()->getAvgRatingsByReviewsForClinic($clinic->id),
				'reviews' => $reviewsData['reviews'],
				'countReviews' => $clinic->getCountReviews(),
			],
			'doctorsData' => [
				'clinic' => $clinic,
				'sectors' => $sectors,
				'doctors' => $doctorsData['doctors'],
				'countDoctors' => $doctorsData['countAll'],
			],
		];

		$this->render('clinic', $vars);
	}

	/**
	 * Подгрузка отзывов клиники
	 */
	public function actionMoreReviews()
	{
		$request = \Yii::app()->request;

		$offset = $request->getQuery('offset');
		$limit = $offset ? $this->limitReviewsMore : $this->limitReviewsFirst;

		$data = $this->findReviews($request->getQuery('clinicId'), $limit, $offset);

		$htmlReviews = [];
		foreach ($data['reviews'] as $review) {
			$htmlReviews[] = $this->renderPartial('review', ['review' => $review], true);
		}

		$this->renderJSON([
			'success' => true,
			'reviews' => $htmlReviews,
		]);
	}

	/**
	 * Подгрузка докторов клиники
	 */
	public function actionMoreDoctors()
	{
		$request = \Yii::app()->request;

		$clinic = ClinicModel::model()->findByPk($request->getQuery('clinicId'));

		if (!$clinic) {
			throw new \CHttpException(404, 'Врач не найден');
		}

		$offset = $request->getQuery('offset');
		$limit = $offset ? $this->limitDoctorsMore : $this->limitDoctorsFirst;

		$data = $this->findDoctors($clinic, $limit, $offset);

		$htmlDoctors = [];
		foreach ($data['doctors'] as $doctor) {
			$htmlDoctors[] = $this->renderPartial('/doctors/teaser', [
				'doctor' => $doctor,
				'teaserType' => 'clinicList',
			], true);
		}

		$this->renderJSON([
			'success' => true,
			'doctors' => $htmlDoctors,
			'countAll' => $data['countAll'],
		]);
	}

	/**
	 * Выборка отзывов для клиники
	 *
	 * @param int $clinicId
	 * @param int $limit
	 * @param int | null  $offset
	 *
	 * @return array
	 */
	protected function findReviews($clinicId, $limit = 10, $offset = null)
	{
		$scopes = [
			'allowed' => [],
			'byClinic' => [$clinicId],
		];

		return [
			'reviews' => DoctorOpinionModel::model()->findAll([
				'scopes' => $scopes,
				'order' => 't.created DESC',
				'limit' => $limit,
				'offset' => $offset,
			]),
		];
	}

	/**
	 * Выборка докторов в клинике
	 *
	 * @param ClinicModel $clinic
	 * @param int         $limit
	 * @param int | null  $offset
	 *
	 * @return array
	 */
	protected function findDoctors($clinic, $limit = 10, $offset = null)
	{
		$request = \Yii::app()->request;

		$doctorStatus = [DoctorModel::STATUS_ACTIVE];
		if ($clinic->status != ClinicModel::STATUS_ACTIVE) {
			$doctorStatus[] = DoctorModel::STATUS_BLOCKED;
		}

		$scopes = [
			'inCity' => [\Yii::app()->city->getCityId()],
			'inClinics' => [[$clinic->id]],
			'inStatuses' => [$doctorStatus]
		];

		$specialityId = $request->getQuery('speciality');
		if ($specialityId) {
			$scopes['inSector'] = $specialityId;
		}

		return [
			'doctors' => DoctorModel::model()->findAll([
				'scopes' => $scopes,
				'order' => 't.rating_internal DESC',
				'limit' => $limit,
				'offset' => $offset,
			]),
			'countAll' => DoctorModel::model()->count([
				'scopes' => $scopes,
			]),
		];
	}

	/**
	 * Установка данных в сессию (используется в шапке сайта, для SEO и для ссылки "вернуться назад")
	 *
	 * @param ClinicList $clinicList
	 */
	protected function setSessionData(ClinicList $clinicList)
	{
		$session = \Yii::app()->session;

		$this->setSessionSpeciality($clinicList->getSpeciality());

		$station = $clinicList->getStation();
		$stationIds = $clinicList->getStationIds();

		$this->setSessionStations($station ? [$station] : ($stationIds ? StationModel::model()->findAllByPk($stationIds) : null));

		$session['resultsUrlForClinic'] = \Yii::app()->request->url;
	}

	/**
	 * Сформировать ссылку на список клиник
	 *
	 * @return string
	 */
	protected function getRefUrlFromSession()
	{
		$session = \Yii::app()->session;

		$refUrl = $session->get('resultsUrlForClinic');

		return $refUrl ?: '/clinic';
	}
}
