<?php
namespace dfs\docdoc\components;
use dfs\docdoc\models\CityModel;
use dfs\docdoc\models\StationModel;
use dfs\docdoc\objects\Phone;
use dfs\common\config\Environment;
use dfs\docdoc\objects\sypexgeo\SxGeoCity;
use \Yii;
use CException;
use CHttpCookie;

/**
 * Class City
 *
 * Модель на вырост, пока нигде не используется.
 *
 * @package dfs\docdoc\components
 */
class City extends \CApplicationComponent
{
	/**
	 * Время в течение которого хранится ид города в кэше (неделя)
	 */
	const TIME_LIFE_IN_CACHE = 604800;

	/**
	 * @var CityModel экземпляр модели для города
	 */
	private $_city;

	/**
	 * город по-умолчанию
	 * @var int
	 */
	public $id_city = 1;

	/**
	 * определение города по ip (по умолчанию отключен)
	 *
	 * @var bool
	 */
	public $autodetect = false;

	/**
	 * @var string имя куки, в которую передается признак того, что город определен
	 */
	public $cookieParam = 'c_det';


	/**
	 * инициализация компонента
	 */
	public function init()
	{
		parent::init();

		// @TODO Нужно убрать отсюда костыль для отмены определения города по ip для виджетов
		$requestUrl = Yii::app()->request->requestUri;
		if (strpos($requestUrl, 'widget') !== false) {
			$this->autodetect = false;
		}

		$this->autodetect ? $this->autodetect() : $this->detect();
	}

	/**
	 * Определение региона
	 * @return string
	 */
	public function getCityPrefix()
	{
		return $this->getCity()->rewrite_name;
	}

	/**
	 * Геттер для модели CityModel
	 * @return CityModel
	 */
	public function getCity()
	{
		if ($this->_city === null) {
			$this->_city = CityModel::model()->cache(3600)->findByPk($this->id_city);
		}

		return $this->_city;
	}

	/**
	 * Хост в соотвествии текущему городу
	 * @return string
	 */
	public function host()
	{
		$appName = Yii::app()->params['appName'];
		$host = Yii::app()->params['hosts'][$appName];

		$city = $this->getCity();

		if (!empty($city->prefix) && !$this->isMoscow()) {

			$host = $city->prefix . $host;
		}

		return $host;
	}

	/**
	 * Получает URL адрес для текущего города
	 *
	 * @return string
	 */
	public function getUrl()
	{
		return 'http://' . $this->host();
	}

	/**
	 *  редирект на сайт города
	 */
	public function redirect($url = "")
	{
		header("Location: http://" . $this->host() . $url);
		Yii::app()->end();
	}

	/**
	 * Метод определения города в системе по домену
	 * @return $this
	 */
	public function detect()
	{
		if (Yii::app()->request->getQuery("city")) {
			$prefix = Yii::app()->request->getQuery("city");
		} else {
			$host = "";
			if (isset($_SERVER['HTTP_X_FORWARDED_HOST'])) {
				$host = $_SERVER['HTTP_X_FORWARDED_HOST'];
			} else if (isset($_SERVER['HTTP_HOST'])) {
				$host = $_SERVER['HTTP_HOST'];
			}
			$prefix = substr($host, 0, strpos($host, '.'));
		}

		$this->_city = CityModel::model()
			->cache(3600)
			->searchByPrefix($prefix . '.')
			->find();

		return $this;
	}

	/**
	 * Метод автоопределения грода по IP
	 *
	 * @return $this
	 */
	public function autodetect()
	{
		$request = Yii::app()->request;
		$detected = $request->cookies->contains($this->cookieParam);

		$this->detect();

		if (!empty($detected) || php_sapi_name() === 'cli') {
			return $this;
		}

		$ip = Yii::app()->request->getUserHostAddress();
		$cityId = (int)Yii::app()->cache->get($ip);

		if ($cityId === 0) {
			$cityId = (new SxGeoCity($ip))->getCity();
			Yii::app()->cache->set($ip, $cityId, self::TIME_LIFE_IN_CACHE);
		}

		$this->setCityDetected();

		if ((int)$this->getCityId() <> $cityId) {
			$this->_city = CityModel::model()
				->cache(3600)
				->findByPk($cityId);
			$this->redirect();
		}

		$this->_city = CityModel::model()
			->cache(3600)
			->findByPk($cityId);

		return $this;
	}

