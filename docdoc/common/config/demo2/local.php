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
			'front'       => 'front.demo2.docdoc.pro',
			'back'        => 'back.demo2.docdoc.pro',
			'diagnostica' => 'diagnostica.demo2.docdoc.pro',
			'static'      => 's.demo2.docdoc.pro',
		],
	),
	'components' => array(
		// База данных
		'db' => array(
			'connectionString' => 'mysql:host=localhost;dbname=docdoc_demo2',
			'username'         => 'demo2',
			'password'         => 'cbGhdn12Ws',
		),
	),
);