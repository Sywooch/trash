<?php

namespace dfs\docdoc\front\controllers;

use dfs\docdoc\components\AppController;
use dfs\docdoc\models\CityModel;
use dfs\docdoc\models\SectorModel;
use dfs\docdoc\models\StationModel;
use dfs\docdoc\objects\Phone;
use dfs\docdoc\components\DocDocStat;
use RussianTextUtils;

require_once ROOT_PATH . '/front/public/include/header.php';

/**
 * Class FrontController
 *
 * @package dfs\docdoc\front\controllers
 */
class FrontController extends AppController
{
	/**
	 * Дефолтный layout для ЛК
	 *
	 * @var string
	 */
	public $layout = 'default';

	/**
	 * Отображение шапки и футера
	 *
	 * @var string
	 */
	public $mode = null;

	/**
	 * Главная страница или нет
	 *
	 * @var bool
	 */
	public $isMainPage = false;

	/**
	 * Мобильная версия сайта или нет
	 *
	 * @var bool
	 */
	public $isMobile = false;

	/**
	 * Страница лендинга
	 *
	 * @var bool
	 */
	public $isLandingPage = false;

	/**
	 * Определение телефона для страницы
	 *
	 * @var Phone
	 */
	public $phoneForPage = null;

	/**
	 * Передача в js данных для отслеживания
	 *
	 * @var array | null
	 */
	public $globalTrack = null;

	/**
	 * Путь до старых xsl-шаблонов
	 *
	 * @var string
	 */
	protected $_pathToXsl = '/front/public/xsl/';


	/**
	 * Выполняем перед любым действием
	 *
	 * @param \CAction $action
	 *
	 * @return bool
	 */
	public function beforeAction($action)
	{
		$app = \Yii::app();

		$whiteLabelMode = $app->whiteLabel->getMode();
		if ($whiteLabelMode) {
			$this->mode = $whiteLabelMode;
		}

		$url = explode('?', urldecode($_SERVER['REQUEST_URI']), 2);
		$this->isMainPage = isset($url[0]) && $url[0] == '/';

		$this->isLandingPage = strpos($_SERVER['REQUEST_URI'], '/landing') !== false;

		$this->isMobile = $app->mobileDetect->isAdaptedMobile();

		$this->phoneForPage = $this->detectPhoneForPage();
		if ($this->phoneForPage && !$this->phoneForPage->getNumber()) {
			$this->phoneForPage = null;
		}

		if ($this->globalTrack === null) {
			$this->globalTrack = $app->params['globalTrack'];
		}

		return parent::beforeAction($action);
	}

	/**
	 * Установка параметров для xsl шаблонов
	 *
	 * @param \XSLTProcessor $proc
	 */
	protected function xslParameters(\XSLTProcessor $proc)
	{
		$proc->setParameter('', 'headerType', $this->mode);

		if (socialKey == 'yes') {
			$proc->setParameter('', 'socialKey', socialKey);
			$proc->setParameter('', 'socialVK', socialVK);
			$proc->setParameter('', 'socialFB', socialFB);
		}

		parent::xslParameters($proc);
	}

	/**
	 * Статистика для сайта
	 *
	 * @return int[]
	 */
	public function getStatistics()
	{
		$stat = new DocDocStat(\Yii::app()->params['DocDocStatisticFactor']);

		return [
			'RequestCount' => $stat->getRequestsCount(),
			'DoctorsCount' => $stat->getDoctorsCount(),
			'ReviewsCount' => $stat->getReviewsCount(),
		];
	}

	/**
	 * Список активных городов
	 *
	 * @return CityModel[]
	 */
	public function getCityList()
	{
		static $cityList = null;

		if ($cityList === null) {
			$cityList = CityModel::model()
				->cache(10800)
				->active(true)
				->findAll(['order' => 'title']);
		}

		return $cityList;
	}

	/**
	 * Определение телефона для страницы
	 *
	 * @return Phone
	 */
	public function detectPhoneForPage()
	{
		$app = \Yii::app();

		if ($app->referral->getId()) {
			return new Phone($app->referral->getPhone());
		}

		$params = $app->getParams();

		if ($app->mobileDetect->isAdaptedMobile() && !$app->trafficSource->isContext()) {
			return new Phone($params['phoneForMobile']);
		}

		if (!is_null($params['phoneForABTest']) && $app->city->isMoscow()) {
			return new Phone($params['phoneForABTest']);
		}

		return $app->city->getSitePhone();
	}

	/**
	 * Инициализация xml-данных, если используем старые шаблоны
	 */
	protected function initXslTemplates()
	{
		\initDomXML();
		\getHeaderXML();
	}


	/**
	 * Установка специальности в сессию
	 *
	 * @param SectorModel $speciality
	 */
	protected function setSessionSpeciality($speciality)
	{
		if ($speciality) {
			\Yii::app()->session['speciality'] = [
				'Id' => $speciality->id,
				'Alias' => $speciality->rewrite_name,
				'SpecAlias' => $speciality->rewrite_spec_name,
				'Name' => $speciality->name,
				'NameInLower' => mb_strtolower($speciality->name),
				'Specialization' => $speciality->spec_name,
				'ClinicName' => $speciality->clinic_seo_title,
				'ClinicInGenitive' => mb_strtolower(RussianTextUtils::wordInGenitive($speciality->clinic_seo_title, true)),
				'InGenitive' => RussianTextUtils::wordInGenitive($speciality->name),
				'InGenitiveLC' => mb_strtolower(RussianTextUtils::wordInGenitive($speciality->name)),
				'InGenitivePlural' => RussianTextUtils::wordInGenitive($speciality->name, true),
				'InGenitivePluralLC' => mb_strtolower(RussianTextUtils::wordInGenitive($speciality->name, true)),
				'InPlural' => RussianTextUtils::wordInNominative($speciality->name, true),
				'InPluralLC' => mb_strtolower(RussianTextUtils::wordInNominative($speciality->name, true)),
			];
		} else {
			\Yii::app()->session->remove('speciality');
		}
	}

	/**
	 * Установка станций в сессию
	 *
	 * @param StationModel[] $stations
	 */
	protected function setSessionStations($stations)
	{
		$stationData = [];

		if ($stations) {
			foreach ($stations as $station) {
				$stationData[] = [
					'Id'    => $station->id,
					'Alias' => $station->rewrite_name,
					'Name'  => $station->name,
				];
			}
		}

		if ($stationData) {
			\Yii::app()->session['stations'] = $stationData;
		} else {
			\Yii::app()->session->remove('stations');
		}
	}
}