	/**
	 * Устанавливает признак того, что город определен для пользователя
	 */
	public function setCityDetected()
	{
		if (php_sapi_name() !== 'cli') {
			$request = Yii::app()->request;
			$appName = \Yii::app()->params['appName'];
			$params = [
				'expire' => time() + self::TIME_LIFE_IN_CACHE,
				'domain' => Yii::app()->params['hosts'][$appName],
			];

			$cookie = new CHttpCookie($this->cookieParam, 1, $params);
			$request->cookies[$this->cookieParam] = $cookie;
		}
	}

	/**
	 * @return int
	 */
	public function getSearchType()
	{
		return $this->getCity()->search_type;
	}

	/**
	 * Возвращает навзание города в нужном падеже
	 * @param null $type
	 *
	 * @return string
	 */
	public function getTitle($type = null)
	{
		$city = $this->getCity();

		switch ($type){
			case 'genitive':
				$title = $city->title_genitive;
				break;
			case 'prepositional':
				$title = $city->title_prepositional;
				break;
			case 'dative':
				$title = $city->title_dative;
				break;
			default:
				$title = $city->title;
		}

		return $title;
	}

	/**
	 * @return int
	 */
	public function getCityId()
	{
		return $this->getCity()->id_city;
	}

	/**
	 * @return Phone
	 */
	public function getSitePhone()
	{
		return $this->getCity()->site_phone;
	}

	/**
	 * @return Phone
	 */
	public function getSiteOffice()
	{
		return $this->getCity()->site_office;
	}

	/**
	 * @return bool
	 */
	public function hasMobile()
	{
		return $this->getCity()->has_mobile;
	}

	/**
	 * Изменение текущего города
	 * @param $cityId
	 * @throws \CException
	 * @return $this
	 */
	public function changeCity($cityId)
	{
		$city = CityModel::model()->cache(3600)->findByPk($cityId);
		if (!$city) {
			throw new CException("cityId unknown");
		}
		$this->_city = $city;

		return $this;
	}

	/**
	 * Проверка, что текущий город Москва
	 * @return bool
	 */
	public function isMoscow()
	{
		return $this->getCityPrefix() === 'msk';
	}

	/**
	 * Получение поддомена
	 * @return mixed
	 */
	public function getSubDomain()
	{
		return $this->getCity()->prefix;
	}

	/**
	 * возвращает адрес ссылки для диагностики для данного города
	 * @return string
	 */
	public function getDiagnosticUrl()
	{
		return 'http://' . $this->getDiagnosticHost();
	}

	/**
	 * Хост для диагностики для текущего города
	 *
	 * @return string
	 */
	public function getDiagnosticHost()
	{
		return ($this->_city->has_diagnostic ? $this->_city->prefix : '') . Yii::app()->params['hosts']['diagnostica'];
	}

	/**
	 * Возвращает идентификатор профиля для Yandex.Metrika
	 * @return string
	 */
	public function getYandexMetrikaProfileId()
	{
		$ya = null;

		if (Environment::isProduction()) {
			$ya = $this->getCity()->site_YA;
		}

		if ($ya === null || $ya === '') {
			$ya = \Yii::app()->getParams()['ya-metrika-id'];
		}

		return $ya;
	}

	/**
	 * Возвращает идентификатор профиля для Yandex.Metrika на диагностике
	 * @return string
	 */
	public function getDiagnosticYandexMetrikaProfileId()
	{
		$ya = null;

		if (Environment::isProduction()) {
			$ya = $this->getCity()->diagnostic_site_YA;
		}

		if ($ya === null || $ya === '') {
			$ya = \Yii::app()->getParams()['ya-metrika-id-diagnostic'];
		}

		return $ya;
	}

	/**
	 * Определяет, есть ли в городе метро
	 * @return bool
	 */
	public function hasMetro()
	{
		return (bool)StationModel::model()->cache(3600)->inCity($this->getCityId())->count();
	}
}
