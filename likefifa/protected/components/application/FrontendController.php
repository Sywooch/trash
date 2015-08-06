<?php

use likefifa\models\CityModel;
use likefifa\models\RegionModel;
use likefifa\components\helpers\ListHelper;

/**
 * Class FrontendController
 */
abstract class FrontendController extends Controller
{

	public $masterLoginForm = null;

	/**
	 * Мастер, если залогинен
	 *
	 * @var LfMaster|null
	 */
	public $loggedMaster = null;
	public $loggedSalon = null;

	public $articles = array();

	public $area = null;
	public $district = null;
	public $specialization = null;
	public $service = null;
	public $hasDeparture = null;
	public $city = null;

	public $points = array();

	public $pageHeader = null;
	public $pageSubheader = null;
	public $metaKeywords = null;
	public $metaDescription = null;
	public $profilePage = null;
	public $firstTime = false;
	public $showPopup = false;

	protected $searchEntity = 'masters';
	public $userType;

	public $mastersCount = 0;
	public $salonsCount = 0;
	public $worksCount = 0;

	public $lkRefresh = false;

	/**
	 * Вызывается при инициализации контроллера
	 *
	 * @return void
	 */
	public function init()
	{
		Yii::app()->activeRegion->init($_SERVER['HTTP_HOST']);
	}

	/**
	 * Подсчет цифр для шапки сайта
	 *
	 * @return void
	 */
	protected function calcCounts()
	{
		$days = ceil((time() - mktime(0, 0, 0, 1, 17, 2014)) / 60 / 60 / 24);

		$this->mastersCount = $this->getMastersCount();
		$this->salonsCount = ceil(LfSalon::START_COUNT + LfSalon::PER_DAY * $days);
		$this->worksCount = ceil(LfWork::START_COUNT + LfWork::PER_DAY * $days);
	}

	public function beforeAction($action)
	{
		Yii::app()->session->open();

		$this->calcCounts();

		$this->layout = '//layouts/likefifa';

		Yii::app()->session['controller'] = get_class($this);
		Yii::app()->session['action'] = $action;

		$this->masterLoginForm = new MasterLoginForm;

		if (isset($_POST['MasterLoginForm'])) {
			$this->masterLoginForm->attributes = $_POST['MasterLoginForm'];
			if ($this->masterLoginForm->validate() && $this->masterLoginForm->login()) {
				if ($this->masterLoginForm->userType == 'master') {
					$this->redirect(LfMaster::model()->findByPk(Yii::app()->user->getId())->getLkUrl());
				}
				if ($this->masterLoginForm->userType == 'salon') {
					$this->redirect(LfSalon::model()->findByPk(Yii::app()->user->getId())->getLkUrl());
				}
			}

		}

		if (Yii::app()->user->getState('masterLoggedInPublic')) {
			$this->loggedMaster = LfMaster::model()->findByPk(Yii::app()->user->getId());
		}

		if (Yii::app()->user->getState('salonLoggedInPublic')) {
			$this->loggedSalon = LfSalon::model()->findByPk(Yii::app()->user->getId());
		}

		$this->articles = Article::model()->onlyActive()->last()->with(array('section'))->findAll();

		return true;
	}

	protected function validateAjaxFields($keys)
	{
		$errors = array();

		foreach ($keys as $key) {
			if (!isset($_POST[$key]) || !mb_strlen($_POST[$key])) {
				$errors[] = $key;
			}
		}

		if ($errors) {
			$errors = array_combine($errors, $errors);

			echo json_encode(compact('errors'));
			Yii::app()->end();
		}
	}

	protected function sendAjaxMessage($message, $url = null)
	{
		echo json_encode(compact('message', 'url'));
		Yii::app()->end();
	}

	protected function setTitle($title = null)
	{
		$this->pageTitle = $title;
		return $this;
	}

	protected function setMetaKeywords($keywords)
	{
		$this->metaKeywords = $keywords;
		return $this;
	}

	protected function setMetaDescription($description)
	{
		$this->metaDescription = $description;
		return $this;
	}

