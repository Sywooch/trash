<?php

class PageController extends FrontendController
{

	public function actionProgram()
	{
		$this->layout = 'page';
		$this->render("program");
	}

	public function actionNews()
	{
		$this->layout = 'page';

		$id = Yii::app()->request->getQuery("id");
		if (!$id) {
			$criteria = new CDbCriteria;
			$criteria->order = "t.date DESC, t.id DESC";
			$models = News::model()->findAll($criteria);
			$this->render("news", compact("models"));
		} else {
			$model = News::model()->findByPk($id);
			$this->render("news_item", compact("model"));
		}
	}

	public function actionDocs()
	{
		$this->layout = 'page';
		$this->render("docs");
	}

	public function actionShop()
	{
		$this->layout = 'page';
		$this->render("shop");
	}

	public function actionAdvertisement()
	{
		$this->layout = 'page';
		$this->render("advertisement");
	}

	public function actionRules()
	{
		$this->layout = 'page';
		$this->render("rules");
	}

	public function actionRegistration()
	{
		$this->layout = 'page';

		$model = new User;
		$post = Yii::app()->request->getPost("User");
		if ($post) {
			$model->attributes = $post;

			$criteria = new CDbCriteria();
			$criteria->order = "group_number DESC";
			$aaa = User::model()->find($criteria);

			$model->attributes = $_POST['User'];
			$model->group_number = $aaa->group_number + 1;

			$password = $model->makePassword();
			$model->password = UserIdentity::getPassword($password);
			if (Yii::app()->session['invite_user']) {
				$model->parent_id = Yii::app()->session['invite_user'];
			}

			if ($model->save()) {

				$new1 = new User;
				$new1->attributes = $model->attributes;
				$new1->email = $model->email . 1;
				$new1->id = null;
				$new1->isNewRecord = true;
				$new1->type = 1;
				$new1->group_number = $aaa->group_number + 1;
				if ($model->parent_id) {
					$new1->parent_id = $model->parent_id + 1;
				}
				$new1->save();

				$new1 = new User;
				$new1->attributes = $model->attributes;
				$new1->email = $model->email . 2;
				$new1->id = null;
				$new1->isNewRecord = true;
				$new1->type = 2;
				$new1->group_number = $aaa->group_number + 1;
				if ($model->parent_id) {
					$new1->parent_id = $model->parent_id + 2;
				}
				$new1->save();

				$model->sendFirstMail($password);
				$identity = new UserIdentity($model->email, $model->password);
				Yii::app()->user->login($identity, 60 * 60 * 24 * 30);
				$this->redirect("/lk/payment/");
			}
		}

		$this->render("registration", compact("model"));
	}

	public function actionFaq()
	{
		$this->layout = 'page';

		$criteria = new CDbCriteria;
		$criteria->order = "t.sort DESC";
		$models = Faq::model()->findAll($criteria);
		$this->render("faq", compact("models"));
	}

	public function actionOffers()
	{
		$this->layout = 'page';

		$model = new Offer;
		$isSend = false;

		$post = Yii::app()->request->getPost("Offer");
		if ($post) {
			$model->attributes = $post;
			if ($model->save()) {
				$isSend = true;
			}
		}

		$this->render("offers", compact("model", "isSend"));
	}

	public function actionContacts()
	{
		$this->layout = 'page';

		$model = new Contacts;
		$isSend = false;

		$post = Yii::app()->request->getPost("Contacts");
		if ($post) {
			$model->attributes = $post;
			if ($model->save()) {
				$isSend = true;
			}
		}

		$this->render("contacts", compact("model", "isSend"));
	}

	public function actionHow()
	{
		$this->layout = 'page';
		$this->render("how");
	}

	public function actionHistory()
	{
		$this->layout = 'page';
		$this->render("history");
	}

}