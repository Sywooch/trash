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
			'front'       => 'front.demo4.docdoc.pro',
			'back'        => 'back.demo4.docdoc.pro',
			'diagnostica' => 'diagnostica.demo4.docdoc.pro',
			'static'      => 's.demo4.docdoc.pro',
		],
	),
	'components' => array(
		// База данных
		'db' => array(
			'connectionString' => 'mysql:host=localhost;dbname=docdoc_demo4',
			'username'         => 'demo4',
			'password'         => 'Yae0bewu',
		),
	),
);