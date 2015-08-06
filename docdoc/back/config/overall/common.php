<?php
/**
 * Yii Конфиг
 *
 * Поверх Back
 *
 * @see https://docdoc.atlassian.net/wiki/pages/viewpage.action?pageId=5373985#id-Концепцияновойархитектуры-Управлениеконфигурацией
 */
return [
	'basePath' => ROOT_PATH . "/back",
	'params'   => [
		'appName' => 'back',
		'php_user_ini' => [
			'upload_max_filesize' => '64M',
			'post_max_size'       => '64M',
		],
	],
	'components' => [
		'user'=> [
			'loginUrl'=> ['index.php'],
			'stateKeyPrefix' => 'back_',
		],
	],
];
