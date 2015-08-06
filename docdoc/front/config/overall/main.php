<?php
/**
 * Yii Конфиг
 *
 * Front
 * Для Web Окружения
 *
 * @see https://docdoc.atlassian.net/wiki/pages/viewpage.action?pageId=5373985#id-Концепцияновойархитектуры-Управлениеконфигурацией
 */
return array(
	// preloading 'referral' component
	'preload'=>array('referral','city','mobileDetect', 'rating', 'trafficSource'),
	'controllerNamespace' => '\dfs\docdoc\front\controllers',
	'controllerPath'      => ROOT_PATH . "/common/classes/docdoc/front/controllers",
	'components'=>array(
		'errorHandler' => array(
			'errorAction' => 'site/error',
		),
		//компонент, учитывающий партнерскую ссылку
		'referral'=>array(
			'class' => dfs\docdoc\components\Partner::class,
			//хранить в $_SESSION['ReferralObj']
			'sessParam' => 'ReferralObj',
			//брать из $_GET['pid']
			'getParam' => 'pid',
			'runABTest' => false,
		),
		'mobileDetect' => array(
			'class'  => dfs\docdoc\components\MobileDetect::class,
			'rulesFile' => ROOT_PATH . "/common/config/overall/urlMobileManagerRules.php",
			'mobileDetectKeys' => ['Phone'],
		),
		'whiteLabel' => [
			'class' => dfs\docdoc\components\WhiteLabel::class,
		],
		'city' => [
			'autodetect' => true,
		],
		'urlManager' => [
			'urlFormat'      => 'path',
			'showScriptName' => false,
			'rules'          => [
				'clinic/moreReviews'                               => 'clinic/moreReviews',
				'clinic/moreDoctors'                               => 'clinic/moreDoctors',
				'clinic/order/<values:.+>'                         => 'clinic/index/order/<values>',
				'clinic/page/<values:.+>'                          => 'clinic/index/page/<values>',
				'clinic/spec/<spec:[^\/]+>/area/<area:[^\/]+>/<values:.+>' => 'clinic/index/spec/<spec>/area/<area>/district/<values>',
				'clinic/spec/<spec:[^\/]+>/<type:(order|page|city|district|area)>/<values:.+>'  => 'clinic/index/spec/<spec>/<type>/<values>',
				'clinic/spec/<spec:[^\/]+>/<values:.+>'            => 'clinic/index/spec/<spec>/station/<values>',
				'clinic/area/<area:[^\/]+>/<values:.+>'            => 'clinic/index/area/<area>/district/<values>',
				'clinic/<type:(spec|station|street|district|area)>/<values:.+>'  => 'clinic/index/<type>/<values>',
				'clinic/print/<alias:.*>'                          => 'clinic/print',
				'clinic/<alias:.*>'                                => 'clinic/show',
				'illness/alphabet/<letter:.*>'                     => 'illness/alphabet',
				'illness/<spec:[^\/]+>/<alias:.+>'                 => 'illness/show',
				'illness/<alias:.*>'                               => 'illness/show',
			],
		],
	),
);
