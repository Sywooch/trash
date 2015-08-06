<?php

namespace dfs\docdoc\back\controllers;

use dfs\docdoc\models\ClinicModel;
use dfs\docdoc\models\DoctorClinicModel;
use dfs\docdoc\models\DoctorModel;
use dfs\docdoc\models\RatingStrategyModel;
use dfs\docdoc\objects\Formula;
use CHttpException;
use Yii;
use CHtml;

/**
 * Файл класса RatingStrategyController.
 *
 * Контроллер партнеров
 *
 * @author  Mikhail Vasilyev <mvasilyev@docdoc.ru>
 * @link    https://docdoc.atlassian.net/browse/DD-21
 * @package dfs.docdoc.back.controllers
 */
class RatingStrategyController extends BackendController
{

	public $breadcrumbs = [
		'Рейтинги врачей' => "/2.0/rating",
		"Стратегии" => "/2.0/ratingStrategy/index"
	];

	/**
	 * Обновлеет конкретную модель.
	 *
	 * @param int $id идентификатор модели, которая будет редактироваться
	 *
	 * @return void
	 */
	public function actionUpdate($id)
	{
		$this->actionEdit($id);
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$this->actionEdit(0);
	}

	/**
	 * Создает новую модель.
	 *
	 * @param int $id
	 * @return void
	 *
	 * @throws CHttpException
	 */
	public function actionEdit($id = 0)
	{
		$model = $this->loadModel($id);
		$this->render("form", ["model" => $model]);
	}

	/**
	 * Обновляет конкретную модель.
	 *
	 * @param int $id
	 * @return void
	 */
	public function actionSave($id = 0)
	{
		$model = $this->loadModel($id);

		if ($model->save()) {
			$this->redirect(
				Yii::app()->createUrl("ratingStrategy/index")
			);
		}

		$this->render("form", ["model" => $model]);
	}

	/**
	 * Удаляет модель
	 *
	 * @param integer $id идентификатор модели, которая будет удаляться
	 *
	 * @throws CHttpException
	 *
	 * @return void
	 */
	public function actionDelete($id)
	{
		$model = $this->loadModel($id, 'delete');
		$model->delete();

		$this->redirect(
			Yii::app()->createUrl("ratingStrategy/index")
		);
	}

	/**
	 * Список моделей
	 *
	 * @return void
	 */
	public function actionIndex()
	{
		$model = $this->loadModel(null, 'search');
		$this->render("index", ['model' => $model]);
	}

	/**
	 * Проверка формулы
	 *
	 * @param $id
	 */
	public function actionCheck($id)
	{
		$vars = [];
		$model = $this->loadModel($id, 'search');

		$clinicId = Yii::app()->request->getParam('clinicId');
		$doctorId = Yii::app()->request->getParam('doctorId');

		$vars['model'] = $model;

		if (!empty($clinicId)) {
			$vars['formula'] = new Formula($model->params);
			$vars['clinic'] = ClinicModel::model()->findByPk($clinicId);
			$object = $vars['clinic'];
			if ($model->for_object == $model::FOR_DOCTOR && !empty($doctorId)) {
				$vars['doctor'] = DoctorModel::model()->findByPk($doctorId);
				$object = DoctorClinicModel::model()->findDoctorClinic($doctorId, $clinicId);
			}

			if (!is_null($object)) {
				$vars['result'] = $model->calcRatingFormula($object);
			}
		}

		$this->render("validator", $vars);
	}

	/**
	 * Получает модель по идентификатору
	 *
	 * @param int $id идентификатор модели, которая будет загружаться
	 * @param string $scenario
	 *
	 * @throws CHttpException
	 *
	 * @return RatingStrategyModel
	 */
	public function loadModel($id = null, $scenario = 'insert')
	{
		$model = ($id > 0) ? RatingStrategyModel::model()->findByPk($id) : new RatingStrategyModel($scenario);

		if (!$model) {
			throw new CHttpException(404, 'Стратегия с данным идентификатором не существует');
		}

		if ($scenario == 'delete') {
			return $model;
		}

		$get = Yii::app()->request->getQuery(CHtml::modelName($model));
		if ($get) {
			$model->attributes = $get;
		}

		$post = Yii::app()->request->getPost(CHtml::modelName($model));

		if ($post) {
			$model->attributes = $post;
		}

		return $model;
	}
}
