<?php

namespace dfs\docdoc\back\controllers;

use dfs\docdoc\models\PartnerWidgetModel;
use CHttpException;
use Yii;
use CHtml;

/**
 * Файл класса PartnerWidgetController.
 *
 */
class PartnerWidgetController extends BackendController
{

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
				Yii::app()->createUrl(
					"partnerWidget/index",
					array(
						CHtml::activeName(new PartnerWidgetModel, "partner_id") => $model->partner_id
					)
				)
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
		$model = $this->loadModel($id);
		$model->delete();

		$this->redirect(
			Yii::app()->createUrl(
				"partnerWidget/index",
				array(
					CHtml::activeName(new PartnerWidgetModel, "partner_id") => $model->partner_id
				)
		));
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
	 * Получает модель по идентификатору
	 *
	 * @param int $id идентификатор модели, которая будет загружаться
	 * @param string $scenario
	 *
	 * @throws CHttpException
	 *
	 * @return PartnerWidgetModel
	 */
	public function loadModel($id = null, $scenario = 'insert')
	{
		$model = ($id > 0) ? PartnerWidgetModel::model()->findByPk($id) : new PartnerWidgetModel($scenario);

		if (!$model) {
			throw new CHttpException(404, 'Виджет с данным идентификатором не существует');
		}

		$get = Yii::app()->request->getQuery(CHtml::modelName($model));
		if ($get) {
			$model->attributes = $get;
		}

		$post = Yii::app()->request->getPost(CHtml::modelName($model));

		if ($post) {
			$model->attributes = $post;
		}

		if (!$model->partner) {
			throw new CHttpException(400, "Не выбран партнер");
		}

		$this->breadcrumbs = [
			'Партнеры' => "/2.0/partner",
			"{$model->partner->name}. Виджеты" => Yii::app()->createUrl(
				"partnerWidget/index",
				array(
					CHtml::activeName(new PartnerWidgetModel, "partner_id") => $model->partner_id
				)
			)
		];


		return $model;
	}


}
