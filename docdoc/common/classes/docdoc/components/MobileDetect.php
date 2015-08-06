<?php
namespace dfs\docdoc\components;

use Mobile_Detect;
use CException;

/**
 * Class MobileDetect
 *
 * Определение мобильных устройств
 *
 * @package dfs\docdoc\components
 */
class MobileDetect extends \CApplicationComponent
{
	/**
	 * С помощью каких методов определять мобильное устройство
	 * @var array
	 */
	public $mobileDetectKeys = array('Mobile', 'Tablet');

	/**
	 * Файл с правилами перенаправления
	 * @var string
	 */
	public $rulesFile;

	/**
	 * Объект для работы с Mobile Detect Library
	 * @var Mobile_Detect
	 */
	private $_mobileDetect;

	/**
	 * Признак мобильной версии
	 * @var bool
	 */
	private $_isMobile = false;

	/**
	 * Инициализация компонента
	 * @return void
	 */
	public function init()
	{
		parent::init();
		$this->_mobileDetect = new Mobile_Detect();
		$this->_isMobile = $this->detect();

		if ($this->_isMobile) {
			$this->redirect();
		}
	}

	/**
	 * Определение мобильной версии
	 * @return bool
	 */
	protected function detect() {
		if (!\Yii::app()->city->hasMobile()) {
			return false;
		}
		if (!empty(\Yii::app()->referral) && \Yii::app()->referral->getId() && \Yii::app()->referral->isABTest() < 0 ) {
			return false;
		}
		if (!empty(\Yii::app()->whiteLabel) && \Yii::app()->whiteLabel->isWhiteLabel()) {
			return false;
		}

		$request = \Yii::app()->request;

		if ($request->getQuery('switchToMobile') == 1) {
			if (php_sapi_name() !== 'cli') {
				$request->cookies['isMobile'] = new \CHttpCookie('isMobile', 1, ['expire' => time() + 86400]);
			}
			return true;
		}

		if ($request->getQuery('switchToDesktop') == 1) {
			if (php_sapi_name() !== 'cli') {
				$request->cookies['isMobile'] = new \CHttpCookie('isMobile', 0, ['expire' => time() + 86400]);
			}
			return false;
		}

		if (isset($request->cookies['isMobile'])) {
			return strval($request->cookies['isMobile']) === '1';
		}

		$mobileDetect = $this->_mobileDetect;
		foreach ($this->mobileDetectKeys as $key) {
			if ($key == 'Phone') {
				if ($mobileDetect->isMobile($request->userAgent) && !$mobileDetect->isTablet($request->userAgent)) {
					return true;
				}
			} else {
				if ($mobileDetect->is($key, $request->userAgent)) {
					return true;
				}
			}
		}

		return false;
	}

	/**
	 * Признак мобильного сайта
	 * @return bool
	 */
	public function isMobile()
	{
		return $this->_isMobile;
	}

	/**
	 * Признак адаптированной мобильной версии сайта
	 *
	 * @return bool
	 */
	public function isAdaptedMobile()
	{
		$mobileDetect = $this->getMobileDetect();

		return $mobileDetect->isMobile() || $mobileDetect->isTablet();
	}

	/**
	 * Получение объекта для работы с Mobile Detect Library
	 * @return Mobile_Detect
	 */
	public function getMobileDetect()
	{
		return $this->_mobileDetect;
	}

	/**
	 *  Сформировать url для мобильной версии
	 *  @throws \CException
	 *  @return string
	 */
	public function getMobileUrl()
	{
		if (!$this->rulesFile || !file_exists($this->rulesFile)) {
			return null;
		}

		$route = \Yii::createComponent([
			'class'          => \CUrlManager::class,
			'urlFormat'      => \CUrlManager::PATH_FORMAT,
			'rules'          => require($this->rulesFile),
		]);
		$route->init();
		$pathInfo = $route->parseUrl(\Yii::app()->request);

		if ($pathInfo === 'not_redirect' || $pathInfo === \Yii::app()->request->getQuery('r')) {
			return null;
		}

		$queryParams = '';
		if ($_SERVER['REQUEST_URI']) {
			$pos = strpos($_SERVER['REQUEST_URI'], '?');
			if ($pos !== false) {
				$queryParams = substr($_SERVER['REQUEST_URI'], $pos);
			}
		}

		return 'http://m.' . $_SERVER['HTTP_HOST'] . '/' . $pathInfo . $queryParams;
	}

	/**
	 *  Редирект на мобильную версию сайта
	 */
	public function redirect()
	{
		$redirectUrl = $this->getMobileUrl();

		if ($redirectUrl !== null && php_sapi_name() !== 'cli') {
			\Yii::app()->request->redirect($redirectUrl);
		}
	}
}
