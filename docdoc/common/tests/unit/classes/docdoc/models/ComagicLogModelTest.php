<?php
/**
 * Created by PhpStorm.
 * User: a.tyutyunnikov
 * Date: 01.08.14
 * Time: 10:58
 */

namespace dfs\tests\docdoc\models;

use dfs\docdoc\models\ComagicLogModel;
use dfs\docdoc\models\RequestModel;

class ComagicLogModelTest extends \CDbTestCase
{
	/**
	 * @var bool
	 */
	static $setUped = false;

	/**
	 * Настройка фикстур
	 */
	public function setUp()
	{
		if (!self::$setUped) {
			$this->getFixtureManager()->checkIntegrity(false);
			$this->getFixtureManager()->truncateTable('comagic_log');
			$this->getFixtureManager()->loadFixture('comagic_log');

			$this->getFixtureManager()->truncateTable('request');
			$this->getFixtureManager()->loadFixture('request');

			$this->getFixtureManager()->truncateTable('booking');
			$this->getFixtureManager()->loadFixture('booking');

			self::$setUped = true;
		}
	}

	/**
	 * Проверяет уникальность из двух колонок
	 */
	public function testMultiUniqueRule()
	{
		$log = ComagicLogModel::model()->findByPk(1);
		$anotherLog = new ComagicLogModel('log_collector');
		$anotherLog->attributes = $log->attributes;
		$anotherLog->save();

		$this->assertEquals(1, count($anotherLog->getErrors()));
	}

	/**
	 * Тест поиска заявки
	 *
	 * @param int      $logId
	 * @param \Closure $checkFunction
	 *
	 * @dataProvider findRequestDataProvider
	 */
	public function testFindRequest($logId, \Closure $checkFunction)
	{
		$log = ComagicLogModel::model()->findByPk($logId);
		$log->saveRequest();

		$checkFunction($log);
	}

	/**
	 * Провайдер для теста заявки
	 *
	 * @return array
	 */
	public function findRequestDataProvider()
	{
		return [
			[
				//тут есть заявка
				2,
				function (ComagicLogModel $log) {
					$this->assertNotNull($log->request_id);
					$this->assertNotNull($log->checked_time);
				}
			],
			[
				//тут нет заявки
				1,
				function (ComagicLogModel $log) {
					$this->assertNull($log->request_id);
					$this->assertNotNull($log->checked_time);
				}
			],
			[
				//тут нет заявки, потому что номер не определился
				3,
				function (ComagicLogModel $log) {
					$this->assertNull($log->request_id);
					$this->assertNotNull($log->checked_time);
				}
			],
		];
	}

	/**
	 * Проверяю что за пределами дельты времения заявка не находится
	 */
	public function testDeltaTolerance()
	{
		$request = RequestModel::model()->findByPk(1);
		$l = ComagicLogModel::model()->findByPk(1);

		$log = new ComagicLogModel('log_collector');
		$log->attributes = $l->attributes;
		$log->call_date = date('Y-m-d H:i:s', $request->req_created);
		$log->numa = $request->client_phone;
		$log->request_id = null;
		$log->checked_time = null;
		$log->saveRequest();

		$this->assertEquals($log->request_id, $request->req_id);
		$this->assertNotNull($log->checked_time);

		$log2 = new ComagicLogModel('log_collector');
		$log2->attributes = $log->attributes;
		$log2->call_date = date('Y-m-d H:i:s', $request->req_created + ComagicLogModel::DELTA_TOLERANCE + 1);
		$log2->numa = $request->client_phone;
		$log2->request_id = null;
		$log2->checked_time = null;
		$log2->saveRequest();

		$this->assertNull($log2->request_id);
		$this->assertNotNull($log->checked_time);
	}

}
