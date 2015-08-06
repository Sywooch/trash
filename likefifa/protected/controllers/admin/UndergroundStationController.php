<?php

use likefifa\models\CityModel;

class UndergroundStationController extends BackendController
{
	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate($model = null)
	{
		$model = new UndergroundStation;

		$this->breadcrumbs=array(
			'Станции метро'=>array('index'),
			'Создание станции метро',
		);
		$this->pageTitle = 'Создание станции метро';

		if (isset($_POST['UndergroundStation'])) {
			$model->attributes = $_POST['UndergroundStation'];
			if ($model->save()) {
				$this->redirect(array('index', 'id' => $model->id));
			}
		}

		$this->render(
			'form',
			array(
				'model' => $model,
				'citiesList' => [],
				'stationsCities' => []
			)
		);
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 *
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id)
	{
		$model = $this->loadModel($id);

		$this->breadcrumbs=array(
			'Станции метро'=>array('index'),
			'Изменение станции метро',
		);
		$this->pageTitle = 'Изменение станции метро';

		$citiesList = array();
		$cities = CityModel::model()->active()->orderByTitle()->findAll();
		foreach ($cities as $city) {
			if ($city->id == 1) {
				continue;
			}
			$citiesList[$city->id] = $city->name;
		}
		$stationsCities = array();
		foreach ($model->city as $city) {
			$stationsCities[] = $city->id;
		}

		if (isset($_POST['UndergroundStation'])) {

			$citiesIds = !empty($_POST['cities']) ? $_POST['cities'] : array();
			$oldCitiesIds = array();
			foreach ($model->city as $city) {
				$oldCitiesIds[] = $city->id;
			}
			$deleteCities =
				UndergroundStationCity::model()->findAll(
					'underground_station_id = ' .
					$model->id .
					($citiesIds ? ' AND city_id NOT IN (' . implode(', ', $citiesIds) . ')' : '')
				);
			foreach ($deleteCities as $city) {
				$city->delete();
			}
			foreach ($citiesIds as $cityId) {
				if (!in_array($cityId, $oldCitiesIds)) {
					$stationCity = new UndergroundStationCity;
					$stationCity->underground_station_id = $model->id;
					$stationCity->city_id = $cityId;
					$stationCity->save();
				}
			}

			$model->attributes = $_POST['UndergroundStation'];
			if ($model->save()) {
				$this->redirect(array('index'));
			}
		}

		$this->render(
			'form',
			compact(
				'model',
				'citiesList',
				'stationsCities'
			)
		);
	}

	/**
	 * @param integer $id the ID of the model to be deleted
	 *
	 * @throws CHttpException
	 */
	public function actionDelete($id)
	{
		if (Yii::app()->request->isPostRequest) {
			// we only allow deletion via POST request
			$this->loadModel($id)->delete();

			// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
			if (!isset($_GET['ajax'])) {
				$this->saveRedirect();
			}
		} else {
			throw new CHttpException(400, 'Invalid request. Please do not repeat this request again.');
		}
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		$model = new UndergroundStation('search');
		if (isset($_GET['UndergroundStation'])) {
			$model->attributes = $_GET['UndergroundStation'];
		}

		$this->render(
			'index',
			array(
				'model' => $model,
			)
		);
	}

	/**
	 * @param $id
	 *
	 * @return UndergroundStation
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model = UndergroundStation::model()->findByPk($id);
		if ($model === null) {
			throw new CHttpException(404, 'The requested page does not exist.');
		}
		return $model;
	}
}
