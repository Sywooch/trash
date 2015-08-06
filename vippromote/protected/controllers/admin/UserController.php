<?php

class UserController extends BackendController
{

	public function actionCreate()
	{
		$model = new User;

		$post = Yii::app()->request->getPost("User");
		if ($post) {
			$model->attributes = $post;
			$password = $post["password"];
			$model->password = UserIdentity::getPassword($password);
			if ($model->save()) {
				$model->sendFirstMail($password);
				$this->redirect(array('index'));
			}
		}

		$this->render("create", compact("model"));
	}

	public function actionUpdate($id)
	{
		$model = $this->loadModel($id);

		$post = Yii::app()->request->getPost("User");
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
		$model = new User('search');
		$model->unsetAttributes();
		$get = Yii::app()->request->getQuery("User");
		if ($get) {
			$model->attributes = $get;
		}

		$model->DbCriteria->order = "t.id DESC";

		$this->render('index', compact("model"));
	}

	public function loadModel($id)
	{
		$model = User::model()->findByPk($id);
		if ($model === null) {
			throw new CHttpException(404, 'Пользователя с таким ID не существует!');
		}

		return $model;
	}

	protected function performAjaxValidation($model)
	{
		$ajax = Yii::app()->request->getPost("ajax");
		if ($ajax && $ajax === 'user-form') {
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}