<?php
return [
	// Алиас для отладки миграций внутри платёжного модуля
	'commandMap' => [
		'migrate' => array(
			'class' => 'likefifa\components\console\MigrateCommand',
			'schemaFilePath' => 'application.data'
		),
		'migrate_payments' => [
			'class'         => 'system.cli.commands.MigrateCommand',
			'migrationPath' => 'dfs.modules.payments.migrations',
		],
		'smsSender'        => [
			'class' => 'dfs\modules\sms\commands\SmsSenderCommand',
		],
		'clearCache'        => [
			'class' => 'dfs\common\commands\ClearCacheCommand',
		],
		'mailerStatus'        => [
			'class' => 'likefifa\components\extensions\MailerHistory\MailerStatusCommand',
		]
	],
];