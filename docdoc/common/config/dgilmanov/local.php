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
			'front'       => 'front.dgilmanov.docdoc.pro',
			'back'        => 'back.dgilmanov.docdoc.pro',
			'diagnostica' => 'diagnostica.dgilmanov.docdoc.pro',
			'static'      => 's.dgilmanov.docdoc.pro',
		],
		'php_user_ini' => [
			'xdebug.idekey' => 'dgilmanov',
		],
	),
	'components' => array(
		// База данных
		'db' => array(
			'connectionString' => 'mysql:host=localhost;dbname=docdoc_dgilmanov',
			'username'         => 'dgilmanov',
			'password'         => 'cgTgdVBg23W',
		),
	),
);
