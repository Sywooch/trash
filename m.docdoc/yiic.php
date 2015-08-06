<?php
use dfs\common\extensions\config\SimpleConfigBuilder;
use dfs\common\extensions\config\YiiAppRunner;

define('VENDOR_PATH', __DIR__ . DIRECTORY_SEPARATOR . 'vendor');
define('YII_PATH', VENDOR_PATH . DIRECTORY_SEPARATOR . 'yiisoft/yii/framework');

require VENDOR_PATH . DIRECTORY_SEPARATOR . 'autoload.php';

$cb = new SimpleConfigBuilder(__DIR__ . "/config" , 'console');
YiiAppRunner::createAndRun($cb);
