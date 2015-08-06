<?php

use likefifa\models\RegionModel;

/**
 * Файл класса RegionController.
 *
 * Контроллер для администраторов в БО
 *
 * @author  Mikhail Vasilyev <m.vasilyev@likefifa.ru>
 * @link    https://docdoc.megaplan.ru/task/1003365/card/
 * @package controllers.admin
 */
class RegionController extends BackendController
{

	/**
	 * Создание региона
	 *
	 * @return void
	 */
	public function actionCreate()
	{
		$model = new RegionModel;

		$post = Yii::app()->request->getPost(CHtml::modelName($model));
		if ($post) {
			$model->attributes = $post;
			if ($model->save()) {
				$this->redirect(array('index'));
			}
		}

		$this->breadcrumbs = array(
			'Регионы' => array('index'),
			'Добавление',
		);

		$h1 = "Добавить регион";

		$this->render('form', compact("model", "h1"));
	}

	/**
	 * Обновление региона
	 *
	 * @param int $id идентификатор модели
	 *
	 * @return bool
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
			'Регионы' => array('index'),
			'Редактирование',
		);

		$h1 = "Редактирование региона №{$model->id}";

		$this->render('form', compact("model", "h1"));
	}

	/**
	 * Удаление региона
	 *
	 * @param int $id идентификатор модели
	 */
	public function actionDelete($id)
	{
		$this->loadModel($id)->delete();

		$this->redirect(array("index"));
	}

	/**
	 * Список регионов
	 *
	 * @return void
	 */
	public function actionIndex()
	{
		$model = new RegionModel('search');
		$model->unsetAttributes();
		$get = Yii::app()->request->getQuery(CHtml::modelName($model));
		if ($get) {
			$model->attributes = $get;
		}

		$this->render('index', compact("model"));
	}

	/**
	 * Получает модель по идентификатору
	 *
	 * @param integer $id идентификатор модели
	 *
	 * @return RegionModel
	 *
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model = RegionModel::model()->findByPk($id);
		if ($model === null) {
			throw new CHttpException(404, 'Региона с таким ID не существует!');
		}

		return $model;
	}
}
