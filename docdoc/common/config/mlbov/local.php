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
			'front'       => 'front.mlbov.docdoc.pro',
			'back'        => 'back.mlbov.docdoc.pro',
			'diagnostica' => 'diagnostica.mlbov.docdoc.pro',
			'static'      => 's.mlbov.docdoc.pro',
		],
		'php_user_ini' => [
			'xdebug.idekey' => 'mlbov',
		],
	),
	'components' => array(
		// База данных
		'db' => array(
			'connectionString' => 'mysql:host=localhost;dbname=docdoc_mlbov',
			'username'         => 'mlbov',
			'password'         => 'fghyGbzgf',
		),
	),
);
