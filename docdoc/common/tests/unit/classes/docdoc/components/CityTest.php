<?php

namespace dfs\tests\docdoc\components;

use CDbTestCase;

/**
 * Class PartnerTest
 *
 * @package dfs\tests\docdoc\components
 */
class CityTest extends CDbTestCase
{
	/**
	 * Тест инициализацию компонента City по домену
	 */
	public function testCityFromDomain()
	{
		$config = $this->initComponent();

		$_SERVER['HTTP_HOST'] = 'spb.docdoc.ru';

		$city = \Yii::createComponent($config['components']['city']);
		$city->init();
		$this->assertEquals(2, $city->getCityId());
	}

	/**
	 * Тест изменение города
	 */
	public function testChangeCity()
	{
		$config = $this->initComponent();

		$_SERVER['HTTP_HOST'] = 'spb.docdoc.ru';

		$city = \Yii::createComponent($config['components']['city']);
		$city->init();
		$city->getCity();

		$city->changeCity(1);
		$this->assertEquals(1, $city->getCityId());

		try {
			$city->changeCity(-1);
			$this->fail('Должно быть выброшено исключение о не верном cityId');
		}
		catch (\CException $e){
			$this->assertEquals("cityId unknown", $e->getMessage());
			$this->assertEquals(1, $city->getCityId());
		}
	}

	/**
	 * Тест определения города по домену
	 */
	public function testDetectCity()
	{
		$config = $this->initComponent();

		$_SERVER['HTTP_HOST'] = 'docdoc.ru';

		$city = \Yii::createComponent($config['components']['city']);
		$city->init();

		$_SERVER['HTTP_HOST'] = 'spb.docdoc.ru';
		$city->detect();
		$this->assertEquals(2, $city->getCityId());

		$_SERVER['HTTP_HOST'] = 'diagnostics.docdoc.ru';
		$city->detect();
		$this->assertEquals(1, $city->getCityId());
	}

	/**
	 * Инициализация компонентов и получение конфига для тестов
	 * @return array
	 */
	private function initComponent()
	{
		//убираем проверку первичных ключей
		$this->getFixtureManager()->checkIntegrity(false);

		$this->getFixtureManager()->truncateTable('city');
		$this->getFixtureManager()->loadFixture('city');

		$config = require ROOT_PATH . "/common/config/overall/common.php";

		if (!isset($config['components']['city'])) {
			$this->fail('Не найден конфиг для компонента');
		}

		return $config;
	}

}