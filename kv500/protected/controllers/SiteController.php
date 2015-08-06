<?php

class SiteController extends FrontendController
{

	/**
	 * Главная страница
	 *
	 * @return void
	 */
	public function actionIndex()
	{
		$this->title = "Квартал 500";
		$this->layout = 'page';

		$inviteUserId = Yii::app()->request->getQuery("invite_user");
		if ($inviteUserId) {
			$user = User::model()->findByPk($inviteUserId);
			if ($user) {
				Yii::app()->session['invite_user'] = $user->id;
			}
		}

		$model = new User;
		$post = Yii::app()->request->getPost("User");
		if ($post) {
			$model->attributes = $post;
			$password = $model->makePassword();
			$model->password = UserIdentity::getPassword($password);
			if (Yii::app()->session['invite_user']) {
				$model->parent_id = Yii::app()->session['invite_user'];
			}

			if ($model->save()) {
				$model->sendFirstMail($password);
				$identity = new UserIdentity($model->email, $model->password);
				Yii::app()->user->login($identity, 60 * 60 * 24 * 30);
				$this->redirect("/lk/payment/");
			}
		}

		$this->render("index", compact("model"));
	}

	/**
	 * Displays the login page
	 */
	public function actionLogin()
	{
		$this->layout = '//layouts/admin';

		$model = new LoginForm;

		// if it is ajax validation request
		if (isset($_POST['ajax']) && $_POST['ajax'] === 'login-form') {
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}

		// collect user input data
		if (isset($_POST['LoginForm'])) {
			$model->attributes = $_POST['LoginForm'];
			// validate user input and redirect to the previous page if valid
			if ($model->validate() && $model->login()) {
				$this->redirect(Yii::app()->user->returnUrl);
			}
		}
		// display the login form
		$this->render('login', array('model' => $model));
	}

	public function actionLogout()
	{
		Yii::app()->user->logout();
		$this->redirect($this->createUrl('site/index'));
	}

	public function actionRecovery()
	{
		$this->title = "Восстановление пароля";
		$this->layout = 'page';
		$model = new User;
		$success = false;

		$post = Yii::app()->request->getPost("User");
		if ($post) {
			$success = true;
			$criteria = new CDbCriteria;
			$criteria->condition = "t.email = :email";
			$criteria->params["email"] = $post["email"];
			$model = User::model()->find($criteria);
			if ($model) {
				$password = $model->makePassword();
				$model->password = UserIdentity::getPassword($password);
				if ($model->save()) {
					$model->sendRecoveryMail($password);
				}
			}
		}

		$this->render("recovery", compact("model", "success"));
	}
}