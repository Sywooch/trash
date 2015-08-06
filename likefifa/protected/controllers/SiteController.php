<?php

class SiteController extends FrontendController
{

	/**
	 * Declares class-based actions.
	 */
	public function actions()
	{
		return array(
			// captcha action renders the CAPTCHA image displayed on the contact page
			'captcha'=>array(
				'class'=>'CCaptchaAction',
				'backColor'=>0xFFFFFF,
			),
			// page action renders "static" pages stored under 'protected/views/site/pages'
			// They can be accessed via: index.php?r=site/page&view=FileName
			'page'=>array(
				'class'=>'CViewAction',
			),
		);
	}

	/**
	 * Главная страница
	 *
	 * @return void
	 */
	public function actionIndex()
	{
		$this->pageTitle = 'LikeFifa: поиск мастеров и салонов красоты в ' .
			Yii::app()->activeRegion->getModel()->name_prepositional;
		$this->metaDescription = 'На нашем портале представлены лучшие мастера и салоны красоты в ' .
			Yii::app()->activeRegion->getModel()->name_prepositional .
			'. У нас Вы можете посмотреть отзывы о мастерах и салонах, посмотреть цены на услуги.';
		$this->metaKeywords = 'мастера красоты, частные мастера, салоны красоты, студии красоты ';

		$this->render("index");
	}

	/**
	 * This is the action to handle external exceptions.
	 */
	public function actionError()
	{
	    if(!($error=Yii::app()->errorHandler->error))
	    {
	    	$error = array(
	    		'code' => '404',
	    		'message' => '',
	    	);
	    }
	    if(Yii::app()->request->isAjaxRequest)
	    	echo $error['message'];
	    else
	    	$this->render($error['code'] == '404' ? '404' : 'error', $error);
	}

	/**
	 * Displays the login page
	 */
	public function actionLogin()
	{
		Yii::app()->setComponent(
			'bootstrap',
			[
				'class' => 'application.vendors.clevertech.yii-booster.src.components.Booster',
			]
		);
		Yii::app()->bootstrap->init();

		Yii::app()->theme = 'admin';
		$this->layout = '//layouts/admin';

		$model=new LoginForm;

		// if it is ajax validation request
		if(isset($_POST['ajax']) && $_POST['ajax']==='login-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}

		// collect user input data
		if(isset($_POST['LoginForm']))
		{
			$model->attributes=$_POST['LoginForm'];
			// validate user input and redirect to the previous page if valid
			if($model->validate() && $model->login())
				$this->redirect(Yii::app()->user->returnUrl);
		}
		// display the login form

		$this->render('login',array('model'=>$model));
	}

	public function actionAdminLogin()
	{
		$this->layout = '//layouts/admin';

		$model=new LoginForm;

		// if it is ajax validation request
		if(isset($_POST['ajax']) && $_POST['ajax']==='login-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}

		// collect user input data
		if(isset($_POST['LoginForm']))
		{
			$model->attributes=$_POST['LoginForm'];
			// validate user input and redirect to the previous page if valid
			if($model->validate() && $model->login())
			$this->redirect('/admin/');
		}
		// display the login form
		$this->render('login',array('model'=>$model));
	}

	/**
	 * Logs out the current user and redirect to homepage.
	 */
	public function actionLogout()
	{
        Yii::app()->user->logout();
        $this->redirect($this->createUrl('site/index'));
	}

	public function actionPage($page) {
		$this->render('pages/'.$page);
	}
}