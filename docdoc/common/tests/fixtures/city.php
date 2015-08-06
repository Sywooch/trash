<?php

/**
 * Fixture для CityModel
 *
 * @author  Mikhail Vasilyev <m.vasilyev@likefifa.ru>
 * @link    https://docdoc.megaplan.ru/task/1003803/card/
 * @package dfs.tests.fictures
 */

return array(
	'moscow' => array(
		'id_city'             => 1,
		'title'               => 'Москва',
		'rewrite_name'        => 'msk',
		'long'                => null,
		'lat'                 => null,
		'prefix'              => '',
		'is_active'           => 1,
		'title_genitive'      => 'Москвы',
		'title_prepositional' => 'Москве',
		'has_diagnostic'      => 1,
		'has_mobile'          => 1,
		'search_type'         => 3,
		'site_phone'          => '74952367276',
		'site_office'         => '74955653293',
		'opinion_phone'       => '74952367271',
		'site_YA'             => '11631337',
		'gtm'                 => 'gtm1',
		'diagnostic_gtm'      => 'diagnostic_gtm1'
	),
	'spb'    => array(
		'id_city'             => 2,
		'title'               => 'Санкт-Петербург',
		'rewrite_name'        => 'spb',
		'long'                => null,
		'lat'                 => null,
		'prefix'              => 'spb.',
		'is_active'           => 1,
		'title_genitive'      => 'Санкт-Петербурга',
		'title_prepositional' => 'Санкт-Петербурге',
		'has_diagnostic'      => 1,
		'has_mobile'          => 0,
		'search_type'         => 2,
		'site_phone'          => '78123856652',
		'site_office'         => '78123856652',
		'opinion_phone'       => '78123856651',
		'site_YA'             => '19018384',
		'gtm'                 => 'gtm2',
		'diagnostic_gtm'      => 'diagnostic_gtm2'
	),
	'other'  => array(
		'id_city'             => 3,
		'title'               => 'Другие города',
		'rewrite_name'        => 'default',
		'long'                => null,
		'lat'                 => null,
		'prefix'              => '',
		'is_active'           => 0,
		'title_genitive'      => '',
		'title_prepositional' => '',
		'has_diagnostic'      => 0,
		'has_mobile'          => 0,
		'search_type'         => 0,
	),
	'klg'    => [
		'id_city'             => 4,
		'title'               => 'Калуга',
		'rewrite_name'        => 'klg',
		'long'                => null,
		'lat'                 => null,
		'prefix'              => 'klg.',
		'is_active'           => 1,
		'title_genitive'      => 'Калуги',
		'title_prepositional' => 'Калуге',
		'has_diagnostic'      => 1,
		'has_mobile'          => 0,
		'search_type'         => 2,
		'site_phone'          => '78001112233',
		'site_office'         => '78002223344',
		'opinion_phone'       => '78003334455',
		'site_YA'             => '19018384',
		'site_GA'             => 'UA-7682182-17'
	],
);
