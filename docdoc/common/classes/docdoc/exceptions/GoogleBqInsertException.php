<?php
/**
 * Created by PhpStorm.
 * User: ndunaev
 * Date: 25.06.14
 * Time: 13:30
 */

namespace dfs\docdoc\exceptions;

/**
 * Class GoogleBqInsertException
 *
 * @package dfs\docdoc\exceptions
 */
class GoogleBqInsertException extends \Exception
{
	CONST REASON_INVALID = 'invalid';

	CONST REASON_ACCESS_DINIED = 'accessDenied';
	CONST REASON_BACKEND_ERROR = 'backendError';
	CONST REASON_BILLING_NOT_ENABLED = 'billingNotEnabled';
	CONST REASON_BLOCKED = 'blocked';
	CONST REASON_INTERNAL_ERROR = 'internalError';
	CONST REASON_QUOTA_EXCEEDED = 'quotaExceeded';
	CONST REASON_RATE_LIMIT = 'rateLimitExceeded';
	CONST REASON_IN_USE = 'resourceInUse';
	CONST REASON_RESOURCE_EXCEEDED = 'resourcesExceeded';
	CONST REASON_TOO_LARGE = 'responseTooLarge';


	/**
	 * Данные для дебага
	 *
	 * @var mixed
	 */
	private $_insertErrors = null;

	/**
	 * Установка отладочной информации
	 *
	 * @param mixed $errors
	 */
	public function setInsertErrors($errors)
	{
		$this->_insertErrors = $errors;
	}

	/**
	 * Получение отладочной информации
	 *
	 * @return mixed
	 */
	public function getInsertErrors()
	{
		return $this->_insertErrors;
	}

	/**
	 * Получить ошибку, которая случилась при вставке записи с индексом $index
	 *
	 * @param $index
	 *
	 * @return array
	 */
	public function getErrorInfo($index)
	{
		foreach ($this->_insertErrors as $i => $error) {
			if ($index == $error['index']) {
				return $error['errors'][0];
			}
		}

		return null;
	}

	/**
	 * Нужно ли повторно посылать запись в BigQuery
	 *
	 * @param string $reason
	 * @return bool
	 */
	public function isRetry($reason)
	{
		//коды ошибок, по которым нужна повторная отправка
		$retryReasons = [
			self::REASON_ACCESS_DINIED,
			self::REASON_BACKEND_ERROR,
			self::REASON_BILLING_NOT_ENABLED,
			self::REASON_BLOCKED,
			self::REASON_INTERNAL_ERROR,
			self::REASON_QUOTA_EXCEEDED,
			self::REASON_RATE_LIMIT,
			self::REASON_IN_USE,
			self::REASON_RESOURCE_EXCEEDED,
			self::REASON_TOO_LARGE,
		];

		return in_array($reason, $retryReasons);
	}



}