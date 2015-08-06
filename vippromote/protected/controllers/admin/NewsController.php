<?php

class NewsController extends BackendController
{

	public function actionCreate()
	{
		$model = new News;

		$post = Yii::app()->request->getPost("News");
		if ($post) {
			$model->attributes = $post;
			if ($post["date"]) {
				$model->date = $model->getTimestamp($post["date"]);
			}
			if ($model->save()) {
				$this->redirect(array('index'));
			}
		}

		$this->render("create", compact("model"));
	}

	public function actionUpdate($id)
	{
		$model = $this->loadModel($id);

		$post = Yii::app()->request->getPost("News");
		if ($post) {
			$model->attributes = $post;
			if ($post["date"]) {
				$model->date = $model->getTimestamp($post["date"]);
			}
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
		$model = new News('search');
		$model->unsetAttributes();
		$get = Yii::app()->request->getQuery("News");
		if ($get) {
			$model->attributes = $get;
		}

		$model->DbCriteria->order = "t.date DESC, t.id DESC";

		$this->render('index', compact("model"));
	}

	public function loadModel($id)
	{
		$model = News::model()->findByPk($id);
		if ($model === null) {
			throw new CHttpException(404, 'Новости с таким ID не существует!');
		}

		return $model;
	}

	protected function performAjaxValidation($model)
	{
		$ajax = Yii::app()->request->getPost("ajax");
		if ($ajax && $ajax === 'news-form') {
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}