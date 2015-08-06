<?php
/**
 * Yii Конфиг
 *
 * Тестовый для вей команды
 *
 * @see https://docdoc.atlassian.net/wiki/pages/viewpage.action?pageId=5373985#id-Концепцияновойархитектуры-Управлениеконфигурацией
 */
return array(
	'params'     => array(
		'env'     => 'dev',
		'hosts'   => [
			'front'       => 'front.releaseunit.docdoc.pro',
			'back'        => 'back.releaseunit.docdoc.pro',
			'diagnostica' => 'diagnostica.releaseunit.docdoc.pro',
			'static'      => 's.releaseunit.docdoc.pro',
		],
		'booking' => array(
			'apiUrl'   => 'https://booking.stage.docdoc.pro/api/1.0/',
			'password' => 'test-partner',
			'login'    => 'docdoc_test654',
			//на сколько дней вперед загружать расписание
			'loadDays' => 5,
		),
		'logger' => [
			'autoDump'  => false,
		],
	),
	'components' => array(
		// Доступы к базе данных
		'db'       => array(
			'connectionString' => 'mysql:host=localhost;dbname=docdoc_releaseunit',
			'username'         => 'releaseunit',
			'password'         => 'hjUkjknBB',
		),
		'fixture'  => array(
			'class' => 'system.test.CDbFixtureManager',
		),
		'mixpanel' => array(
			//токен для тестового проекта
			'token' => '60317ed0401251d6ffc7f810faf9177f',
		),
		'log'      => [
			'class'  => 'CLogRouter',
			'routes' => [
				'db_errors_to_mail' => [
					'subject' => 'DocDoc - SQL error [Test]',
				]
			]
		],
		'session' => [
			'class' => \dfs\docdoc\components\HttpSession::class,
		]
	),
	'import'     => array(
		'system.test.*',
	),
);
