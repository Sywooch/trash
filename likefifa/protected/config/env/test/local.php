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
		'db' => [
			'connectionString'   => 'mysql:host=localhost;dbname=likefifa_test',
			'username'           => 'likefifa_test',
			'password'           => 'jhskdfsdukhj',
			'enableProfiling'    => true,
			'enableParamLogging' => true,
		],
	],
	'params'     => array(
		'FSLockPrefix' => 'likefifa-test',
		'env'          => 'dev',
		'adminEmail'   => 'lnghost@hotmail.com',
	),
);
