<?php

return [
	'controllerNamespace' => '\dfs\docdoc\front\controllers\lk',
	'controllerPath'      => ROOT_PATH . '/common/classes/docdoc/front/controllers/lk',
	'viewPath'            => ROOT_PATH . '/front/views/lk',
	'components'          => [
		'user' => [
			'class'          => dfs\docdoc\front\components\ClinicUser::class,
			'loginUrl'       => '/lk/auth',
			'returnUrl'      => '/lk',
			'allowAutoLogin' => true,
			'stateKeyPrefix' => 'lk_',
		],
		'urlManager' => [
			'urlFormat'      => 'path',
			'showScriptName' => false,
			'rules'          => [
				''                       => 'site/index',
				'auth'                   => 'auth/auth',
				'recoveryPassword'       => 'auth/recoveryPassword',
				'service/login'          => 'auth/login',
				'service/logout'         => 'auth/logout',
				'service/recovery'       => 'auth/recovery',
				'settings'               => 'site/settings',
				'info'                   => 'site/info',
				'service/sendQuestion'   => 'site/sendQuestion',
				'service/changePassword' => 'site/changePassword',
				'service/changeClinic'   => 'site/changeClinic',
				'patients'               => 'requests/index/kind/doctor',
				'drequest'               => 'requests/index/kind/diagnostic',
			],
		],
		'mobileDetect' => [
			'rulesFile' => null,
		],
	],
	'params' => [
		'appName' => 'front/lk',
	]
];
