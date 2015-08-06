<?php
use dfs\docdoc\models\StationModel;
use dfs\docdoc\models\RegCityModel;
use dfs\docdoc\models\DistrictModel;
use dfs\docdoc\models\DiagnosticaModel;

/**
 * Class SitemapController
 */
class SitemapController extends FrontendController
{

	public function actionIndex()
	{
		$this->setTitle('Карта сайта');

		$diagnostics = Diagnostica::model()->ordered()->findAll();

		$this->render('index', compact(
			'diagnostics'
		));
	}

	/**
	 * Просмотр карты сайта
	 *
	 * @param int $id идентификатор диагностики
	 *
	 * @return void
	 */
	public function actionView($id)
	{
		$cityId = Yii::app()->city->getCityId();
		$diagnostic = DiagnosticaModel::model()->findByPk($id);

		$this->setTitle('Карта сайта. Диагностические центры по станциям метро');

		$stations = StationModel::model()->inCity($cityId)->findAll(array('order' => 't.name'));
		$areas = AreaMoscow::model()->findAll();
		$districts = DistrictModel::model()->inCity($cityId)->findAll(array('order' => 't.name'));

		$this->render('view', compact(
			'diagnostic',
			'stations',
			'areas',
			'districts'
		));
	}

	/**
	 * Гео-страница диагностических центров
	 */
	public function actionAll()
	{
		$cityId = Yii::app()->city->getCityId();
		$stations = StationModel::model()->inCity($cityId)->findAll(array('order' => 't.name'));
		$districts = DistrictModel::model()->inCity($cityId)->findAll(array('order' => 't.name'));
		$regCities = RegCityModel::model()->inCity($cityId)->findAll(array('order' => 't.name'));
		$this->render('all', compact(
			'stations',
			'districts',
			'regCities'
		));
	}

}