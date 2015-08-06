<?php
/**
 * Created by PhpStorm.
 * User: atyutyunnikov
 * Date: 25.03.15
 * Time: 12:38
 */

namespace dfs\docdoc\back\controllers;

use dfs\docdoc\models\PhoneProviderModel;
use Yii;
use CHtml;

/**
 * Провайдеры телефонов
 *
 * Class PhoneProviderController
 *
 * @package dfs\docdoc\back\controllers
 */
class PhoneProviderController extends BackendController
{
	/**
	 * Список
	 */
	public function actionIndex()
	{
		$model = new PhoneProviderModel('search');
		$model->unsetAttributes();

		$get = Yii::app()->request->getQuery(CHtml::modelName($model));

		if ($get) {
			$model->attributes = $get;
		}

		$this->render("index", ["model" => $model]);
	}

	public function actionCreate()
	{
		$model = new PhoneProviderModel('backend');
		$post = Yii::app()->request->getPost(CHtml::modelName($model));

		if ($post) {
			$model->attributes = $post;

			if ($model->save()) {
				$this->redirect(["index"]);
			}
		}

		$this->breadcrumbs = [
			'Телефонный провайдеры' => ['index'],
			'Добавление провайдера',
		];

		$this->render("form", ["model" => $model]);
	}

	public function actionUpdate($id)
	{
		$model = $this->loadModel($id);

		if ($post = Yii::app()->request->getPost(CHtml::modelName($model))) {
			$model->attributes = $post;

			if ($model->save()) {
				$this->redirect(array('index'));
			}
		}

		$this->breadcrumbs = array(
			'Телефонный провайдеры' => ['index'],
			'Редактирование провайдера',
		);

		$this->render("form", ["model" => $model]);
	}

	/**
	 * Удаляет модель
	 *
	 * @param integer $id идентификатор модели, которая будет удаляться
	 *
	 * @throws \CHttpException
	 *
	 * @return void
	 */
	public function actionDelete($id)
	{
		if (!Yii::app()->request->isPostRequest) {
			throw new \CHttpException(400, 'Неверный запрос');
		}

		$this->loadModel($id)->delete();
		$this->redirect(array("index"));
	}

	/**
	 * Получает модель по идентификатору
	 *
	 * @param int $id идентификатор модели, которая будет загружаться
	 *
	 * @throws \CHttpException
	 *
	 * @return PhoneProviderModel
	 */
	public function loadModel($id)
	{
		if (!($model = PhoneProviderModel::model()->findByPk($id))) {
			throw new \CHttpException(404, 'Телефона с данным идентификатором не существует');
		}

		$model->setScenario('backend');

		return $model;
	}
}