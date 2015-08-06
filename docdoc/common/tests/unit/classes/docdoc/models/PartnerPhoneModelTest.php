<?php

namespace dfs\tests\docdoc\models;

use CDbTestCase;
use dfs\docdoc\models\PartnerPhoneModel;
use dfs\docdoc\models\PhoneProviderModel;
use dfs\docdoc\models\PhoneModel;

class PartnerPhoneModelTest extends CDbTestCase
{

	/**
	 * Выполнять при запуске каждого теста
	 *
	 * @return void
	 */
	protected function setUp()
	{
		$this->getFixtureManager()->checkIntegrity(false);
		$this->getFixtureManager()->truncateTable('partner_phones');
		$this->getFixtureManager()->loadFixture('partner_phones');
		$this->getFixtureManager()->truncateTable(PhoneProviderModel::model()->tableName());
		$this->getFixtureManager()->loadFixture(PhoneProviderModel::model()->tableName());
		$this->getFixtureManager()->truncateTable(PhoneModel::model()->tableName());
		$this->getFixtureManager()->loadFixture(PhoneModel::model()->tableName());

		parent::setUp();
	}

	/**
	 * Тест для правил валидации модели
	 *
	 * @dataProvider partnerPhoneRulesProvider
	 *
	 * @param string   $scenario        сценарий
	 * @param string[] $attributes      атрибуты
	 * @param callable $checkFunction   функция проверки
	 * @param bool     $isTruncateTable производить ли очистку таблицы
	 *
	 * @return void
	 */
	public function testPartnerCostRules($scenario, $attributes, $checkFunction, $isTruncateTable = true)
	{
		if ($isTruncateTable) {
			$this->getFixtureManager()->truncateTable(PartnerPhoneModel::model()->tableName());
		}

		$model = ($scenario == 'insert') ? new PartnerPhoneModel : PartnerPhoneModel::model()->findByPk(1);
		$model->attributes = $attributes;
		$model->save();

		$checkFunction($this, $model, $attributes);
	}

	/**
	 * Данные для создания записи
	 *
	 * @return string[]
	 */
	public function partnerPhoneRulesProvider()
	{
		return [
			// Создание записи с неуникальной комбинацией
			[
				'insert',
				[
					'partner_id' => 1,
					'city_id'    => 1,
					'phone_id'   => 1
				],
				function (CDbTestCase $test, PartnerPhoneModel $model) {
					$test->assertEquals(
						false,
						empty($model->errors["partner_id"]),
						"Запись не распозналась как НЕуникальная"
					);
				},
				false
			],
			// Создание записи
			[
				'insert',
				[
					'partner_id' => 1,
					'city_id'    => 2,
					'phone_id' => 1
				],
				function (CDbTestCase $test, PartnerPhoneModel $model, $attributes) {
					$test->assertNotNull($model->partner_id, 'Ошибка при создании записи');
					foreach ($attributes as $k => $v) {
						$test->assertEquals($model->$k, $v, "Аттрибут {$k}. {$model->$k} != {$v}");
					}
				},
			],
			// Создание записи с неправильными параметрами
			[
				'insert',
				[
					'partner_id' => 'aaa',
					'city_id'    => 'aaa',
					'phone_id'   => 'aaa',
				],
				function (CDbTestCase $test, PartnerPhoneModel $model) {
					$num = 0;
					foreach ($model->getErrors() as $e) {
						$num += count($e);
					}
					$test->assertEquals(3, $num, "Ожидается 3 ошибки, отловили {$num}");
				},
			],
			// Пустая запись
			array(
				'insert',
				[],
				function (CDbTestCase $test, PartnerPhoneModel $model) {
					$num = 0;
					foreach ($model->getErrors() as $e) {
						$num += count($e);
					}
					$test->assertEquals(3, $num, "Ожидается 3 ошибки, отловили {$num}");
				},
			),
			// Создание записи
			[
				'insert',
				[
					'partner_id' => 1,
					'city_id' => 2,
					'phone_id' => -1 //не найден
				],
				function (CDbTestCase $test, PartnerPhoneModel $model) {
					$test->assertTrue($model->hasErrors('phone_id'));
					$test->assertEquals(1, count($model->getErrors('phone_id')));
					$test->assertEquals('Телефон не найден', $model->getErrors('phone_id')[0]);
				},
			],
			// Создание записи
			[
				'insert',
				[
					'partner_id' => 1,
					'city_id' => 2,
					'phone_id' => 9 //занято
				],
				function (CDbTestCase $test, PartnerPhoneModel $model) {
					$test->assertTrue($model->hasErrors('phone_id'));
					$test->assertEquals(1, count($model->getErrors('phone_id')));
					$test->assertEquals('Телефон уже занят', $model->getErrors('phone_id')[0]);
				},
			],
		];
	}
}