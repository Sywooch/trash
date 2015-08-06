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
		'env'          => 'dev',
		'hosts'        => [
			'front'       => 'front.ndunaev.docdoc.pro',
			'back'        => 'back.ndunaev.docdoc.pro',
			'diagnostica' => 'diagnostica.ndunaev.docdoc.pro',
			'static'      => 's.ndunaev.docdoc.pro',
		],
		'booking'               => [
			'apiUrl'             => 'https://booking.ndunaev.docdoc.pro/api/1.0/',
		],
		'php_user_ini' => [
			'xdebug.idekey' => 'ndunaev',
		],
	),
	'components' => array(
		// База данных
		'db' => array(
			'connectionString' => 'mysql:host=localhost;dbname=docdoc_ndunaev',
			'username'         => 'ndunaev',
			'password'         => 'dgfv23Dczs2',
		),
	),
);
