<?php
namespace dfs\common\config;

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
	const ENV_STAGE      = 'stage';
	const ENV_DEVELOPER  = 'dev';
	const ENV_TEST       = 'test';

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
	 * Окружения в которых активирован хендлер ошибок
	 *
	 * @var string[]
	 */
	private static $enableErrorHandlerEnvs = array(
		self::ENV_DEVELOPER,
		self::ENV_PRODUCTION,
		self::ENV_STAGE,
	);

	/**
	 * Окружения в которых активирован хендлер исключений
	 *
	 * @var string[]
	 */
	private static $enableExceptionHandlerEnvs = array(
		self::ENV_DEVELOPER,
		self::ENV_STAGE,
		self::ENV_PRODUCTION,
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
	 * Включать отладочные хандлеры или нет
	 *
	 * @return bool
	 */
	public static function isEnableErrorHandler()
	{
		return in_array(self::getEnv(), self::$enableErrorHandlerEnvs);
	}

	/**
	 * Включать отладочные хандлеры или нет
	 *
	 * @return bool
	 */
	public static function isEnableExceptionHandler()
	{
		return in_array(self::getEnv(), self::$enableExceptionHandlerEnvs);
	}

	/**
	 * Дебаг или не дебаг окружение
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
	 * Тестовое окруждение
	 *
	 * @return bool
	 */
	public static function isTest()
	{
		return self::getEnv() === self::ENV_TEST;
	}

	/**
	 * Устанавливает константы yii для дебага
	 */
	public static function setupYiiDebug()
	{
		define('YII_DEBUG', self::isDebug());

		/**
		 * Временная проблема с ошибками потому как yii по умолчанию прерывает выполенние скрипта
		 * и убивает выполнение в продакшене
		 */
		define("YII_ENABLE_EXCEPTION_HANDLER", self::isEnableExceptionHandler());
        define("YII_ENABLE_ERROR_HANDLER", self::isEnableErrorHandler());
		define('YII_TRACE_LEVEL', 3);
	}

	/**
	 * Определение IP клиента
	 *
	 * @return null|string
	 */
	public static function getIp() {
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
