<?php

use dfs\common\config\YiiAppRunner;
use dfs\common\config\Environment;
use dfs\common\test\TestDbLoader;

/**
 * Пусть до самого коря проекта
 *
 * @var string
 */
define('ROOT_PATH', realpath(__DIR__ . "/../.."));
require ROOT_PATH . '/common/include/common.php';

(new YiiAppRunner('common', Environment::ENV_TEST))
	->create();

new TestDbLoader(ROOT_PATH . '/common/data/schema.mysql.sql');

require_once LIB_PATH . "/php/connect.php";

define('BASEDIR', ROOT_PATH . DIRECTORY_SEPARATOR . 'back/public/');

if (!defined("SMS_GateId")) {
	define ("SMS_GateId", 1);
}