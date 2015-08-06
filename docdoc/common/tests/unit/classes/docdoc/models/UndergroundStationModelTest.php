<?php

namespace dfs\tests\docdoc\models;

use \dfs\docdoc\models\UndergroundStationModel;
use \CDbTestCase;

/**
 * Файл класса UndergroundStationModelTest
 *
 * Класс для тестирования модели UndergroundStationModel
 *
 * @author  Mikhail Vasilyev <m.vasilyev@likefifa.ru>
 * @link    https://docdoc.megaplan.ru/task/1003744/card/
 * @package dfs.tests.docdoc.models
 */
class UndergroundStationModelTest extends CDbTestCase
{

	/**
	 * Выполнять при запуске каждого теста
	 *
	 * @return void
	 */
	protected function setUp()
	{
		$this->getFixtureManager()->checkIntegrity(false);
		$this->getFixtureManager()->truncateTable(UndergroundStationModel::model()->tableName());

		return parent::setUp();
	}

	/**
	 * Тест для правил валидации модели
	 *
	 * @dataProvider undergroundStationModelRulesProvider
	 *
	 * @param string   $scenario
	 * @param string[] $attributes
	 * @param callable $checkFunction
	 *
	 * @return void
	 */
	public function testUserRules($scenario, $attributes, $checkFunction)
	{
		if ($scenario == 'insert') {
			$model = new UndergroundStationModel();
		} else {
			$this->getFixtureManager()->loadFixture('underground_station');
			$model = UndergroundStationModel::model()->findByPk(1);
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
	public function undergroundStationModelRulesProvider()
	{
		return array(
			// Создание записи
			array(
				'insert',
				array(
					'name'                => 'Авиамоторная',
					'underground_line_id' => 3,
					'index'               => 4,
					'rewrite_name'        => 'aviamotornaya',
					'longitude'           => 0.00,
					'latitude'            => 0.00
				),
				function (CDbTestCase $test, UndergroundStationModel $model, $attributes) {
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
					'name'                => '<b></b>',
					'underground_line_id' => 3,
					'index'               => 4,
					'rewrite_name'        => '<b></b>',
					'longitude'           => 0.00,
					'latitude'            => 0.00
				),
				function (CDbTestCase $test, UndergroundStationModel $model, $attributes) {
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
					'name'                => 'Автозаводская',
					'underground_line_id' => 1,
					'index'               => 13,
					'rewrite_name'        => 'avtozavodskaya',
					'longitude'           => 0.00,
					'latitude'            => 0.00
				),
				function (CDbTestCase $test, UndergroundStationModel $model, $attributes) {
					foreach ($attributes as $k => $v) {
						$test->assertEquals($model->$k, $v, "Аттрибут {$k}. {$model->$k} != {$v}");
					}
				},
			),
			// Пустая запись
			array(
				'insert',
				array(),
				function (CDbTestCase $test, UndergroundStationModel $model) {
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
					'name'                => 3,
					'underground_line_id' => 'Авиамоторная',
					'index'               => 'aviamotornaya',
					'rewrite_name'        => 4,
					'longitude'           => "4,7",
					'latitude'            => "5,6"
				),
				function (CDbTestCase $test, UndergroundStationModel $model) {
					$num = 0;
					foreach ($model->getErrors() as $e) {
						$num += count($e);
					}
					$test->assertEquals(5, $num, "Ожидается 5 ошибок, отловили {$num}");
				},
			),
		);
	}
}