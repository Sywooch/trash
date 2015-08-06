<?php

namespace dfs\tests\docdoc\models;

use dfs\docdoc\models\RequestModel;
use dfs\docdoc\models\RequestRecordModel;
use CDbTestCase;
use dfs\docdoc\objects\call\ProviderInterface;

/**
 * Class RequestRecordModelTest
 *
 * @package dfs\tests\docdoc\models
 */
class RequestRecordModelTest extends CDbTestCase
{
	/**
	 * выполнять при запуске каждого теста
	 */
	public function setUp()
	{
		$this->getFixtureManager()->checkIntegrity(false);
		$this->getFixtureManager()->truncateTable('request');
		$this->getFixtureManager()->truncateTable('request_history');
		$this->getFixtureManager()->truncateTable('request_record');
		$this->getFixtureManager()->loadFixture('request_record');
		$this->getFixtureManager()->loadFixture('request');
	}

	/**
	 * Проверка изменения статуса isAppointment
	 *
	 * @dataProvider dataSaveAppointment
	 *
	 * @param int      $request_id
	 * @param int      $record_id
	 * @param string   $isAppointment
	 * @param callable $callback
	 */
	public function testSaveAppointment($request_id, $record_id, $isAppointment, $callback)
	{
		$request = RequestModel::model()->findByPk($request_id);
		$record = RequestRecordModel::model()->findByPk($record_id);
		$record->saveAppointment($isAppointment);
		$request->appointmentByRecord($record);

		$this->assertEquals($isAppointment, $record->isAppointment);
		$this->assertEquals(0, count($record->getErrors()), 'Ошибки и при сохранении модели');

		$callback($this, $request);

	}

	/**
	 * Дата провайдер для testSaveAppointment
	 *
	 * @return array
	 */
	public function dataSaveAppointment()
	{
		return array(
			//заявка без клиники и без доктора
			//в заявке должна проставиться клиника, измениться дата приема,  в истории залогироваться 2 записи
			array(
				1,
				1,
				'yes',
				function (CDbTestCase $test, $request) {
					$test->assertEquals(1, $request->clinic_id, 'Должна проставиться клиника');

					$test->assertEquals(
						'2014-06-04 17:49:10',
						$request->date_record,
						'Должна проставиться дата записи на прием'
					);

					$test->assertEquals(2, count($request->request_history));
					$test->assertEquals($request->request_history[1]->text, 'Изменена клиника на  Клиника №1');
					$test->assertEquals(
						$request->request_history[0]->text,
						'Установлен признак приёма в аудиозаписи (1401889564.20777.mp3)'
					);
				}
			),
			//заявка без клиники и без доктора
			//в заявке не должна проставиться клиника, в истории залогироваться 1 записи
			array(
				1,
				1,
				'no',
				function (CDbTestCase $test, $request) {
					$test->assertNull($request->clinic_id, 'Не должна проставиться клиника');
					$test->assertNotEquals(
						'2014-06-04 17:49:10',
						$request->date_record,
						'Не нолжна проставиться дата записи на прием'
					);
					$test->assertEquals(1, count($request->request_history));
					$test->assertEquals(
						$request->request_history[0]->text,
						'Удалён признак приёма в аудиозаписи(1401889564.20777.mp3)'
					);
				}
			),
			//заявка с  клиникой и без доктора
			//в заявке должна измениться клиника, измениться дата приема,  в истории залогироваться 2 записи
			array(
				2,
				2,
				'yes',
				function (CDbTestCase $test, $request) {
					$test->assertEquals(2, $request->clinic_id, 'Должна измениться клиника');

					$test->assertEquals(
						'2014-06-04 17:58:00',
						$request->date_record,
						'Не должна измениться дата записи на прием'
					);

					$test->assertEquals(2, count($request->request_history));
					$test->assertEquals($request->request_history[1]->text, 'Изменена клиника на  Клиника №2');
					$test->assertEquals(
						$request->request_history[0]->text,
						'Установлен признак приёма в аудиозаписи (1401889564.20777.mp3)'
					);
				}
			),
			//заявка с  клиникой и без доктора
			//в заявке не должна проставиться клиника, в истории залогироваться 1 записи
			array(
				2,
				2,
				'no',
				function (CDbTestCase $test, $request) {
					$test->assertEquals(1, $request->clinic_id, 'Не должна измениться клиника');
					$test->assertNotEquals(
						'2014-06-04 17:49:11',
						$request->date_record,
						'Не нолжна измениться дата записи на прием'
					);
					$test->assertEquals(1, count($request->request_history));
					$test->assertEquals(
						$request->request_history[0]->text,
						'Удалён признак приёма в аудиозаписи(1401889564.20777.mp3)'
					);
				}
			),
			//заявка с клиникой и доктором
			//в заявке не должна измениться клиника, должна измениться дата приема, в истории должна залогироваться 1 запись
			array(
				3,
				3,
				'yes',
				function (CDbTestCase $test, $request) {
					$test->assertEquals(1, $request->clinic_id, 'Не должна измениться клиника');

					$test->assertEquals(
						'2014-06-04 17:58:00',
						$request->date_record,
						'Не нолжна измениться дата записи на прием'
					);
					$test->assertEquals(1, count($request->request_history));
					$test->assertEquals(
						$request->request_history[0]->text,
						'Установлен признак приёма в аудиозаписи (1401889564.20777.mp3)'
					);
				}
			),

		);
	}

	/**
	 * Тест получения провайдера записи
	 */
	public function testGetSourceProvider()
	{
		$this->getFixtureManager()->checkIntegrity(false);
		$this->getFixtureManager()->truncateTable('request_record');
		$this->getFixtureManager()->loadFixture('request_record');

		$record = RequestRecordModel::model()->findByPk(17);
		$this->assertInstanceOf(ProviderInterface::class, $record->getSourceProvider());

		$record = RequestRecordModel::model()->findByPk(5);
		$this->assertNull($record->getSourceProvider());
	}
}
