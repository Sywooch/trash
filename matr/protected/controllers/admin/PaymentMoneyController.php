<?php

class PaymentMoneyController extends BackendController
{

	public function actionView($id)
	{
		$model = $this->loadModel($id);

		$post = Yii::app()->request->getPost("edit");
		if ($post) {
			if ($model->user->getBalance() >= $model->withdrawal) {
				$diff = $model->user->getBalance() - $model->withdrawal;

				$criteria = new CDbCriteria();
				$criteria->condition = "t.group_number = {$model->user->group_number}";
				foreach (User::model()->findAll($criteria) as $u) {
					$u->balance_personal = 0;
					$u->save();
				}

				$model->user->balance_personal = $diff;

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