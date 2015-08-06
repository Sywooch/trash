<?php
/**
 * Yii Конфиг
 *
 * Back
 * Для Web Окружения
 *
 * @see https://docdoc.atlassian.net/wiki/pages/viewpage.action?pageId=5373985#id-Концепцияновойархитектуры-Управлениеконфигурацией
 */
return array(
	'sourceLanguage'=>'en_US',
	'controllerNamespace' => '\dfs\docdoc\back\controllers',
	'controllerPath'      => ROOT_PATH . "/common/classes/docdoc/back/controllers",
	'components'          => array(
		'urlManager' => array(
			'urlFormat'      => 'path',
			'showScriptName' => false,
			'rules'          => require(__DIR__ . '/urlManagerRules.php'),
		),
		//компонент для работы с городом
		'city'=>array(
			'class'   => 'dfs\docdoc\components\City',
			//по-умолчанию Москва
			'id_city' => 1,
		),
		//компонент, учитывающий партнерскую ссылку
		'referral'=>array(
			'class'     => 'dfs\docdoc\components\Partner',
			//хранить в $_SESSION['ReferralObj']
			'sessParam' => 'ReferralObj',
			//брать из $_GET['pid']
			'getParam'  => 'pid',
		),
		'trafficSource' => [
			'cookieParam'   => null,
		],
	),
);