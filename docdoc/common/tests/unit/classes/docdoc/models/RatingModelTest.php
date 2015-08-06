<?php
/**
 * Created by PhpStorm.
 * User: a.tyutyunnikov
 * Date: 16.09.14
 * Time: 15:56
 */

namespace dfs\tests\docdoc\models;

use dfs\docdoc\models\RatingModel;

class RatingModelTest extends \CDbTestCase
{
	/**
	 * Тест на создание и изменение модели
	 *
	 * @param string $scenario Тестируемый сценарий
	 * @param array $attributes Атрибуты
	 * @param callable $checkFunction Валидаторы
	 *
	 * @dataProvider ruleDataProvider
	 * @throws \CException
	 */
	public function testRules($scenario, array $attributes, callable $checkFunction)
	{
		$this->getFixtureManager()->checkIntegrity(false);
		$this->getFixtureManager()->truncateTable(RatingModel::model()->tableName());

		if ($scenario == 'insert') {
			$model = new RatingModel();
		} else {
			$this->getFixtureManager()->loadFixture(RatingModel::model()->tableName());
			$model = RatingModel::model()->findByPk(1);
		}

		$model->attributes = $attributes;
		$model->save();

		$checkFunction($model, $attributes);
	}

	/**
	 * Данные для тестирования записей
	 *
	 * @return array
	 */
	public function ruleDataProvider()
	{
		return [
			// Создание записи
			[
				'insert',
				[
					'id' => 1,
					'object_id' => 'sadf',
					'object_type' => 'asdf',
					'strategy_id' => 'asdfasdf',
					'rating_value' => 'asdf',
				],
				function (RatingModel $model) {
					$num = count($model->getErrors());
					$this->assertEquals(4, $num, "Ожидается 4 ошибки. Возврашено {$num}");
				},
			],
		];
	}

	/**
	 * Теси получения временного файла для пересчета рейтингов
	 *
	 * @param int $type
	 * @param string $expected
	 * @dataProvider getCsvTmpFileNameDataProvider
	 */
	public function testGetCsvTmpFileName($type, $expected)
	{
		$actual = RatingModel::model()->getTempCsvFileName($type);
		$this->assertEquals($expected, $actual);
	}

	/**
	 * Данные для testGetCsvTmpFileName
	 * @return array
	 */
	public function getCsvTmpFileNameDataProvider()
	{
		return [
			[RatingModel::TYPE_DOCTOR, ROOT_PATH . '/back/runtime/ratings/doctor_rating.csv'],
			[RatingModel::TYPE_CLINIC, ROOT_PATH . '/back/runtime/ratings/clinic_rating.csv'],
		];
	}

	/**
	 * @param $data
	 * @param $type
	 * @param $isValid
	 * @param $exception
	 * @throws \CException
	 * @dataProvider validateCSVDataDataProvider
	 */
	public function testValidateCSVData($data, $type, $isValid, $exception = false)
	{
		if($exception){
			$this->setExpectedException('\CException');
		}

		$res = RatingModel::model()->validateCSVData($data, $type);

		$this->assertEquals($isValid, $res);
	}

	public function validateCSVDataDataProvider()
	{
		return [
			//правильно
			[
				[
					[
						'ID' => 123,
						'KB' => 1,
					]

				],
				RatingModel::TYPE_DOCTOR,
				true
			],
			//неправильно
			[
				[
					[
						'IsadfD' => 123,
						'asdf' => 1,
					]

				],
				RatingModel::TYPE_DOCTOR,
				false
			],
			//правильно
			[
				[
					[
						'ID' => 123,
						'KK' => 1,
						'PK' => 1,
						'CO' => 1,
					]

				],
				RatingModel::TYPE_CLINIC,
				true
			],
			//неправильно
			[
				[
					[
						'аID' => 123,
						'KK' => 1,
						'PK' => 1,
						'CO' => 1,
					]

				],
				RatingModel::TYPE_CLINIC,
				false
			],
			//правильно, но левый тип
			[
				[
					[
						'ID' => 123,
						'KB' => 1,
					]

				],
				'doctor_bla',
				false,
				true,
			],
		];
	}


}
