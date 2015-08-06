<?php

class TextController extends BackendController
{

	public function actionUpdate($id)
	{
		$model = $this->loadModel($id);

		$post = Yii::app()->request->getPost("Text");
		if ($post) {
			$model->text = $post["text"];
			if ($model->save()) {
				$this->redirect(array('index'));
			}
		}

		$this->render('update', compact("model"));
	}

	public function actionIndex()
	{
		$model = new Text('search');
		$model->unsetAttributes();
		$get = Yii::app()->request->getQuery("Text");
		if ($get) {
			$model->attributes = $get;
		}

		$model->DbCriteria->order = "t.id";

		$this->render('index', compact("model"));
	}

	public function loadModel($id)
	{
		$model = Text::model()->findByPk($id);
		if ($model === null) {
			throw new CHttpException(404, 'Текста с таким ID не существует!');
		}

		return $model;
	}

	protected function performAjaxValidation($model)
	{
		$ajax = Yii::app()->request->getPost("ajax");
		if ($ajax && $ajax === 'text-form') {
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}