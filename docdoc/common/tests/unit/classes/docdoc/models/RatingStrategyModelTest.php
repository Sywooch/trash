<?php
/**
 * Created by PhpStorm.
 * User: a.tyutyunnikov
 * Date: 16.09.14
 * Time: 15:56
 */

namespace dfs\tests\docdoc\models;

use dfs\docdoc\models\ClinicModel;
use dfs\docdoc\models\DoctorClinicModel;
use dfs\docdoc\models\RatingModel;
use dfs\docdoc\models\RatingStrategyModel;

class RatingStrategyModelTest extends \CDbTestCase
{
	/**
	 * Тест на создание и изменение модели
	 *
	 * @dataProvider rulesData
	 *
	 * @param string $scenario Тестируемый сценарий
	 * @param array $attributes Атрибуты
	 * @param callable $checkFunction Валидаторы
	 *
	 * @throws \CException
	 */
	public function testRules($scenario, array $attributes, callable $checkFunction)
	{
		$this->getFixtureManager()->checkIntegrity(false);
		$this->getFixtureManager()->truncateTable(RatingStrategyModel::model()->tableName());

		if ($scenario == 'insert') {
			$model = new RatingStrategyModel();
		} else {
			$this->getFixtureManager()->loadFixture(RatingStrategyModel::model()->tableName());
			$model = RatingStrategyModel::model()->findByPk(1);
		}

		$model->attributes = $attributes;
		$model->save();

		$checkFunction($model, $attributes);
	}

	/**
	 * Данные для создания записи
	 *
	 * @return array
	 */
	public function rulesData()
	{
		return [
			// Создание записи
			[
				'insert',
				[
					'id' => 'asdf',
					'name' => 'default',
					'chance' => 'df',
				],
				function (RatingStrategyModel $model) {
					$num = count($model->getErrors());
					$this->assertEquals(2, $num, "Ожидается 2 ошибки. Возврашено {$num}");
				},
			],
		];
	}

	/**
	 * Тест расчета рейтинга
	 *
	 * @param int $id
	 * @param ClinicModel $class
	 * @param string $strategy
	 * @param string $expected
	 *
	 * @dataProvider calculateRatingDataProvider
	 */
	public function testCalculateRating($id, $class, $strategyId, $expected)
	{
		static $_loaded = false;

		if(!$_loaded){
			$this->getFixtureManager()->checkIntegrity(false);
			$this->getFixtureManager()->truncateTable('clinic');
			$this->getFixtureManager()->loadFixture('clinic');
			$this->getFixtureManager()->truncateTable('doctor');
			$this->getFixtureManager()->loadFixture('doctor');
			$this->getFixtureManager()->truncateTable('doctor_4_clinic');
			$this->getFixtureManager()->loadFixture('doctor_4_clinic');
			$this->getFixtureManager()->truncateTable('rating_strategy');
			$this->getFixtureManager()->loadFixture('rating_strategy');

			ClinicModel::model()->clearStaticVariableCache();
			DoctorClinicModel::model()->clearStaticVariableCache();

			$_loaded = true;
		}

		$object = $class->findByPk($id);
		$actual = RatingStrategyModel::model()->findByPk($strategyId)->calcRating($object);

		$this->assertEquals($expected, $actual);
	}

	/**
	 * Данные для testCalculateRating
	 *
	 * @return array
	 */
	public function calculateRatingDataProvider()
	{
		return [
			[1, ClinicModel::model(), 1, 0.459999999],
			[2, ClinicModel::model(), 2, 3000],
			[1, DoctorClinicModel::model(), 1, 10.097995],
			[2, DoctorClinicModel::model(), 2, 1200.0],
		];
	}


	/**
	 * Проверка на инсерты и на апдейты в таблицу rating
	 * @throws \CException
	 */
	public function testSaveRating()
	{
		$this->getFixtureManager()->truncateTable('clinic');
		$this->getFixtureManager()->loadFixture('clinic');
		$this->getFixtureManager()->truncateTable('rating');
		$this->getFixtureManager()->truncateTable('rating_strategy');
		$this->getFixtureManager()->loadFixture('rating_strategy');

		$object = ClinicModel::model()->findByPk(1);
		$countStrategies = RatingStrategyModel::model()->count();

		RatingStrategyModel::model()->saveRatings($object);
		$countRatings = RatingModel::model()->count();

		$this->assertEquals($countStrategies, $countRatings);

		//вызываю метод еще раз. должны пойти апдейты
		RatingStrategyModel::model()->saveRatings($object);
		$countRatings = RatingModel::model()->count();

		$this->assertEquals($countStrategies, $countRatings);
	}
}
