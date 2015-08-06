<?php

namespace dfs\tests\docdoc\models;

use dfs\docdoc\models\RegCityModel;
use CDbTestCase;
use Yii;

/**
 * Class RegCityModelTest
 *
 * @package dfs\tests\docdoc\models
 */
class RegCityModelTest extends CDbTestCase
{

	/**
	 * Проверяем поиск по алиасу
	 *
	 * @throws \CException
	 */
	public function testSearchByAlias()
	{
		$this->getFixtureManager()->checkIntegrity(false);
		$this->getFixtureManager()->truncateTable(RegCityModel::model()->tableName());
		$this->getFixtureManager()->loadFixture(RegCityModel::model()->tableName());

		// Поверяем что нашелся город
		$model = RegCityModel::model()->searchByAlias('korolev')->find();
		$this->assertNotNull($model);
		$this->assertEquals(4, $model->id);
	}

}