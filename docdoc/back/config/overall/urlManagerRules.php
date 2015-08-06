<?php
/**
 * Правила создания URL для Back
 */
return array(
	// backend rules
	'2.0'                                          => 'index/index',
	'2.0/<controller:\w+>/'                        => '<controller>/index',
	'2.0/<controller:\w+>/<action:\w+>/<id:[\d\w]+>' => '<controller>/<action>',
	'2.0/<controller:\w+>/<action:\w+>/*'          => '<controller>/<action>',
	'2.0/<controller:\w+>/*'                       => '<controller>/index',
);