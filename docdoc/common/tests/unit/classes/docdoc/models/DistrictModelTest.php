<?php

namespace dfs\tests\docdoc\models;

use \dfs\docdoc\models\DistrictModel;
use \CDbTestCase;

/**
 * Файл класса DistrictModelTest
 *
 * Класс для тестирования модели UserModel
 *
 * @author  Mikhail Vasilyev <m.vasilyev@likefifa.ru>
 * @link    https://docdoc.megaplan.ru/task/1003744/card/
 * @package dfs.tests.docdoc.models
 */
class DistrictModelTest extends CDbTestCase
{

	/**
	 * Выполнять при запуске каждого теста
	 *
	 * @return void
	 */
	protected function setUp()
	{
		$this->getFixtureManager()->checkIntegrity(false);
		$this->getFixtureManager()->truncateTable(DistrictModel::model()->tableName());

		return parent::setUp();
	}

	/**
	 * Тест для правил валидации модели
	 *
	 * @dataProvider districtRulesProvider
	 *
	 * @param string   $scenario
	 * @param string[] $attributes
	 * @param callable $checkFunction
	 *
	 * @return void
	 */
	public function testDistrictRules($scenario, $attributes, $checkFunction)
	{
		if ($scenario == 'insert') {
			$model = new DistrictModel();
		} else {
			$this->getFixtureManager()->loadFixture('district');
			$model = DistrictModel::model()->findByPk(1);
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
	public function districtRulesProvider()
	{
		return array(
			// Создание записи
			array(
				'insert',
				array(
					'name'         => 'Арбат',
					'rewrite_name' => 'arbat',
					'id_city'      => 1,
					'id_area'      => 1,
				),
				function (CDbTestCase $test, DistrictModel $model, $attributes) {
					$test->assertNotNull($model->id, 'Ошибка при создании записи');
					foreach ($attributes as $k => $v) {
						$test->assertEquals($model->$k, $v, "Аттрибут {$k}. {$model->$k} != {$v}");
					}
				},
			),
			// Проверка XSS
			array(
				'insert',
				array(
					"name"         => "<b></b>",
					"rewrite_name" => "<b></b>",
					"id_city"      => 1,
					"id_area"      => 1,
				),
				function (CDbTestCase $test, DistrictModel $model, $attributes) {
					$test->assertNotEquals(
						$model->name,
						$attributes['name'],
						'name. Не работает очистка от тегов'
					);
					$test->assertNotEquals(
						$model->rewrite_name,
						$attributes['rewrite_name'],
						'rewrite_name. Не работает очистка от тегов'
					);
				},
			),
			// Изменение записи
			array(
				'update',
				array(
					'name'         => 'Басманный',
					'rewrite_name' => 'basmannyj',
					'id_city'      => 2,
					'id_area'      => 2,
				),
				function (CDbTestCase $test, DistrictModel $model, $attributes) {
					foreach ($attributes as $k => $v) {
						$test->assertEquals($model->$k, $v, "Аттрибут {$k}. {$model->$k} != {$v}");
					}
				},
			),
			// Пустая запись
			array(
				'insert',
				array(),
				function (CDbTestCase $test, DistrictModel $model) {
					$num = 0;
					foreach ($model->getErrors() as $e) {
						$num += count($e);
					}
					$test->assertEquals(2, $num, "Ожидается 2 ошибки, отловили {$num}");
				},
			),
			// Создание записи с некорректными названием и абривиатурой
			array(
				'insert',
				array(
					'name'         => 'arbat',
					'rewrite_name' => 'Арбат',
					'id_city'      => 1,
					'id_area'      => 1,
				),
				function (CDbTestCase $test, DistrictModel $model) {
					$num = 0;
					foreach ($model->getErrors() as $e) {
						$num += count($e);
					}
					$test->assertEquals(1, $num, "Ожидается 1 ошибки, отловили {$num}");
				},
			),
		);
	}
}