<?php

namespace dfs\docdoc\back\controllers;

use dfs\docdoc\models\PageModel;
use CHttpException;
use Yii;
use CModel;
use CHtml;
use CActiveForm;

/**
 * Файл класса PageController.
 *
 * Контроллер SEO страниц
 *
 * @author  Mikhail Vasilyev <m.vasilyev@likefifa.ru>
 * @link    https://docdoc.megaplan.ru/task/1003885/card/
 * @package dfs.docdoc.back.controllers
 */
class PageController extends BackendController
{

	/**
	 * Создает новую модель.
	 *
	 * @return void
	 */
	public function actionCreate()
	{
		$model = new PageModel('backend');

		$modelName = CHtml::modelName($model);
		$post = Yii::app()->request->getPost($modelName);
		if ($post) {
			$model->attributes = $post;
			if ($model->save()) {
				$this->redirect(array("index"));
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
			$model->attributes = $post;
			if ($model->save()) {
				$this->redirect(array('index'));
			}
		}

		$this->render("update", compact("model"));
	}

	/**
	 * Обновлеет конкретную модель.
	 *
	 * @param int $id идентификатор модели, которая будет удаляться
	 *
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
		$model = new PageModel('search');
		$modelName = CHtml::modelName($model);
		$get = Yii::app()->request->getQuery($modelName);
		if ($get) {
			$model->attributes = $get;
		}

		if (!isset($get["site"])) {
			$model->site = null;
		}
		if (!isset($get["id_city"])) {
			$model->id_city = null;
		}
		if (!isset($get["is_show"])) {
			$model->is_show = null;
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
	 * @return PageModel
	 */
	public function loadModel($id)
	{
		$model = PageModel::model()->findByPk($id);
		$model->setScenario('backend');
		if ($model === null) {
			throw new CHttpException(404, 'The requested page does not exist.');
		}

		return $model;
	}

	/**
	 * AJAX валидация
	 *
	 * @param PageModel $model модель района
	 *
	 * @return void
	 */
	protected function performAjaxValidation($model)
	{
		if (Yii::app()->request->getPost("ajax") === 'page-form') {
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
