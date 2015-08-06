<?php

namespace dfs\tests\docdoc\models;

use dfs\docdoc\models\PhoneProviderModel;
use dfs\docdoc\models\PhoneModel;
use dfs\docdoc\models\ClinicModel;
use CDbTestCase;
use dfs\docdoc\models\DoctorClinicModel;
use dfs\docdoc\models\DoctorModel;
use dfs\docdoc\models\RatingModel;
use dfs\docdoc\models\RatingStrategyModel;
use dfs\docdoc\models\RequestModel;
use Yii;

/**
 * Class ClinicModelTest
 *
 * @package dfs\tests\docdoc\models
 */
class ClinicModelTest extends CDbTestCase
{
	/**
	 * Тест на создание и изменение модели
	 *
	 * @dataProvider clinicData
	 *
	 * @param string   $scenario      Тестируемый сценарий
	 * @param array    $attributes    Атрибуты
	 * @param callable $checkFunction Валидаторы
	 *
	 * @throws \CException
	 */
	public function testClinic($scenario, array $attributes, callable $checkFunction)
	{
		$this->getFixtureManager()->checkIntegrity(false);
		$this->getFixtureManager()->truncateTable(ClinicModel::model()->tableName());

		$this->getFixtureManager()->truncateTable(PhoneProviderModel::model()->tableName());
		$this->getFixtureManager()->loadFixture(PhoneProviderModel::model()->tableName());
		$this->getFixtureManager()->truncateTable(PhoneModel::model()->tableName());
		$this->getFixtureManager()->loadFixture(PhoneModel::model()->tableName());

		if ($scenario == 'insert') {
			$model = new ClinicModel();
		} else {
			$this->getFixtureManager()->loadFixture(ClinicModel::model()->tableName());
			$model = ClinicModel::model()->findByPk(1);
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
	public function clinicData()
	{
		return [
			// Создание записи
				[
					'insert',
					[
						'name' => '',
						'status' => null,
						'rewrite_name' => 'некорректное значение',
						'notify_emails' => 'not_email',
						'notify_phones' => 'not_phone'
					],
					function (ClinicModelTest $test, ClinicModel $model) {
						$num = count($model->getErrors());
						$test->assertEquals(5, $num, "Ожидается 5 ошибки. Возврашено {$num}");
					},
				],
				[
					'insert',
					[
						'name' => 'Новая клиника',
						'status' => 1,
						'phone' => '74951234567',
						'rewrite_name' => 'rewrite_name',
						'notify_emails' => 'test@test.ru,test2@test.com',
						'notify_phones' => '89099201111,89399293944'
					],
					function (ClinicModelTest $test, ClinicModel $model) {

						$test->assertFalse($model->hasErrors());

						$test->assertEquals('74951234567', $model->phone);
						$test->assertNull($model->asterisk_phone);
						$test->assertEquals('test@test.ru,test2@test.com', $model->notify_emails);
						$test->assertEquals('79099201111,79399293944', $model->notify_phones); //приведет телефон к нужному формату
					},
				],
			[
				'insert',
				[
					'name' => 'Новая клиника',
					'status' => 1,
					'phone' => '74951234567',
					'asterisk_phone' => '+7 (333) 333 - 33- 33', //нет в фикстурах
					'rewrite_name' => 'rewrite_name',
					'notify_emails' => 'test@test.ru,test2@test.com',
					'notify_phones' => '89099201111,89399293944'
				],
				function (ClinicModelTest $test, ClinicModel $model) {

					$test->assertTrue($model->hasErrors('asterisk_phone'));
					$test->assertEquals(1, count($model->getErrors('asterisk_phone')));
					$test->assertEquals('Подменный телефон не найден', $model->getErrors('asterisk_phone')[0]);

				},
			],
			[
				'insert',
				[
					'name' => 'Новая клиника',
					'status' => 1,
					'phone' => '74951234567',
					'asterisk_phone' => '+7 (495) 123 - 45- 67',//занят партнером
					'rewrite_name' => 'rewrite_name',
					'notify_emails' => 'test@test.ru,test2@test.com',
					'notify_phones' => '89099201111,89399293944'
				],
				function (ClinicModelTest $test, ClinicModel $model) {

					$test->assertTrue($model->hasErrors('asterisk_phone'));
					$test->assertEquals(1, count($model->getErrors('asterisk_phone')));
					$test->assertEquals('Подменный номер уже занят', $model->getErrors('asterisk_phone')[0]);
				},
			],
			];
	}

	/**
	 * Проверка поиска клиник по специальности врача
	 */
	public function testSearchBySpecialities()
	{
		$this->loadFixtures();

		$clinics = ClinicModel::model()->searchBySpecialities(array(1, 2))->findAll();
		$this->assertCount(3, $clinics);
	}

	/**
	 * Проверка выборки клиник с активными врачами
	 */
	public function testHavingActiveDoctors()
	{
		$this->loadFixtures();

		$clinics = ClinicModel::model()->havingActiveDoctors()->count();

		$num = Yii::app()->db->createCommand("
			SELECT COUNT(DISTINCT t.id) FROM `clinic` `t`
				INNER JOIN `doctor_4_clinic` `doctors_doctors` ON (`t`.`id` = `doctors_doctors`.`clinic_id`)
				INNER JOIN `doctor` `doctors` ON (`doctors`.`id` = `doctors_doctors`.`doctor_id`)
			WHERE (doctors.status = ".DoctorModel::STATUS_ACTIVE.")
		")->queryScalar();

		$this->assertEquals($clinics, $num);
	}

	/**
	 * проверка получения станций метро
	 */
	public function testGetStations()
	{
		$this->loadFixtures();

		$clinic = ClinicModel::model()->findByPk(1);
		$this->assertCount(3, $clinic->getStations());
	}

	/**
	 * проверка нахождения клиники в районе
	 */
	public function testInDistrict()
	{
		$this->loadFixtures();

		$clinic = ClinicModel::model()->inDistrict(1);
		$this->assertEquals(6, $clinic->count());

	}

	/**
	 * проверка нахождения клиники в районах
	 */
	public function testInDistricts()
	{
		$this->loadFixtures();

		$clinic = ClinicModel::model()->inDistricts(array(1, 2));
		$this->assertEquals(8, $clinic->count());

	}

	/**
	 * тестирование relation clinicDoctors
	 */
	public function testClinicDoctors()
	{
		$this->loadFixtures(true);
		$clinic = ClinicModel::model()->findByPk(1);

		$num = Yii::app()->db->createCommand("SELECt COUNT(*) FROM doctor_4_clinic WHERE clinic_id = 1 and type = " . DoctorClinicModel::TYPE_DOCTOR)->queryScalar();

		$this->assertEquals($num, count($clinic->doctors));
		$this->assertEquals($num, count($clinic->clinicDoctors));
	}

	/**
	 * проверка поиска по координатам
	 */
	public function testSearchByCoordinates()
	{
		$this->loadFixtures();
		$count = ClinicModel::model()->searchByCoordinates(7.767698, 55.4, 7.767699, 55.8)->count();
		$this->assertEquals(4, $count);
	}


	/**
	 * проверка сохранения внешнего идентификатора клиники
	 */
	public function testSaveClinicExternalId()
	{
		$this->loadFixtures();
		$clinic = ClinicModel::model()->findByPk(1);
		$src = $clinic->getAttributes();
		$clinic->name = "Новое название клиники";
		$clinic->saveClinicExternalId('new_external_id');

		$clinic = ClinicModel::model()->findByPk(1);
		$this->assertEquals('new_external_id', $clinic->external_id);
		$src['external_id'] = 'new_external_id';

		//проверяем, что изменилось значение только одного поля external_id
		$this->assertEquals($src, $clinic->getAttributes());
	}

	/**
	 * Проверка выборки платных объявлений по клиникам
	 *
	 * @dataProvider provideTestPaidItems
	 *
	 * @param array $diagnostics
	 * @param array $exceptionIds
	 * @param integer $cityId
	 * @param array $expected
	 */
	public function testPaidItems(array $diagnostics, array $exceptionIds, $cityId, array $expected)
	{
		$items = ClinicModel::model()
			->paidItems($diagnostics, $exceptionIds, $cityId)
			->findAll();

		$paidClinics = array();
		foreach ($items as $item) {
			$paidClinics[] = (int)$item->id;
		}

		sort($expected);
		sort($paidClinics);

		$this->assertEquals(json_encode($expected), json_encode($paidClinics));
	}

	/**
	 * Данные для testPaidItems
	 * @return array
	 */
	public function provideTestPaidItems()
	{
		return array(
			// Выбрана диагностика
			array(
				array(1),
				array(),
				1,
				array(1),
			),
			// Без диагностики
			array(
				array(),
				array(),
				1,
				array(1),
			)
		);
	}

	/**
	 * Подготовка данных
	 *
	 * @throws \CException
	 */
	public function loadFixtures()
	{
		$fm = $this->getFixtureManager();
		$fm->checkIntegrity(false);
		$fm->truncateTable('clinic');
		$fm->truncateTable('contract_dict');
		$fm->truncateTable('clinic_contract');
		$fm->truncateTable('clinic_contract_cost');

		$fm->truncateTable('doctor');
		$fm->truncateTable('doctor_4_clinic');
		$fm->truncateTable('doctor_sector');
		$fm->truncateTable('underground_station');
		$fm->truncateTable('underground_station_4_clinic');

		$fm->loadFixture('clinic');
		$fm->loadFixture('doctor');
		$fm->loadFixture('doctor_4_clinic');

		$fm->loadFixture('doctor_sector');
		$fm->loadFixture('underground_station');
		$fm->loadFixture('underground_station_4_clinic');

		$fm->loadFixture('contract_dict');
		$fm->loadFixture('clinic_contract');
		$fm->loadFixture('clinic_contract_cost');

	}


	/**
	 * тест связи с клиникой
	 *
	 * @dataProvider provideTestRelatedWithClinic
	 * @param array $data
	 * @param bool $result
	 */
	public function testRelatedWithClinic($data, $result)
	{
		$this->loadFixtures();

		$clinic = ClinicModel::model()->findByPk($data['clinic1']);
		$this->assertEquals($result, $clinic->relatedWithClinic($data['clinic2']));
	}

	/**
	 * Данные для теста связи клиник
	 *
	 * @return array
	 */
	public function provideTestRelatedWithClinic()
	{
		return array(
			// является филиалом головной клиники
			array(
				array('clinic1' => 10, 'clinic2' => 9),
				true
			),
			// является головной клиникой для второй
			array(
				array('clinic1' => 9, 'clinic2' => 10),
				true
			),
			// находится в одной группе
			array(
				array('clinic1' => 10, 'clinic2' => 11),
				true
			),
			// не связана с данной клиникой
			array(
				array('clinic1' => 1, 'clinic2' => 9),
				false
			),
			// нет клиники с таким id
			array(
				array('clinic1' => 1, 'clinic2' => 0),
				false
			),
			// клиники совпадают
			array(
				array
				('clinic1' => 9, 'clinic2' => 9),
				true
			),
			// обе клиники головные
			array(
				array
				('clinic1' => 9, 'clinic2' => 1),
				false
			),
		);
	}

	/**
	 * @dataProvider getRequestContractData
	 */
	public function testGetRequestContract($clinicId, $attributes, $contractId)
	{
		$this->loadFixtures();

		$request = new RequestModel();
		$request->setAttributes($attributes);
		$clinic = ClinicModel::model()->findByPk($clinicId);
		$contract = $clinic->getRequestContract($request);
		$this->assertEquals(
			$contractId,
			(!is_null($contractId)) ? $contract->contract_id : $contract
		);
	}

	/**
	 * Данные для GetRequestContract
	 */
	public function getRequestContractData()
	{
		return [
			[
				//клиника 1
				//Оплата за дошедших (800/1200/1500)
				1,
				[
					"kind" => RequestModel::KIND_DOCTOR,
				],
				1
			],
			[
				//клиника 1
				//диагностика Оплата за звонки
				1,
				[
					"req_type" => RequestModel::TYPE_CALL,
					"kind" => RequestModel::KIND_DIAGNOSTICS,
				],
				3
			],
			[
				//клиника 1
				//Диагностика. Онлайн-запись
				1,
				[
					"req_type" => RequestModel::TYPE_ONLINE_RECORD,
					"kind" => RequestModel::KIND_DIAGNOSTICS,
				],
				3
			],
			[
				//клиника 2
				//нет контракта на онлайн-диагностику, есть по дошедшим на диагностику
				2,
				[
					"req_type" => RequestModel::TYPE_ONLINE_RECORD,
					"kind" => RequestModel::KIND_DIAGNOSTICS,
				],
				4
			],
			[
				//клиника 5
				//котнракт родительской клиники
				5,
				[
					"req_type" => RequestModel::TYPE_ONLINE_RECORD,
					"kind" => RequestModel::KIND_DIAGNOSTICS,
				],
				3
			],
		];
	}

	/**
	 * Обновление рейтинга после сохранения
	 *
	 * @throws \CException
	 */
	public function testRecalcRatingAfterSave()
	{
		$this->getFixtureManager()->checkIntegrity(false);
		$this->getFixtureManager()->truncateTable('clinic');
		$this->getFixtureManager()->loadFixture('clinic');
		$this->getFixtureManager()->truncateTable('rating');
		$this->getFixtureManager()->loadFixture('rating');
		$this->getFixtureManager()->truncateTable('rating_strategy');
		$this->getFixtureManager()->loadFixture('rating_strategy');

		$clinic = ClinicModel::model()->findByPk(1);
		$ratingStrategy = RatingStrategyModel::model()->findByPk(1);

		$rating = RatingModel::model()->byObject($clinic->id, RatingModel::TYPE_CLINIC)
			->byStrategy($ratingStrategy->id)
			->find();

		$expected = $rating->rating_value;

		$clinic->rating_total += 1;
		$clinic->save();

		$newRating = RatingModel::model()->byObject($clinic->id, RatingModel::TYPE_CLINIC)
			->byStrategy($ratingStrategy->id)
			->find();

		$actual = $newRating->rating_value;

		$this->assertNotEquals($expected, $actual);
	}

	/**
	 * Поиск клиники по улице
	 *
	 */
	public function testInNearestStreet()
	{
		$fm = $this->getFixtureManager();
		$fm->truncateTable('street_dict');
		$fm->truncateTable('clinic');
		$fm->loadFixture('street_dict');
		$fm->loadFixture('clinic');
		$this->assertEquals(4,  ClinicModel::model()->inNearestStreet(2)->count());
	}

	/**
	 * Поиск ближайших клиник
	 *
	 */
	public function testNearestClinic()
	{
		$fm = $this->getFixtureManager();
		$fm->truncateTable('clinic');
		$fm->loadFixture('clinic');

		$clinic = ClinicModel::model()->findByPk(1);
		$clinics = $clinic->nearestClinics(100);
		$this->assertEquals(3,  count($clinics));

		// Проверяем поиск диагностических центров
		$clinics = $clinic->nearestClinics(100, true);
		$this->assertEquals(1,  count($clinics));
	}

}
