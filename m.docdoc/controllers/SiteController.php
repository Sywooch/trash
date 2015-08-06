<?php

use dfs\components\Controller;

/**
 * Class SiteController
 */
class SiteController extends Controller
{
	/**
	 * Главная страница
	 */
	public function actionIndex()
	{
		$session = new CHttpSession;
		$session->open();

		$apiDto = new ApiDto();

		$city = Yii::app()->city->getModel();

		$eventParams = [
			'City' => $city->getName(),
		];
		Yii::app()->mixpanel->addTrack('HomePage', $eventParams);

		$metroList = array();
		$districtList = array();
		if ($city->hasMetro()) {
			$metroList = $apiDto->getMetroList();
		} else {
			$districtList = $apiDto->getDistrictList();
		}

		$this->render('index', [
				'model'          => $apiDto->getStats(),
				'metroList'      => $metroList,
				'districtList'   => $districtList,
				'specialityList' => $apiDto->getSpecialityList(),
				'city'           => $city
			]);
	}

	/**
	 * Вывод ошибки
	 */
	public function actionError()
	{
		if (!($error = Yii::app()->errorHandler->error)) {
			$error = [
				'code'    => '404',
				'message' => '',
			];
		}
		if (Yii::app()->request->isAjaxRequest) {
			echo $error['message'];
		} else {
			$this->render('error', $error);
		}
	}
} 