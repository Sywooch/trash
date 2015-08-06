<?php
/**
 * Created by PhpStorm.
 * User: ndunaev
 * Date: 11.06.14
 * Time: 18:53
 */

namespace dfs\tests\docdoc\components\seo\docdoc\index;

use dfs\docdoc\components\seo\docdoc\index\DefaultSeo;
use dfs\docdoc\models\CityModel;

class DefaultSeoTest extends \CDbTestCase
{
	/**
	 * Тест seo-информации
	 *
	 * @dataProvider seoDataProvider
	 *
	 */
	public function testSeo($params, $expect)
	{
		$seo = new DefaultSeo();

		foreach ($params as $k=>$v) {
			$seo->addParam($k, $v);
		}
		$seo->seoInfo();

		foreach ($expect as $method => $result) {
			$this->assertEquals($seo->$method(), $result);
		}
		
	}

	/**
	 * Дата-провайдер для testSeo
	 *
	 * @return array
	 */
	public function seoDataProvider()
	{
		//загрузка фикстур
		$this->getFixtureManager()->checkIntegrity(false);
		$this->getFixtureManager()->truncateTable('city');
		$this->getFixtureManager()->loadFixture('city');

		return array(
			array(
				array(
					'city' => CityModel::model()->findByPk(1)->getAttributes(),
					'page' => array(),
				),
				array(
					'getTitle' => 'DocDoc - поиск врачей в Москве',
					'getMetaKeywords' => 'врач, врачи Москвы, найти врача, поиск врачей',
					'getMetaDescription' => 'DocDoc.ru – сервис по поиску врачей',
					'getHead' => '',
					'getSeoTexts' => array(),
				)
			),
			array(
				array(
					'city' => CityModel::model()->findByPk(2)->getAttributes(),
					'page' => array(),
				),
				array(
					'getTitle' => 'DocDoc - поиск врачей в Санкт-Петербурге',
					'getMetaKeywords' => 'врач, врачи Санкт-Петербурга, найти врача, поиск врачей',
					'getMetaDescription' => 'DocDoc.ru – сервис по поиску врачей',
					'getHead' => '',
					'getSeoTexts' => array(),
				)
			),
		);
	}


} 