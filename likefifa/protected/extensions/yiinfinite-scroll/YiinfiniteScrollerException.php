<?php

/**
 * Эксепшн для отлавливания 404 ошибок скролла
 *
 * Class YiinfiniteScrollerException
 */
class YiinfiniteScrollerException extends CHttpException
{
	/**
	 * @var integer HTTP status code, such as 403, 404, 500, etc.
	 */
	public $statusCode;

	/**
	 * Constructor.
	 *
	 * @param integer $status  HTTP status code, such as 404, 500, etc.
	 * @param string  $message error message
	 * @param integer $code    error code
	 */
	public function __construct($status, $message = null, $code = 0)
	{
		parent::__construct($status, $message, $code);
	}
} 