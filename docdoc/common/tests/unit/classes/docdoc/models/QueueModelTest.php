<?php

namespace dfs\tests\docdoc\models;

use dfs\docdoc\models\QueueModel;
use CDbTestCase;
use Yii;

/**
 * Class QueueModelTest
 *
 * @package dfs\tests\docdoc\models
 */
class QueueModelTest extends CDbTestCase
{
	/**
	 * Тест на создание модели
	 */
	public function testCreateNewQueue()
	{
		$this->getFixtureManager()->checkIntegrity(false);
		$this->getFixtureManager()->truncateTable(QueueModel::model()->tableName());

		$queue = new QueueModel();
		$queue->SIP = 201;
		$queue->user_id = 2;
		$queue->status = QueueModel::STATUS_REGISTERED;
		if (!$queue->save()) {
			$message = array();
			foreach($queue->getErrors() as $att => $msg) {
				$message = sprintf("%s: %s\n", $att, join(', ', $msg));
			}
			$this->fail($message);
		}

		$this->assertEquals(1, $queue->count());
	}

	/**
	 * Проверка регистрации в очереди
	 */
	public function testRegisterInQueue()
	{
		$this->getFixtureManager()->checkIntegrity(false);
		$this->getFixtureManager()->truncateTable(QueueModel::model()->tableName());
		$this->getFixtureManager()->loadFixture('queue');

		// Новый оператор в очереди
		$queue = QueueModel::model()->register('testq', 201, 1);
		$this->assertNotNull($queue);

		// Попытка зарегистрироваться еще раз под другим SIP
		$queue = QueueModel::model()->register('callcenter', 202, 1);
		$this->assertNull($queue);

		// Попытка регистрации другого оператора с тем же SIP
		$queue = QueueModel::model()->register('callcenter', 201, 2);
		$this->assertNull($queue);
	}

	/**
	 * Проверка регистрации в очереди
	 */
	public function testUnregisterFromQueue()
	{
		// Выход оператора из очереди
		$queue = QueueModel::model()->findByPk(203);
		$queue->unregister();
		$this->assertNotNull($queue);
	}

}