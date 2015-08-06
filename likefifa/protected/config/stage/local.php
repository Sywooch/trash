<?php
return [
	'modules'    => [
		'payments' => [
			'isActive' => true,
		],
	],
	'components' => [
		'cache' => [
			'class' => defined('YII_DEBUG') && YII_DEBUG && empty($_GET["noc"]) ? 'CDummyCache' : 'CRedisCache',
		],
		'db'    => [
			'connectionString' => 'mysql:host=localhost;dbname=likefifa_stage',
			'username'         => 'likefifa_stage',
			'password'         => 'g({9N}R?qP',
		],
	],
	'params'     => [
		'FSLockPrefix' => 'likefifa-stage',
		// GA
		"gaAccount"    => "UA-37101956-2",
		/**
		 * Корневой URL
		 */
		"baseUrl"      => "http://stage.likefifa.ru",
		"baseUrlMO"    => "http://mo.stage.likefifa.ru",
		'env'       => 'stage',
		// Тестовые почтовые адреса
		"devEmails" => [
			"aparshukov@docdoc.ru",
			"dangil87@gmail.com",
			"denikeev@docdoc.ru",
			"dkolbasin@docdoc.ru",
			"elenkachrabrova@yandex.ru",
			"esamoilenko@docdoc.ru",
			"ibogomolov@docdoc.ru",
			"jmayorova@docdoc.ru",
			"korotin@sysntec.ru",
			"kruelladevil@yandex.ru",
			"kuzmin@sysntec.ru",
			"leon.k87@gmail.com",
			"loktev@sysntec.ru",
			"mazarov@smart-media.ru",
			"mogenstern@gmail.com",
			"m.vasilyev@likefifa.ru",
			"naumovets@ya.ru",
			"nshelest@docdoc.ru",
			"o.moskalenko@likefifa.ru",
			"opervuninskaya@docdoc.ru",
			"petrd@advsmart.ru",
			"rannev@sysntec.ru",
			"rantonov@docdoc.ru",
			"revas@docdoc.ru",
			"spa79@bk.ru",
			"support@docdoc.ru",
			"tgorodnicheva@docdoc.ru",
			"moyregion@inbox.ru",
			'lnghost@hotmail.com', // Eugene Podosenov
		],
		// Телефоны разработчиков. в девеломерском окружении только они получают уведомления
		'devPhones' => [
			'79163559375',
			'79035643002',
			'79150099181', // Михаил
			'79639243365',
			'79263638978',
			'79670369294',
			'79269304796',
			'79166731448',
			'79151472431',
			'79032777106',
			'79175435685',
			'79266025266',
			'79168422507',
			'79036704567',
			'79105174817',
			'79154736453',
			'79269097741',
			'79175404406',
			'79151474580',
			'79167509307',
			'79168133534',
			'79654036994',
			'79258999268',
			'79688030964', // кира, тестовый номер
			'79053286769', // Сергей Новиков
			'79192338919', // Евгений Подосенов
		],
	],
];
