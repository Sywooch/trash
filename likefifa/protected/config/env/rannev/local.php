<?php
return array(
	'modules'    => array(
		'gii'      => array(
			'class'          => 'system.gii.GiiModule',
			'password'       => 'q123456',
			'ipFilters'      => false,
			'generatorPaths' => array('application.gii'),
		),
	),
	'components' => [
		'db'    => [
			'connectionString' => 'mysql:host=localhost;dbname=rannev_likefifa',
			'username'         => 'rannev',
			'password'         => 'hcgbGt12AzA',
		],
	],
	'params'     => array(
		'FSLockPrefix' => 'likefifa-rannev',
		'env'          => 'dev',
		'adminEmail'   => 'lnghost@hotmail.com',
	),
);
