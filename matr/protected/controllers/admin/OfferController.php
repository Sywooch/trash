<?php

class OfferController extends BackendController
{

	public function actionView($id)
	{
		$model = $this->loadModel($id);

		$model->is_read = 1;
		$model->save();

		$this->render('view', compact("model"));
	}

	public function actionDelete($id)
	{
		$this->loadModel($id)->delete();

		$this->redirect(array("index"));
	}

	public function actionIndex()
	{
		$model = new Offer('search');
		$model->unsetAttributes();
		$get = Yii::app()->request->getQuery("Offer");
		if ($get) {
			$model->attributes = $get;
		}

		$model->DbCriteria->order = "t.is_read, t.id DESC";

		$this->render('index', compact("model"));
	}

	public function loadModel($id)
	{
		$model = Offer::model()->findByPk($id);
		if ($model === null) {
			throw new CHttpException(404, 'Такого ID не существует!');
		}

		return $model;
	}

	protected function performAjaxValidation($model)
	{
		$ajax = Yii::app()->request->getPost("ajax");
		if ($ajax && $ajax === 'offer-form') {
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}