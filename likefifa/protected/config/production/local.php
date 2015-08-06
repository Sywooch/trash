<?php
return array(
	'modules'    => array(
		'payments' => array(
			'isActive' => true,
		),
	),
	'components' => array(
		'db'    => [
			'connectionString' => 'mysql:host=localhost;dbname=likefifa_ru',
			'username'         => 'likefifa',
			'password'         => 'ddhgyGbnav',
		],
	),
	'params'     => array(
		'devPhones'    => false, // For erery one
		'FSLockPrefix' => 'likefifa-production',
		'env'          => 'production',
	),
);
