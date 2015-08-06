<?php
/**
 * Yii Конфиг
 *
 * Девелоперский
 * Самый высокий приоритет.
 *
 * @see https://docdoc.atlassian.net/wiki/pages/viewpage.action?pageId=5373985#id-Концепцияновойархитектуры-Управлениеконфигурацией
 */
return [
	'params'     => [
		'env'                        => 'dev',
		'ya-metrika-id'              => '',
		'ya-metrika-id-diagnostic'   => '',
		'booking'                    => [
			'apiUrl'   => 'https://booking.sample.docdoc.pro/api/1.0/',
			'password' => 'test-partner',
			'login'    => 'docdoc_test654',
			//на сколько дней вперед загружать расписание
			'loadDays' => 5,
		],
		'phone_providers'            => [
			'download_dir' => ROOT_PATH . DIRECTORY_SEPARATOR . 'back/records',
		],
		'php_user_ini'               => [
			'error_reporting'          => 'E_ALL',
			'xdebug.remote_host'       => 'msk-tcentr.docdoc.pro', //офисная циска
			'xdebug.remote_port'       => '9000', //порт проброшен на dbgpproxy.docdoc.pro, где поднят dbgp proxy
			'zend_extension_ts'        => 'xdebug.so',
			'xdebug.remote_enable'     => '1',
			'session.save_handler'     => 'redis',
			'session.save_path'        => '"tcp://127.0.0.1:6380?weight=1&timeout=1&database=0"',
			'session.gc_maxlifetime'   => '86400',
			'newrelic.appname'         => 'PHP Application',
		],
		'allowOnlineBooking'         => false,
		'google_big_query'           => [
			'env' => 'dev',
		],
		'asterisk'                   => array(
			'context' => 'test-outgoing',
			'db_servers' => [
				'sel-ast3' => [
					'connectionString' => 'mysql:host=sel-ast3.docdoc.pro;dbname=asterisk_docdoc_dev',
				],
				'sel-ast18' => [
					'connectionString' => 'mysql:host=sel-ast18.docdoc.pro;dbname=asterisk_docdoc_dev',
				],
				'ihc-ast-web1' => [
					'connectionString' => 'mysql:host=ihc-ast-web1.docdoc.pro;dbname=asterisk_docdoc_dev',
				],
				'flops-ast4' => [
					'connectionString' => 'mysql:host=flops-ast4.docdoc.pro;dbname=asterisk_docdoc_dev',
				]
			]
		),
	],
	'components' => [
		'cache'    => [
			'hostname' => 'localhost',
			'database' => 11,
		],
		'log'      => [
			'class'  => 'CLogRouter',
			'routes' => [
				'web_log'           => [
					'class'         => 'CWebLogRoute',
					'showInFireBug' => true,
					'enabled'       => PHP_SAPI !== 'cli',
				],
				'db_errors_to_mail' => [
					'enabled' => false,
				]
			],
		],
		'mixpanel' => [
			//токен для и девелоперского проектов
			'token' => 'fb7f1db4eb67de3f8c26e7bdee574e97',
		],
		'db'       => [
			'enableParamLogging' => true,
		],
	],
];
