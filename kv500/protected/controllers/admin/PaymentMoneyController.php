<?php

class PaymentMoneyController extends BackendController
{

	public function actionView($id)
	{
		$model = $this->loadModel($id);

		$post = Yii::app()->request->getPost("edit");
		if ($post) {
			if ($model->user->balance_personal >= $model->withdrawal) {
				$model->user->balance_personal = $model->user->balance_personal - $model->withdrawal;
				$model->is_read = 1;
				if ($model->user->save() && $model->save()) {
					$this->redirect(array('index'));
				}
			}
		}

		$this->render('view', compact("model"));
	}

	public function actionDelete($id)
	{
		$this->loadModel($id)->delete();

		$this->redirect(array("index"));
	}

	public function actionIndex()
	{
		$model = new PaymentMoney('search');
		$model->unsetAttributes();
		$get = Yii::app()->request->getQuery("PaymentMoney");
		if ($get) {
			$model->attributes = $get;
		}

		$model->DbCriteria->order = "t.is_read, t.id DESC";

		$this->render('index', compact("model"));
	}

	public function loadModel($id)
	{
		$model = PaymentMoney::model()->findByPk($id);
		if ($model === null) {
			throw new CHttpException(404, 'Запроса с таким ID не существует!');
		}

		return $model;
	}

	protected function performAjaxValidation($model)
	{
		$ajax = Yii::app()->request->getPost("ajax");
		if ($ajax && $ajax === 'payment_money-form') {
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}