<?php
/**
 * Created by PhpStorm.
 * User: a.tyutyunnikov
 * Date: 17.06.14
 * Time: 12:14
 */

namespace dfs\tests\docdoc\components\seo\docdoc\sitemap;

use dfs\docdoc\components\seo\docdoc\sitemap\DefaultSeo;

/**
 * Class DefaultSeoTest
 *
 * @package dfs\tests\docdoc\components\seo\docdoc\sitemap
 */
class DefaultSeoTest extends \CDbTestCase {

	/**
	 * Тест дефолтного seo для страницы sitemap
	 * @dataProvider seoDataProvider
	 */
	public function testSeo(array $params)
	{
		$seo = new DefaultSeo();
		$seo->seoInfo();

		foreach($params as $method => $out){
			$this->assertEquals($seo->$method(), $out);
		}
	}

	/**
	 * Дата провайдер для testSeo
	 * @return array
	 */
	public function seoDataProvider()
	{
		return [
			[
				[
				'getTitle' => 'Карта сайта - DocDoc.ru',
				'getMetaKeywords' => '',
				'getMetaDescription' => '',
				'getHead' => '',
				'getSeoTexts' => [],
				]
			]
		];
	}
}
