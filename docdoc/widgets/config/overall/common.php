<?php
return [
	'basePath' => ROOT_PATH . "/widgets",
	'controllerNamespace' => '\dfs\docdoc\widgets\controllers',
	'params' => [
		'appName' => 'widgets',
		'availableComponents' => [
			'redis',
			'log',
			'newRelic',
			'gaClient',
			'city',
			'db'
		]
	],
];