<?php

namespace dfs\tests\docdoc\models;

use dfs\docdoc\models\ClinicAdminModel;
use CDbTestCase;
use Yii;

/**
 * Class ClinicModelTest
 *
 * @package dfs\tests\docdoc\models
 */
class ClinicAdminModelTest extends CDbTestCase
{
	/**
	 * Выполнять при запуске каждого теста
	 */
	public function setUp()
	{
		return parent::setUp();
	}

	/**
	 * Тест enabled
	 */
	public function testClinicAdminEnabled()
	{
		$this->getFixtureManager()->checkIntegrity(false);

		$this->getFixtureManager()->truncateTable('clinic');
		$this->getFixtureManager()->loadFixture('clinic');

		$this->getFixtureManager()->truncateTable('clinic_admin');
		$this->getFixtureManager()->loadFixture('clinic_admin');

		$this->assertEquals(1, ClinicAdminModel::model()->enabled()->count()) ;
	}

	/**
	 * Тест для правил валидации модели
	 *
	 * @dataProvider rulesProvider
	 *
	 * @param string   $scenario
	 * @param string[] $attributes
	 * @param callable $checkFunction
	 *
	 * @return void
	 */
	public function testRules($scenario, $attributes, $checkFunction)
	{
		if ($scenario == 'insert') {
			$model = new ClinicAdminModel();
		} else {
			$model = ClinicAdminModel::model()->findByPk(1);
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
	public function rulesProvider()
	{
		return array(
			// Создание записи
			array(
				'insert',
				array(
					"email"  => "test@email.ru",
					"passwd" => 'password1',
				),
				function (CDbTestCase $test, ClinicAdminModel $model, $attributes) {
					$test->assertNotNull($model->clinic_admin_id, 'Ошибка при создании записи');

					foreach ($attributes as $k => $v) {
						if ($k === "passwd") {
							$v = $model->getUserPasswordHash($v);
						}
						$test->assertEquals($model->$k, $v, "Аттрибут {$k}. {$model->$k} != {$v}");
					}
				},
			),
			// insert уникального мыла
			array(
				'insert',
				array(
					"email"  => "test@email.ru",
					"passwd" => 'asdfasdfasdfasfasdfd',
				),
				function (CDbTestCase $test, ClinicAdminModel $model) {
					$err_count = 0;
					foreach ($model->getErrors() as $e) {
						$err_count += count($e);
					}
					$test->assertEquals(1, $err_count, "Ожидается 1 ошибки, отловили {$err_count}");
				},
			),

			// update записи без смены пароля
			array(
				'update',
				array(
					"passwd" => "login3",
				),
				function (CDbTestCase $test, ClinicAdminModel $model) {
					$modelFix = ClinicAdminModel::model()->findByPk(1);
					$test->assertEquals(
						$model->passwd,
						$modelFix->passwd,
						"Аттрибут user_password был изменен"
					);
				},
			),
			// update с ошибкой уникальности
			array(
				'update',
				array(
					"email" => "test@email.ru",
				),
				function (CDbTestCase $test, ClinicAdminModel $model) {
					$err_count = 0;
					foreach ($model->getErrors() as $e) {
						$err_count += count($e);
					}
					$test->assertEquals(1, $err_count, "Ожидается 1 ошибки, отловили {$err_count}");
				},
			),
			// Пустая запись
			array(
				'insert',
				array(),
				function (CDbTestCase $test, ClinicAdminModel $model) {
					$num = 0;
					foreach ($model->getErrors() as $e) {
						$num += count($e);
					}
					$test->assertEquals(2, $num, "Ожидается 2 ошибки, отловили {$num}");
				},
			),
		);
	}

	/**
	 * Тест сохранения manytomany клиник к админам
	 */
	public function testManyToMany()
	{
		$this->getFixtureManager()->truncateTable('clinic');
		$this->getFixtureManager()->loadFixture('clinic');

		$this->getFixtureManager()->truncateTable('admin_4_clinic');

		$this->getFixtureManager()->truncateTable('clinic_admin');
		$this->getFixtureManager()->loadFixture('clinic_admin');

		$admin = ClinicAdminModel::model()->findByPk(1);
		$this->assertEquals(0, count($admin->clinics));

		$admin->clinics = [1];
		$admin->save();

		$admin = ClinicAdminModel::model()->findByPk(1);
		$this->assertEquals(1, count($admin->clinics));

		$admin = ClinicAdminModel::model()->findByPk(1);
		$clinics = array_merge($admin->clinics, [2]);
		$admin->clinics = $clinics;
		$admin->save();

		$admin = ClinicAdminModel::model()->findByPk(1);
		$this->assertEquals(2, count($admin->clinics));
	}
}
