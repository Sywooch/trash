<?php
/**
 * Yii Конфиг
 *
 * Локально.
 * Самый высокий приоритет.
 *
 * @see https://docdoc.atlassian.net/wiki/pages/viewpage.action?pageId=5373985#id-Концепцияновойархитектуры-Управлениеконфигурацией
 */
return array(
	'params'     => array(
		'env' => 'production',
		'php_user_ini'  => [
			'newrelic.appname' => 'DocDoc [Production]',
		],
		'ya-metrika-id'   => '',
		'ya-metrika-id-diagnostic'   => '15482359',
		//параметры интеграционного шлюза к клиникам
		'booking'               => array(
			'apiUrl'             => 'https://booking.docdoc.pro/api/1.0/',
			'login'              => 'test-partner',
			'password'           => 'docdoc_test654',
			//на сколько дней вперед загружать расписание
			'loadDays'           => 30,
			'testClinic'         => [
				'positive' => 13
			],
		),
		'asterisk' => [
			'db_servers' => [
				'sel-ast3' => [
					'connectionString' => 'mysql:host=sel-ast3.docdoc.pro;dbname=asterisk_docdoc',
					'username'         => 'docdoc',
					'password'         => 'zLVTkthJ',
				],
				'sel-ast18' => [
					'connectionString' => 'mysql:host=sel-ast18.docdoc.pro;dbname=asterisk_docdoc',
					'username'         => 'docdoc',
					'password'         => 'zLVTkthJ',
				],
				'ihc-ast-web1' => [
					'connectionString' => 'mysql:host=ihc-ast-web1.docdoc.pro;dbname=asterisk_docdoc',
					'username'         => 'docdoc',
					'password'         => 'zLVTkthJ',
				],
				'flops-ast4' => [
					'connectionString' => 'mysql:host=flops-ast4.docdoc.pro;dbname=asterisk_docdoc',
					'username'         => 'docdoc',
					'password'         => 'zLVTkthJ',
				]
			]
		]
	),
	'components' => array(
		'cache' => array(
			'hostname' => 'sel-web4.docdoc.pro',
		),
		'redis' => array(
			'hostname' => 'sel-web4.docdoc.pro',
		),
		// База данных
		'db'    => array(
			'connectionString' => 'mysql:host=sel-web4.docdoc.pro;dbname=docdoc_docdoc',
			'username'         => 'docdoc',
			'password'         => 'SE08R1D8',
		),
	),
);
