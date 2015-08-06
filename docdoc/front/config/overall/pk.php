<?php

return [
	'controllerNamespace' => '\dfs\docdoc\front\controllers\pk',
	'controllerPath'      => ROOT_PATH . '/common/classes/docdoc/front/controllers/pk',
	'viewPath'            => ROOT_PATH . '/front/views/pk',
	'components'          => [
		'user' => [
			'class'          => dfs\docdoc\front\components\PartnerUser::class,
			'loginUrl'       => '/pk/auth',
			'returnUrl'      => '/pk/patients',
			'allowAutoLogin' => true,
			'stateKeyPrefix' => 'pk_',
		],
		'urlManager' => [
			'urlFormat'      => 'path',
			'showScriptName' => false,
			'rules'          => [
				''                       => 'patients/index',
				'auth'                   => 'auth/auth',
				'recoveryPassword'       => 'auth/recoveryPassword',
				'service/login'          => 'auth/login',
				'service/logout'         => 'auth/logout',
				'service/recovery'       => 'auth/recovery',
				'settings'               => 'site/settings',
				'info'                   => 'site/info',
				'acceptOffer'            => 'site/acceptOffer',
				'service/changePassword' => 'site/changePassword',
				'service/acceptOffer'    => 'site/acceptOfferApply',
				'service/sendQuestion'   => 'site/sendQuestion',
			],
		],
		'mobileDetect' => [
			'rulesFile' => null,
		],
	],
	'params' => [
		'appName' => 'front/pk',
	]
];
