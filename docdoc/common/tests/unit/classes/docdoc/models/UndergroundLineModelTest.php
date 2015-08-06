<?php

namespace dfs\tests\docdoc\models;

use dfs\docdoc\models\UndergroundLineModel;
use CDbTestCase;

/**
 * Файл класса UndergroundLineModelTest
 *
 * Класс для тестирования модели UndergroundLineModel
 *
 * @author  Mikhail Vasilyev <m.vasilyev@likefifa.ru>
 * @link    https://docdoc.megaplan.ru/task/1003744/card/
 * @package dfs.tests.docdoc.models
 */
class UndergroundLineModelTest extends CDbTestCase
{

	/**
	 * Выполнять при запуске каждого теста
	 *
	 * @return void
	 */
	protected function setUp()
	{
		$this->getFixtureManager()->checkIntegrity(false);
		$this->getFixtureManager()->truncateTable(UndergroundLineModel::model()->tableName());

		return parent::setUp();
	}

	/**
	 * Тест для правил валидации модели
	 *
	 * @dataProvider undergroundLineRulesProvider
	 *
	 * @param string   $scenario
	 * @param string[] $attributes
	 * @param callable $checkFunction
	 *
	 * @return void
	 */
	public function testUndergroundLineRules($scenario, $attributes, $checkFunction)
	{
		if ($scenario == 'insert') {
			$model = new UndergroundLineModel();
		} else {
			$this->getFixtureManager()->loadFixture('underground_line');
			$model = UndergroundLineModel::model()->findByPk(1);
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
	public function undergroundLineRulesProvider()
	{
		return array(
			// Создание записи
			array(
				'insert',
				array(
					'name'    => 'Замоскворецкая',
					'color'   => "0a6f20",
					'city_id' => 1,
				),
				function (CDbTestCase $test, UndergroundLineModel $model, $attributes) {
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
					'name'    => '<b></b>',
					'color'   => "<b></b>",
					'city_id' => 1,
				),
				function (CDbTestCase $test, UndergroundLineModel $model, $attributes) {
					$test->assertNotEquals(
						$model->name,
						$attributes['name'],
						'name. Не работает очистка от тегов'
					);
					$test->assertNotEquals(
						$model->color,
						$attributes['color'],
						'color. Не работает очистка от тегов'
					);
				},
			),
			// Изменение записи
			array(
				'update',
				array(
					'name'    => 'Филевская',
					'color'   => "0099cc",
					'city_id' => 2,
				),
				function (CDbTestCase $test, UndergroundLineModel $model, $attributes) {
					foreach ($attributes as $k => $v) {
						$test->assertEquals($model->$k, $v, "Аттрибут {$k}. {$model->$k} != {$v}");
					}
				},
			),
			// Пустая запись
			array(
				'insert',
				array(),
				function (CDbTestCase $test, UndergroundLineModel $model) {
					$num = 0;
					foreach ($model->getErrors() as $e) {
						$num += count($e);
					}
					$test->assertEquals(2, $num, "Ожидается 2 ошибки, отловили {$num}");
				},
			),
			// Создание записи с некорректными данными
			array(
				'insert',
				array(
					'name'    => 'Filevskaya',
					'color'   => '0099cc',
					'city_id' => "msk",
				),
				function (CDbTestCase $test, UndergroundLineModel $model) {
					$num = 0;
					foreach ($model->getErrors() as $e) {
						$num += count($e);
					}
					$test->assertEquals(1, $num, "Ожидается 1 ошибок, отловили {$num}");
				},
			),
		);
	}
}