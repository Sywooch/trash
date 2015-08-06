<?php
/**
 * Created by PhpStorm.
 * User: a.tyutyunnikov
 * Date: 27.06.14
 * Time: 15:56
 */

namespace dfs\tests\docdoc\models;

use CDbTestCase;
use dfs\docdoc\models\PartnerCostModel;

class PartnerCostModelTest extends CDbTestCase
{
	/**
	 * @param $partner_id
	 * @param $service_id
	 * @param $city_id
	 * @param $cost
	 *
	 * @dataProvider getCostProvider
	 */
	public function testGetCost($partner_id, $service_id, $city_id, $cost)
	{
		$this->getFixtureManager()->truncateTable('partner_cost');
		$this->getFixtureManager()->loadFixture('partner_cost');

		$model = PartnerCostModel::model();
		$model->clearCache();
		$model->partner_id = $partner_id;
		$model->service_id = $service_id;
		$model->city_id = $city_id;

		$this->assertEquals($cost, $model->getCost());
	}

	public function getCostProvider()
	{
		return [
			[1, 1, 1, 400],
			[1, 3, null, 500],
			[1, 3, 1, 550],
			[1, 3, 2, 500],
			[2, 3, 1, 650],
			[2, 3, 2, 100],
		];
	}

	/**
	 *
	 * @param $partner_id
	 * @param $service_id
	 *
	 * @dataProvider getCostWithFakeParamsProvider
	 */
	public function testGetCostWithFakeParams($partner_id, $service_id)
	{
		$this->getFixtureManager()->truncateTable('partner_cost');
		$this->getFixtureManager()->loadFixture('partner_cost');

		$model = PartnerCostModel::model();
		$model->partner_id = $partner_id;
		$model->service_id = $service_id;

		$this->assertEquals(0, $model->getCost());
	}

	public function getCostWithFakeParamsProvider()
	{
		return [
			[123123123123, 123123123123],
			[null, null],
			[null, 1],
			[1, null],  //partner_id is real
			[1, 34345234523452345],
		];
	}

	/**
	 * Тест для правил валидации модели
	 *
	 * @dataProvider partnerCostRulesProvider
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
			$this->getFixtureManager()->truncateTable(PartnerCostModel::model()->tableName());
		}

		if ($scenario == 'insert') {
			$model = new PartnerCostModel;
		} else {
			$this->getFixtureManager()->loadFixture('partner_cost');
			$model = PartnerCostModel::model()->findByPk(1);
		}

		$model->attributes = $attributes;
		$model->save();

		$checkFunction($this, $model, $attributes);
	}

	/**
	 * Данные для создания записи
	 *
	 * @return string[]
	 */
	public function partnerCostRulesProvider()
	{
		return [
			// Создание записи с неуникальной комбинацией
			[
				'insert',
				[
					'partner_id' => null,
					'service_id' => 3,
					'cost'       => 650,
					'city_id'    => 1,
				],
				function (CDbTestCase $test, PartnerCostModel $model) {
					$test->assertEquals(
						false,
						empty($model->errors["unique"]),
						"Запись не распозналась как уникальная"
					);
				},
				false
			],

			// Обновление записи с неуникальной комбинацией
			[
				'update',
				[
					'partner_id' => null,
					'service_id' => 3,
					'cost'       => 650,
					'city_id'    => 1,
				],
				function (CDbTestCase $test, PartnerCostModel $model) {
					$test->assertEquals(
						false,
						empty($model->errors["unique"]),
						"Запись не распозналась как уникальная"
					);
				}
			],

			// Создание записи
			[
				'insert',
				[
					'partner_id' => 2,
					'service_id' => 1,
					'cost'       => 400,
					'city_id'    => 2,
				],
				function (CDbTestCase $test, PartnerCostModel $model, $attributes) {
					$test->assertNotNull($model->id, 'Ошибка при создании записи');
					foreach ($attributes as $k => $v) {
						$test->assertEquals($model->$k, $v, "Аттрибут {$k}. {$model->$k} != {$v}");
					}
				},
			],

			// Обновление записи
			[
				'update',
				[
					'partner_id' => null,
					'service_id' => 1,
					'cost'       => 650,
					'city_id'    => 1,
				],
				function (CDbTestCase $test, PartnerCostModel $model, $attributes) {
					foreach ($attributes as $k => $v) {
						$test->assertEquals($model->$k, $v, "Аттрибут {$k}. {$model->$k} != {$v}");
					}
				},
			],

			// Создание записи с неправильными параметрами
			[
				'insert',
				[
					'partner_id' => "abc",
					'service_id' => "abc",
					'cost'       => "abc",
					'city_id'    => "abc",
				],
				function (CDbTestCase $test, PartnerCostModel $model) {
					$num = 0;
					foreach ($model->getErrors() as $e) {
						$num += count($e);
					}
					$test->assertEquals(4, $num, "Ожидается 4 ошибки, отловили {$num}");
				},
			],

			// Пустая запись
			array(
				'insert',
				[],
				function (CDbTestCase $test, PartnerCostModel $model) {
					$num = 0;
					foreach ($model->getErrors() as $e) {
						$num += count($e);
					}
					$test->assertEquals(1, $num, "Ожидается 1 ошибка, отловили {$num}");
				},
			),
		];
	}
}
