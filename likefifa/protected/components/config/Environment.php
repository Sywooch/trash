<?php


namespace likefifa\components\config;

/**
 * Class Environment
 *
 * Класс работы с окружением
 *
 * @package dfs\common\config
 */
class Environment
{
	const ENV_PRODUCTION = 'production';
	const ENV_STAGE = 'stage';
	const ENV_DEVELOPER = 'dev';
	const ENV_TEST = 'test';

	const DEFAULT_ENV = self::ENV_PRODUCTION;

	/**
	 * Окружения предназначенные для дебага
	 *
	 * @var string[]
	 */
	private static $debugEnvs = array(
		self::ENV_DEVELOPER,
		self::ENV_TEST,
	);

	/**
	 * Окружение
	 *
	 * @return string
	 */
	private static function getEnv()
	{
		return ConfigBuilder::getEnv();
	}

	/**
	 * Дебаг или не дебаг окружение
	 *
	 * @return bool
	 */
	public static function isDebug()
	{
		return in_array(self::getEnv(), self::$debugEnvs);
	}

	/**
	 * Production окружение
	 *
	 * @return bool
	 */
	public static function isProduction()
	{
		return self::getEnv() === self::ENV_PRODUCTION;
	}

	/**
	 * Stage или не stage окружение
	 *
	 * @return bool
	 */
	public static function isStage()
	{
		return self::getEnv() === self::ENV_STAGE;
	}

	/**
	 * Тестовое окружение
	 *
	 * @return bool
	 */
	public static function isTest()
	{
		return self::getEnv() === self::ENV_TEST;
	}

	/**
	 * Окружение
	 *
	 * @return bool
	 */
	public static function isCli()
	{
		return PHP_SAPI === 'cli';
	}

	/**
	 * Определение IP клиента
	 *
	 * @return null|string
	 */
	public static function getIp()
	{
		$ip = null;

		if (getenv("HTTP_CLIENT_IP")) {
			$ip = getenv("HTTP_CLIENT_IP");
		} elseif (getenv("HTTP_X_FORWARDED_FOR")) {
			$ip = getenv("HTTP_X_FORWARDED_FOR");
		} else {
			$ip = getenv("REMOTE_ADDR");
		}

		return $ip;
	}

}