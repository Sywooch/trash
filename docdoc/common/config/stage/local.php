<?php
/**
 * Yii Конфиг
 *
 * Stage.
 * Самый высокий приоритет.
 *
 * @see https://docdoc.atlassian.net/wiki/pages/viewpage.action?pageId=5373985#id-Концепцияновойархитектуры-Управлениеконфигурацией
 */
return array(
	'params'     => array(
		'env'                        => 'stage',
		'booking'                    => array(
			'apiUrl'   => 'https://booking.stage.docdoc.pro/api/1.0/',
			'password' => 'test-partner',
			'login'    => 'docdoc_test654',
		),
		'ya-metrika-id'              => '',
		'ya-metrika-id-diagnostic'   => '15482359',
		'asterisk'                   => array(
			'context' => 'test-outgoing',
			'db_servers' => [
				'sel-ast3' => [
					'connectionString' => 'mysql:host=sel-ast3.docdoc.pro;dbname=asterisk_docdoc_stage',
				],
				'sel-ast18' => [
					'connectionString' => 'mysql:host=sel-ast18.docdoc.pro;dbname=asterisk_docdoc_stage',
				],
				'ihc-ast-web1' => [
					'connectionString' => 'mysql:host=ihc-ast-web1.docdoc.pro;dbname=asterisk_docdoc_stage',
				],
				'flops-ast4' => [
					'connectionString' => 'mysql:host=flops-ast4.docdoc.pro;dbname=asterisk_docdoc_stage',
				]
			]
		),
		'hosts'                      => [
			'front'       => 'front.stage.docdoc.pro',
			'back'        => 'back.stage.docdoc.pro',
			'diagnostica' => 'diagnostica.stage.docdoc.pro',
			'static'      => 's.stage.docdoc.pro',
		],
		'php_user_ini'               => [
			'session.save_handler'     => 'redis',
			'session.save_path'        => '"tcp://127.0.0.1:6380?weight=1&timeout=1&database=0"',
			'newrelic.appname'         => 'Stage.App [Stage]',
		],
		'google_big_query' => [
			'env' => 'stage',
		],
		'antispamEnabled' => false,
	),
	'components' => array(
		'cache'    => array(
			'hostname' => 'localhost',
			'database' => 10,
		),
		'redis'    => array(
			'hostname' => 'localhost',
			'database' => 0,
		),
		// База данных
		'db'       => array(
			'connectionString' => 'mysql:host=localhost;dbname=docdoc_stage',
			'username'         => 'docdoc_stage',
			'password'         => 'hgtyb122s',
		),
		'mixpanel' => array(
			//токен для stage и девелоперских проектов
			'token' => 'fb7f1db4eb67de3f8c26e7bdee574e97',
		),
		'log'      => [
			'class'  => 'CLogRouter',
			'routes' => [
				'db_errors_to_mail' => [
					'subject' => 'DocDoc - SQL error [Stage]',
				]
			]
		]
	),
);
