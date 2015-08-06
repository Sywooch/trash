<?php

namespace components;

use Exception;

/**
 * Class App
 *
 * Sets config, starts controller
 *
 * @package components
 */
class App
{

	/**
	 * Config
	 *
	 * @var array
	 */
	public static $config;

	/**
	 * Array class map used by autoloading mechanism.
	 *
	 * @var array
	 */
	public static $classMap = [];

	/**
	 * Constructor
	 *
	 * @param array $config config/common.php
	 *
	 * @throws Exception
	 */
	public function __construct($config)
	{
		self::$config = $config;

		if (!Db::setPdo(
			$config["db"]["host"],
			$config["db"]["user"],
			$config["db"]["password"],
			$config["db"]["name"]
		)
		) {
			throw new Exception("Не удалось соединиться с базой данных");
		}
	}

	/**
	 * Starts controller using URL
	 *
	 * @return void
	 */
	public static function runController()
	{
		$url = trim($_SERVER["REQUEST_URI"], "/");

		foreach (self::$config["urlManager"] as $regularExpression => $controllerAction) {
			$regularExpression = str_replace("/", "\/", $regularExpression);
			if (preg_match("/^{$regularExpression}$/i", $url, $matches)) {
				$controllerActionExplode = explode("/", $controllerAction, 2);
				$controllerName = "\\controllers\\" . ucfirst($controllerActionExplode[0]) . "Controller";
				$actionName = "action" . ucfirst($controllerActionExplode[1]);
				$controller = new $controllerName;
				if (!empty($matches[2])) {
					$controller->$actionName($matches[1], $matches[2]);
				} else {
					if (!empty($matches[1])) {
						$controller->$actionName($matches[1]);
					} else {
						$controller->$actionName();
					}
				}
			}
		}
	}

	/**
	 * Class autoload loader
	 *
	 * @param string $className class name
	 *
	 * @return bool|mixed
	 */
	public static function autoload($className)
	{
		if (array_key_exists($className, self::$classMap)) {
			return false;
		}

		include(
			__DIR__ .
			DIRECTORY_SEPARATOR .
			".." .
			DIRECTORY_SEPARATOR .
			str_replace("\\", DIRECTORY_SEPARATOR, $className) .
			".php");
		self::$classMap[] = $className;

		return true;
	}

	/**
	 * Gets POST variable by name
	 *
	 * @param string $name $_POST["name"]
	 *
	 * @return array|null
	 */
	public static function getPost($name = "Data")
	{
		if (!empty($_POST[$name])) {
			return $_POST[$name];
		}

		return null;
	}
}

spl_autoload_register(['components\App', 'autoload']);