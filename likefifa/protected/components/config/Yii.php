<?php
use likefifa\components\config\ConsoleApplication;
use likefifa\components\config\WebApplication;

/**
 * Перегружает стандартный Yii класс
 *
 * @package likefifa\components\config
 */
class Yii extends YiiBase
{
	/**
	 * @static
	 * @return WebApplication|ConsoleApplication
	 */
	public static function app()
	{
		return parent::app();
	}

	public static function param($p)
	{
		return self::app()->params[$p];
	}
}