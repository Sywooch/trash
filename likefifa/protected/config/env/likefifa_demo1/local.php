<?php
return array(
	'components' => [
		'db' => [
			'connectionString' => 'mysql:host=localhost;dbname=likefifa_demo1',
			'username'         => 'likefifa_demo1',
			'password'         => 'gdyGHvGaNj',
		],
	],
	'params'     => array(
		'adminEmail'   => 'lf@docdoc.ru',
		'FSLockPrefix' => 'demo1-likefifa-docdoc-pro',
		"baseUrl"      => "http://demo1.likefifa.docdoc.pro/",
		'env'          => 'dev',
	),
);
