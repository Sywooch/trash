<?php

class FaqController extends BackendController
{

	public function actionCreate()
	{
		$model = new Faq;

		$post = Yii::app()->request->getPost("Faq");
		if ($post) {
			$model->attributes = $post;
			if ($model->save()) {
				$this->redirect(array('index'));
			}
		}

		$this->render("create", compact("model"));
	}

	public function actionUpdate($id)
	{
		$model = $this->loadModel($id);

		$post = Yii::app()->request->getPost("Faq");
		if ($post) {
			$model->attributes = $post;
			if ($model->save()) {
				$this->redirect(array('index'));
			}
		}

		$this->render('update', compact("model"));
	}

	public function actionView($id)
	{
		$model = $this->loadModel($id);

		$this->render('view', compact("model"));
	}

	public function actionDelete($id)
	{
		$this->loadModel($id)->delete();

		$this->redirect(array("index"));
	}

	public function actionIndex()
	{
		$model = new Faq('search');
		$model->unsetAttributes();
		$get = Yii::app()->request->getQuery("Faq");
		if ($get) {
			$model->attributes = $get;
		}

		$model->DbCriteria->order = "t.sort DESC";

		$this->render('index', compact("model"));
	}

	public function loadModel($id)
	{
		$model = Faq::model()->findByPk($id);
		if ($model === null) {
			throw new CHttpException(404, 'Такого ID не существует!');
		}

		return $model;
	}

	protected function performAjaxValidation($model)
	{
		$ajax = Yii::app()->request->getPost("ajax");
		if ($ajax && $ajax === 'faq-form') {
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}