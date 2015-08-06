<?php

/**
 * Created by PhpStorm.
 * User: v.knyazev
 * Date: 02.09.14
 * Time: 10:05
 */
class CityModel
{
	private $id;
	private $name;
	private $alias;
	private $phone;

	/**
	 * @return string
	 */
	public function getAlias()
	{
		return $this->alias;
	}

	/**
	 * @param string $alias
	 */
	public function setAlias($alias)
	{
		$this->alias = $alias;
	}

	/**
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * @param int $id
	 */
	public function setId($id)
	{
		$this->id = $id;
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * @param string $name
	 */
	public function setName($name)
	{
		$this->name = $name;
	}

	/**
	 * @return string
	 */
	public function getPhone()
	{
		return $this->phone;
	}

	/**
	 * @param string $phone
	 */
	public function setPhone($phone)
	{
		if (preg_match('/^7(\d{3})(\d{3})(\d{2})(\d{2})$/', $phone, $matches)) {
			$phone = "8 ({$matches[1]}) {$matches[2]}-{$matches[3]}-{$matches[4]}";
		}

		$this->phone = $phone;
	}

	/**
	 * @return string
	 */
	public function getPhoneSkypeFormat()
	{
		return str_replace([' (', ') '], '-', $this->getPhone());
	}

	/**
	 * Получает URL
	 *
	 * @return string
	 */
	public function getUrl()
	{
		return "http://m.{$this->getHostPrefix()}" . Yii::app()->params->main_site;
	}

	/**
	 * Префикс для генерации ссылки
	 *
	 * @return string
	 */
	public function getHostPrefix()
	{
		if ($this->isMoscow()) {
			return '';
		}

		return $this->getAlias() . ".";
	}

	/**
	 * Проверяет, является ли город Москвой
	 *
	 * @return bool
	 */
	public function isMoscow()
	{
		return $this->getAlias() === 'msk';
	}

	/**
	 * Проверяет, есть ли в данном городе метро
	 *
	 * @return bool
	 */
	public function hasMetro()
	{
		return $this->getAlias() === 'msk' || $this->getAlias() === 'spb';
	}
}