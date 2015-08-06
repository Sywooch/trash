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
			'front'       => 'front.snovikov.docdoc.pro',
			'back'        => 'back.snovikov.docdoc.pro',
			'diagnostica' => 'diagnostica.snovikov.docdoc.pro',
			'static'      => 's.snovikov.docdoc.pro',
		],
	),
	'components' => array(
		// База данных
		'db' => array(
			'connectionString' => 'mysql:host=localhost;dbname=docdoc_snovikov',
			'username'         => 'snovikov',
			'password'         => 'fDDcce231asA',
		),
	),
);