<?php

namespace dfs\tests\docdoc\models;

use dfs\docdoc\models\DoctorOpinionModel;
use CDbTestCase;
use Yii;

/**
 * Class DoctorOpinionModelTest
 *
 * @package dfs\tests\docdoc\models
 */
class DoctorOpinionModelTest extends CDbTestCase
{

	/**
	 * при запуске каждого теста
	 */
	function setUp()
	{
		$this->getFixtureManager()->checkIntegrity(false);
		$this->getFixtureManager()->truncateTable('doctor_opinion');
		$this->getFixtureManager()->loadFixture('doctor_opinion');
	}

	/**
	 * Тест поиска по врачу
	 */
	public function testFindByDoctorId()
	{
		// Поиск всех отзывов
		$count = DoctorOpinionModel::model()
			->byDoctor(2)
			->count();

		$this->assertEquals(3, $count);

		// Поиск только опубликованных отзывов
		$count = DoctorOpinionModel::model()
			->byDoctor(2)
			->allowed()
			->count();

		$this->assertEquals(2, $count);

	}

	/**
	 * Тест поиска опубликованных отзывов
	 */
	public function testAllowed()
	{
		$count = DoctorOpinionModel::model()->allowed()->count();
		$this->assertEquals(3, $count);
	}

	/**
	 * проверка атрибутов отзыва
	 *
	 * @param $opinion
	 */
	private function assertCommon(DoctorOpinionModel $opinion)
	{
		$this->assertEquals('Иван Иванов', $opinion->name);

		$this->assertNotEmpty($opinion->doctor_id);

		$this->assertEquals('XSS', $opinion->text, "Aктивная xss в opinion->text");

		$this->assertEquals(
			11,
			strlen($opinion->phone),
			"Длина телефона opinion->client_phone не равна 11 символам"
		);

		$this->assertLessThanOrEqual(5, $opinion->rating_qualification);
		$this->assertLessThanOrEqual(5, $opinion->rating_attention);
		$this->assertLessThanOrEqual(5, $opinion->rating_room);
		$this->assertGreaterThan(0, $opinion->rating_qualification);
		$this->assertGreaterThan(0, $opinion->rating_attention);
		$this->assertGreaterThan(0, $opinion->rating_room);

		$this->assertEquals(0, $opinion->allowed);
		$this->assertEquals('gues', $opinion->author);

		$this->assertEquals(1, $opinion->count(), 'Количество записей в таблице opinion != 1');

	}

	/**
	 * Тест создания отзыва с сайта
	 *
	 */
	public function testScenarioSite()
	{
		$this->getFixtureManager()->truncateTable('doctor_opinion');

		$opinion = new DoctorOpinionModel();
		$opinion->setScenario(DoctorOpinionModel::SCENARIO_SITE);
		$opinion->attributes = array(
			'doctor_id' => 1,
			'name' => 'Иван Иванов',
			'phone' => '79261234567',
			'rating_qualification' => 2,
			'rating_attention' => 4,
			'rating_room' => 5,
			'text' => '<script>XSS</script>',
		);

		if (!$opinion->save()) {
			$message = "";

			foreach ($opinion->getErrors() as $att => $msg) {
				$message .= sprintf("%s: %s\n", $att, join(', ', $msg));
			}

			$this->fail($message);
		}

		$this->assertCommon($opinion);
	}

} 