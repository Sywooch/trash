<?php

namespace dfs\tests\docdoc\models;

use dfs\docdoc\models\TrafficParamsModel;
use CDbTestCase;
use Yii;

/**
 * Class TrafficParamsModelTest
 *
 * @package dfs\tests\docdoc\models
 */
class TrafficParamsModelTest extends CDbTestCase
{
	/**
	 * Проверка добавления параметров
	 *
	 * @param array $params
	 * @param int $id
	 * @param int $type
	 * @param int $count
	 * @dataProvider saveByParamsProvider
	 */
	public function testSaveByParams(array $params, $id, $type, $count)
	{
		$this->getFixtureManager()->checkIntegrity(false);
		$this->getFixtureManager()->truncateTable('traffic_params');
		$this->getFixtureManager()->truncateTable('traffic_params_dict');
		$this->getFixtureManager()->loadFixture('traffic_params');
		$this->getFixtureManager()->loadFixture('traffic_params_dict');

		TrafficParamsModel::model()->saveByParams($params, $id, $type);

		$this->assertEquals($count, TrafficParamsModel::model()->byType($type)->count());
	}

	/**
	 * @return array
	 */
	public function saveByParamsProvider()
	{
		return [
			[
				['pid' => 34, 'referrer' => 'test'],
				1,
				TrafficParamsModel::OBJECT_REQUEST,
				3
			],
			[
				['pid' => 34, 'referrer' => 'test', 'utm_source' => 'test'],
				10,
				TrafficParamsModel::OBJECT_CLIENT,
				3
			],
		];
	}
}