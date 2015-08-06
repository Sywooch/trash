<?php
/**
 * Created by PhpStorm.
 * User: a.tyutyunnikov
 * Date: 15.09.14
 * Time: 11:42
 */

namespace dfs\docdoc\components;

class GaClient extends \CApplicationComponent
{
	/**
	 * @var string id
	 */
	protected $_id = null;

	/**
	 * @var string имя куки, в которой передается id стратегии
	 */
	protected $_cookieParam = '_ga_cl';

	/**
	 * Время жизни куки в секундах
	 * По умолчанию 2 год
	 *
	 * @var int
	 */
	public $cookieLifeTime = 71672000;

	/**
	 * Гетер для имени куки
	 *
	 * @return string
	 */
	public function getCookieParam()
	{
		return $this->_cookieParam;
	}

	/**
	 * инициализация компонента
	 */
	public function init()
	{
		parent::init();

		$app = \Yii::app();
		if ($app->request->cookies->contains($this->_cookieParam)) {
			$this->_id = (string)$app->request->cookies[$this->_cookieParam];
		} else {
			$this->generateId();
		}
	}

	public function getDomain()
	{
		$domain = \Yii::app()->params['hosts']['front'];
		$domain = "." . mb_substr($domain,  mb_strpos($domain, "docdoc."));
		return $domain;
	}

	/**
	 * Ставит куку
	 */
	public function setCookie()
	{
		$domain = $this->getDomain();
		if (php_sapi_name() !== 'cli') {
			$cookie = new \CHttpCookie($this->_cookieParam, $this->_id, ['expire' => time() + $this->cookieLifeTime, "domain" => $domain ]);
			\Yii::app()->request->cookies[$this->_cookieParam] = $cookie;
			$cookie2 = new \CHttpCookie("_ga", "GA1.2.".$this->_id, ['expire' => time() + $this->cookieLifeTime, "domain" => $domain ]);
			\Yii::app()->request->cookies["_ga"] = $cookie2;

		}
	}

	/**
	 * Поиск и установка стратегии
	 */
	public function generateId()
	{
		$this->_id = rand(100000000, 999999999) . "." . time();

		$this->setCookie();
	}

	/**
	 * Гетер для id
	 * @return int
	 */
	public function getId()
	{
		return $this->_id;
	}

	/**
	 * Установить стратегию
	 *
	 * @param int $id
	 * @return $this
	 */
	public function setId($id)
	{
		$this->_id = $id;
		return $this;
	}
} 
