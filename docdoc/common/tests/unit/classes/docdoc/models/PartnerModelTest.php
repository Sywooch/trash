<?php

namespace dfs\tests\docdoc\models;

use dfs\docdoc\models\PartnerModel;
use CDbTestCase;

/**
 * Файл класса PartnerModelTest
 *
 * Класс для тестирования модели PartnerModel
 *
 * @author  Mikhail Vasilyev <mvasilyev@docdoc.ru>
 * @link    https://docdoc.atlassian.net/browse/DD-21
 * @package dfs.tests.docdoc.models
 */
class PartnerModelTest extends CDbTestCase
{

	/**
	 * Выполнять при запуске каждого теста
	 *
	 * @return void
	 */
	protected function setUp()
	{
		parent::setUp();

		$this->getFixtureManager()->checkIntegrity(false);
		$this->getFixtureManager()->truncateTable(PartnerModel::model()->tableName());
	}

	/**
	 * Тест для IsMobileApi
	 *
	 */
	public function testIsMobileApi()
	{
		$this->getFixtureManager()->loadFixture('partner');
		$this->assertFalse(PartnerModel::model()->isMobileApi(1));
		$this->assertTrue(PartnerModel::model()->isMobileApi(12));

	}

	/**
	 * Проверяет на валидость введенного пароля
	 *
	 * @param int     $partnerId
	 * @param string  $password
	 * @param boolean $assertTrue
	 *
	 * @dataProvider checkPasswordDataProvider
	 */
	public function testCheckPasswordForEquals($partnerId, $password, $assertTrue)
	{
		$this->getFixtureManager()->truncateTable('partner');
		$this->getFixtureManager()->loadFixture('partner');

		$partner = PartnerModel::model()->findByPk($partnerId);

		if ($assertTrue) {
			$this->assertTrue($partner->checkPasswordForEquals($password));
		} else {
			$this->assertFalse($partner->checkPasswordForEquals($password));
		}
	}

	/**
	 * Дата провайден для проверки правильности введенных паролей
	 *
	 * @return array
	 */
	public function checkPasswordDataProvider()
	{
		return [
			[1, 'поработаем лопатой', true],
			[2, 'чет нехочется', false],
			[12, 'а придется', true],
		];
	}

	/**
	 * Тест для правил валидации модели
	 *
	 * @dataProvider pageRulesProvider
	 *
	 * @param string   $scenario
	 * @param string[] $attributes
	 * @param callable $checkFunction
	 *
	 * @return void
	 */
	public function testPageRules($scenario, $attributes, $checkFunction)
	{
		if ($scenario == 'insert') {
			$model = new PartnerModel();
		} else {
			$this->getFixtureManager()->loadFixture('partner');
			$model = PartnerModel::model()->findByPk(1);
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
	public function pageRulesProvider()
	{
		return array(
			// Создание записи
			array(
				'insert',
				array(
					'name'                          => 'Красота и медецина',
					'login'                         => 'krasotaimedicina',
					'password'                      => 'fab289d495b967893f37967fc5c498dd',
					'contact_name'                  => 'Мазулин Михаил Александрович',
					'contact_phone'                 => '+7 (666) 666-66-66',
					'contact_email'                 => 'misha@bk.ru',
					'city_id'                       => 4,
					'password_salt'                 => 'krasota',
					'offer_accepted'                => 1,
					'offer_accepted_timestamp'      => '2013-09-18 15:41:07',
					'offer_accepted_from_addresses' => 'X-R: 212.41.32.119, R-ADDR: 37.200.68.16',
					'cost_per_request'              => 250,
				),
				function (CDbTestCase $test, PartnerModel $model, $attributes) {
					$test->assertNotNull($model->id, 'Ошибка при создании записи');
					foreach ($attributes as $k => $v) {
						if ($k === "password") {
							$v = PartnerModel::makePassword($attributes["password"], $attributes["password_salt"]);
						}
						$test->assertEquals($model->$k, $v, "Аттрибут {$k}. {$model->$k} != {$v}");
					}
				},
			),
			// Проверка XSS
			array(
				'insert',
				array(
					'name'                          => '<b>pulsplus</b>',
					'login'                         => '<b>e7644b9ffd646372cd335aa3d7dac081</b>',
					'password'                      => 'fab289d495b967893f37967fc5c498dd',
					'contact_name'                  => '<b>Ольга Резник</b>',
					'contact_phone'                 => '<b>+7(903)7243931</b>',
					'contact_email'                 => 'olgareznik10@gmail.com',
					'city_id'                       => 1,
					'password_salt'                 => 'O6CBR7Hb1F4r',
					'offer_accepted'                => 1,
					'offer_accepted_timestamp'      => '2013-12-12 11:43:45',
					'offer_accepted_from_addresses' => '<b>X-R: 141.136.112.3, R-ADDR: 127.0.0.1</b>',
					'cost_per_request'              => 250,
				),
				function (CDbTestCase $test, PartnerModel $model, $attributes) {
					$test->assertNotEquals(
						$model->name,
						$attributes['name'],
						'name. Не работает очистка от тегов'
					);
					$test->assertNotEquals(
						$model->login,
						$attributes['login'],
						'login. Не работает очистка от тегов'
					);
					$test->assertNotEquals(
						$model->contact_name,
						$attributes['contact_name'],
						'contact_name. Не работает очистка от тегов'
					);
					$test->assertNotEquals(
						$model->contact_phone,
						$attributes['contact_phone'],
						'contact_phone. Не работает очистка от тегов'
					);
					$test->assertNotEquals(
						$model->offer_accepted_from_addresses,
						$attributes['offer_accepted_from_addresses'],
						'offer_accepted_from_addresses. Не работает очистка от тегов'
					);
				},
			),
			// Изменение записи
			array(
				'update',
				array(
					'name'                          => 'pulsplus',
					'login'                         => 'e7644b9ffd646372cd335aa3d7dac081',
					'password'                      => 'fab289d495b967893f37967fc5c498dd',
					'contact_name'                  => 'Ольга Резник',
					'contact_phone'                 => '+7(903)7243931',
					'contact_email'                 => 'olgareznik10@gmail.com',
					'city_id'                       => 1,
					'password_salt'                 => 'O6CBR7Hb1F4r',
					'offer_accepted'                => 1,
					'offer_accepted_timestamp'      => '2013-12-12 11:43:45',
					'offer_accepted_from_addresses' => 'X-R: 141.136.112.3, R-ADDR: 127.0.0.1',
					'cost_per_request'              => 250,
				),
				function (CDbTestCase $test, PartnerModel $model, $attributes) {
					foreach ($attributes as $k => $v) {
						$test->assertEquals($model->$k, $v, "Аттрибут {$k}. {$model->$k} != {$v}");
					}
				},
			),
			// Пустая запись
			array(
				'insert',
				array(),
				function (CDbTestCase $test, PartnerModel $model) {
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
