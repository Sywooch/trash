<?php

class UserController extends FrontendController
{

	public function actionLogin()
	{
		$model = User::model();

		$this->render("login", compact("model"));
	}

	public function actionCheckLogin()
	{
		echo User::login(Yii::app()->request->getPost("User"));
	}

	public function actionRegistration()
	{
		$this->layout = "page";

		$model = new User;

		if(isset($_POST['User'])) {
			if($model->save()) {
				$identity = new UserIdentity($model->email, $model->password);
				Yii::app()->user->login($identity, 60 * 60 * 24 * 30);

				$this->redirect("/lk/add/");
			}
		}

		$this->render("registration", compact("model"));
	}
}