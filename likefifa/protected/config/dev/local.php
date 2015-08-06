<?php
return [
	'components'   => [
		'log' => [
			'routes' => [
				[
					'class'  => 'CFileLogRoute',
					'levels' => 'error, warning, info',
					'except' => 'exception.CHttpException.404',
				],
				[
					'class'         => 'CWebLogRoute',
					'showInFireBug' => true,
					'enabled'       => Yii::app() instanceof CWebApplication,
				],
			],
		],
	],
	'params' => [
		'FSLockPrefix' => 'likefifa-dev',
	],
];