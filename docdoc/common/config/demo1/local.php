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
			'front'       => 'front.demo1.docdoc.pro',
			'back'        => 'back.demo1.docdoc.pro',
			'diagnostica' => 'diagnostica.demo1.docdoc.pro',
			'static'      => 's.demo1.docdoc.pro',
		],
	),
	'components' => array(
		// База данных
		'db' => array(
			'connectionString' => 'mysql:host=localhost;dbname=docdoc_demo1',
			'username'         => 'demo1',
			'password'         => 'HbfgT23Gbz',
		),
	),
);