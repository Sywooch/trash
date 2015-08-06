<?php

namespace dfs\docdoc\back\controllers;

use dfs\docdoc\models\PartnerCostModel;
use CHttpException;
use Yii;
use CModel;
use CHtml;

/**
 * Файл класса PartnerCostController.
 *
 * Контроллер телефонов
 *
 * @author  Mikhail Vasilyev <mvasilyev@docdoc.ru>
 * @link    https://docdoc.atlassian.net/browse/DD-21
 * @package dfs.docdoc.back.controllers
 */
class PartnerCostController extends BackendController
{

	/**
	 * Создает новую модель.
	 *
	 * @return void
	 */
	public function actionCreate()
	{
		$model = new PartnerCostModel('backend');

		$post = Yii::app()->request->getPost(CHtml::modelName($model));
		if ($post) {
			$model->attributes = $post;
			if ($model->save()) {
				$this->redirect(array("index"));
			}
		}

		$this->breadcrumbs = array(
			'Стоимости заявок для партнера' => array('index'),
			'Добавление стоимости',
		);
		$h1 = "Добавление стоимости";

		$this->render("form", compact("model", "h1"));
	}

	/**
	 * Обновлеет конкретную модель.
	 *
	 * @param integer $id идентификатор модели, которая будет редактироваться
	 *
	 * @return void
	 */
	public function actionUpdate($id)
	{
		$model = $this->loadModel($id);

		$post = Yii::app()->request->getPost(CHtml::modelName($model));
		if ($post) {
			$model->attributes = $post;
			if ($model->save()) {
				$this->redirect(array('index'));
			}
		}

		$this->breadcrumbs = array(
			'Стоимости заявок для партнера' => array('index'),
			'Редактирование стоимости',
		);
		$h1 = "Редактирование стоимости №{$model->id}";

		$this->render("form", compact("model", "h1"));
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
		if (!Yii::app()->request->isPostRequest) {
			throw new CHttpException(400, 'Неверный запрос');
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
		$model = new PartnerCostModel('search');
		$model->unsetAttributes();

		$get = Yii::app()->request->getQuery(CHtml::modelName($model));
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
	 * @return PartnerCostModel
	 */
	public function loadModel($id)
	{
		$model = PartnerCostModel::model()->findByPk($id);
		if (!$model) {
			throw new CHttpException(404, 'Стоимости с данным идентификатором не существует');
		}
		$model->setScenario('backend');

		return $model;
	}
}
