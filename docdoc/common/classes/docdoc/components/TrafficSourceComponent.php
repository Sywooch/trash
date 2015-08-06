<?php

namespace dfs\docdoc\components;

use Yii;
use CHttpCookie;

/**
 *  Класс TrafficSourceComponent
 */
class TrafficSourceComponent extends \CApplicationComponent
{
	/**
	 * @var string имя куки, в которой передаются параметры траффика
	 */
	public $cookieParam = null;

	/**
	 * @var array метки для контекстного траффика
	 */
	public $contextParams = [
		'source-name=direct.yandex.ru',
		'utm_source=direct',
		'utm_source=adwords',
		'yclid',
		'gclid',
	];

	/**
	 * @var string ключ массива в сессии
	 */
	private $_sessionParam = 'traffic_source';

	/**
	 *  Ключ куки для гугл клиента
	 *
	 * @var string
	 */
	private $_google_client_id_cookie_param = '_ga_cl';

	/**
	 * @var array массив с параметрами траффика
	 */
	private $_trafficParams = [];

	/**
	 * Время жизни куки в секундах
	 * По умолчанию 10 лет
	 *
	 * @var int
	 */
	public $cookieLifeTime = 315360000;

	/**
	 * Строка с параметрами для новой сессии
	 *
	 * @var string
	 */
	public $paramForNewSession = 'utm_source=typein&utm_medium=typein';

	/**
	 * Первое посещение юзера
	 */
	const FIRST_VISIT   = 0;
	/**
	 * Текущее посещение
	 */
	const CURRENT_VISIT = 1;

	/**
	 * Источник контекст
	 */
	const SOURCE_CONTEXT = 'context';
	/**
	 * Неизвестный источник
	 */
	const SOURCE_UNKNOWN = 'unknown';

	/**
	 * инициализация компонента
	 */
	public function init()
	{
		parent::init();

		$this->initTrafficParams();
	}

	/**
	 * Инициализация параметров траффика
	 */
	public function initTrafficParams()
	{
		$app = Yii::app();

		if (!is_null($this->cookieParam)) {
			$this->_trafficParams = $this->getCookieParams();
		}

		if (!empty($app->request->getQueryString())) {
			$exceptHosts = [
				Yii::app()->params['hosts']['front'],
				Yii::app()->params['hosts']['diagnostica'],
			];
			if (!in_array($this->getReferrer(), $exceptHosts)) {
				$this->setTrafficParamsFromGet();
			}
		} else {
			if (!isset($app->session[$this->_sessionParam]) && !empty($this->_trafficParams)) {
				$params = [];
				$params['params'] = $this->paramForNewSession;
				if (!is_null($this->getReferrer())) {
					$params['referrer'] = $this->getReferrer();
				}
				$this->_trafficParams[self::CURRENT_VISIT] = $params;
			}
		}

		if (!isset($app->session[$this->_sessionParam])) {
			$app->session[$this->_sessionParam] = $this->getSource();
		}

		if (!is_null($this->cookieParam)) {
			$this->setCookie();
		}
	}

	/**
	 * Ставит куку с параметрами траффика
	 */
	public function setCookie()
	{
		if(php_sapi_name() !== 'cli' && !empty($this->_trafficParams)){
			$cookie = new CHttpCookie($this->cookieParam, serialize($this->_trafficParams), ['expire' => time() + $this->cookieLifeTime]);
			Yii::app()->request->cookies[$this->cookieParam] = $cookie;
		}
	}

	/**
	 * Получение ранее установленной куки
	 *
	 * @return array
	 */
	public function getCookieParams()
	{
		$params = [];

		if (Yii::app()->request->cookies->contains($this->cookieParam)) {
			$params = @unserialize(Yii::app()->request->cookies[$this->cookieParam]);

			//если битые даные пришли, ресечу их
			if (!is_array($params)) {
				$params = [];
			}
		}

		return $params;
	}

	/**
	 * Установка параметров траффика из GET параметров
	 *
	 * @return array
	 */
	public function setTrafficParamsFromGet()
	{
		$request = Yii::app()->request;

		$params = [];
		$params['params'] = Yii::app()->request->getQueryString();

		if (!is_null($this->getReferrer())) {
			$params['referrer'] = $this->getReferrer();
		}

		$googleAnalyticsClientId = null;

		if($request->cookies->contains($this->_google_client_id_cookie_param)){
			$params[$this->_google_client_id_cookie_param] = $googleAnalyticsClientId = $request->cookies[$this->_google_client_id_cookie_param]->value;
		}

		if (empty($this->_trafficParams)) {
			$this->_trafficParams[self::FIRST_VISIT] = $params;
		}

		$this->_trafficParams[self::CURRENT_VISIT] = $params;

		//если при первом заходе не отпечатся клиент
		if($googleAnalyticsClientId && !isset($this->_trafficParams[self::FIRST_VISIT][$this->_google_client_id_cookie_param])){
			$this->_trafficParams[self::FIRST_VISIT][$this->_google_client_id_cookie_param] = $googleAnalyticsClientId;
		}
	}

	/**
	 * Получение реферрера
	 *
	 * @return string|null
	 */
	public function getReferrer()
	{
		$request = Yii::app()->request;

		if (is_null($request->getUrlReferrer())) {
			return null;
		}

		$refHost = parse_url($request->getUrlReferrer(), PHP_URL_HOST);

		return $refHost;
	}

	/**
	 * Получение параметров в виде ассоциативного массива в зависимости от посещения
	 *
	 * @param integer $visit
	 * @return array
	 */
	public function getParams($visit)
	{
		$params = [];

		$trafficParams = !empty($this->_trafficParams[$visit]) ? $this->_trafficParams[$visit] : [];

		if (isset($trafficParams['params'])) {
			parse_str($trafficParams['params'], $params);
			unset($trafficParams['params']);
		}

		$params = array_merge($params, $trafficParams);

		return $params;
	}

	/**
	 * Получение источника траффика
	 *
	 * @return string
	 */
	public function getSource()
	{
		$query = Yii::app()->request->getQueryString();
		foreach ($this->contextParams as $param) {
			if (strpos($query, $param) !== false) {
				return self::SOURCE_CONTEXT;
			}
		}
		return self::SOURCE_UNKNOWN;
	}

	/**
	 * Возвращает, что источник траффика контекст
	 *
	 * @return bool
	 */
	public function isContext()
	{
		return Yii::app()->session[$this->_sessionParam] == self::SOURCE_CONTEXT;
	}
}
