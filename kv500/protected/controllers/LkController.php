<?php

class LkController extends FrontendController
{

	public function actionIndex()
	{
		$model = $this->_getModel();

		$this->layout = 'page';
		$this->render("index", compact("model"));
	}

	public function actionPayment()
	{
		$model = $this->_getModel();

		$post = Yii::app()->request->getPost("User");
		if ($post && $post["balanceAdd"]) {
			Yii::app()->session['balanceAdd'] = $post["balanceAdd"];
			Yii::app()->session['userId'] = $model->id;
			if (IS_PAYMENT) {
				$this->redirect("/lk/pay");
			} else {
				$this->redirect("/payment/payment");
			}

		}

		$this->layout = 'page';
		$this->render("payment", compact("model"));
	}

	public function actionContacts()
	{
		$model = $this->_getModel();

		$post = Yii::app()->request->getPost("User");
		if ($post) {
			$model->attributes = $post;
			if ($model->save()) {
				$this->redirect("/lk");
			}
		}

		$this->layout = 'page';
		$this->render("contacts", compact("model"));
	}

	private function _getModel()
	{
		$userId = User::model()->getUserId();
		if (!$userId) {
			$this->redirect("/logout");
		}

		$model = User::model()->findByPk($userId);
		if (!$model) {
			$this->redirect("/logout");
		}

		return $model;
	}

	public function actionGet()
	{
		$model = $this->_getModel();
		$paymentMoney = new PaymentMoney;


		$post = Yii::app()->request->getPost("PaymentMoney");
		if ($post) {
			$paymentMoney->user_id = $model->id;
			$paymentMoney->withdrawal = $model->balance_personal;
			$paymentMoney->text = $post["text"];
			if ($paymentMoney->save()) {
				$this->redirect("/lk/getSuccess/");
			}
		}

		$this->layout = 'page';
		$this->render("get", compact("model", "paymentMoney"));
	}

	public function actionGetSuccess()
	{
		$model = $this->_getModel();

		$this->layout = 'page';
		$this->render("get_success", compact("model"));
	}

	public function actionPay()
	{
		$model = $this->_getModel();
		$this->layout = 'page';

		$balanceAdd = Yii::app()->session['balanceAdd'];
		if ($balanceAdd) {
			$this->render("pay", compact("model", "balanceAdd"));
		}

	}
}