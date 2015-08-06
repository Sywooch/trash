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
			'front'       => 'front.aponomarev.docdoc.pro',
			'back'        => 'back.aponomarev.docdoc.pro',
			'diagnostica' => 'diagnostica.aponomarev.docdoc.pro',
			'static'      => 's.aponomarev.docdoc.pro',
		],
		'php_user_ini' => [
			'xdebug.idekey' => 'aponomarev',
		],
	),
	'components' => array(
		// База данных
		'db' => array(
			'connectionString' => 'mysql:host=localhost;dbname=docdoc_aponomarev',
			'username'         => 'aponomarev',
			'password'         => 'ghBBMgyug',
		),
	),
);
