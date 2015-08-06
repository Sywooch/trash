<?php

return [
	'components' => [
		'cache' => [
			'class'    => 'CRedisCache',
			'hostname' => 'localhost',
			'database' => 14,
		],
	],
	'params'     => [
		'rest_api_domain' => 'back.demo3.docdoc.pro',
		'main_site'       => 'front.demo3.docdoc.pro',
	],
];
