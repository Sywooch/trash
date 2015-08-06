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

		$type = Yii::app()->request->getPost("type");
		$criteria = new CDbCriteria();
		$criteria->addCondition("t.group_number = {$model->group_number}");
		if ($type == 0) {
			Yii::app()->session['balanceAdd'] = 1500;
			$criteria->addCondition("t.type = 0");
		} else if ($type == 1) {
			Yii::app()->session['balanceAdd'] = 3500;
			$criteria->addCondition("t.type = 1");
		} else if ($type == 2) {
			Yii::app()->session['balanceAdd'] = 5500;
			$criteria->addCondition("t.type = 2");
		}

		$model = User::model()->find($criteria);

		$activationCode = Yii::app()->request->getPost("activation_code");
		if ($activationCode) {
			$criteria = new CDbCriteria();
			$criteria->condition = "t.code = :code AND t.is_active = 0";
			$criteria->params[":code"] = $activationCode;
			$codeModel = ActivationCode::model()->find($criteria);
			if ($codeModel) {
				Yii::app()->session['payTo'] = 2;
				Yii::app()->session['userId'] = $model->id;
				$codeModel->is_active = 1;
				$codeModel->save();
				$this->redirect("/payment/payment");
			}
		}

		$post = Yii::app()->request->getPost("User");
		if ($post) {
			Yii::app()->session['payTo'] = !empty($post["payTo"]) ? $post["payTo"] : 2;
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
			$paymentMoney->withdrawal = $model->getBalance();
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