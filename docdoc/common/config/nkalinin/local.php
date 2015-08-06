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
			'front'       => 'front.nkalinin.docdoc.pro',
			'back'        => 'back.nkalinin.docdoc.pro',
			'diagnostica' => 'diagnostica.nkalinin.docdoc.pro',
			'static'      => 's.nkalinin.docdoc.pro',
		],
		'php_user_ini' => [
			'xdebug.idekey' => 'nkalinin',
		],
	),
	'components' => array(
		// База данных
		'db' => array(
			'connectionString' => 'mysql:host=localhost;dbname=docdoc_nkalinin',
			'username'         => 'nkalinin',
			'password'         => 'bGHb12HGhjbn',
		),
	),
);
