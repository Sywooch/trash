<?php

namespace dfs\docdoc\front\controllers;

use dfs\docdoc\models\CityModel;
use dfs\docdoc\models\SectorModel;
use dfs\docdoc\models\StationModel;
use Yii, CHttpException;
use dfs\docdoc\components\AppController;

/**
 * Class WidgetController
 *
 * @package dfs\docdoc\front\controllers
 */
class WidgetController extends AppController
{
	/**
	 * Инициализация виджета
	 *
	 * @param string $widgetName
	 * @param array $params
	 * @return  \dfs\docdoc\front\widgets\partner\PartnerWidget
	 * @throws \CHttpException
	 */
	private function createPartnerWidget($widgetName = null, $params = null)
	{
		$widgetName = is_null($widgetName) ? Yii::app()->request->getQuery('widget') : $widgetName;

		if (!empty($widgetName)) {
			try {
				$params = is_null($params) ? $_GET : $params;
				return $this->createWidget("\\dfs\\docdoc\\front\\widgets\\partner\\". $widgetName, $params);
			} catch (\Exception $e) {
				throw new CHttpException(400);
			}
		} else {
			throw new CHttpException(400);
		}
	}

	/**
	 * Получение js файла для партнера
	 *
	 * @throws \CHttpException
	 */
	public function actionJs()
	{
		header("Content-type: application/javascript");
		echo $this->renderFile(
			$this->getViewFile("core_js"),
			[
				'host'       => Yii::app()->params['hosts']['front'],
				'ip'         => Yii::app()->request->getUserHostAddress(),
				'widgetHost' => "https://w." . str_replace("front.", "", Yii::app()->params['hosts']['front']),
			],
			true
		);

		$this->disableTrace();
		\Yii::app()->end();
	}

	/**
	 * Загрузка виджета
	 */
	public function actionLoadWidget()
	{
		$widget = $this->createPartnerWidget();

		$widget->loadWidget();
		$widget->run();
	}

	/**
	 * Редирект трафика с виджетов
	 */
	public function actionRedirectWidget()
	{
		$params = [];
		$params['sector'] = Yii::app()->request->getQuery('dd_spec_list');
		$params['station'] = Yii::app()->request->getQuery('dd_clinic_station_list');
		$params['district'] = Yii::app()->request->getQuery('dd_clinic_district_list');
		$params['template'] = Yii::app()->request->getQuery('template');
		$params['city'] = Yii::app()->request->getQuery('city');
		$params['searchType'] = Yii::app()->request->getQuery('searchType');
		$params['specName'] = Yii::app()->request->getQuery('spec_name');

		$widget = $this->createPartnerWidget('Search', $params);
		$widget->redirectWidget();
		$widget->run();
	}

	/**
	 * вывод справочника городов в формате JSON
	 */
	public function actionCity()
	{
		$cities = CityModel::model()->active()->findAll();
		$c = [];
		foreach ($cities as $city) {
			$c[$city->rewrite_name] = $city->title;
		}

		$this->renderJSON($c);
	}

	/**
	 * вывод справочника метро в городе
	 */
	public function actionStations()
	{
		$sts = StationModel::model()
			->inCity(Yii::app()->city->getCityId())
			->findAll();

		$c = [];
		foreach ($sts as $st) {
			$c[$st->rewrite_name] = $st->name;
		}

		$this->renderJSON($c);
	}

	/**
	 * вывод справочника специальностей в городе
	 */
	public function actionSectors()
	{
		$sts = SectorModel::model()
			->inCity(Yii::app()->city->getCityId())
			->findAll();

		$c = [];
		foreach ($sts as $st) {
			$c[$st->rewrite_name] = $st->name;
		}

		$this->renderJSON($c);
	}

	/**
	 * вывод справочника специализаций в городе
	 */
	public function actionSpecializations()
	{
		$sts = SectorModel::model()
			->inCity(Yii::app()->city->getCityId())
			->findAll();

		$c = [];
		foreach ($sts as $st) {
			$c[$st->rewrite_spec_name] = $st->spec_name;
		}

		$this->renderJSON($c);
	}

	/**
	 * отключение логов и трейсов
	 */
	public function disableTrace()
	{
		foreach (\Yii::app()->log->routes as $route) {
			if($route instanceof \CWebLogRoute) {
				$route->enabled = false; // disable any weblogroutes
			}
		}
	}

	/**
	 * Создание заявки
	 *
	 */
	public function actionCreateRequest()
	{
		$params['template'] = 'CreateRequest';
		$params['clientName'] = Yii::app()->request->getQuery("clientName");
		$params['phone'] = Yii::app()->request->getQuery("phone");
		$params['clinicId'] = Yii::app()->request->getQuery("clinicId");
		$params['doctorId'] = Yii::app()->request->getQuery("doctorId");
		$params['srcWidget'] = Yii::app()->request->getQuery("widget");
		$params['srcTemplate'] = Yii::app()->request->getQuery("template");
		$params['id'] = Yii::app()->request->getQuery("id");

		/**
		 * @var \dfs\docdoc\front\widgets\partner\Request $widget
		 */
		$widget = $this->createPartnerWidget('Request', $params);
		$widget->createRequest();
		$widget->run();
	}

	/**
	 * загрузка тестовой страницы
	 *
	 * @param string $config
	 */
	public function actionTest($config)
	{
		$config = urldecode($config);
		$this->render('test',
			[
				"config" => $config, "host" => \Yii::app()->params['hosts']['front']
			]
		);
	}

	/**
	 * Выводит на 1 странице все виджеты ДокДока для теста
	 *
	 * @return void
	 */
	public function actionTests()
	{
		$this->layout = null;
		$this->render("tests", ["host" => Yii::app()->params['hosts']['front']]);
	}

	/**
	 * Выводит на 1 странице виджеты клиники для теста
	 *
	 * @return void
	 */
	public function actionClinicWidgetTest()
	{
		$this->layout = null;
		$this->render("clinicWidgetTest", ["host" => Yii::app()->params['hosts']['front']]);
	}
}