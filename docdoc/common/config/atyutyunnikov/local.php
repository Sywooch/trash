<?php
/**
 * Yii Конфиг
 *
 * Локально.
 * Самый высокий приоритет.
 *
 * @see https://docdoc.atlassian.net/wiki/pages/viewpage.action?pageId=5373985#id-Концепцияновойархитектуры-Управлениеконфигурацией
 */
return [
	'params'     => [
		'env'          => 'dev',
		'hosts'        => [
			'front'       => 'front.atyutyunnikov.docdoc.pro',
			'back'        => 'back.atyutyunnikov.docdoc.pro',
			'diagnostica' => 'diagnostica.atyutyunnikov.docdoc.pro',
			'static'      => 's.atyutyunnikov.docdoc.pro',
		],
		'php_user_ini' => [
			'xdebug.idekey' => 'atyutyunnikov',
		],
		'booking'                    => [
			'apiUrl'   => 'https://booking.atyutyunnikov.docdoc.pro/api/1.0/',
		],
	],
	'components' => [
		// База данных
		'db'  => [
			'connectionString' => 'mysql:host=localhost;dbname=docdoc_atyutyunnikov',
			'username'         => 'atyutyunnikov',
			'password'         => 'dghTfsdv',
		],
		'log' => [
			'class'  => 'CLogRouter',
			'routes' => [
				'web_log' => [
					'enabled' => false,
				],
			],
		],
	],
];
