<?php

namespace dfs\components;

use Mixpanel;

/**
 * Class MixpanelComponent
 *
 * Инициализация библиотеки Mixpanel
 *
 * @package dfs\components
 */
class MixpanelComponent extends \CApplicationComponent
{
	/**
	 * Токен Mixpanel
	 * @var string
	 */
	public $token = null;
	/**
	 * Параметры для инициализации
	 * @var array
	 */
	public $options = array();

	/**
	 * Объект для работы с Mixpanel
	 * @var Mixpanel
	 */
	private $_mixpanel;

	/**
	 * Данные для событий при загрузке страницы
	 * @var array
	 */
	private $_tracks = [];

	/**
	 * Инициализация компонента
	 * @return void
	 */
	public function init()
	{
		parent::init();
		$this->_mixpanel = Mixpanel::getInstance($this->token, $this->options);
	}

	/**
	 * Получение объекта Mixpanel
	 * @return Mixpanel
	 */
	public function getMixpanel()
	{
		return $this->_mixpanel;
	}

	/**
	 * Получение токена
	 * @return string
	 */
	public function getToken()
	{
		return $this->token;
	}

	/**
	 * Получение данных для событий при загрузке страницы
	 * @return array
	 */
	public function getTracks()
	{
		return $this->_tracks;
	}

	/**
	 * Получение данных для событий при загрузке страницы
	 *
	 * @param $name
	 * @param $params
	 *
	 * @return $this
	 */
	public function addTrack($name, $params = null)
	{
		$this->_tracks[$name] = $params;

		return $this;
	}

	/**
	 * Идентификация
	 *
	 * @param string $alias номер телефона
	 */
	private function _identify($alias)
	{
		if ($this->token === null) return;

		$this->_mixpanel->identify($alias);
	}

	/**
	 * Фиксирование события
	 *
	 * @param string $eventName
	 * @param string[] $event
	 */
	private function _track($eventName, $event)
	{
		if ($this->token === null) return;

		$this->_mixpanel->track($eventName, $event);
	}
}
