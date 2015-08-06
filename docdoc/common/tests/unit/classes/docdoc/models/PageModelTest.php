<?php

namespace dfs\tests\docdoc\models;

use dfs\docdoc\models\PageModel;
use CDbTestCase;

/**
 * Файл класса PageModelTest
 *
 * Класс для тестирования модели PageModel
 *
 * @author  Mikhail Vasilyev <m.vasilyev@likefifa.ru>
 * @link    https://docdoc.megaplan.ru/task/1003885/card/
 * @package dfs.tests.docdoc.models
 */
class PageModelTest extends CDbTestCase
{

	/**
	 * Выполнять при запуске каждого теста
	 *
	 * @return void
	 */
	protected function setUp()
	{
		$this->getFixtureManager()->checkIntegrity(false);
		$this->getFixtureManager()->truncateTable(PageModel::model()->tableName());

		return parent::setUp();
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
			$model = new PageModel();
		} else {
			$this->getFixtureManager()->loadFixture('page');
			$model = PageModel::model()->findByPk(1);
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
					'url'             => '/doctor/',
					'h1'              => 'Заголовок 1',
					'title'           => 'Название страницы 1',
					'keywords'        => 'Ключевые слова 1',
					'description'     => 'Описание 1',
					'seo_text_top'    => 'СЕО текст вверху 1',
					'seo_text_bottom' => 'СЕО текст внизу 1',
					'is_show'         => '1',
					'id_city'         => '1',
					'site'            => '1',
				),
				function (CDbTestCase $test, PageModel $model, $attributes) {
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
					'url'             => '/doctor/',
					'h1'              => '<u>Заголовок 1</u>',
					'title'           => '<b>Название страницы 1</b>',
					'keywords'        => '<b>Ключевые слова 1</b>',
					'description'     => '<b>Описание 1</b>',
					'seo_text_top'    => '<u>СЕО текст вверху 1</u>',
					'seo_text_bottom' => '<u>СЕО текст внизу 1</u>',
					'is_show'         => '1',
					'id_city'         => '1',
					'site'            => '1',
				),
				function (CDbTestCase $test, PageModel $model, $attributes) {
					$test->assertNotEquals(
						$model->title,
						$attributes['title'],
						'title. Не работает очистка от тегов'
					);
					$test->assertNotEquals(
						$model->keywords,
						$attributes['keywords'],
						'keywords. Не работает очистка от тегов'
					);
					$test->assertNotEquals(
						$model->description,
						$attributes['description'],
						'description. Не работает очистка от тегов'
					);
					$test->assertEquals(
						$model->h1,
						$attributes['h1'],
						"Аттрибут h1. {$model->h1} != {$attributes['h1']}"
					);
					$test->assertEquals(
						$model->seo_text_top,
						$attributes['seo_text_top'],
						"Аттрибут seo_text_top. {$model->seo_text_top} != {$attributes['seo_text_top']}"
					);
					$test->assertEquals(
						$model->seo_text_bottom,
						$attributes['seo_text_bottom'],
						"Аттрибут seo_text_bottom. {$model->seo_text_bottom} != {$attributes['seo_text_bottom']}"
					);
				},
			),
			// Изменение записи
			array(
				'update',
				array(
					'url'             => '/clinic/',
					'h1'              => 'Заголовок 2',
					'title'           => 'Название страницы 2',
					'keywords'        => 'Ключевые слова 2',
					'description'     => 'Описание 2',
					'seo_text_top'    => 'СЕО текст вверху 2',
					'seo_text_bottom' => 'СЕО текст внизу 2',
					'is_show'         => '2',
					'id_city'         => '2',
					'site'            => '2',
				),
				function (CDbTestCase $test, PageModel $model, $attributes) {
					foreach ($attributes as $k => $v) {
						$test->assertEquals($model->$k, $v, "Аттрибут {$k}. {$model->$k} != {$v}");
					}
				},
			),
			// Пустая запись
			array(
				'insert',
				array(),
				function (CDbTestCase $test, PageModel $model) {
					$num = 0;
					foreach ($model->getErrors() as $e) {
						$num += count($e);
					}
					$test->assertEquals(3, $num, "Ожидается 3 ошибки, отловили {$num}");
				},
			),
			// Изменение записи с абсолютным URL
			array(
				'update',
				array(
					'url' => 'https://back.mvasilyev.docdoc.pro/2.0/page/',
				),
				function (CDbTestCase $test, PageModel $model, $attributes) {
					$test->assertNotEquals(
						$model->url,
						$attributes['url'],
						'url. URL не преобразился в относительный'
					);
				},
			),
		);
	}
}