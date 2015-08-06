<?php

use likefifa\models\AdminModel;

/**
 * Файл класса AdminsController.
 *
 * Контроллер для администраторов в БО
 *
 * @author  Mikhail Vasilyev <m.vasilyev@likefifa.ru>
 * @link    https://docdoc.megaplan.ru/task/1002402/card/
 * @package controllers.admin
 */
class AdminsController extends BackendController
{

	/**
	 * Создание администратора
	 *
	 * @param AdminModel $model
	 *
	 * @return void
	 */
	public function actionCreate($model = null)
	{
		if($model == null) {
			$model = new AdminModel;
			$this->breadcrumbs = array(
				'Администраторы' => array('index'),
				'Добавление администратора',
			);
			$this->pageTitle = 'Добавление администратора';
		}

		$post = Yii::app()->request->getPost(CHtml::modelName($model));
		if ($post) {
			$model->attributes = $post;
			if ($model->save()) {
				$this->redirect(array('index'));
			}
		}

		$this->render("form", compact("model"));
	}

	/**
	 * Обновление администратора
	 *
	 * @param int $id идентификатор модели
	 *
	 * @return bool
	 */
	public function actionUpdate($id)
	{
		$model = $this->loadModel($id);
		$this->breadcrumbs = array(
			'Администраторы' => array('index'),
			'Изменение администратора',
		);
		$this->pageTitle = 'Добавление администратора №' . $model->id;
		$this->actionCreate($model);
	}

	/**
	 * Удаляет администратора
	 *
	 * @param int $id идентификатор модели
	 */
	public function actionDelete($id)
	{
		$this->loadModel($id)->delete();

		$this->redirect(array("index"));
	}

	/**
	 * Список администраторов
	 *
	 * @return void
	 */
	public function actionIndex()
	{
		$model = new AdminModel('search');
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
	 * @return AdminModel
	 *
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model = AdminModel::model()->findByPk($id);
		if ($model === null) {
			throw new CHttpException(404, 'Администратора с таким ID не существует!');
		}

		return $model;
	}
}
