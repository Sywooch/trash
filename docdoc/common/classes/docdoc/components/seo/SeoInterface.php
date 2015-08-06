<?php

namespace dfs\docdoc\components\seo;



/**
 * Interface SeoInterface
 *
 * @package dfs\docdoc\front\components\seo
 */
interface SeoInterface {

	/**
	 * позиция дополнительного текста в seo блоках
	 */
	const SEO_TEXT_TOP = 1;
	const SEO_TEXT_BOTTOM = 2;

	/**
	 * создание Seo-информации
	 */
	public function seoInfo();

} 