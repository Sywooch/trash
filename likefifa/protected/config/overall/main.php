<?php
return [
	'preload'       => [
		/*'debug',*/
		'log',
	],
	'import'        => [
		'application.components.application.*',
		'application.components.system.front.*',
		'application.extensions.*',
		'application.extensions.GridViewEdit.*',
		'ext.eoauth.*',
		'ext.eoauth.lib.*',
		'ext.lightopenid.*',
		'ext.eauth.*',
		'ext.eauth.services.*',
	],
	'controllerMap' => [
		'ajax' => 'likefifa\controllers\AjaxController',
	],
	// application components
	'components'    => [
		/*'debug'        => [
			'class'         => 'application.extensions.yii2-debug.Yii2Debug',
			'highlightCode' => false,
		],*/
		'loid'         => [
			'class' => 'ext.lightopenid.loid',
		],
		'geocoder'     => [
			'class'     => 'ext.EGeocoder.EGeocoder',
			'providers' => [
				[
					'name'   => 'Yandex',
					'locale' => 'ru_RU',
				],
			],
		],
		'clientScript' => [
			'scriptMap' => ['jquery-ui.css' => false],
		],
		'user'         => [
			'class'           => 'likefifa\components\system\WebUser',
			'allowAutoLogin'  => true,
			'autoRenewCookie' => true,
			'authTimeout'     => 9999,
		],
		'urlManager'   => [
			'urlFormat'      => 'path',
			'showScriptName' => false,
			'urlSuffix'      => '/',
			'rules'          => require "urls.php",
		],
		'errorHandler' => [
			'errorAction' => '/site/error',
		],
		'session'      => [
			'autoStart'   => false,
			'cookieMode'  => 'only',
			'sessionName' => 'x-rx',
			'timeout'     => 9999,
		],
		'mobileDetect' => [
			'class' => 'application.vendors.iamsalnikov.MobileDetect.MobileDetect.MobileDetect'
		],
	],
];