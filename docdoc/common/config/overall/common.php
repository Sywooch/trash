<?php

/**
 * Yii Конфиг
 *
 * Поверх всех проектов
 *
 * @see https://docdoc.atlassian.net/wiki/pages/viewpage.action?pageId=5373985#id-Концепцияновойархитектуры-Управлениеконфигурацией
 */
return [
	// preloading 'log' component
	'preload'    => ['log'],
	'language'   => 'ru',
	'aliases'    => [
		'vendor' => realpath(__DIR__ . '/../../vendor'),
	],
	'params'     => [
		'RequestProcessingTimeLimit'       => 1200, // 20 minutes
		'DOnlineClinicNotifyInterval'      => 600, //через какое время напомнить клинике о заявке на диагн онлайн
		'DOnlineClinicNotifyDuration'      => 300, //сколько времени пытаться дозвониться в клинику
		'comagic'                          => [
			'url'         => 'http://api.comagic.ru',
			'login'       => 'avasilenko@smart-media.ru',
			'password'    => 'xifTKa5j',
			'customer_id' => 2476,
		],
		'lk_master_password'               => 'ivwV1ie8vO2HbRP9OoHRJ',
		'pk_master_password'               => 'ivwV1ie8vO2HbRP9OoHRJ',
		'email'                            => [
			'content'          => 'content@docdoc.ru',
			'account'          => 'account@docdoc.ru',
			'clinic-registr'   => 'clinic-registr@docdoc.ru',
			'sale'             => 'sale@docdoc.ru',
			'support'          => 'support@docdoc.ru',
			'public'           => 'info@docdoc.ru',
			'partner'          => 'partner@docdoc.ru',
			'analytics'        => 'krasnostup@gmail.com',
			'from'             => 'noreply@docdoc.ru',
			'affiliate'        => 'affiliate@docdoc.ru',
			'generalManager'   => 'petrd@docdoc.ru',
			'executiveManager' => 'omoskalenko@docdoc.ru',
			'projectManager'   => 'mfisenko@docdoc.ru',
			'seo'              => 'rantonov@docdoc.ru',
			'development'      => 'aparshukov@docdoc.ru',
			'bookkeeping'      => 'bookkeeping@docdoc.ru',
		],
		'commonPhone'                      => "8 (800) 775-36-27",
		'centralOffice'                    => [
			"address"   => "м. Автозаводская, ул. Ленинская Слобода, д. 26",
			"longitude" => 37.654286,
			"latitude"  => 55.709992,
		],
		'press'                            => [
			'email' => 'press@docdoc.ru',
			'phone' => '+7 (495) 565-32-93'
		],
		'asterisk'                         => [
			'host'           => 'ast.docdoc.pro',
			'port'           => 65529,
			'context'        => 'outgoing',
			'connectTimeout' => 3,
			'logFile'        => 'asterisk.log',
			'api'            => [
				'login'    => 'asterisk',
				'password' => '4cFzJaKuR36n',
				'logFile'  => 'asterisk_api.log',
			],
		],
		'storageRecords'                   => [
			'connectString' => 'ftp://29291:kMevCqS6Bg@ftp.selcdn.ru:21',
			'login'         => '29291',
			'password'      => 'kMevCqS6Bg',
			'url'           => 'ftp.selcdn.ru',
		],
		'siteList'                         => [
			1 => "docdoc",
			2 => "diagnostica",
		],
		//коэффициент, на который умножать статистику docdoc'a в API и на сайте
		'DocDocStatisticFactor'            => 1,
		//параметры интеграционного шлюза к клиникам
		'booking'                          => [
			'apiUrl'             => 'https://booking.docdoc.pro/api/1.0/',
			'password'           => 'test-partner',
			'login'              => 'docdoc_test654',
			//на сколько дней вперед загружать расписание
			'loadDays'           => 30,
			'testClinic'         => [
				'positive' => 13
			],
		],
		'phone_providers'                  => [
			'download_dir' => '/var/www/records',
			'config'       => [
				'main'       => [
					//основной акаунт
					'id'           => 10,
					'class'        => \dfs\docdoc\objects\call\Uiscom::class,
					'name'         => 'loaderWAVrecords', //имя бывшего крона
					'local_path'   => "/diagnostica",
					'storage_path' => '/diagnostica_calls',
					'ftp'          => [
						'url'         => 'ftp.universe.uiscom.ru',
						'login'       => 'svbmLV4dym',
						'password'    => 'YNSyIfFKPolhsIX8Uo6N',
						'remote_path' => '/centrex/global_conversation',
					],
					'http'         => [
						'url'      => 'http://universe.uiscom.ru',
						'login'    => 'UIS011227',
						'password' => '1039874mama'
					],
				],
				'spb'        => [
					//дополнительный акаунт (точно не знаю что там)
					'id'           => 12,
					'class'        => \dfs\docdoc\objects\call\Uiscom::class,
					'name'         => 'loaderWAVrecordsSpb', //имя бывшего крона
					'local_path'   => "/spb",
					'storage_path' => '/spb',
					'ftp'          => [
						'url'         => 'ftp.spb.uiscom.ru',
						'login'       => '3856652',
						'password'    => 'SULmnsiKogCg8ZONzRDa',
						'remote_path' => '/centrex/global_conversation',
					],
					'http'         => [
						'url'      => 'http://spb.uiscom.ru',
						'login'    => '3856652',
						'password' => '747798'
					],
				],
				//астериски
				'flops-ast4'        => [
					'class'        => \dfs\docdoc\objects\call\Asterisk::class,
					'id'           => 13,
					'name'         => 'flops-ast4',
					'local_path'   => '/flops-ast4',
					'storage_path' => '/flops-ast4',
					'ftp'          => [
						'url'         => 'flops-ast4.docdoc.pro',
						'login'       => 'ast_mp3',
						'password'    => 'waeCh4ea',
						'remote_path' => '/records',
					],
				],
			],
		],
		'hosts'                            => [
			'front'       => 'docdoc.ru',
			'back'        => 'back.docdoc.ru',
			'diagnostica' => 'diagnostica.docdoc.ru',
			'static'      => 's.docdoc.ru',
		],
		'path'                             => [
			'upload' => '/var/www/Images',
		],
		'browserstack'                     => [
			'user'  => 'docdoc',
			'key'   => 'G88YCshCppT5GSsGD49B',
			'debug' => false,
		],
		'sypexgeo' => [
			'key' => '82xqC',
		],
		'records_upload_dir'               => ROOT_PATH . '/front/public/upload/records',
		'logger'                           => [
			'forceTrace' => [
				'system.db.CDbCommand' => ['error', 'warning'],
			]
		],
		'clinicsForLanding'                => [
			/* СМ-Клиника */
			46, 1939, 1861, 775, 351, 241, 240, 238, 237, 236, 235,
			/* Он клиник */
			13, 234, 233, 232, 231, 230,
			/* Евромед */
			2, 264, 265, 266, 267,
			/* КЛИНИКА+31 */
			748, 2402, 2403,
			/* Семейная */
			308, 309, 310, 311, 312, 881, 1682,
			/* Медицина */
			1293,
			/* Добромед */
			1592, 1594, 1595, 1596, 1597, 1598, 1599, 1600, 1601, 1602, 1603, 1605, 1606, 1607, 1608, 2564,
			/* ABC медицина */
			1930, 2056, 2057, 2579,
			/* Семейный доктор №1 */
			2208, 2210, 2211, 2212, 2213, 2214, 2215, 2216, 2217, 2218, 2219, 2220, 2221, 3247,
		],
		'api'                              => [
			'rest' => [
				'strategy' => [
					\dfs\docdoc\models\RatingStrategyModel::FOR_DOCTOR => 1,
					\dfs\docdoc\models\RatingStrategyModel::FOR_CLINIC => 1,
				]
			]
		],
		'2gis'                             => [
			'reward' => [
				'msk'   => 400,
				'spb'   => 250,
				'other' => 50
			]
		],
		'google_big_query'                 => [
			'project'       => 'docdoc-01',
			'client_id'     => '1065853214498-rvrrpcantfp6qqesb2uc0tkqf5f66a16.apps.googleusercontent.com',
			'client_secret' => 'amjjgYl0APuCRDkgbgwwlto5',
			'refresh_token' => '1/TqAiWUUfOa0iO6Zoi8E4YSQYOQzZbXBHUVjaIyJpbaF90RDknAdJa_sgfheVM0XT',
			'env' => '',
		],
		'antispamEnabled' => true,
		'doctorScheduleEnabled' => true,
	],
	'components' => [
		'cache'           => [
			'class'    => 'CRedisCache',
			'hostname' => 'localhost',
			'database' => 0,
		],
		'redis'           => [
			'class'    => '\ARedisConnection',
			'hostname' => 'localhost',
			"database" => 0,
			'port' => 6382,
		],
		// База данных
		'db'              => [
			'connectionString'      => 'mysql:host=localhost;dbname=docdoc_docdoc',
			'emulatePrepare'        => true,
			'username'              => 'username',
			'password'              => 'password',
			'charset'               => 'utf8',
			'schemaCachingDuration' => 3600,
		],
		'session' => [
			'class' => \dfs\docdoc\components\HttpSession::class,
		],
		'log'             => [
			'class'  => 'CLogRouter',
			'routes' => [
				'main'              => [
					'class'  => 'CFileLogRoute',
					'levels' => 'error, warning, info',
					'except' => [
						'exception*',
						'system.db.CDbCommand',
						'php',
						'sms.requests',
					],
				],
				'php_errors'        => [
					'class'      => 'CFileLogRoute',
					'levels'     => 'error, warning',
					'categories' => ['php', 'exception*'],
					'except' => [
						'exception.CDbException',
						'exception.CHttpException.404',
					],
					'logFile'    => 'php_errors.log',
				],
				'db_errors_to_file' => [
					'class'      => 'CFileLogRoute',
					'levels'     => 'error, warning',
					'categories' => 'system.db.CDbCommand',
					'logFile'    => 'sql_error.log',
				],
				'db_errors_to_mail' => [
					'class'      => 'CEmailLogRoute',
					'levels'     => 'error, warning',
					'categories' => 'system.db.CDbCommand',
					'emails'     => 'support@docdoc.ru',
					'subject'    => 'DocDoc - SQL error [Production]',
					'sentfrom'   => 'noreply@docdoc.ru',
					'filter'     => [
						'class'         => 'CLogFilter',
						'prefixSession' => true,
						'prefixUser'    => true,
						'logUser'       => true,
						// по умолчанию '_GET','_POST','_FILES', '_COOKIE','_SESSION','_SERVER'
						'logVars'       => ['_REQUEST', '_SERVER', '_SESSION', '_COOKIE', '_FILES'],
					],
				],
				'sms_for_requests'  => [
					'class'      => 'CFileLogRoute',
					'categories' => 'sms.requests',
					'logFile'    => 'sms_for_requests.log',
				],
			],
		],
		//диспетчер событий
		'eventDispatcher' => [
			'class'  => 'dfs\docdoc\components\EventDispatcher',
			'events' => [
				'onRequestCreated'      => [
					'dfs\docdoc\components\TrafficSourceEvent' => 'requestCreated',
				],
				'onRequestSave'         => [
					'dfs\docdoc\models\RequestHistoryModel' => 'saveLog',
					'dfs\docdoc\models\MailQueryModel'      => 'onChangeRequestMail',
				],
				'onRequestStatusChange' => [
					'dfs\docdoc\models\RequestHistoryModel' => 'saveChangeStatusLog',
					'dfs\docdoc\models\SmsRequestModel'     => 'requestStatusChanged',
					'dfs\docdoc\components\MixPanelEvent'   => 'requestStatusChanged',
				],
				'onRequestKindChange'   => [
					'dfs\docdoc\components\MixPanelEvent' => 'requestStatusChanged',
				],
				'onDateAdmissionChange' => [
					'dfs\docdoc\models\SmsRequestModel' => 'requestDateAdmissionChanged',
				],
				'onRejectReasonChange'  => [
					'dfs\docdoc\models\SmsRequestModel' => 'requestRejectReasonChanged',
				],
				'onClientCreated'       => [
					'dfs\docdoc\components\TrafficSourceEvent' => 'clientCreated',
				],
				'onPhoneChangeAfterValidate' => [
					\dfs\docdoc\models\PhoneModel::class => 'validatePhoneForObject',
				],
				'onPhoneChangeAfterSave' => [
					\dfs\docdoc\models\PhoneModel::class => 'unbindPhoneFromObject',
				]
			],
		],
		'city'            => [
			'class'      => 'dfs\docdoc\components\City',
			//по-умолчанию Москва
			'id_city'    => 1,
			'autodetect' => false,
		],
		'mixpanel'        => [
			'class' => 'dfs\docdoc\components\MixpanelComponent',
			//токен для боя
			'token' => '5c27e016728157987589f02f592d1096',
		],
		'yandexGeoApi'    => [
			'class' => 'dfs\docdoc\components\YandexGeoApi',
		],
		'rating'          => [
			'class' => 'dfs\docdoc\components\RatingComponent',
		],
		'sms'             => [
			'class'    => \dfs\smsc\SmsC::class,
			'login'    => 'medservices',  // логин клиента
			'password' => 'Fgvb12GGGG123', // пароль или MD5-хеш пароля в нижнем регистре
			'post'     => true, // использовать метод POST
			'https'    => true,    // использовать HTTPS протокол
			'charset'  => 'utf-8',   // кодировка сообщения: utf-8, koi8-r или windows-1251 (по умолчанию)
			'debug'    => false,    // флаг отладки
			'sender'   => 'DocDoc' // Имя отправителя
		],
		'newRelic'        => [
			'class' => 'vendor.gtcode.yii-newrelic.YiiNewRelic',
		],
		'mailer'          => [
			'class'    => 'vendor.janisto.yii-mailer.SwiftMailerComponent',
			'type'     => 'mail',
			'host'     => 'mail.smart-media.ru',
			'port'     => 25,
			'username' => 'noreply@docdoc.ru',
			'password' => 'uErwdR5aMS',
			'security' => 'tls',
			'throttle' => 5 * 60,
		],
		'trafficSource'   => [
			'class'       => \dfs\docdoc\components\TrafficSourceComponent::class,
			'cookieParam' => 'traffic_source',
		],
		'gaClient' => array(
			'class'  => dfs\docdoc\components\GaClient::class,
		),
	],
];
