<?php
return array(
	'components' => [
		'db' => [
			'connectionString' => 'mysql:host=localhost;dbname=likefifa_demo2',
			'username'         => 'likefifa_demo2',
			'password'         => 'gdyGHvGaNj',
		],
	],
	'params'     => array(
		'FSLockPrefix' => 'demo2-likefifa-docdoc-pro',
		"baseUrl"      => "http://demo2.likefifa.docdoc.pro/",
		'env'          => 'dev',
		'adminEmail'   => 'lf@docdoc.ru',
	),
);
