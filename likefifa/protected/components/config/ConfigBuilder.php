<?php


namespace likefifa\components\config;

use CMap;

/**
 * Class ConfigBuilder
 *
 * Класс сбооки конфига под yii
 *
 * @see     https://docdoc.atlassian.net/wiki/pages/viewpage.action?pageId=5373985#id-Концепцияновойархитектуры-Управлениеконфигурацией
 *
 * @package dfs\Config
 */
class ConfigBuilder
{
	/**
	 * Текущее значение окружения
	 *
	 * @var string
	 */
	private static $env;

	/**
	 * Приоритет насследования конфигов
	 *
	 * @var string[]
	 */
	private $inheritancePriority = array(
		Environment::ENV_PRODUCTION,
		Environment::ENV_STAGE,
		Environment::ENV_DEVELOPER,
		Environment::ENV_TEST,
	);

	/**
	 * Список конфигов
	 *
	 * @var string[]
	 */
	private $builder = array();

	/**
	 * Окружение
	 *
	 * @var string
	 */
	private $_env;

	/**
	 * @param string $env Окружение
	 */
	public function __construct($env = 'main')
	{
		$this->_env = $env;

		$this->builder[] = ROOT_PATH . "/protected/config/overall/common.php";

		// Определяем консольное ли приложение
		$overallName = Environment::isCli() && !$this->isTest() ? 'console' : 'main';

		$this->builder[] = ROOT_PATH . "/protected/config/overall/{$overallName}.php";

		if ($this->isTest()) {
			$this->builder[] = ROOT_PATH . "/protected/config/overall/test.php";
		}

		/**
		 * Конфиги которые перегружают окружения
		 */
		foreach ($this->inheritancePriority as $evnName) {
			$this->builder[] = ROOT_PATH . "/protected/config/{$evnName}/local.php";
			if ($this->isTest()) {
				$this->builder[] = ROOT_PATH . "/protected/config/{$evnName}/test.php";
			}
			if ($evnName === self::getEnv()) {
				break;
			}
		}

		if (Environment::isDebug()) {
			$this->builder[] = self::getLocalConfigPath();
		}
	}

	/**
	 * Собранный конфиг
	 *
	 * @return array
	 */
	public function getConfig()
	{
		$ret = $this->_collectParams($this->builder);

		/**
		 * Костыль, т.к. в тесте нужно полностью перегружать секцию с кэшом
		 */
		if ($this->_env === 'test' || $ret['components']['cache']['class'] == 'CDummyCache') {
			$ret['components']['cache'] = array(
				'class' => 'CDummyCache',
			);
		}

		return $ret;
	}

	/**
	 * Сборка итогового массива с конфигом или classMap'ом
	 *
	 * @param string[] $params массив с конфигами, который нужно подключить
	 *
	 * @return array
	 */
	private function _collectParams($params)
	{
		$result = array();
		foreach ($params as $p) {
			$result = CMap::mergeArray(
				$result,
				require $p
			);
		}

		return $result;
	}

	/**
	 * Окружение
	 *
	 * @return string
	 */
	public static function getEnv()
	{
		if (is_null(self::$env)) {
			$config = require self::getLocalConfigPath();
			self::$env = isset($config['params']['env']) ? $config['params']['env'] : Environment::DEFAULT_ENV;
		}
		return self::$env;
	}

	/**
	 * Задаёт окружение
	 *
	 * @param string $env
	 */
	public static function setEnv($env)
	{
		self::$env = $env;
	}

	/**
	 * Установлено тестовое окружение
	 *
	 * @return string
	 */
	public function isTest()
	{
		return $this->_env == Environment::ENV_TEST;
	}

	/**
	 * Путь к моему локальному конфигу
	 *
	 * @return string
	 */
	private static function getLocalConfigPath()
	{
		return ROOT_PATH . "/protected/config/overall/local.php";
	}
}