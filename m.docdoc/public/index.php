<?php
use dfs\common\extensions\config\SimpleConfigBuilder;
use dfs\common\extensions\config\YiiAppRunner;

define('VENDOR_PATH', __DIR__ . '/../vendor');
define('YII_PATH', VENDOR_PATH . DIRECTORY_SEPARATOR . 'yiisoft/yii/framework');

require VENDOR_PATH . DIRECTORY_SEPARATOR . 'autoload.php';

$configName = null;
if (preg_match('/^m\.front\.([^.]+)\.docdoc/', $_SERVER['HTTP_HOST'], $matches)) {
	$configName = $matches[1];
}
if (preg_match('/\.docdoc\.ru/', $_SERVER['HTTP_HOST'])) {
	$configName = 'production';
}

$cb = new SimpleConfigBuilder(__DIR__ . "/../config" , 'main', $configName);
YiiAppRunner::createAndRun($cb);