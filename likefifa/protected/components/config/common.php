<?php
use likefifa\components\config\Environment;

define('ROOT_PATH', realpath(__DIR__ . '/../../../'));
defined('YII_DEBUG') or define('YII_DEBUG', Environment::isDebug());
defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL', 3);
define('RELEASE_MEDIA', YII_DEBUG ? rand() : file_get_contents(ROOT_PATH . '/version'));