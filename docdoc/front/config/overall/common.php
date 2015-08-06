<?php

/**
 * Yii Конфиг
 *
 * Поверх Front
 *
 * @see https://docdoc.atlassian.net/wiki/pages/viewpage.action?pageId=5373985#id-Концепцияновойархитектуры-Управлениеконфигурацией
 */
return array(
	'basePath' => ROOT_PATH . "/front",
	'params'   => array(
		'appName' => 'front',
		'lk' => array(
			// Показывать или нет CTR в личном кабинете клиники
			'CTREnabled' => false,
		),
		//имя сайта
		'siteName' => 'docdoc',
		//ID сайта. Для docdoc = 1
		'siteId' => '1',
		'phoneForMobile' => 74952230296,
		//показывать форму онлайн-записи
		'onlineBooking' => false,
		// Настройки для краткой анкеты врача
		'doctorCard' => [
			// Показывать название клиники
			'showClinicName'    => false,
			// Показывать номер телефона клиники
			'showClinicPhone'   => false,
		],
	),
);