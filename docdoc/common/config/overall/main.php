<?php

/**
 * Yii Конфиг
 *
 * Поверх всех проектов
 * ДЛя Web Окружения
 *
 * @see https://docdoc.atlassian.net/wiki/pages/viewpage.action?pageId=5373985#id-Концепцияновойархитектуры-Управлениеконфигурацией
 */
return [
	'params' => [
		'allowOnlineBooking' => false,
		'supportedBrowsers' => [
			\Browser\Browser::CHROME => 35,
			\Browser\Browser::OPERA => 12,
			\Browser\Browser::FIREFOX => 30,
			\Browser\Browser::IE => 9,
		]
	]
];
