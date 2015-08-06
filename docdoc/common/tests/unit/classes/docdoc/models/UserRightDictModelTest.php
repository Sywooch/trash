<?php

namespace dfs\tests\docdoc\models;

use \dfs\docdoc\models\UserRightDictModel;
use \CDbTestCase;

/**
 * Файл класса UserRightDictModelTest
 *
 * Класс для тестирования модели UserRightDictModelTest
 *
 * @author  Mikhail Vasilyev <m.vasilyev@likefifa.ru>
 * @link    https://docdoc.megaplan.ru/task/1003744/card/
 * @package dfs.tests.docdoc.models
 */
class UserRightDictModelTest extends CDbTestCase
{

	/**
	 * Выполнять при запуске каждого теста
	 *
	 * @return void
	 */
	protected function setUp()
	{
		$this->getFixtureManager()->checkIntegrity(false);
		$this->getFixtureManager()->truncateTable(UserRightDictModel::model()->tableName());

		return parent::setUp();
	}

	/**
	 * Тест для правил валидации модели
	 *
	 * @dataProvider userRightDictRulesProvider
	 *
	 * @param string   $scenario
	 * @param string[] $attributes
	 * @param callable $checkFunction
	 *
	 * @return void
	 */
	public function testUserRightDictRules($scenario, $attributes, $checkFunction)
	{
		if ($scenario == 'insert') {
			$model = new UserRightDictModel();
		} else {
			$this->getFixtureManager()->loadFixture('user_right_dict');
			$model = UserRightDictModel::model()->findByPk(1);
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
	public function userRightDictRulesProvider()
	{
		return array(
			// Создание записи
			array(
				'insert',
				array(
					"title"    => "Название1",
					"code"     => "Код1",
				),
				function (CDbTestCase $test, UserRightDictModel $model, $attributes) {
					$test->assertTrue($model->hasErrors(), 'Ошибка при создании записи');

					foreach ($attributes as $k => $v) {
						$test->assertEquals($model->$k, $v, "Аттрибут {$k}. {$model->$k} != {$v}");
					}
				},
			),
			// Проверка XSS
			array(
				'insert',
				array(
					"title"    => "<b></b>",
					"code"     => "<b></b>",
				),
				function (CDbTestCase $test, UserRightDictModel $model, $attributes) {
					$test->assertNotEquals(
						$model->title,
						$attributes['title'],
						'title. Не работает очистка от тегов'
					);
					$test->assertNotEquals(
						$model->code,
						$attributes['code'],
						'code. Не работает очистка от тегов'
					);
				},
			),
			// Изменение записи
			array(
				'update',
				array(
					"title"    => "Название2",
					"code"     => "Код2",
				),
				function (CDbTestCase $test, UserRightDictModel $model, $attributes) {
					foreach ($attributes as $k => $v) {
						$test->assertEquals($model->$k, $v, "Аттрибут {$k}. {$model->$k} != {$v}");
					}
				},
			),
			// Пустая запись
			array(
				'insert',
				array(),
				function (CDbTestCase $test, UserRightDictModel $model) {
					$num = 0;
					foreach ($model->getErrors() as $e) {
						$num += count($e);
					}
					$test->assertEquals(2, $num, "Ожидается 2 ошибок, отловили {$num}");
				},
			),
		);
	}
}