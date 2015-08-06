<?php

namespace dfs\common\config;

/**
 * Class ConfigBuilder
 *
 * Класс сбооки конфига под yii
 *
 * @see https://docdoc.atlassian.net/wiki/pages/viewpage.action?pageId=5373985#id-Концепцияновойархитектуры-Управлениеконфигурацией
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
	 * Список classMap'ов. Используется для того, чтобы добавить класс в autoloader Yii
	 * для всех проектов или какого-то проекта в отдельности
	 *
	 * @var string[]
	 */
	private $_classMap = array();

	/**
	 * Окружение
	 *
	 * @var string
	 */
	private $_env;

	/**
	 * @param string $project Проект
	 * @param string $env     Окружение
	 * @param array  $extraConfigs Дополнительные конфиги
	 */
	public function __construct($project, $env = 'main', array $extraConfigs = [])
	{
		$this->_env = $env;
		$this->construct('common');

		// Для приложений из комона может получиться повторная загрузка конфига
		if ($project !== 'common') {
			$this->construct($project);
		}

		/**
		 * Конфиги которые перегружают окружения
		 */
		foreach($this->inheritancePriority as $evnName) {
			$this->builder[] = ROOT_PATH . "/common/config/{$evnName}/local.php";
			if ($evnName === self::getEnv()){
				break;
			}
		}

		foreach($extraConfigs as $path) {
			$this->builder[] = ROOT_PATH . $path;
		}

		if (
			Environment::isDebug()
			||
			/**
			 * stage конфиг загрузится 2 раза.
			 * Это нужно чтобы работал Legacy стенд
			 * Насследование для legacy: production -> stage -> legacy
			 */
			Environment::isStage()
		) {
			$this->builder[] = self::getLocalConfigPath();
		}

		if (Environment::isTest()) {
			$evnName = Environment::ENV_TEST;
			$this->builder[] = ROOT_PATH . "/common/config/{$evnName}/test.php";
			$this->builder[] = ROOT_PATH . "/common/config/overall/test.php";
		}
	}

	/**
	 * Конструирует часть окружения в зависимости от проекта
	 *
	 * @param string $project Проект
	 */
	private function construct($project)
	{
		$overallName = $this->_env === 'test'? 'cli' : $this->_env;

		$this->builder[] = ROOT_PATH . "/{$project}/config/overall/common.php";
		$this->builder[] = ROOT_PATH . "/{$project}/config/overall/{$overallName}.php";

		$this->_classMap[] = ROOT_PATH . "/{$project}/config/overall/classMap.php";
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
		if ($this->_env === 'test') {
			$ret['components']['cache'] = array(
				'class' => 'CDummyCache',
			);
		}

		return $ret;
	}

	/**
	 * Собранный classMap
	 *
	 * @return array
	 */
	public function getClassMap()
	{
		return $this->_collectParams($this->_classMap);
	}

	/**
	 * Сборка итогового массива с конфигом или classMap'ом
	 *
	 * @param string[] $params массив с конфигами, который нужно подключить
	 * @return array
	 */
	private function _collectParams($params)
	{
		$result = array();
		foreach($params as $p) {
			$result = \CMap::mergeArray(
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
			$config    = require self::getLocalConfigPath();
			self::$env =  isset($config['params']['env']) ? $config['params']['env'] : Environment::DEFAULT_ENV;
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
	 * Путь к моему локальному конфигу
	 *
	 * @return string
	 */
	private static function getLocalConfigPath()
	{
		return ROOT_PATH . "/common/config/overall/local.php";
	}
}
