<?php

namespace dfs\tests\docdoc\models;

use dfs\docdoc\models\SectorModel;
use CDbTestCase;
use Yii;
use PHPUnit_Framework_Constraint_IsType;

/**
 * Class SectorModelTest
 *
 * @package dfs\tests\docdoc\models
 */
class SectorModelTest extends CDbTestCase
{
	/**
	 * Тест на получение специальностей по станции метро
	 *
	 *  @dataProvider getItemsByStationProvider
	 *
	 */
	public function testGetItemsByStation($station, $count)
	{
		$this->loadFixtures();

		$items = SectorModel::getItemsByStation($station);
		$this->assertCount($count, $items);

		foreach($items as $item) {
			$this->assertInternalType(PHPUnit_Framework_Constraint_IsType::TYPE_STRING, $item['id']);
			$this->assertInternalType(PHPUnit_Framework_Constraint_IsType::TYPE_STRING, $item['name']);
			$this->assertInternalType(PHPUnit_Framework_Constraint_IsType::TYPE_STRING, $item['alias']);
		}

	}

	/**
	 * подготовка базы для тестов
	 */
	private function loadFixtures() {
		//убираем проверку первичных ключей
		$this->getFixtureManager()->checkIntegrity(false);
		$this->getFixtureManager()->truncateTable('doctor');
		$this->getFixtureManager()->truncateTable('clinic');
		$this->getFixtureManager()->truncateTable('sector');
		$this->getFixtureManager()->truncateTable('doctor_sector');
		$this->getFixtureManager()->truncateTable('doctor_4_clinic');
		$this->getFixtureManager()->truncateTable('underground_station_4_clinic');
		$this->getFixtureManager()->loadFixture('doctor');
		$this->getFixtureManager()->loadFixture('clinic');
		$this->getFixtureManager()->loadFixture('sector');
		$this->getFixtureManager()->loadFixture('doctor_sector');
		$this->getFixtureManager()->loadFixture('doctor_4_clinic');
		$this->getFixtureManager()->loadFixture('underground_station_4_clinic');
	}

	/**
	 * @return array
	 */
	public function getItemsByStationProvider()
	{
		return array(
			// Проверяем что на Авиамоторной есть акушеры
			array(1, 1),
			// Проверяем что на Арбатской нет никаких врачей
			array(8, 0),
		);
	}


	/**
	 * активные сектора в городе
	 */
	public function testActiveInCity()
	{
		$this->assertEquals(1, SectorModel::model()->active()->inCity(1)->count());
	}

}