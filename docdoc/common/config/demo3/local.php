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
		'env' => 'dev',

		'hosts' => [
			'front'       => 'front.demo3.docdoc.pro',
			'back'        => 'back.demo3.docdoc.pro',
			'diagnostica' => 'diagnostica.demo3.docdoc.pro',
			'static'      => 's.demo3.docdoc.pro',
		],
	),
	'components' => array(
		// База данных
		'db' => array(
			'connectionString' => 'mysql:host=localhost;dbname=docdoc_demo3',
			'username'         => 'demo3',
			'password'         => 'fhGtxcn12G',
		),
	),
);