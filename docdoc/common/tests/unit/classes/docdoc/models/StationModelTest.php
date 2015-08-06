<?php

namespace dfs\tests\docdoc\models;

use dfs\docdoc\models\ClinicModel;
use dfs\docdoc\models\StationModel;
use CDbTestCase;
use Yii;

/**
 * Class StationModelTest
 *
 * @package dfs\tests\docdoc\models
 */
class StationModelTest extends CDbTestCase
{

	/**
	 * Проверяем поиск по алиасу
	 *
	 * @throws \CException
	 */
	public function testSearchByAlias()
	{
		$this->loadFixtures();

		// Поверяем поиск станции по алиасу
		$model = StationModel::model()->searchByAlias('aviamotornaya')->find();
		$this->assertNotNull($model);
		$this->assertEquals(1, $model->id);
	}

	/**
	 * Проверяем поиск по району
	 *
	 * @throws \CException
	 */
	public function testSearchByDistricts()
	{
		$this->loadFixtures();
		$this->getFixtureManager()->truncateTable('district');
		$this->getFixtureManager()->truncateTable('district_has_underground_station');
		$this->getFixtureManager()->loadFixture('district');
		$this->getFixtureManager()->loadFixture('district_has_underground_station');

		// Поверяем поиск станции по району
		$stations = StationModel::model()->searchByDistricts(array(1))->findAll();
		$this->assertCount(1, $stations);
	}

	/**
	 * Проверяем поиск по городу
	 *
	 * @throws \CException
	 */
	public function testSearchByRegCity()
	{
		$this->loadFixtures();
		$this->getFixtureManager()->truncateTable('reg_city');
		$this->getFixtureManager()->truncateTable('underground_station_4_reg_city');
		$this->getFixtureManager()->loadFixture('reg_city');
		$this->getFixtureManager()->loadFixture('underground_station_4_reg_city');

		$stations = StationModel::model()->searchByRegCity(1)->findAll();
		$this->assertCount(1, $stations);
	}

	/**
	 * Подготовка данных
	 *
	 * @throws \CException
	 */
	private function loadFixtures()
	{
		$this->getFixtureManager()->checkIntegrity(false);
		$this->getFixtureManager()->truncateTable(StationModel::model()->tableName());
		$this->getFixtureManager()->loadFixture(StationModel::model()->tableName());
	}

	/**
	 * Проверка расчета расстояния до клиники
	 */
	public function testGetDistanceToClinic()
	{
		$this->loadFixtures();
		$this->getFixtureManager()->truncateTable('clinic');
		$this->getFixtureManager()->truncateTable('underground_station');
		$this->getFixtureManager()->loadFixture('clinic');
		$this->getFixtureManager()->loadFixture('underground_station');

		$clinic = ClinicModel::model()->findByPk(1); // Клиника в Люблино
		$station = StationModel::model()->findByPk(1); // Авиамоторная

		$this->assertEquals(9000, $station->calcDistanceToClinic($clinic));
	}

}