<?php

namespace dfs\tests\docdoc\models;

use dfs\docdoc\models\ClinicScheduleModel;
use dfs\docdoc\models\ClinicModel;
use CDbTestCase;


/**
 * Class ClinicScheduleModelTest
 *
 * @package dfs\tests\docdoc\models
 */
class ClinicScheduleModelTest extends CDbTestCase
{
	protected function setUp()
	{
		parent::setUp();

		$fxManager = $this->getFixtureManager();

		$fxManager->checkIntegrity(false);
		$fxManager->truncateTable('clinic');
		$fxManager->truncateTable('clinic_schedule');
		$fxManager->loadFixture('clinic');
		$fxManager->loadFixture('clinic_schedule');
	}

	/**
	 * Проверка поиска по клинике
	 */
	public function testSearchByClinic()
	{
		$items = ClinicScheduleModel::model()->searchByClinic(1)->findAll();
		$this->assertCount(2, $items);
		foreach ($items as $item) {
			// Проверяем, что время возвращаяется в формате 09:00
			$this->assertRegExp('~^[0-9]{2}:[0-9]{2}$~', $item->start_time);
			$this->assertRegExp('~^[0-9]{2}:[0-9]{2}$~', $item->end_time);
		}
	}

	/**
	 * Проверка поиска ближайшего время работы клиники
	 */
	public function testBeginningWorkTime()
	{
		$clinic = ClinicModel::model()->findByPk(1);

		$this->assertEquals('2014-12-03 21:00:00', date('Y-m-d H:i:s', $clinic->getEndWorkTime(strtotime('2014-12-04 05:30:30'), 1200)));
		$this->assertEquals('2014-12-04 10:10:30', date('Y-m-d H:i:s', $clinic->getEndWorkTime(strtotime('2014-12-04 10:30:30'), 1200)));
		$this->assertEquals('2014-12-04 20:30:00', date('Y-m-d H:i:s', $clinic->getEndWorkTime(strtotime('2014-12-04 20:50:00'), 1200)));
		$this->assertEquals('2014-12-04 21:00:00', date('Y-m-d H:i:s', $clinic->getEndWorkTime(strtotime('2014-12-04 23:30:30'), 1200)));
		$this->assertEquals('2014-12-06 20:00:00', date('Y-m-d H:i:s', $clinic->getEndWorkTime(strtotime('2014-12-06 20:50:30'), 1200)));
		$this->assertEquals('2014-12-06 20:00:00', date('Y-m-d H:i:s', $clinic->getEndWorkTime(strtotime('2014-12-07 12:30:30'), 1200)));
		$this->assertEquals('2014-12-06 20:00:00', date('Y-m-d H:i:s', $clinic->getEndWorkTime(strtotime('2014-12-08 09:10:30'), 1200)));
		$this->assertEquals('2014-12-08 09:00:30', date('Y-m-d H:i:s', $clinic->getEndWorkTime(strtotime('2014-12-08 09:20:30'), 1200)));
	}

	/**
	 * Проверка поиска ближайшего время работы клиники
	 */
	public function testGetWorkTime()
	{
		$clinic = ClinicModel::model()->findByPk(1);

		$this->assertEquals('2014-12-04 09:00:00', date('Y-m-d H:i:s', $clinic->getWorkTime(strtotime('2014-12-04 05:30:30'))));
		$this->assertEquals('2014-12-04 09:00:00', date('Y-m-d H:i:s', $clinic->getWorkTime(strtotime('2014-12-03 21:00:00'))));
		$this->assertEquals('2014-12-05 09:00:00', date('Y-m-d H:i:s', $clinic->getWorkTime(strtotime('2014-12-04 21:10:00'))));
		$this->assertEquals('2014-12-05 09:00:00', date('Y-m-d H:i:s', $clinic->getWorkTime(strtotime('2014-12-04 23:30:30'))));
		$this->assertEquals('2014-12-06 10:00:00', date('Y-m-d H:i:s', $clinic->getWorkTime(strtotime('2014-12-05 21:10:30'))));
		$this->assertEquals('2014-12-08 09:00:00', date('Y-m-d H:i:s', $clinic->getWorkTime(strtotime('2014-12-06 21:10:30'))));
		$this->assertEquals('2014-12-08 09:00:00', date('Y-m-d H:i:s', $clinic->getWorkTime(strtotime('2014-12-07 12:30:30'))));
		$this->assertEquals('2014-12-08 09:10:30', date('Y-m-d H:i:s', $clinic->getWorkTime(strtotime('2014-12-08 09:10:30'))));
		$this->assertEquals('2014-12-08 09:20:30', date('Y-m-d H:i:s', $clinic->getWorkTime(strtotime('2014-12-08 09:20:30'))));
	}

