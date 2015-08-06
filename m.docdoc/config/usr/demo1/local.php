<?php

return [
	'components' => [
		'cache' => [
			'class'    => 'CRedisCache',
			'hostname' => 'localhost',
			'database' => 12,
		],
	],
	'params'     => [
		'rest_api_domain' => 'back.demo1.docdoc.pro',
		'main_site'       => 'front.demo1.docdoc.pro',
	],
];
