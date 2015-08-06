<?php

return [
	'import'     => [
		'system.test.*',
	],
	'components' => [
		'fixture' => [
			'class' => 'system.test.CDbFixtureManager',
		],
		'db'      => [
			'emulatePrepare' => true,
			'charset'        => 'utf8',
		],
	],
	'modules'    => [
		'payments' => [
			'onInvoiceClose' => null,
		],
	],
];