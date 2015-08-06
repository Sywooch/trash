<?php
namespace dfs\tests\docdoc\components\seo;


use dfs\docdoc\components\seo\SeoFactory,
	Yii;


class SeoFactoryTest extends \CDbTestCase
{

	/**
	 * проверка, что будет брощено исключение при вызове несуществующего seo-класса
	 *
	 * @expectedException \dfs\docdoc\components\seo\SeoException
	 */
	function testClassNotFound()
	{
		Yii::app()->params->siteName = 'docdoc';
		Yii::app()->params->siteId = 1;

		SeoFactory::getSeo('notExistedController', '', '/');
	}


	/**
	 * Диагностика. Проверка работы дефолтных классов
	 */
	function testDefaultSeo()
	{
		Yii::app()->params->siteName = 'diagnostica';
		Yii::app()->params->siteId = 2;


		$this->assertInstanceOf(
			'dfs\docdoc\components\seo\diagnostica\sitemapcontroller\DefaultSeo',
			SeoFactory::getSeo('sitemapcontroller', 'notExistedAction', '/')
		);
	}

	/**
	 * Диагностика. Проверка работы существующего класса
	 */
	function testExistsSeoClass()
	{
		Yii::app()->params->siteName = 'diagnostica';
		Yii::app()->params->siteId = 2;

		$this->assertInstanceOf(
			'dfs\docdoc\components\seo\diagnostica\sitemapcontroller\ViewSeo',
			SeoFactory::getSeo('sitemapcontroller', 'view', '/')
		);
	}

	/**
	 * DocDoc. Проверка работы существующего класса
	 */
	function testExistsDocDocSeoClass()
	{
		Yii::app()->params->siteName = 'docdoc';
		Yii::app()->params->siteId = 1;

		$this->assertInstanceOf(
			'dfs\docdoc\components\seo\docdoc\index\DefaultSeo',
			SeoFactory::getSeo('index', '', '/')
		);
	}


	/**
	 * Проверка переопределения SEO информации из таблицы page
	 */
	function testPageSeoClass()
	{
		//убираем проверку первичных ключей
		$this->getFixtureManager()->checkIntegrity(false);
		$this->getFixtureManager()->truncateTable('page');
		$this->getFixtureManager()->loadFixture('page');

		Yii::app()->params->siteName = 'diagnostica';
		Yii::app()->params->siteId = 2;


		//должны получить запись для всех городов
		$seo = SeoFactory::getSeo('sitemap', 'view', '/test/url', 2);
		$this->assertInstanceOf(
			'dfs\docdoc\components\seo\PageSeo',
			$seo
		);
		$seo->seoInfo();
		$this->assertEquals($seo->getHead(), 'диагностика, для всех городов');


		//проверяем для docdoc'a
		Yii::app()->params->siteName = 'docdoc';
		Yii::app()->params->siteId = 1;

		//должны получить запись для москвы
		$seo = SeoFactory::getSeo('doctorview', '', '/test/url', 1);
		$this->assertInstanceOf(
			'dfs\docdoc\components\seo\PageSeo',
			$seo
		);
		$seo->seoInfo();
		$this->assertEquals($seo->getHead(), 'docdoc, для москвы');
	}


}