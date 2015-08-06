<?php

/**
 * Главный контроллер
 *
 * Class SiteController
 */
class SiteController extends FrontendController
{
	/**
	 * Лэйаут
	 *
	 * @var string
	 */
	public $layout = '//layouts/main';

	/**
	 * Declares class-based actions.
	 */
	public function actions()
	{
		return array(
			// captcha action renders the CAPTCHA image displayed on the contact page
			'captcha' => array(
				'class'     => 'CCaptchaAction',
				'backColor' => 0xFFFFFF,
			),
			// page action renders "static" pages stored under 'protected/views/site/pages'
			// They can be accessed via: index.php?r=site/page&view=FileName
			'page'    => array(
				'class' => 'CViewAction',
			),
		);
	}

	/**
	 * Главная страница по диагностике
	 */
	public function actionIndex()
	{
		$diagnostics = Diagnostica::model()
			->ordered()
			->findAll();

		$diagnosticsListItems = array();
		foreach ($diagnostics as $diagnostic) {
			$diagnosticsListItems[$diagnostic->id] = $diagnostic->name;
		}

		$stations = UndergroundStation::model()
			->ordered()
			->orderedByLine()
			->with('undergroundLine')
			->findAll();

		$this->render(
			'diagnostics',
			compact(
				'diagnostics',
				'diagnosticsListItems',
				'stations'
			)
		);
	}

	/**
	 * This is the action to handle external exceptions.
	 */
	public function actionError()
	{
		if ($error = Yii::app()->errorHandler->error) {
			if (Yii::app()->request->isAjaxRequest) {
				echo $error['message'];
			} else {
				$this->render('error', $error);
			}
		}
	}

	/**
	 * Robots.txt
	 *
	 * @return void
	 */
	public function actionRobots()
	{
		header('Content-Type: text/plain');
		$this->renderPartial("robots", ["host" => Yii::app()->city->getDiagnosticHost()]);
	}

	/**
	 * Смена города
	 */
	public function actionChangeCity()
	{
		$cityId = Yii::app()->request->getParam('cityId') ?: 1;

		Yii::app()
			->city
			->changeCity($cityId)
			->redirect();
	}
}
