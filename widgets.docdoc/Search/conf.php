<?php
/**
 * Created by PhpStorm.
 * User: ndunaev
 * Date: 02.12.14
 * Time: 18:12
 */

return [
	'Search'               => [
		'css' => ["themes/rs/common_css"],
	],
	'SearchClinic_240x400' => [
		'css'        => ["core_css", "Search/Search_css"],
		'js'         => null,
		'attributes' => [
			'specName'   => 'spec_name',
			'searchType' => 'clinic',
		]
	],
	'SearchDoctor_240x400' => [
		'css'        => ["Search/SearchDoctor_240x400_css"],
		'js'         => null,
	],
	'SearchDoctor_vertical' => [
		'css'        => ["Search/Search_vertical_css"],
		'js'         => null,
	],
	'SearchClinic_vertical' => [
		'css'        => ["Search/Search_vertical_css"],
		'js'         => null,
		'attributes' => [
			'specName'   => 'spec_name',
			'searchType' => 'clinic',
		]
	],
];