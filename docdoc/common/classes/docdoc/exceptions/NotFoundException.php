<?php
/**
 * Created by PhpStorm.
 * User: ndunaev
 * Date: 25.06.14
 * Time: 13:30
 */

namespace dfs\docdoc\exceptions;


class NotFoundException extends \Exception
{

	/**
	 * Данные для дебага
	 *
	 * @var mixed
	 */
	private $_debugData = null;

	/**
	 * Установка отладочной информации
	 *
	 * @param mixed $debugData
	 */
	public function setDebugData($debugData)
	{
		$this->_debugData = $debugData;
	}

	/**
	 * Получение отладочной информации
	 *
	 * @return mixed
	 */
	public function getDebugData()
	{
		return $this->_debugData;
	}



}