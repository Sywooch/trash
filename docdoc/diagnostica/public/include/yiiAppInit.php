<?php
use \dfs\common\config\YiiAppRunner;

/**
 * Пусть до самого коря проекта
 *
 * @var string
 */
define('ROOT_PATH', realpath(__DIR__ . "/../../.."));
require ROOT_PATH . '/common/include/common.php';

define ("BASEDIR", ROOT_PATH . "/diagnostica/public/");

$runner = new YiiAppRunner('diagnostica');

$runner->create();
