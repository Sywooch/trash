<?php
/**
 * Правила создания URL для диагностика
 */
return array(
	// backend rules
	'admin'                                          => 'admin/index/index',
	'admin/<controller:\w+>/'                        => 'admin/<controller>/index',
	'admin/<controller:\w+>/<action:\w+>/<id:\d+>/*' => 'admin/<controller>/<action>',
	'admin/<controller:\w+>/<action:\w+>/*'          => 'admin/<controller>/<action>',
	'admin/<controller:\w+>/*'                       => 'admin/<controller>/index',

	// frontend rules

	'login/*'                           => 'site/login',
	'changeCity'                        => 'site/changeCity',
	'robots.txt'                        => 'site/robots',
	'ajax/<action:\w+>/'                => 'diagnostics/ajax/<action>',
	'search/redirect'                   => 'diagnostics/search/redirect',
	'search/*'                          => 'diagnostics/search/custom',
	'library'                           => 'diagnostics/article/index',
	'library/<rewriteName:[\w_-]+>'     => 'diagnostics/article/view',
	'page/<pageName:\w+>'               => 'diagnostics/page/index',
	'popup/photo/<cid:\w+>'             => 'diagnostics/popup/photo',
	'appointment/<doctorId:\d+>/*'      => 'diagnostics/appointment/index',
	'appointment/form/<doctorId:\d+>/*' => 'diagnostics/appointment/form',
	'opinion/<doctorId:\d+>/*'          => 'diagnostics/opinion/index',
	'opinion/more/<doctorId:\d+>/*'     => 'diagnostics/opinion/more',
	'register'                          => 'diagnostics/register',
	'register/thanks'                   => 'diagnostics/register/thanks',
	'sitemap/'                          => 'diagnostics/sitemap/index',
	'sitemap/all'                       => 'diagnostics/sitemap/all',
	'sitemap/<id:\d+>'                  => 'diagnostics/sitemap/view',
	'diagnostici'                       => 'diagnostics/diagnostics',
	'kliniki/<rewriteName:[\w_-]+>/*'   => 'diagnostics/search/guess',
	'kliniki/'                          => 'diagnostics/search/custom',

	'request/<action:\w+>'              => 'diagnostics/request/<action>',
	'schedule/<action:\w+>'             => 'diagnostics/schedule/<action>',
	'diagnostics/<action:\w+>'          => 'diagnostics/diagnostics/<action>',

	'station/<rewriteNameStation:[\w_-]+>'   => 'diagnostics/search/index',
	'district/<rewriteNameDistrict:[\w_-]+>' => 'diagnostics/search/index',
	'city/<rewriteNameCity:[\w_-]+>'         => 'diagnostics/search/index',

	'online-zapis-so-skidkoy/<rewriteNameDiagnostic:[\w_-]+>/<rewriteName:(?!page)[\w_-]+>/*' => 'diagnostics/search/landing',
	'online-zapis-so-skidkoy/<rewriteNameDiagnostic:[\w_-]+>/*' => 'diagnostics/search/landing',

	'uzi/<rewriteNameDiagnostic:[\w_-]+>/area/<rewriteName:[\w_-]+>/<rewriteNameDistrict:[\w_-]+>/' => 'diagnostics'.'/search/area',
	'komputernaya-tomografiya/<rewriteNameDiagnostic:[\w_-]+>/area/<rewriteName:[\w_-]+>/<rewriteNameDistrict:[\w_-]+>/' => 'diagnostics'.'/search/area',
	'mrt/<rewriteNameDiagnostic:[\w_-]+>/area/<rewriteName:[\w_-]+>/<rewriteNameDistrict:[\w_-]+>/' => 'diagnostics'.'/search/area',
	'rentgen/<rewriteNameDiagnostic:[\w_-]+>/area/<rewriteName:[\w_-]+>/<rewriteNameDistrict:[\w_-]+>/' => 'diagnostics'.'/search/area',
	'dupleksnoe-skanirovanie/<rewriteNameDiagnostic:[\w_-]+>/area/<rewriteName:[\w_-]+>/<rewriteNameDistrict:[\w_-]+>/' => 'diagnostics'.'/search/area',
	'func-diagnostika/<rewriteNameDiagnostic:[\w_-]+>/area/<rewriteName:[\w_-]+>/<rewriteNameDistrict:[\w_-]+>/' => 'diagnostics'.'/search/area',
	'endoskopicheskie-issledovaniya/<rewriteNameDiagnostic:[\w_-]+>/area/<rewriteName:[\w_-]+>/<rewriteNameDistrict:[\w_-]+>/' => 'diagnostics'.'/search/area',
	'uzi-dlya-beremennih/<rewriteNameDiagnostic:[\w_-]+>/area/<rewriteName:[\w_-]+>/<rewriteNameDistrict:[\w_-]+>/' => 'diagnostics'.'/search/area',

	'<rewriteNameDiagnostic:^(?!popup)[\w_-]+>/area/<rewriteName:[\w_-]+>/<rewriteNameDistrict:[\w_-]+>/' => 'diagnostics'.'/search/area',
	'<rewriteNameDiagnostic:^(?!popup)[\w_-]+>/area/<rewriteName:[\w_-]+>/*' => 'diagnostics'.'/search/area',

	'uzi/<rewriteName:[\w_-]+>/*'                            => 'diagnostics/search/guess',
	'komputernaya-tomografiya/<rewriteName:[\w_-]+>/*'       => 'diagnostics/search/guess',
	'mrt/<rewriteName:[\w_-]+>/*'                            => 'diagnostics/search/guess',
	'rentgen/<rewriteName:[\w_-]+>/*'                        => 'diagnostics/search/guess',
	'dupleksnoe-skanirovanie/<rewriteName:[\w_-]+>/*'        => 'diagnostics/search/guess',
	'func-diagnostika/<rewriteName:[\w_-]+>/*'               => 'diagnostics/search/guess',
	'endoskopicheskie-issledovaniya/<rewriteName:[\w_-]+>/*' => 'diagnostics/search/guess',
	'uzi-dlya-beremennih/<rewriteName:[\w_-]+>/*'            => 'diagnostics/search/guess',
	'<rewriteName:^(?!popup)[\w_-]+>/*'                      => 'diagnostics/search/guess',

	// base rules
	'<controller:\w+>/'                      => 'diagnostics/<controller>',
	'<controller:\w+>/<id:\d+>'              => 'diagnostics/<controller>/view',
	'<controller:\w+>/<action:\w+>/<id:\d+>' => 'diagnostics/<controller>/<action>',
	'<controller:\w+>/<action:\w+>'          => 'diagnostics/<controller>/<action>',
);
