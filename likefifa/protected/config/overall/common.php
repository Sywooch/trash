<?php
// Идентификаторы социальных сетей
$vkApiId = "3769852";
$vkApiSecret = "fnkqfPR6nVLqcjkV91hI";

/*$vkApiId = "4495416";
$vkApiSecret = "h4Q1cAREibxpzehaB4nz";*/

$facebookApiId = "1480681702187358";
$facebookApiSecret = "e1189688a6d4c8298cfc726ff3f74363";

return [
	'language'   => 'ru',
	'name'       => 'LikeFifa',
	'charset'    => 'UTF-8',
	'basePath'   => dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..',
	'import'     => [
		'application.models.*',
		'application.components.*',
		'application.components.extensions.ImageManager.*',
	],
	'modules'    => [
		// Модуль платеженй
		'payments' => [
			'class'          => 'dfs\modules\payments\PaymentsModule',
			'onInvoiceClose' => [
				["Sms", "onPaymentInvoiceClose"],
			],
			'isActive'       => true, // Отключает монитизацию
			'processors'     => [
				'robokassa' => [
					// 'url' => 'http://test.robokassa.ru/Index.aspx', // ссылка на тестовый аккаунт
					'password1' => 'O55)q2Dcv-_8"6Tn',
					'password2' => '1K6BIf@:]224t[5#',
					'login'     => 'likefifa',
				],
			],
		],
	],
	'components' => [
		'gaTracking'   => [
			'class' => 'likefifa\components\extensions\GaTrackingComponent',
		],
		'mailer'       => [
			'class'  => 'likefifa\components\extensions\LfMandrill',
			'apikey' => 'jfl3xe6dRD6LhGwtq1EpHw',
		],
		'sms'          => [
			'class'    => '\dfs\smsc\SmsC',
			'login'    => 'medservices', // логин клиента
			'password' => 'Fgvb12GGGG123', // пароль или MD5-хеш пароля в нижнем регистре
			'post'     => true, // использовать метод POST
			'https'    => true, // использовать HTTPS протокол
			'charset'  => 'utf-8', // кодировка сообщения: utf-8, koi8-r или windows-1251 (по умолчанию)
			'debug'    => true,
			'sender'   => 'likefifa'
		],
		'eauth'        => [
			'class'       => 'ext.eauth.EAuth',
			'popup'       => true, // Use the popup window instead of redirecting.
			'cache'       => false, // Cache component name or false to disable cache. Defaults to 'cache'.
			'cacheExpire' => 0, // Cache lifetime. Defaults to 0 - means unlimited.
			'services'    => [ // You can change the providers and their classes.
				'vkontakte'     => [
					'class'         => 'VKontakteOAuthService',
					'client_id'     => $vkApiId,
					'client_secret' => $vkApiSecret,
				],
				'facebook'      => [
					// register your app here: https://developers.facebook.com/apps/
					'class'         => 'FacebookOAuthService',
					'client_id'     => $facebookApiId,
					'client_secret' => $facebookApiSecret,
				],
				'odnoklassniki' => [
					// register your app here: http://dev.odnoklassniki.ru/wiki/pages/viewpage.action?pageId=13992188
					// ... or here: http://www.odnoklassniki.ru/dk?st.cmd=appsInfoMyDevList&st._aid=Apps_Info_MyDev
					'class'         => 'OdnoklassnikiOAuthService',
					'client_id'     => '189251840',
					'client_public' => 'CBACMIEMABABABABA',
					'client_secret' => 'E1D458A39337068755405494',
					'title'         => 'Odnoklassniki',
				],
			],
		],
		'cache'        => [
			'class'    => defined('YII_DEBUG') && YII_DEBUG && empty($_GET["noc"]) ? 'CDummyCache' : 'CRedisCache',
			'hostname' => '127.0.0.1',
			'port'     => 6379,
			'database' => 2,
		],
		'db'           => [
			'emulatePrepare'     => true,
			'charset'            => 'utf8',
			'enableParamLogging' => true,
		],
		'log'          => [
			'class'  => 'CLogRouter',
			'routes' => [
				[
					'class'  => 'CFileLogRoute',
					'levels' => 'error, warning, info',
					'except' => 'exception.YiinfiniteScrollerException.404',
					'filter' => [
						'class'    => 'likefifa\components\system\LogFilter',
						'excluded' => [],
					],
				],
			],
		],
		'search'       => [
			'class' => 'application.extensions.DGSphinxSearch.DGSphinxSearch',
			'server' => '127.0.0.1',
			'port' => 9312,
			'maxQueryTime' => 3000,
			'enableProfiling' => 0,
			'enableResultTrace' => 0,
		],
		'activeRegion' => [
			'class' => 'likefifa\components\application\ActiveRegion',
		],
		'elephant' => [
			'class' => 'likefifa\components\extensions\YiiElephantIOComponent',
			'host' => '127.0.0.1',
			'sslPort' => 3000,
			'port' => 3001,
		],
	],
	'params'     => [
		'adminEmail'                      => 'fifa@likefifa.ru',
		'appointmentCommission'           => 0.3,
		'universalPassword'               => 'likefifapassword17', // Универсальный пароль для входа в ЛК

		// Рассылка
		"mailing"                         => [
			"email" => "fifa@likefifa.ru",
			"phone" => "+7(968)903-56-03",
		],
		'FSLockPrefix'                    => 'likefifa-common',
		/**
		 * Количество секунд, в течение которых мастер
		 * может принять заявку
		 */
		'AppointmentsAutoRejectedTimeout' => 2 * 60 * 60, // 2 часа

		/**
		 * Количество секунд до конца таймаута, в течение которого
		 * заявка считается горящей
		 */
		'AppointmentsRedAlarmTimeout'     => 15 * 60, // 15 минут

		/**
		 * Социальные сети
		 */
		"vk"                              => [
			"apiId"     => $vkApiId,
			"apiSecret" => $vkApiSecret,
		],
		"facebook"                        => [
			"apiId"     => $facebookApiId,
			"apiSecret" => $facebookApiSecret,
		],
		/**
		 * E-mail адреса администраторов и операторов
		 */
		"groupEmail"                      => 'callcenter@likefifa.ru',
		/**
		 * Время жизни кэша
		 */
		"cacheTime"                       => 60 * 60, // в секундах

		/**
		 * Подарок мастерам в рублях
		 */
		"bonusMaster"                     => 200,
		/**
		 * Маленький баланс мастера
		 */
		"littelBalance"                   => 300,
		/**
		 * Корневой URL
		 */
		"baseUrl"                         => "http://likefifa.ru",
		// Время до которого незавершенные заявки не получают уведомлений
		"remindersTimeFrom"               => "2014-02-12 00:00:00",
		// GA
		"gaAccount"                       => "UA-37101956-1",
		// Социальные кнопки на главной странице
		"socialMain"                      => false,
		"commonEmail"                     => "fifa@likefifa.ru",
		"commonPhone"                     => "8-968-903-56-03",
		'filesDir'                        => '/upload/files',
		'imagesDir'                       => '/upload/images',
		'tinyMCE'                         => [
			'widthToLink'  => 500,
			'heightToLink' => 500,
			'classLink'    => 'lightview',
			'relLink'      => 'lightbox',
		],
	],
];
