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
			'front'       => 'front.aghostuhin.docdoc.pro',
			'back'        => 'back.aghostuhin.docdoc.pro',
			'diagnostica' => 'diagnostica.aghostuhin.docdoc.pro',
			'static'      => 's.aghostuhin.docdoc.pro',
		],
		'php_user_ini' => [
			'xdebug.idekey' => 'aghostuhin',
		],
	),
	'components' => array(
		// База данных
		'db' => array(
			'connectionString' => 'mysql:host=localhost;dbname=docdoc_aghostuhin',
			'username'         => 'aghostuhin',
			'password'         => 'Neno2aeg',
		),
		'referral' => [
			'runABTest' => true,
		],
	),
);
