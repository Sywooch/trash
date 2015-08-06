<?php

use likefifa\models\DevEvent;

class DevEventsController extends BackendController
{
	public function actionIndex()
	{
		$criteria = new CDbCriteria;
		$criteria->order = 't.date desc';
		$dataProvider = new CActiveDataProvider('likefifa\models\DevEvent', [
			'criteria' => $criteria
		]);
		$this->render('index', compact('dataProvider'));
	}

	/**
	 * Создание события
	 *
	 * @param DevEvent $model
	 *
	 * @return void
	 */
	public function actionCreate($model = null)
	{
		if($model == null) {
			$model = new DevEvent;
			$this->breadcrumbs = array(
				'События' => array('index'),
				'Добавление события',
			);
			$this->pageTitle = 'Добавление события';
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
			'события' => array('index'),
			'Изменение события',
		);
		$this->pageTitle = 'Добавление события №' . $model->id;
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
	 * Получает модель по идентификатору
	 *
	 * @param integer $id идентификатор модели
	 *
	 * @return DevEvent
	 *
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model = DevEvent::model()->findByPk($id);
		if ($model === null) {
			throw new CHttpException(404, 'События с таким ID не существует!');
		}

		return $model;
	}
} 