<?php
return array(
	'modules'    => array(
		'gii'      => array(
			'class'          => 'system.gii.GiiModule',
			'password'       => 'q123456',
			// If removed, Gii defaults to localhost only. Edit carefully to taste.
			'ipFilters'      => array('127.0.0.1', '::1', '217.76.34.46'),
			'ipFilters'      => false,
			'generatorPaths' => array('application.gii'),
		),
	),
	'components' => [
		'db'    => [
			'connectionString' => 'mysql:host=localhost;dbname=aparshukov_likefifa',
			'username'         => 'aparshukov',
			'password'         => 'cgTgdVBg',
		],
	],
	'params'     => array(
		// 'devPhones'    => false, // For erery one
		'FSLockPrefix' => 'likefifa-aparshukov',
		'env'          => 'dev',
	),
);
