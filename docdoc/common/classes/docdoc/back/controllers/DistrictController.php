<?php

namespace dfs\docdoc\back\controllers;

use dfs\docdoc\models\AreaModel;
use dfs\docdoc\models\ClosestDistrictModel;
use dfs\docdoc\models\DistrictModel;
use dfs\docdoc\models\CityModel;
use CHttpException;
use Yii;
use CModel;
use CHtml;
use CActiveForm;
use Exception;

/**
 * Файл класса DistrictController.
 *
 * Контроллер районов
 *
 * @author  Mikhail Vasilyev <m.vasilyev@likefifa.ru>
 * @link    https://docdoc.megaplan.ru/task/1003804/card/
 * @package dfs.docdoc.back.controllers
 */
class DistrictController extends BackendController
{

	/**
	 * Создает новую модель.
	 *
	 * @return void
	 */
	public function actionCreate()
	{
		$model = new DistrictModel('backend');

		$modelName = CHtml::modelName($model);
		$post = Yii::app()->request->getPost($modelName);
		if ($post) {
			$model->attributes = $post;
			if ($model->save()) {
				$this->_saveClosestDistricts($model);
				$this->redirect(array("index"));
			}
		}
		else {
			if (isset(Yii::app()->session['city'])) {
				$model->id_city = Yii::app()->session['city'];
			}
		}

		$this->render("create", compact("model"));
	}

	/**
	 * Обновлеет конкретную модель.
	 *
	 * @param int $id идентификатор модели, которая будет редактироваться
	 *
	 * @return void
	 */
	public function actionUpdate($id)
	{
		$model = $this->loadModel($id);

		$modelName = CHtml::modelName($model);
		$post = Yii::app()->request->getPost($modelName);
		if ($post) {
			if (empty($post['id_area'])) {
				$post['id_area'] = 0;
			}
			$model->attributes = $post;
			if ($model->save()) {
				$this->_saveClosestDistricts($model);
				$this->redirect(array('index'));
			}
		}

		$this->render("update", compact("model"));
	}

	/**
	 * Сохраняет ближайшие районы
	 *
	 * @param DistrictModel $district модель района
	 *
	 * @throws Exception
	 * @throws \CDbException
	 */
	private function _saveClosestDistricts(DistrictModel $district)
	{
		$closestDistricts = Yii::app()->request->getPost("closestDistrict", []);
		$closestDistrictPriority = Yii::app()->request->getPost("closestDistrictPriority", []);

		$transaction = $district->dbConnection->beginTransaction();
		try {
			foreach ($district->closestDistricts as $closestDistrict) {
				if (!$closestDistrict->delete()) {
					$transaction->rollback();
				}
			}

			foreach ($closestDistricts as $key => $value) {
				$model = new ClosestDistrictModel();
				$model->district_id = $district->id;
				$model->closest_district_id = $key;
				$model->priority = !empty($closestDistrictPriority[$key]) ? $closestDistrictPriority[$key] : 0;
				if (!$model->save()) {
					$transaction->rollback();
				}
			}

			$transaction->commit();
		} catch (Exception $e) {
			$transaction->rollback();
			throw $e;
		}
	}

	/**
	 * Обновлеет конкретную модель.
	 *
	 * @param int $id идентификатор модели, которая будет удаляться
	 *
	 * @throws CDbException
	 * @throws CHttpException
	 *
	 * @return void
	 */
	public function actionDelete($id)
	{
		if (!Yii::app()->request->isPostRequest) {
			throw new CHttpException(400, 'Invalid request. Please do not repeat this request again.');
		}

		$this->loadModel($id)->delete();
		$this->redirect(array("index"));
	}

	/**
	 * Список моделей
	 *
	 * @return void
	 */
	public function actionIndex()
	{
		$model = new DistrictModel('search');
		$modelName = CHtml::modelName($model);
		$get = Yii::app()->request->getQuery($modelName);
		if ($get) {
			$model->attributes = $get;
		}

		$this->render("index", compact("model"));
	}

	/**
	 * Получает модель по идентификатору
	 *
	 * @param int $id идентификатор модели, которая будет загружаться
	 *
	 * @throws CHttpException
	 *
	 * @return DistrictModel
	 */
	public function loadModel($id)
	{
		$model = DistrictModel::model()->findByPk($id);
		$model->setScenario('backend');
		if ($model === null) {
			throw new CHttpException(404, 'The requested page does not exist.');
		}

		return $model;
	}

	/**
	 * AJAX валидация
	 *
	 * @param DistrictModel $model модель района
	 *
	 * @return void
	 */
	protected function performAjaxValidation($model)
	{
		if (Yii::app()->request->getPost("ajax") === 'district-form') {
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}

	/**
	 * Выводит на экран список округов
	 *
	 * @param int $id     идентификатор города
	 * @param int $areaId идентификатор активного округа
	 *
	 * @throws \CHttpException
	 *
	 * @return void
	 */
	public function actionAreaList($id, $areaId)
	{
		$model = CityModel::model()->findByPk($id);
		if (!$model) {
			throw new CHttpException(404, "Не существует города с таким ID");
		}

		$list = [];
		if ($model->isMoscow()) {
			foreach (AreaModel::model()->findAll() as $area) {
				$list[$area->id] = $area->name;
			}
		}

		echo Chtml::dropDownList("dfs_docdoc_models_DistrictModel[id_area]", $areaId, $list);
	}

	public function actionClosestDistricts($cityId, $districtId)
	{
		$model = CityModel::model()->findByPk($cityId);
		if (!$model) {
			throw new CHttpException(404, "Не существует города с таким ID");
		}

		$districts = DistrictModel::model()->inCity($cityId)->findAll();
		$closestDistricts = [];
		if ($districtId) {
			$model = DistrictModel::model()->findByPk($districtId);
			$closestDistricts = $model->closestDistricts;
		}

		$list = [];
		foreach ($districts as $district) {
			$priority = null;
			foreach ($closestDistricts as $closestDistrict) {
				if ($closestDistrict->closest_district_id == $district->id) {
					$priority = $closestDistrict->priority;
				}
			}
			$list[] = [
				"id"       => $district->id,
				"name"     => $district->name,
				"priority" => $priority
			];
		}

		$this->renderPartial("_closestDistrict", ["list" => $list]);
	}
}
