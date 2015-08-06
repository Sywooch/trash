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
			'connectionString' => 'mysql:host=localhost;dbname=docdoc_releaseunit_test',
		),
		'cache'   => array(
			'class' => 'CDummyCache',
		),
		'mixpanel' => array(
			//токен для и тестово проектов
			//'token' => '60317ed0401251d6ffc7f810faf9177f',
			//если token = null, в тестах не будет обращений к микспанели
			'token' => null,
		),
	),
);
