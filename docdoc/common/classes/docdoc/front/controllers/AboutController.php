<?php

namespace dfs\docdoc\front\controllers;

use dfs\docdoc\components\DocDocStat;
use dfs\docdoc\objects\Phone;
use dfs\docdoc\models\CityModel;
use Yii;

/**
 * Class AboutController
 *
 * @package dfs\docdoc\front\controllers
 */
class AboutController extends FrontController
{
	/**
	 * Меню
	 *
	 * @var array
	 */
	protected $_leftmenu = [
		'about' => [
			'title'      => 'О нас',
			'link'       => '/about',
		],
		'team' => [
			'title'      => 'Команда',
			'link'       => '/about/team',
		],
		'press' => [
			'title'      => 'Пресса',
			'link'       => '/about/press',
		],
		'faq' => [
			'title'      => 'FAQ',
			'link'       => '/about/faq',
		],
		'contacts' => [
			'title'      => 'Контакты',
			'link'       => '/about/contacts',
		],
	];


	/**
	 * Страница "О нас"
	 */
	public function actionIndex()
	{
		$this->initXslTemplates();
		$this->render(
			'index',
			[
				"docDocStat" => new DocDocStat(Yii::app()->params['DocDocStatisticFactor'])
			]
		);
	}

	/**
	 * Страница "Команда"
	 */
	public function actionTeam()
	{
		$this->initXslTemplates();
		$this->render(
			'team',
			[
				"emails" => Yii::app()->params->email,
			]
		);
	}

	/**
	 * Страница "Пресса"
	 */
	public function actionPress()
	{
		$this->initXslTemplates();
		$this->render(
			'press',
			[
				"press" => Yii::app()->params->press
			]
		);
	}

	/**
	 * Страница "FAQ"
	 */
	public function actionFaq()
	{
		$this->initXslTemplates();
		$this->render(
			'faq',
			[
				"partnersEmail" => Yii::app()->params->email["affiliate"]
			]
		);
	}

	/**
	 * Страница "Контакты"
	 */
	public function actionContacts()
	{
		$this->initXslTemplates();
		$this->render(
			'contacts',
			[
				"centralOffice"    => Yii::app()->params->centralOffice,
				"emails"           => Yii::app()->params->email,
				"press"            => Yii::app()->params->press,
				"callCenterPhones" => [
					"msk"    => (new Phone(CityModel::model()->findByPk(1)->site_phone))->prettyFormat(),
					"spb"    => (new Phone(CityModel::model()->findByPk(2)->site_phone))->prettyFormat(),
					"common" => Yii::app()->params->commonPhone,
				]
			]
		);
	}
}
