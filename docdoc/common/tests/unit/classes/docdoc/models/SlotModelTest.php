<?php

namespace dfs\tests\docdoc\models;

use dfs\docdoc\models\SlotModel;

use CDbTestCase;

/**
 * Class ClinicModelTest
 *
 * @package dfs\tests\docdoc\models
 */
class SlotModelTest extends CDbTestCase
{
	/**
	 * выполнять при запуске каждого теста
	 */
	public function setUp()
	{
		$this->getFixtureManager()->checkIntegrity(false);
		$this->getFixtureManager()->truncateTable(SlotModel::model()->tableName());
		$this->getFixtureManager()->truncateTable('doctor_4_clinic');
		$this->getFixtureManager()->truncateTable('booking');
		$this->getFixtureManager()->loadFixture('doctor_4_clinic');
		$this->getFixtureManager()->loadFixture('booking');
		$this->getFixtureManager()->truncateTable('request');
		$this->getFixtureManager()->loadFixture('request');
	}

	/**
	 * Тест, создания записи
	 *
	 * @dataProvider slotCreateProvider
	 *
	 * @param string   $scenario
	 * @param array    $attributes
	 * @param callable $checkFunction
	 */
	public function testSlotCreate($scenario, $attributes, $checkFunction)
	{
		if ($scenario == 'insert') {
			$model = new SlotModel();
		} else {
			$this->getFixtureManager()->loadFixture('slot');
			$model = SlotModel::model()->findByPk(1);
		}

		$model->attributes = $attributes;
		$model->save();

		$checkFunction($this, $model, $attributes);
	}

	/**
	 * данные для создания записи
	 *
	 * @return array
	 */
	public function slotCreateProvider()
	{
		return array(
			array(
				'insert',
				array(),
				function (CDbTestCase $test, SlotModel $model) {
					$test->assertEquals(
						3,
						count($model->getErrors()),
						'Ожидается 4 ошибки при создании пустого объекта'
					);
				},
			),
			array(
				//проверка создания
				'insert',
				array(
					'doctor_4_clinic_id' => 1,
					'start_time'         => '2014-01-01 01:01:01',
					'finish_time'        => '2014-01-01 10:10:10',
					'external_id'        => 'external_id',
				),
				function (CDbTestCase $test, $model, $attributes) {
					foreach ($attributes as $k => $v) {
						$test->assertEquals($model->$k, $v, "Аттрибут {$k}. {$model->$k} != {$v}");
					}
				},
			),
			array(
				//проверка doctorClinic
				'insert',
				array(
					'doctor_4_clinic_id' => 1,
					'start_time'         => '2014-01-01 01:01:01',
					'finish_time'        => '2014-01-01 10:10:10',
					'external_id'        => 'external_id',
				),
				function (CDbTestCase $test, $model) {
					$test->assertNotNull($model->doctorClinic, 'Не создано relation с doctor_4_clinic');
				},
			),
			array(
				//изменение записи. менять можно только время
				'update',
				array(
					'start_time'  => '2014-01-01 20:01:01',
					'finish_time' => '2014-01-01 21:10:10',
				),
				function (CDbTestCase $test, $model, $attributes) {
					foreach ($attributes as $k => $v) {
						$test->assertEquals($model->$k, $v, "Аттрибут {$k}. {$model->$k} != {$v}");
					}
				},
			),
			array(
				//изменение записи. нельзя изменять doctor_4_clinic_id и external_id
				'update',
				array(
					'doctor_4_clinic_id' => 2,
					'external_id'        => 'new_external_id',
				),
				function (CDbTestCase $test, $model, $attributes) {
					foreach ($attributes as $k => $v) {
						$test->assertNotEquals(
							$model->$k,
							$v,
							"Нельзя изменить значение аттрибута {$k}. Значение изменено на {$v}"
						);
					}
				},
			),
		);
	}

	/**
	 * Проверка возможности бронировать и резервировать слот
	 *
	 * @param int      $slotId
	 * @param int|null $requestId
	 * @param bool     $expected
	 *
	 * @dataProvider isAvailableDataProvider
	 */
	public function testIsAvailable($slotId, $requestId, $expected)
	{
		$this->getFixtureManager()->loadFixture('slot');

		$slot = SlotModel::model()->findByPk($slotId);
		$res = $slot->isAvailable($requestId);
		$this->assertEquals($expected, (bool)$res);
	}

	/**
	 * @return array
	 */
	public function isAvailableDataProvider()
	{
		return [
			[1, null, false], //забукан
			[2, null, true], //свободен
		];
	}
}
