<?php

namespace dfs\docdoc\back\controllers;

use dfs\docdoc\models\PhoneModel;
use CHttpException;
use Yii;
use CHtml;

/**
 * Файл класса PhoneController.
 *
 * Контроллер телефонов
 *
 * @author  Mikhail Vasilyev <mvasilyev@docdoc.ru>
 * @link    https://docdoc.atlassian.net/browse/DD-21
 * @package dfs.docdoc.back.controllers
 */
class PhoneController extends BackendController
{

	/**
	 * Создает новую модель.
	 *
	 * @return void
	 */
	public function actionCreate()
	{
		$model = new PhoneModel('backend');

		$post = Yii::app()->request->getPost(CHtml::modelName($model));
		if ($post) {
			$model->attributes = $post;
			if ($model->save()) {
				$this->redirect(array("index"));
			}
		}

		$this->breadcrumbs = array(
			'Телефоны' => array('index'),
			'Добавление телефона',
		);
		$h1 = "Добавление телефона";

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
			'Телефоны' => array('index'),
			'Редактирование телефона',
		);
		$h1 = "Редактирование телефона №{$model->id}";

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
		$model = new PhoneModel('search');
		$model->unsetAttributes();
		$model->dbCriteria->order = $model->getTableAlias() . '.id desc';

		if ($get = Yii::app()->request->getQuery(CHtml::modelName($model))) {
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
	 * @return PhoneModel
	 */
	public function loadModel($id)
	{
		$model = PhoneModel::model()->findByPk($id);
		if (!$model) {
			throw new CHttpException(404, 'Телефона с данным идентификатором не существует');
		}
		$model->setScenario('backend');

		return $model;
	}

	/**
	 * Выводит на экран JSON список телефонов
	 *
	 * @param string $term искомое совпадение
	 *
	 * @throws CHttpException
	 *
	 * @return void
	 */
	public function actionPhones($term)
	{
		if (!$term) {
			throw new CHttpException("404", "Некорректный запрос");
		}

		echo json_encode(PhoneModel::model()->getFormatListByTerm($term));
	}
}
