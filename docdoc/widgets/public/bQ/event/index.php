<?php
use dfs\common\config\YiiAppRunner;
/**
 * Пусть до самого коря проекта
 *
 * @var string
 */
define('ROOT_PATH', realpath(__DIR__ . "/../../../.."));
require ROOT_PATH . '/common/include/common.php';

$appRunner = new YiiAppRunner('widgets');
$config = $appRunner->buildConfig();


$availableComponents = isset($config['params']['availableComponents']) ? $config['params']['availableComponents'] : [];

foreach(array_keys($config['components']) as $componentName){
	if(!in_array($componentName, $availableComponents)){
		unset($config['components'][$componentName]);
	}
}
$_GET['r'] = 'bQ/event';

$appRunner->create($config)->run();