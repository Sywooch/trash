<?php
setlocale(LC_ALL, 'ru_RU.UTF8');

define('BASE_PATH', realpath(ROOT_PATH . "/diagnostica/public/protected"));

// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
return array(
	'basePath'   => BASE_PATH,
	'preload'	 => array('referral', 'city', 'trafficSource'),
	'homeUrl'    => '/',
	'name'       => 'DocDoc',
	'language'   => 'ru',
	// autoloading model and component classes
	'import'     => array(
		'application.models.*',
		'application.components.*',
		'application.extensions.*',
	),
	// application components
	'components' => array(
		'user'         => array(
			// enable cookie-based authentication
			'allowAutoLogin' => true,
		),
		// uncomment the following to enable URLs in path-format

		'urlManager'   => array(
			'urlFormat'      => 'path',
			'showScriptName' => false,
			'rules'          => require(__DIR__ . '/urlManagerRules.php'),
		),
		'errorHandler' => array(
			// use 'site/error' action to display errors
			'errorAction' => '/site/error',
		),
		'session'      => array(
			'autoStart'   => true,
			'cookieMode'  => 'only',
			'sessionName' => 'x-rx',
		),
		'city' => [
			'autodetect' => true,
		],
		//компонент, учитывающий партнерскую ссылку
		'referral'=>array(
			'class' => 'dfs\docdoc\components\Partner',
			//хранить в $_SESSION['ReferralObj']
			'sessParam' => 'ReferralObj',
			//брать из $_GET['pid']
			'getParam' => 'pid',
		),
		//компонент для опрееделения мобильной версии сайта
		'mobileDetect' => array(
			'class'  => 'dfs\docdoc\components\MobileDetect',
		),
	),
	'params'     => array(
		'appName' => 'diagnostica',
		// this is used in contact page
		'adminEmail' => 'support@docdoc.ru',
		//имя сайта
		'siteName' => 'diagnostica',
		//ID сайта. Для диагностики = 2
		'siteId' => '2',
		'phoneForMobile' => 74952230296,
	),
);
