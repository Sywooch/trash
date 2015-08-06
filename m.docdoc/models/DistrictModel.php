<?php

namespace dfs\models;

/**
 * Файл класса DistrictModel
 *
 * Модель для работы с регионами
 *
 * @author  Mikhail Vasilyev <mvasilyev@docdoc.ru>
 * @link    https://docdoc.atlassian.net/browse/DDM-9
 * @package dfs\models
 */
class DistrictModel
{

	/**
	 * Идентификатор
	 *
	 * @var integer
	 */
	private $_id;

	/**
	 * Название
	 *
	 * @var string
	 */
	private $_name;

	/**
	 * Абривиатура URL
	 *
	 * @var string
	 */
	private $_alias;

	/**
	 * Получает идентификатор
	 *
	 * @return integer
	 */
	public function getId()
	{
		return $this->_id;
	}

	/**
	 * Устанавливает идентификатор
	 *
	 * @param integer $id идентификатор
	 */
	public function setId($id)
	{
		$this->_id = $id;
	}

	/**
	 * Получает название
	 *
	 * @return string
	 */
	public function getName()
	{
		return $this->_name;
	}

	/**
	 * Устанавливает название
	 *
	 * @param string $name название
	 */
	public function setName($name)
	{
		$this->_name = $name;
	}

	/**
	 * Получает абривиатуру URL
	 *
	 * @return string
	 */
	public function getAlias()
	{
		return $this->_alias;
	}

	/**
	 * Устанавливает абривиатуру URL
	 *
	 * @param string $alias абривиатура URL
	 *
	 * @return void
	 */
	public function setAlias($alias)
	{
		$this->_alias = $alias;
	}
}