	/**
	 * Проверка поиска ближайшего время работы клиники
	 *
	 * @dataProvider getSlotsData
	 *
	 * @param string $date
	 * @param string $currentTime
	 * @param array  $checkSlots
	 * @param int    $count
	 */
	public function testGetSlots($date, $currentTime, $checkSlots, $count)
	{
		$clinic = ClinicModel::model()->findByPk(1);

		$result = $clinic->getSlots($date, 30, 7200, strtotime($currentTime));

		$day = date('d-m-Y', strtotime($date));

		$slots = isset($result[$day]) ? $result[$day] : [];

		foreach ($checkSlots as $i => $slot) {
			$this->assertEquals($slot, isset($slots[$i]) ? $slots[$i] : null);
		}

		$this->assertEquals($count, count($slots));
	}

	/**
	 * Данные для GetSlots
	 */
	public function getSlotsData()
	{
		return [
			[
				'date' => '2015-02-17', // Вторник
				'currentTime' => '2015-02-17 08:00:00',
				'checkSlots' => [
					0 => [
						'start_time'  => '09:00',
						'finish_time' => '09:30',
						'active' => false,
					],
					3 => [
						'start_time'  => '10:30',
						'finish_time' => '11:00',
						'active' => false,
					],
					4 => [
						'start_time'  => '11:00',
						'finish_time' => '11:30',
						'active' => true,
					],
					23 => [
						'start_time'  => '20:30',
						'finish_time' => '21:00',
						'active' => true,
					],
				],
				'count' => 24,
			],
			[
				'date' => '2015-02-18', // Среда
				'currentTime' => '2015-02-18 09:40:00',
				'checkSlots' => [
					0 => [
						'start_time'  => '09:00',
						'finish_time' => '09:30',
						'active' => false,
					],
					1 => [
						'start_time'  => '09:30',
						'finish_time' => '10:00',
						'active' => false,
					],
					2 => [
						'start_time'  => '10:00',
						'finish_time' => '10:30',
						'active' => false,
					],
					23 => [
						'start_time'  => '20:30',
						'finish_time' => '21:00',
						'active' => true,
					],
				],
				'count' => 24,
			],
			[
				'date' => '2015-02-14', // Суббота
				'currentTime' => '2015-02-13 20:30:00',
				'checkSlots' => [
					0 => [
						'start_time'  => '10:00',
						'finish_time' => '10:30',
						'active' => true,
					],
					19 => [
						'start_time'  => '19:30',
						'finish_time' => '20:00',
						'active' => true,
					],
				],
				'count' => 20,
			],
			[
				'date' => '2015-02-14', // Суббота
				'currentTime' => '2015-02-13 21:30:00',
				'checkSlots' => [
					0 => [
						'start_time'  => '10:00',
						'finish_time' => '10:30',
						'active' => false,
					],
					4 => [
						'start_time'  => '12:00',
						'finish_time' => '12:30',
						'active' => true,
					],
					19 => [
						'start_time'  => '19:30',
						'finish_time' => '20:00',
						'active' => true,
					],
				],
				'count' => 20,
			],
			[
				'date' => '2015-02-15', // Воскресенье
				'currentTime' => '2015-02-13 21:30:00',
				'checkSlots' => [],
				'count' => 0,
			],
			[
				'date' => '2015-02-16', // Понедельник
				'currentTime' => '2015-02-14 20:10:00',
				'checkSlots' => [
					0 => [
						'start_time'  => '09:00',
						'finish_time' => '09:30',
						'active' => false,
					],
					1 => [
						'start_time'  => '09:30',
						'finish_time' => '10:00',
						'active' => false,
					],
					4 => [
						'start_time'  => '11:00',
						'finish_time' => '11:30',
						'active' => true,
					],
					23 => [
						'start_time'  => '20:30',
						'finish_time' => '21:00',
						'active' => true,
					],
				],
				'count' => 24,
			],
		];
	}
}