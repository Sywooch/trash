<?php
use dfs\common\config\YiiAppRunner;

/**
 * Пусть до самого коря проекта
 *
 * @var string
 */
define('ROOT_PATH', realpath(__DIR__ . "/../../.."));
require ROOT_PATH . '/common/include/common.php';

(new YiiAppRunner('front'))
	->create();

require ROOT_PATH . '/front/config/overall/conf.php';

ini_set('default_charset', 'utf-8');

require_once LIB_PATH . "../lib/php/connect.php";
require_once dirname(__FILE__) . "/../lib/php/page.class.php";
require_once dirname(__FILE__) . "/../lib/php/commonFunctions.php";

require_once LIB_PATH . "../lib/php/errorLog.php";
require_once LIB_PATH . "../lib/php/validate.php";
require_once LIB_PATH . "../lib/php/emailQuery.class.php";
require_once LIB_PATH . "../lib/php/russianTextUtils.class.php";
require_once dirname(__FILE__) . "/../lib/php/dictionary.php";
require_once LIB_PATH . "../lib/php/dateTimeLib.php";
require_once LIB_PATH . "../lib/php/MobileDetect.php";