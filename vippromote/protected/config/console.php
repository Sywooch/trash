<?php

//require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'settings.php';

// This is the configuration for yiic console application.
// Any writable CConsoleApplication properties can be configured here.
$return = array(
	'basePath'   => dirname(__FILE__) . DIRECTORY_SEPARATOR . '..',
	'name'       => 'DocDoc - Cron',
	'import'     => array(
		'application.components.*',
		'application.models.*',
	),
	// application components
	'components' => array(
		'db' => array(
			'connectionString' => 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME,
			'emulatePrepare'   => true,
			'username'         => DB_USERNAME,
			'password'         => DB_PASSWORD,
			'charset'          => 'utf8',
		),
	),
);

return $return;