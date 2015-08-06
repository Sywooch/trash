<?php
use dfs\common\config\YiiAppRunner;

/**
 * Пусть до самого коря проекта
 *
 * @var string
 */
define('ROOT_PATH', realpath(__DIR__ . "/../../.."));
require ROOT_PATH . '/common/include/common.php';

(new YiiAppRunner('back'))
	->create();

require_once ROOT_PATH . '/back/config/overall/conf.php';
session_name("BACKDOCDOC_SID");

/**
 * CRONE
 *
 * Старые константы
 *
 * @deprecated
 */
define("LOCK_FILE_CRONE", BASEDIR . "include/croneLock.conf");
/**
 * @deprecated
 */
define("LOCK_FILE_CRONE_DIR", BASEDIR . "include/cronConf/");
/**
 * @deprecated
 */
define ("LOCK_FILE", BASEDIR . "include/SMSquery.conf");

require_once dirname(__FILE__) . "/../lib/php/connect.php";
require_once dirname(__FILE__) . "/../lib/php/validate.php";
require_once dirname(__FILE__) . "/../lib/php/user.class.php";
require_once dirname(__FILE__) . "/../lib/php/errorLog.php";
require_once dirname(__FILE__) . "/../lib/php/city.class.php";
require_once dirname(__FILE__) . "/../lib/php/dateconvertionLib.php";
require_once dirname(__FILE__) . "/../lib/php/russianTextUtils.class.php";
require_once dirname(__FILE__) . "/../lib/php/serviceFunctions.php";

$path = dirname(__FILE__);
