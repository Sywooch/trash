<?php

return [
	'debug'  => false,
	'components' => [
		'mixpanel' => [
			'class' => 'dfs\components\MixpanelComponent',
			//токен для боя
			'token' => '5c27e016728157987589f02f592d1096',
		],
		'cache' => [
			'class' => 'CRedisCache',
			'hostname' => 'sel-web4.docdoc.pro',
			'database' => 1,
		],
	],
	'params' => [
		'env' => 'production',

		'rest_api_domain' => 'back.docdoc.ru',

		'main_site' => 'docdoc.ru',
		'ga-universal-id' => 'UA-7682182-11',
		'gtm-id' => 'GTM-5TKX3Z',
	],
];
