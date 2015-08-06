<?php
return array(
	'modules'    => array(
		'gii' => array(
			'class'          => 'system.gii.GiiModule',
			'password'       => 'q123456',
			'ipFilters'      => false,
			'generatorPaths' => array('application.gii'),
		),
	),
	'components' => [
		'db'    => [
			'connectionString'   => 'mysql:host=localhost;dbname=epodosenov_likefifa',
			'username'           => 'epodosenov',
			'password'           => 'dGhdbagTTYH11',
			'enableProfiling'    => true,
			'enableParamLogging' => true,
		],
		'eauth' => [
			'services' => [
				'vkontakte' => [
					'client_id'     => 4495416,
					'client_secret' => 'h4Q1cAREibxpzehaB4nz',
				]
			]
		]
	],
	'params'     => array(
		'FSLockPrefix' => 'likefifa-epodosenov',
		'env'          => 'dev',
		'adminEmail'   => 'lnghost@hotmail.com',
		'vk'           => [
			'apiId'     => 4495416,
			'apiSecret' => 'h4Q1cAREibxpzehaB4nz',
		],
	),
);