	public function forMasters()
	{
		$this->searchEntity = 'masters';
		return $this;
	}

	public function forSalons()
	{
		$this->searchEntity = 'salons';
		return $this;
	}

	public function createRedirectUrl($mode = 'custom')
	{
		return $this->createUrl($this->searchEntity . '/redirect', compact('mode'));
	}

	/**
	 * Получает ссылку для округа
	 *
	 * @param AreaMoscow       $area           модель округа
	 * @param LfSpecialization $specialization модель специализации
	 *
	 * @return string
	 */
	public function createAreaUrl($area, $specialization = null)
	{
		$specializationName = "";
		if ($specialization) {
			$specializationName = "{$specialization->rewrite_name}/";
		}
		return "/{$this->searchEntity}/{$specializationName}districts/{$area->rewrite_name}/";
	}

	/**
	 * Создает URL
	 *
	 * @param LfSpecialization     $specialization специализация
	 * @param LfService            $service        сервис
	 * @param int                  $hasDeparture   есть ли выезд
	 * @param UndergroundStation[] $stations       станции метро
	 * @param AreaMoscow           $area           округ
	 * @param DistrictMoscow       $districts      районы
	 * @param CityModel            $city           гоорд
	 * @param bool                 $showAll        показать все
	 * @param string               $sorting        сортировка
	 * @param string               $direction      направление сортировки
	 * @param string               $speciality     специальность
	 *
	 * @return string
	 */
	public function createSearchUrl(
		LfSpecialization $specialization = null,
		LfService $service = null,
		$hasDeparture = null,
		$stations = array(),
		AreaMoscow $area = null,
		$districts = null,
		$city = null,
		$showAll = false,
		$sorting = null,
		$direction = null,
		$speciality = null
	)
	{
		$params = array();

		if ($specialization) {
			$params['specialization'] = $specialization->getRewriteName();
		}
		if ($service) {
			$params['service'] = $service->getRewriteName();
		}
		if ($hasDeparture && $this->searchEntity != 'salons') {
			$params['hasDeparture'] = 1;
		}
		if ($showAll) {
			$params['showAll'] = 1;
		}
		if ($sorting && ($sorting !== 'rating' || $direction !== 'desc')) {
			$params['sorting'] = $sorting;
			if ($direction) {
				$params['direction'] = $direction;
			}
		}
		if ($city && $city->id != 1) {
			$params['city'] = $city->getRewriteName();
		}

		if ($stations) {
			$stationNames = array();
			foreach ($stations as $station) {
				$stationNames[] = $station->getRewriteName();
			}

			$params['stations'] = implode(',', $stationNames);
		}

		if ($area) {
			$params['area'] = $area->getRewriteName();
		}

		if ($districts) {
			$districtNames = array();
			foreach ($districts as $district) {
				$districtNames[] = $district->getRewriteName();
			}

			$params['districts'] = implode(',', $districtNames);
		}

		if ($speciality) {
			$params["speciality"] = $speciality;
		}

		return $this->createUrl($this->searchEntity . '/custom', $params);
	}

	public function createCountUrl(
		LfSpecialization $specialization = null,
		LfService $service = null,
		$hasDeparture = null,
		$stations = null,
		AreaMoscow $area = null,
		$districts = null,
		$city = null,
		$type = 'custom'
	)
	{
		$params = array();

		if ($specialization) {
			$params['specialization'] = $specialization->getRewriteName();
		}
		if ($service) {
			$params['service'] = $service->getRewriteName();
		}
		if ($hasDeparture) {
			$params['hasDeparture'] = 1;
		}
		if ($city) {
			$params['city'] = $city->getRewriteName();
		}
		if ($stations) {
			$stationNames = array();
			foreach ($stations as $station) {
				$stationNames[] = $station->getRewriteName();
			}

			$params['stations'] = implode(',', $stationNames);
		}

		if ($area) {
			$params['area'] = $area->getRewriteName();
		}

		if ($districts) {
			$districtNames = array();
			foreach ($districts as $district) {
				$districtNames[] = $district->getRewriteName();
			}

			$params['districts'] = implode(',', $districtNames);
		}

		$params['type'] = $type;

		return $this->createUrl($this->searchEntity . '/count', $params);
	}

