<?php

/**
 * Yii Конфиг
 *
 * Поверх всех проектов
 * Для Cli Окружения
 *
 * @see https://docdoc.atlassian.net/wiki/pages/viewpage.action?pageId=5373985#id-Концепцияновойархитектуры-Управлениеконфигурацией
 */
return [
	'basePath'   => ROOT_PATH . "/common",
	'params'     => [
		'php_user_ini' => [
			'error_reporting'        => 'E_ALL',
			'display_errors'         => 'On',
			'display_startup_errors' => 'On',
			'html_errors'            => 'Off',
		],
		'logger' => [
			'autoFlush' => 1,
			'autoDump'  => true,
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
					'class'  => 'dfs\docdoc\extensions\ConsoleLogRoute',
					'levels' => 'error, warning, info',
				],

			],
		],
	],
	'commandMap'=> [
		'clearcache' => [
			'class' =>'dfs\common\commands\ClearCacheCommand',
		],
	],
	'onRunCommand' => ['dfs\docdoc\components\CommandHandler', 'onRunCommand'],
];
