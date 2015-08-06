<?php

namespace dfs\docdoc\components;

use Yii;

/**
 *  Класс WhiteLabel
 */
class WhiteLabel extends \CApplicationComponent
{
	/**
	 * Режим показа без шапки
	 */
	const MODE_NO_HEAD = 'noHead';
	/**
	 * Режим показа по умолчанию
	 */
	const MODE_DEFAULT = null;
	/**
	 * Признак метки whitelabel
	 */
	const UTM_CAMPAIGN = 'whitelabel';

	/**
	 * Имя куки
	 *
	 * @var string
	 */
	private $_cookieParam = 'whitelabel';

	/**
	 * Время жизни куки в секундах
	 * По умолчанию 1 месяц
	 *
	 * @var int
	 */
	private $_cookieLifeTime = 2628000;

	/**
	 * Инициализация компонента
	 */
	public function init()
	{
		parent::init();

		if ($this->isWhiteLabel()) {
			if (!Yii::app()->request->cookies->contains($this->_cookieParam)) {
				$this->setCookie();
			}
		}
	}

	/**
	 * Установка куки
	 */
	public function setCookie()
	{
		if (php_sapi_name() !== 'cli') {
			$cookie = new \CHttpCookie($this->_cookieParam, 'enabled', ['expire' => time() + $this->_cookieLifeTime]);
			\Yii::app()->request->cookies[$this->_cookieParam] = $cookie;
		}
	}

	/**
	 * Определяет, что страница с whitelabel
	 *
	 * @return bool
	 */
	public function isWhiteLabel()
	{
		return (Yii::app()->request->getQuery('utm_campaign') == self::UTM_CAMPAIGN
			|| Yii::app()->request->cookies->contains(self::UTM_CAMPAIGN));
	}

	/**
	 * Получение варианта отображения страницы
	 *
	 * @return null|string
	 */
	public function getMode() {
		return ($this->isWhiteLabel() && Yii::app()->request->cookies[$this->_cookieParam] == 'enabled')
			? self::MODE_NO_HEAD
			: self::MODE_DEFAULT;
	}
}
