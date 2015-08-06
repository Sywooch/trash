<?php
return [
	'name'     => 'ConsoleApplication',
	'commandMap'=> [
		'ClearCache' => [
			'class' =>'dfs\common\commands\ClearCacheCommand',
		],
	],
	'components' => [
		'log' => [
			'class'  => 'CLogRouter',
			'routes' => [
				'main'   => [
					'class'  => 'CFileLogRoute',
					'levels' => 'error, warning, info',
				],
				'stdout' => [
					'class'  => 'dfs\common\extensions\log\StdOutLogRoute',
					'levels' => 'error, warning, info',
				],

			],
		],
	],
];