<?php
ini_set ("error_reporting", E_ALL);
ini_set ("display_errors", "On");
ini_set ("register_globals", "Off");

define('YII_DEBUG',		true);
defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL', 3);

define('DB_HOST',		'localhost');
define('DP_PORT',		3306);
define('DB_NAME',		'matr');
define('DB_USERNAME',	'matr');
define('DB_PASSWORD',	'mypasS77');

define('SESSION_LIFETIME', '9999');

mb_internal_encoding('UTF-8');

define('EMAIL_HOST', "109.120.173.219");
define('EMAIL_PORT', 25);
define('EMAIL_FROM', "mailer@300rub.net");
define('EMAIL_PASS', "?vQ5we65");

define('SHOP_PERCENT', 0);

define("IS_PAYMENT", false);
define("M_SHOP", 20900850);
define("M_KEY", "mypasS77");
define("PAY_DIFF", 1);

define("DOLLAR", 1);