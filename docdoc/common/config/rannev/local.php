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
		'env'       => 'dev',

		'hosts' => [
			'front'       => 'front.rannev.docdoc.pro',
			'back'        => 'back.rannev.docdoc.pro',
			'diagnostica' => 'diagnostica.rannev.docdoc.pro',
			'static'      => 's.rannev.docdoc.pro',
		],
	),
	'components' => array(
		// База данных
		'db' => array(
			'connectionString' => 'mysql:host=localhost;dbname=docdoc_rannev',
			'username'         => 'rannev',
			'password'         => 'hcgbGt12AzA',
		),
	),
);