	public function createQueryUrl($query)
	{
		return $this->createUrl($this->searchEntity . '/query', compact('query'));
	}

	public function createGalleryUrl(
		LfSpecialization $specialization = null,
		LfService $service = null,
		$hasDeparture = null,
		$showAll = false
	)
	{
		$params = array();

		if ($specialization) {
			$params['specialization'] = $specialization->getRewriteName();
		}
		if ($service) {
			$params['service'] = $service->getRewriteName();
		}
		if ($hasDeparture) {
			$params['hasDeparture'] = 1;
		}
		if ($showAll) {
			$params['all'] = true;
		}

		return $this->createUrl($this->searchEntity . '/gallery', $params);
	}

	public function createMapUrl(
		LfSpecialization $specialization = null,
		LfService $service = null,
		$hasDeparture = null,
		$sorting = null,
		$direction = null
	)
	{
		$params = array();

		if ($specialization) {
			$params['specialization'] = $specialization->getRewriteName();
		}
		if ($service) {
			$params['service'] = $service->getRewriteName();
		}
		if ($hasDeparture && $this->searchEntity != 'salons') {
			$params['hasDeparture'] = 1;
		}
		if ($sorting && ($sorting !== 'rating' || $direction !== 'desc')) {
			$params['sorting'] = $sorting;
			if ($direction) {
				$params['direction'] = $direction;
			}
		}

		return $this->createUrl($this->searchEntity . '/map', $params);
	}

	/**
	 * Получает название специальности или услуги
	 *
	 * @param mixed $params параметры
	 *
	 * @return null|string
	 */
	protected function getServiceName($params)
	{
		if ($params['service']) {
			return su::ucfirst($params['service']->name);
		}

		if ($params['specialization']) {
			return su::ucfirst($params['specialization']->name);
		}

		return null;
	}

	/**
	 * Получает ссылку для города
	 *
	 * @param string           $cityUrl           Абривиатура URL города
	 * @param LfSpecialization $specialization    модель специализации
	 *
	 * @return string
	 */
	public function createCityUrl($cityUrl, $specialization = null)
	{
		$specializationName = "";
		if ($specialization) {
			$specializationName = "{$specialization->rewrite_name}/";
		}

		$url = "/{$this->searchEntity}/{$specializationName}";
		if ($cityUrl) {
			$url .= "city/{$cityUrl}/";
		}

		return $url;
	}

	/**
	 * Получает текстовую строку в шапке сайта
	 *
	 * @return string
	 */
	public function getHeadText()
	{
		if (Yii::app()->activeRegion->isMoscow()) {
			return "Все мастера Москвы";
		}

		$city = CityModel::model()->getModelByRewriteName(Yii::app()->request->getQuery("city"));
		if ($city) {
			$text = "Все мастера в городе {$city->name}";
		} else {
			$text = "Все мастера в " . Yii::app()->activeRegion->getModel()->name_prepositional;
		}

		return $text;
	}

	/**
	 * Возвращает количество клиентов за неделю
	 *
	 * @return integer
	 */
	public function getClientCount()
	{
		$count = Yii::app()->cache->get('weekClientCount');
		if (!$count) {
			$count = rand(199, 255);
			Yii::app()->cache->set('weekClientCount', $count, 604800);
		}

		return $count;
	}

	/**
	 * Возвращает количество мастеров
	 *
	 * @return integer
	 */
	public function getMastersCount()
	{
		$count = Yii::app()->cache->get('mastersCount');
		if (!$count) {
			$count = Yii::app()->db->createCommand()
				->select(new CDbExpression('count(id)'))
				->from(LfMaster::model()->tableName())
				->queryScalar();
			Yii::app()->cache->set('mastersCount', $count, Yii::app()->params["cacheTime"]);
		}
		return $count;
	}
}