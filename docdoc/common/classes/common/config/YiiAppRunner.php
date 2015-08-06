<?php

namespace dfs\common\config;

use dfs\docdoc\extensions\Logger;
use Yii;
use dfs\docdoc\extensions\WebApplication;
use dfs\docdoc\extensions\ConsoleApplication;

/**
 * Class YiiAppRunner
 *
 * Инициализирцет Yii приложение в многосайтовой среде
 *
 * @package dfs\common\config
 */
class YiiAppRunner
{
	/**
	 * Проект
	 *
	 * @var string
	 */
	private $_project;

	/**
	 * Дополнительные конфиги
	 *
	 * @var array
	 */
	protected $_extraConfigs = [];


	/**
	 * @param string $project Проект
	 * @param string $env     Окружение
	 */
	public function __construct($project, $env = null)
	{
		$this->_project = $project;
		if ($env) {
			ConfigBuilder::setEnv($env);
		}
		Environment::setupYiiDebug();

		$frameworkFile = Environment::isDebug() ? 'yii' : 'yiilite';
		require_once YII_PATH . DIRECTORY_SEPARATOR . $frameworkFile . '.php';
	}

	/**
	 * подключение дополнительных конфигов
	 *
	 * @param string $path
	 *
	 * @return $this
	 */
	public function addConfig($path)
	{
		$this->_extraConfigs[] = $path;

		return $this;
	}

	/**
	 * Создаёт Yii приложение
	 *
	 * @param array|null $config Конфиг можно передать заранее
	 *
	 * @return \CApplication
	 */
	public function create(array $config = null)
	{
		is_null($config) && $config = $this->buildConfig($this->_project, $this->getConfigEnv());

		$appClass = $this->isCli() ? ConsoleApplication::class : WebApplication::class;

		//установка нашего настраиваемого логера
		$loggerConfig = isset($config['params']['logger']) ? $config['params']['logger'] : [];
		$logger = new Logger();
		$logger->setParams($loggerConfig);
		Yii::setLogger($logger);

		//создание приложения
		$app = Yii::createApplication($appClass, $config);

		if (Environment::isTest()) {
			// make sure non existing PHPUnit classes do not break with Yii autoloader
			Yii::$enableIncludePath = false;;
		}

		return $app;
	}

	/**
	 * Получение конфига
	 *
	 * @param null|string $project
	 * @param null|string $env
	 *
	 * @return array
	 */
	public function buildConfig($project = null, $env = null)
	{
		is_null($project) && $project = $this->_project;
		is_null($env) && $env = $this->getConfigEnv();

		$configBuilder = new ConfigBuilder($project, $env, $this->_extraConfigs);
		//подключаем необходимые классы
		Yii::$classMap = $configBuilder->getClassMap();
		$config = $configBuilder->getConfig();

		return $config;
	}

	/**
	 * Тип окружения для загрузки конфига
	 *
	 * @return string
	 */
	private function getConfigEnv()
	{
		if (Environment::isTest()) {
			return 'test';
		}

		return $this->isCli()
			? 'cli'
			: 'main';
	}

	/**
	 * Окружение
	 *
	 * @return bool
	 */
	private function isCli()
	{
		return PHP_SAPI === 'cli';
	}
} 
