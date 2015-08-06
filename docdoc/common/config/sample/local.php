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
			'front'       => 'front.sample.docdoc.pro',
			'back'        => 'back.sample.docdoc.pro',
			'diagnostica' => 'diagnostica.sample.docdoc.pro',
			'static'      => 's.sample.docdoc.pro',
		],
		'php_user_ini' => [
			'xdebug.idekey' => 'sample',
		],
	),
	'components' => array(
		// База данных
		'db' => array(
			'connectionString' => 'mysql:host=localhost;dbname=docdoc_sample',
			'username'         => 'sample',
			'password'         => 'asdTYbhjgjhjw',
		),
	),
);
