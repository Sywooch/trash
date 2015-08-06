<?php

namespace dfs\tests\docdoc\models;

use \dfs\docdoc\models\CityModel;
use \CDbTestCase;
use dfs\docdoc\models\PhoneModel;
use dfs\docdoc\models\PhoneProviderModel;

/**
 * Файл класса CityModelTest
 *
 * Класс для тестирования модели CityModel
 *
 * @author  Mikhail Vasilyev <m.vasilyev@likefifa.ru>
 * @link    https://docdoc.megaplan.ru/task/1003803/card/
 * @package dfs.tests.docdoc.models
 */
class CityModelTest extends CDbTestCase
{
	/**
	 * Тест ошибки при отсуствии в базе дефолтного города
	 * @expectedException \CException
	 */
	public function testFailedFindCity()
	{
		CityModel::model()->findCity('select this string not found in database!!!!!!!!!');
	}

	/**
	 * Тест поиска дефолтного города при отсутсвии заданного rewrite_name
	 */
	public function testSuccessFindCity()
	{
		$this->getFixtureManager()->loadFixture(CityModel::model()->tableName());
		$city = CityModel::model()->findCity('select this string not found in database!!!!!!!!!');
		$this->assertEquals($city->rewrite_name, CityModel::DEFAULT_REWRITE_NAME);
		$this->assertEquals($city->is_active, 0);
	}

	/**
	 * Выполнять при запуске каждого теста
	 *
	 * @return void
	 */
	protected function setUp()
	{
		$this->getFixtureManager()->checkIntegrity(false);
		$this->getFixtureManager()->truncateTable(CityModel::model()->tableName());
		$this->getFixtureManager()->truncateTable(PhoneProviderModel::model()->tableName());
		$this->getFixtureManager()->loadFixture(PhoneProviderModel::model()->tableName());
		$this->getFixtureManager()->truncateTable(PhoneModel::model()->tableName());
		$this->getFixtureManager()->loadFixture(PhoneModel::model()->tableName());

		parent::setUp();
	}

	/**
	 * Тест для правил валидации модели
	 *
	 * @dataProvider cityRulesProvider
	 *
	 * @param string   $scenario
	 * @param string[] $attributes
	 * @param callable $checkFunction
	 *
	 * @return void
	 */
	public function testCityRules($scenario, $attributes, $checkFunction)
	{
		if ($scenario == 'insert') {
			$model = new CityModel();
		} else {
			$this->getFixtureManager()->loadFixture('city');
			$model = CityModel::model()->findByPk(1);
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
	public function cityRulesProvider()
	{
		return array(
			// Создание записи
			array(
				'insert',
				array(
					'title'               => 'Калуга',
					'title_genitive'      => 'Калуги',
					'title_prepositional' => 'Калуге',
					'title_dative'        => 'Калуге',
					'rewrite_name'        => 'klg',
					'long'                => 123.321,
					'lat'                 => 345.756,
					'prefix'              => 'spb.',
					'is_active'           => 1,
					'search_type'         => 1,
					'site_phone'          => '78123856652',
					'site_office'         => '78123856652',
					'opinion_phone'       => '78123856652',
					'site_YA'             => '19018384',
					'gtm'                 => 'gtm1',
					'diagnostic_gtm'      => 'diagnostic_gtm1',
				),
				function (CDbTestCase $test, CityModel $model, $attributes) {
					$test->assertNotNull($model->id_city, 'Ошибка при создании города');

					foreach ($attributes as $k => $v) {
						$test->assertEquals($model->$k, $v, "Аттрибут {$k}. {$model->$k} != {$v}");
					}
				},
			),
			// Проверка XSS
			array(
				'insert',
				array(
					'title'          => '<b></b>',
					'rewrite_name'   => '<b></b>',
					'long'           => '<b></b>',
					'lat'            => '<b></b>',
					'prefix'         => '<b></b>',
					'site_phone'     => '<b></b>',
					'site_office'    => '<b></b>',
					'opinion_phone'  => '<b></b>',
					'site_YA'        => '<b></b>',
					'gtm'            => '<b></b>',
					'diagnostic_gtm' => '<b></b>',
				),
				function (CDbTestCase $test, CityModel $model, $attributes) {
					$test->assertNotEquals(
						$model->title,
						$attributes['title'],
						'title. Не работает очистка от тегов'
					);
					$test->assertNotEquals(
						$model->rewrite_name,
						$attributes['rewrite_name'],
						'rewrite_name. Не работает очистка от тегов'
					);
					$test->assertNotEquals(
						$model->long,
						$attributes['long'],
						'long. Не работает очистка от тегов'
					);
					$test->assertNotEquals(
						$model->lat,
						$attributes['lat'],
						'lat. Не работает очистка от тегов'
					);
					$test->assertNotEquals(
						$model->prefix,
						$attributes['prefix'],
						'prefix. Не работает очистка от тегов'
					);
					$test->assertNotEquals(
						$model->site_phone,
						$attributes['site_phone'],
						'site_phone. Не работает очистка от тегов'
					);
					$test->assertNotEquals(
						$model->site_office,
						$attributes['site_office'],
						'site_office. Не работает очистка от тегов'
					);
					$test->assertNotEquals(
						$model->opinion_phone,
						$attributes['opinion_phone'],
						'opinion_phone. Не работает очистка от тегов'
					);
					$test->assertNotEquals(
						$model->site_YA,
						$attributes['site_YA'],
						'site_YA. Не работает очистка от тегов'
					);
					$test->assertNotEquals(
						$model->gtm,
						$attributes['gtm'],
						'gtm. Не работает очистка от тегов'
					);
					$test->assertNotEquals(
						$model->diagnostic_gtm,
						$attributes['diagnostic_gtm'],
						'diagnostic_gtm. Не работает очистка от тегов'
					);
				},
			),
			// Изменение записи
			array(
				'update',
				array(
					'title'               => 'Обнинск',
					'title_genitive'      => 'Обнинск',
					'title_prepositional' => 'Обнинск',
					'title_dative'        => 'Обнинску',
					'rewrite_name'        => 'obninsk',
					'long'                => 999.888,
					'lat'                 => 777.555,
					'prefix'              => 'obninsk.',
					'is_active'           => 0,
					'search_type'         => 123,
					'site_phone'          => '78123850000',
					'site_office'         => '78123850000',
					'opinion_phone'       => '78123850000',
					'site_YA'             => '19018000',
					'gtm'                 => 'gtm3',
					'diagnostic_gtm'      => 'diagnostic_gtm3',
				),
				function (CDbTestCase $test, CityModel $model, $attributes) {
					foreach ($attributes as $k => $v) {
						$test->assertEquals($model->$k, $v, "Аттрибут {$k}. {$model->$k} != {$v}");
					}
				},
			),
			// Пустая запись
			array(
				'insert',
				array(
					'site_phone' => '73333333333', //нет в фикстурах
					'opinion_phone' => '74951234567',  //занят партнером
				),
				function (CDbTestCase $test, CityModel $model) {
					$num = 0;
					foreach ($model->getErrors() as $e) {
						$num += count($e);
					}
					$test->assertEquals(8, $num, "Ожидается 8 ошибок, отловили {$num}");
				},
			),
		);
	}


}
