<?php

namespace dfs\tests\docdoc\models;

use \dfs\docdoc\models\UserModel;
use \CDbTestCase;

/**
 * Файл класса UserModelTest
 *
 * Класс для тестирования модели UserModel
 *
 * @author  Mikhail Vasilyev <m.vasilyev@likefifa.ru>
 * @link    https://docdoc.megaplan.ru/task/1003744/card/
 * @package dfs.tests.docdoc.models
 */
class UserModelTest extends CDbTestCase
{

	/**
	 * Выполнять при запуске каждого теста
	 *
	 * @return void
	 */
	protected function setUp()
	{
		$this->getFixtureManager()->checkIntegrity(false);
		$this->getFixtureManager()->truncateTable(UserModel::model()->tableName());

		return parent::setUp();
	}

	/**
	 * Тест для правил валидации модели
	 *
	 * @dataProvider userRulesProvider
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
			$model = new UserModel();
		} else {
			$this->getFixtureManager()->loadFixture('user');
			$model = UserModel::model()->findByPk(1);
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
	public function userRulesProvider()
	{
		return array(
			// Создание записи
			array(
				'insert',
				array(
					"user_login"    => "login1",
					"user_password" => "password1",
					"user_role"     => 1,
					"user_fname"    => "Имя1",
					"user_lname"    => "Фамилия1",
					"user_mname"    => "Отчество1",
					"user_email"    => "E-mail1",
					"user_status"   => 1,
					"status"        => "Статус1",
					"phone"         => "Телефон1",
					"skype"         => "Skype1",
				),
				function (CDbTestCase $test, UserModel $model, $attributes) {
					$test->assertNotNull($model->user_id, 'Ошибка при создании записи');

					foreach ($attributes as $k => $v) {
						if ($k === "user_password") {
							$v = $model->getUserPasswordHash($v);
						}
						$test->assertEquals($model->$k, $v, "Аттрибут {$k}. {$model->$k} != {$v}");
					}
				},
			),
			// Проверка XSS
			array(
				'insert',
				array(
					"user_login"    => "<b></b>",
					"user_password" => "<b></b>",
					"user_role"     => 1,
					"user_fname"    => "<b></b>",
					"user_lname"    => "<b></b>",
					"user_mname"    => "<b></b>",
					"user_email"    => "<b></b>",
					"user_status"   => 1,
					"status"        => "<b></b>",
					"phone"         => "<b></b>",
					"skype"         => "<b></b>",
				),
				function (CDbTestCase $test, UserModel $model, $attributes) {
					$test->assertNotEquals(
						$model->user_login,
						$attributes['user_login'],
						'user_login. Не работает очистка от тегов'
					);
					$test->assertNotEquals(
						$model->user_password,
						$attributes['user_password'],
						'user_password. Не работает очистка от тегов'
					);
					$test->assertNotEquals(
						$model->user_fname,
						$attributes['user_fname'],
						'user_fname. Не работает очистка от тегов'
					);
					$test->assertNotEquals(
						$model->user_lname,
						$attributes['user_lname'],
						'user_lname. Не работает очистка от тегов'
					);
					$test->assertNotEquals(
						$model->user_mname,
						$attributes['user_mname'],
						'user_mname. Не работает очистка от тегов'
					);
					$test->assertNotEquals(
						$model->user_email,
						$attributes['user_email'],
						'user_email. Не работает очистка от тегов'
					);
					$test->assertNotEquals(
						$model->status,
						$attributes['status'],
						'status. Не работает очистка от тегов'
					);
					$test->assertNotEquals(
						$model->phone,
						$attributes['phone'],
						'phone. Не работает очистка от тегов'
					);
					$test->assertNotEquals(
						$model->skype,
						$attributes['skype'],
						'skype. Не работает очистка от тегов'
					);
				},
			),
			// Изменение записи со сменой пароля
			array(
				'update',
				array(
					"user_login"    => "login2",
					"user_password" => "password2",
					"user_role"     => 2,
					"user_fname"    => "Имя2",
					"user_lname"    => "Фамилия2",
					"user_mname"    => "Отчество2",
					"user_email"    => "E-mail2",
					"user_status"   => 2,
					"status"        => "Статус2",
					"phone"         => "Телефон2",
					"skype"         => "Skype2",
				),
				function (CDbTestCase $test, UserModel $model, $attributes) {
					foreach ($attributes as $k => $v) {
						if ($k === "user_password") {
							$v = $model->getUserPasswordHash($v);
						}
						$test->assertEquals($model->$k, $v, "Аттрибут {$k}. {$model->$k} != {$v}");
					}
				},
			),
			// Изменение записи без смены пароля
			array(
				'update',
				array(
					"user_login" => "login3",
				),
				function (CDbTestCase $test, UserModel $model) {
					$modelFix = UserModel::model()->findByPk(1);
					$test->assertEquals(
						$model->user_password,
						$modelFix->user_password,
						"Аттрибут user_password был изменен"
					);
				},
			),
			// Пустая запись
			array(
				'insert',
				array(),
				function (CDbTestCase $test, UserModel $model) {
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