<?php
/**
 * Правила редиректа на мобильный сайт
 */
return array(
	'search/stations/<stations:[\d,]+>*'        => '/search/stations/<stations>',
	'doctor/<name:[\w_-]+>/city/<city:[\w_-]+>*' => 'not_redirect',
	'doctor/<q:.*>'                             => '/doctor/<q>',
	'doctor'                                    => '/doctor',
	'context/<q:.*>'                            => '/context/<q>',
	'context'                                   => '/context',
	'landing/<q:.*>'                            => '/landing/<q>',
	'landing'                                   => '/landing',
	'request'                                   => '/request',
	'/' 										=> '/',
	'<all>*' 									=> 'not_redirect',
);
