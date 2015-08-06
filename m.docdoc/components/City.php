<?php

namespace dfs\components;

use CityModel;
use ApiDto;
use Yii;
use CApplicationComponent;

/**
 * Файл класса City
 *
 * Компонент для работы с текущим городом
 *
 * @author  Mikhail Vasilyev <mvasilyev@docdoc.ru>
 * @link    https://docdoc.atlassian.net/browse/DDM-9
 * @package dfs\components
 */
class City extends CApplicationComponent
{
	/**
	 * Идентификатор текущего города
	 *
	 * @var integer
	 */
	public $defaultId = 1;

	/**
	 * Модель текущего города
	 *
	 * @var CityModel
	 */
	private $_model = null;

	/**
	 * Модель класса ApiDto
	 *
	 * @var ApiDto
	 */
	private $_apiDto = null;

	/**
	 * Список всех городов
	 *
	 * @var CityModel[]
	 */
	private $_cityList = [];

	/**
	 * Инициализация компонента
	 *
	 * @return void
	 */
	public function init()
	{
		parent::init();

		$this->_apiDto = new ApiDto();
		$this->_cityList = $this->_apiDto->getCityList();
		$this->_setModel();
	}

	/**
	 * Определяет модель текущего города
	 *
	 * @return void
	 */
	private function _setModel()
	{
		$host = $_SERVER['HTTP_HOST'];
		if (!empty($_SERVER['HTTP_X_FORWARDED_HOST'])) {
			$host = $_SERVER['HTTP_X_FORWARDED_HOST'];
		}

		$host = substr($host, 2);
		$prefix = substr($host, 0, strpos($host, '.'));

		foreach ($this->_cityList as $city) {
			if ($prefix == $city->getAlias()) {
				$this->_model = $city;
			}
		}

		if ($this->_model === null) {
			$this->_model = $this->_apiDto->getCityById($this->defaultId);
		}
	}

	/**
	 * Получает модель текущего города
	 *
	 * @return CityModel
	 */
	public function getModel()
	{
		return $this->_model;
	}

	/**
	 * Получает массив для выпадающего списка
	 *
	 * @return array
	 */
	public function getListForDropdown()
	{
		$list = [];
		$names = [];

		foreach ($this->_cityList as $city) {
			$list[] = [
				"name"       => $city->getName(),
				"url"        => $city->getUrl(),
				"isSelected" => $city->getId() == $this->_model->getId()
			];
			$names[] = $city->getName();
		}

		if ($list) {
			array_multisort($names, SORT_ASC, $list);
		}

		return $list;
	}

	/**
	 * Ссылка на основной сайт
	 *
	 * @return string
	 */
	public function getMainSiteUrl()
	{
		return 'http://'
		. $this->getModel()->getHostPrefix()
		. Yii::app()->params->main_site
	;
	}

	/**
	 * Возвращает Canonical ссылку
	 *
	 * @return string
	 */
	public function getCanonicalUrl()
	{
		return
			$this->getMainSiteUrl()
			. Yii::app()->request->getUrl()
		;
	}

	/**
	 * Ссылка на основную версию сайта
	 *
	 * @return string
	 */
	public function getDesktopUrl()
	{
		return $this->getCanonicalUrl()
			. (
				strpos($this->getCanonicalUrl(), '?') === false
					? '?'
					: '&'
			)
			. 'switchToDesktop=1';
	}

}