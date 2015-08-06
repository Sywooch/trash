<?php
/**
 * Yii Конфиг
 *
 * Локально, тестовый
 * Выше чем самый локальный
 *
 * @see https://docdoc.atlassian.net/wiki/pages/viewpage.action?pageId=5373985#id-Концепцияновойархитектуры-Управлениеконфигурацией
 */
return array(
	'components' => array(
		// База данных
		'db' => array(
			'connectionString' => 'mysql:host=localhost;dbname=docdoc_demo2_test',
		),
	),
);