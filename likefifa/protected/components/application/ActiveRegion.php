<?php

namespace likefifa\components\application;

use likefifa\models\RegionModel;

/**
 * Файл класса ActiveRegion
 *
 * Класс для работы с активным регионом
 *
 * @author  Mikhail Vasilyev <m.vasilyev@likefifa.ru>
 * @link    https://docdoc.megaplan.ru/task/1003365/card/
 * @package models
 */
class ActiveRegion
{

	/**
	 * Модель активного региона
	 *
	 * @var RegionModel
	 */
	private static $_activeModel = null;

	/**
	 * Устанавливает модель активнго региона
	 *
	 * @param string $httpHost $_SERVER['HTTP_HOST']
	 *
	 * @return void
	 */
	public function init($httpHost = "")
	{
		$id = RegionModel::MOSCOW_ID;

		foreach (RegionModel::model()->findAll() as $model) {
			if ($model->prefix && substr_count($httpHost, $model->prefix) > 0) {
				$id = $model->id;
			}
		}

		self::$_activeModel = RegionModel::model()->findByPk($id);
	}

	/**
	 * Получает модель текущего региона
	 *
	 * @return RegionModel
	 */
	public function getModel()
	{
		return self::$_activeModel;
	}

	/**
	 * Проверяет, является ли активный регион Москвой
	 *
	 * @return bool
	 */
	public function isMoscow()
	{
		return self::$_activeModel->id == RegionModel::MOSCOW_ID;
	}

	/**
	 * Можно ли выводить список городов в фильтре каталога
	 *
	 * @return bool
	 */
	public function canShowCities()
	{
		return !$this->isMoscow();
	}

	/**
	 * Можно ли выводить список услуг в фильтре каталога
	 *
	 * @return bool
	 */
	public function canShowServices()
	{
		return $this->isMoscow();
	}

	/**
	 * Можно ли выводить метро и регионы в фильтре каталога
	 *
	 * @return bool
	 */
	public function canShowGeo()
	{
		return $this->isMoscow();
	}

	/**
	 * Проверяет, является ли активный регион Московской областью
	 *
	 * @return bool
	 */
	public function isMO()
	{
		return self::$_activeModel->id == RegionModel::MO_ID;
	}
}