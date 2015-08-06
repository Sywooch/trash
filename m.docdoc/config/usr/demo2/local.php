<?php

return [
	'components' => [
		'cache' => [
			'class'    => 'CRedisCache',
			'hostname' => 'localhost',
			'database' => 13,
		],
	],
	'params'     => [
		'rest_api_domain' => 'back.demo2.docdoc.pro',
		'main_site'       => 'front.demo2.docdoc.pro',
	],
];
