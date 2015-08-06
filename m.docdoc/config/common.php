<?php
return [
	'basePath' => dirname(__FILE__) . DIRECTORY_SEPARATOR . '..',
	'language' => 'ru',
	'charset' => 'UTF-8',
	'preload' => ['log', 'CURL', 'RESTClient'],
	'import' => [
		'application.components.*',
		'application.models.*',
		'application.extensions.*',
		'application.dto.*',
	],
	'modules' => [],
	'components' => [
		'cache' => [
			'class' => 'CRedisCache',
			'hostname' => 'localhost',
			'database' => 0,
		],
		'session' => array(
			'autoStart' => true,
		),
		'errorHandler' => array(
			'errorAction' => 'site/error',
		),
		'urlManager' => [
			'urlFormat' => 'path',
			'showScriptName' => false,
			'urlSuffix' => '',
			'rules' => require __DIR__ . '/urlManagerRules.php',
		],
		'db' => [
			'emulatePrepare' => true,
			'charset' => 'utf8',
			'enableParamLogging' => true,
		],
		'log' => [
			'class' => 'CLogRouter',
			'routes' => [
				[
					'class' => 'CFileLogRoute',
					'levels' => 'error, warning, info',
				],
			],
		],
		'RESTClient' => array(
			'class' => 'ext.RESTClient.RESTClient',
		),
		'CURL' => array(
			'class' => 'ext.RESTClient.CURL',
		),
		'mixpanel' => [
			'class' => 'dfs\components\MixpanelComponent',
			//токен для stage и девелоперских проектов
			'token' => 'fb7f1db4eb67de3f8c26e7bdee574e97',
		],
		'city' => array(
			'class'     => 'dfs\components\City',
			'defaultId' => 1, // Москва (по-умолчанию)
		),
	],
	'params' => [
		'env' => 'dev',
		//rest
		'rest_api_login' => 'docdoc_m',
		'rest_api_password' => 'dgHBhq213hhl',
		'rest_api_domain' => 'back.stage.docdoc.pro',
		'rest_api_version' => '1.0.5',
		//paging
		'page_size' => 10,
		//order
		'default_sort' => 'rating_internal',
		//other
		'main_site' => 'docdoc.ru',
		'ga-universal-id' => null,
		'gtm-id' => null,

		'phone' => '74957831875', // Телефон нужно получать из API!!!
		'isValidSsl' => true,
		'isRestCache' => true,
	],
];