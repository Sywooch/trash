<?php

class ActivationCodesController extends BackendController
{

	public function actionCreate()
	{
		$model = new ActivationCode;

		$post = Yii::app()->request->getPost("ActivationCode");
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

		$post = Yii::app()->request->getPost("ActivationCode");
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
		$model = new ActivationCode('search');
		$model->unsetAttributes();
		$get = Yii::app()->request->getQuery("ActivationCode");
		if ($get) {
			$model->attributes = $get;
		}

		$model->DbCriteria->order = "t.is_active DESC, t.id DESC";

		$this->render('index', compact("model"));
	}

	public function loadModel($id)
	{
		$model = ActivationCode::model()->findByPk($id);
		if ($model === null) {
			throw new CHttpException(404, 'Кода с таким ID не существует!');
		}

		return $model;
	}
}