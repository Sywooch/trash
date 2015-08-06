<?php
ini_set ("error_reporting", E_ALL);
ini_set ("display_errors", "On");
ini_set ("register_globals", "Off");

define('YII_DEBUG',		true);
defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL', 3);

define('DB_HOST',		'localhost');
define('DP_PORT',		3306);
define('DB_NAME',		'dev');
define('DB_USERNAME',	'mike');
define('DB_PASSWORD',	'mypasS77');

define('SESSION_LIFETIME', '9999');

mb_internal_encoding('UTF-8');

define('EMAIL_FROM', "moyregion@inbox.ru");

define('SHOP_PERCENT', 10);

define("IS_PAYMENT", TRUE);
define("M_SHOP", 20900850);
define("M_KEY", "mypasS77");
define("PAY_DIFF", 3000);