<?php

namespace dfs\tests\docdoc\objects\sypexgeo;

use dfs\docdoc\objects\sypexgeo\SxGeoCity;

class SxGeoCityTest extends \CTestCase
{
	/**
	 * Проверка сервиса на таймаут
	 */
	public function testTimeout()
	{
		// 81.222.128.0 Питер
		$sxGeo = new SxGeoCity('81.222.128.0');

		$sxGeo->setTimeout(1);
		$this->assertEquals(SxGeoCity::DEFAULT_CITY, $sxGeo->getCity());
	}

	/**
	 * Проверка обработки битого ответа
	 */
	public function testIncorrectResponse()
	{
		$sxGeo = new SxGeoCity('127.0.0.1');
		$this->assertEquals(SxGeoCity::DEFAULT_CITY, $sxGeo->getCity());

		$sxGeo = new SxGeoCity('127.0.0.1');
		$sxGeo->setUrl('http://docdoc.ru');
		$this->assertEquals(SxGeoCity::DEFAULT_CITY, $sxGeo->getCity());

		$sxGeo = new SxGeoCity('127.0.0.1');
		$sxGeo->setUrl('/response.json');
		$this->assertEquals(SxGeoCity::DEFAULT_CITY, $sxGeo->getCity());
	}

}
