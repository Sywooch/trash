<?php
return array(
	'components' => [
		'db' => [
			'connectionString' => 'mysql:host=localhost;dbname=mvasilyev_likefifa',
			'username'         => 'mv_likefifa',
			'password'         => 'gtGfzt1',
		],
	],
	'params'     => array(
		'FSLockPrefix' => 'likefifa-stage',
		// GA
		"gaAccount"    => "UA-37101956-2",
		/**
		 * Корневой URL
		 */
		"baseUrl"      => "http://mvasilyev.likefifa.docdoc.pro",
		'env'          => 'dev',
		'adminEmail'   => 'm.vasilyev@likefifa.ru',
	),

);
