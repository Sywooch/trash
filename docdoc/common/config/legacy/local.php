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
		'env'        => 'stage',
		'booking'    => array(
			'apiUrl'   => 'https://booking.stage.docdoc.pro/api/1.0/',
			'password' => 'test-partner',
			'login'    => 'docdoc_test654',
		),
		'ya-metrika-id'   => '',
		'ya-metrika-id-diagnostic'   => '15482359',
		'asterisk'   => array(
			'context' => 'test-outgoing',
		),
		'hosts'      => [
			'front'       => 'front.legacy.docdoc.pro',
			'back'        => 'back.legacy.docdoc.pro',
			'diagnostica' => 'diagnostica.legacy.docdoc.pro',
			'static'      => 's.legacy.docdoc.pro',
		],
	),
	'components' => array(
		'cache'    => array(
			'hostname' => 'localhost',
			'database' => 10,
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
		'log' => [
			'class'  => 'CLogRouter',
			'routes' => [
				'db_errors_to_mail' => [
					'subject' => 'DocDoc - SQL error [Stage]',
				]
			]
		]
	),
);
