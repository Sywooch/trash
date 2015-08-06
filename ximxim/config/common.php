<?php

ini_set('error_reporting', E_ALL);
ini_set('display_errors', 'On');

return [
	'db'         => [
		'host'     => 'localhost',
		'user'     => 'test',
		'password' => 'test',
		'name'     => 'testtask',
	],
	'rootDir'    => __DIR__ . DIRECTORY_SEPARATOR . '..',
	'urlManager' => [
		''                              => 'site/index',
		'topic/(\d+)'                   => 'topic/view',
		'topic/add'                     => 'topic/add',
		'topic/(\d+)/add'               => 'topic/add',
		'topic/(\d+)/edit'              => 'topic/edit',
		'topic/(\d+)/delete'            => 'topic/delete',
		'topic/(\d+)/news/(\d+)'        => 'news/view',
		'topic/(\d+)/news/add'          => 'news/add',
		'topic/(\d+)/news/(\d+)/edit'   => 'news/edit',
		'topic/(\d+)/news/(\d+)/delete' => 'news/delete',
		'comment/add'                   => 'comment/add',
		'comment/delete/(\d+)'          => 'comment/delete',
	]
];