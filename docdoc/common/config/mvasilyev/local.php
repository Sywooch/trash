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
			'front'       => 'front.mvasilyev.docdoc.pro',
			'back'        => 'back.mvasilyev.docdoc.pro',
			'diagnostica' => 'diagnostica.mvasilyev.docdoc.pro',
			'static'      => 's.mvasilyev.docdoc.pro',
		],
	),
	'components' => array(
		// База данных
		'db' => array(
			'connectionString' => 'mysql:host=localhost;dbname=docdoc_mvasilyev',
			'username'         => 'mvasilyev',
			'password'         => 'WmJh51DfzcR13',
		),
	),
);
