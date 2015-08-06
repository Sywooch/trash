<?php

namespace dfs\docdoc\exceptions;

/**
 * Class SpamException
 * @package dfs\docdoc\exceptions
 */
class SpamException extends \Exception
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