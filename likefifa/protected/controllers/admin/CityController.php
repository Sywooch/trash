<?php

use likefifa\models\CityModel;
use likefifa\models\forms\CityModelAdminForm;

/**
 * Файл класса CityController.
 *
 * Контроллер для администраторов в БО
 *
 * @author  Mikhail Vasilyev <m.vasilyev@likefifa.ru>
 * @link    https://docdoc.megaplan.ru/task/1003365/card/
 * @package controllers.admin
 */
class CityController extends BackendController
{

	/**
	 * Создание города
	 *
	 * @return void
	 */
	public function actionCreate()
	{
		$model = new CityModelAdminForm;

		$post = Yii::app()->request->getPost(CHtml::modelName($model));
		if ($post) {
			$model->attributes = $post;
			if ($model->save()) {
				$this->saveRedirect();
			}
		}

		$this->breadcrumbs = array(
			'Города' => array('index'),
			'Добавление',
		);

		$h1 = "Добавить город";

		$this->render('form', compact("model", "h1"));
	}

	/**
	 * Обновление города
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
				//$this->saveRedirect();
			}
		}

		$this->breadcrumbs = array(
			'Города' => array('index'),
			'Редактирование',
		);

		$h1 = "Редактирование города №{$model->id}";

		$this->render('form', compact("model", "h1"));
	}

	/**
	 * Удаление города
	 *
	 * @param int $id идентификатор модели
	 */
	public function actionDelete($id)
	{
		$this->loadModel($id)->delete();

		$this->redirect(array("index"));
	}

	/**
	 * Список городов
	 *
	 * @return void
	 */
	public function actionIndex()
	{
		$model = new CityModel('search');
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
	 * @return CityModel
	 *
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model = CityModelAdminForm::model()->findByPk($id);
		if ($model === null) {
			throw new CHttpException(404, 'Города с таким ID не существует!');
		}

		return $model;
	}
}
