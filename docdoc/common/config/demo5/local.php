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
			'front'       => 'front.demo5.docdoc.pro',
			'back'        => 'back.demo5.docdoc.pro',
			'diagnostica' => 'diagnostica.demo5.docdoc.pro',
			'static'      => 's.demo5.docdoc.pro',
		],
	),
	'components' => array(
		// База данных
		'db' => array(
			'connectionString' => 'mysql:host=localhost;dbname=docdoc_demo5',
			'username'         => 'demo5',
			'password'         => 'uquoh3Ph',
		),
	),
);