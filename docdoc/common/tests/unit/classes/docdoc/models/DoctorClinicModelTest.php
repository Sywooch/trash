<?php

namespace dfs\tests\docdoc\models;
use dfs\docdoc\models\DoctorClinicModel;
use CDbTestCase;
use dfs\docdoc\models\SlotModel;
use dfs\docdoc\api\clinic\ClinicApiClient;

/**
 * Class ClinicModelTest
 *
 * @package dfs\tests\docdoc\models
 */
class DoctorClinicModelTest extends CDbTestCase
{
	/**
	 * выполнять при запуске каждого теста
	 */
	public function setUp()
	{
		$this->getFixtureManager()->checkIntegrity(false);
		$this->getFixtureManager()->truncateTable(DoctorClinicModel::model()->tableName());
		$this->getFixtureManager()->truncateTable('clinic');
		$this->getFixtureManager()->truncateTable('doctor');
		$this->getFixtureManager()->loadFixture('clinic');
		$this->getFixtureManager()->loadFixture('doctor');
		$this->getFixtureManager()->loadFixture('doctor_4_clinic');
	}

	/**
	 * Тест правил валидации модели
	 *
	 * @dataProvider doctorClinicRulesProvider
	 *
	 * @param string $scenario
	 * @param array $attributes
	 * @param callable $checkFunction
	 */
	public function testDoctorClinic($scenario, $attributes, $checkFunction)
	{
		$this->getFixtureManager()->truncateTable('api_doctor');
		$this->getFixtureManager()->loadFixture('api_doctor');

		if ($scenario == 'insert') {
			$this->getFixtureManager()->truncateTable('doctor_4_clinic');
			$model = new DoctorClinicModel();
		} else {
			$model = DoctorClinicModel::model()->findByPk(1);
		}

		$model->attributes = $attributes;
		$model->save();

		$checkFunction($this, $model, $attributes);

	}

	/**
	 * Тест сохранения параметров расписания
	 *
	 */
	public function testUpdateRules()
	{
		$model = DoctorClinicModel::model()->findByPk(1);

		$attributes = array(
			'param2' => 'b',
			'param1' => "a",
			'schedule_step' => "60"
		);
		$model->saveScheduleRules($attributes);

		$this->assertEquals(serialize($attributes), $model->schedule_rule);
		$this->assertEquals($attributes['schedule_step'], $model->schedule_step);
	}

	/**
	 * Тест сохранения слотов
	 *
	 */
	public function testSaveSlotsFromSchedule()
	{
		$this->getFixtureManager()->truncateTable('slot');

		$model = DoctorClinicModel::model()->findByPk(1);

		$events = array(
			array(
				'start' => date('Y-m-d H:00:00', time() + 3600),
				'end' => date('Y-m-d H:00:00', time() + 3600 * 4),
			),
		);

		$model->saveSlotsFromSchedule($events);
		$this->assertEquals(3, SlotModel::model()->count());

		//проверяем, что сохранился флаг has_slots
		$this->assertEquals(1, $model->has_slots);

		//проверяем, что проставлено время последнего изменения слотов
		$this->assertNotEmpty($model->last_slots_update);

		//повторно сохраняем те же события + 1 новое. Должна быть 1 новая запись
		$events[] = array(
			'start' => date('Y-m-d H:00:00', time() + 3600 * 5),
			'end' => date('Y-m-d H:00:00', time() + 3600 * 6),
		);
		$model->saveSlotsFromSchedule($events);
		$this->assertEquals(4, SlotModel::model()->count());

		//три слота удаляем. Должен остаться один слот
		unset($events[0]);
		$model->saveSlotsFromSchedule($events);
		$this->assertEquals(1, SlotModel::model()->count());

		//Пытаемся добавить слот в прошлое. Ничего не должно измениться
		$events[] = array(
			'start' => date('Y-m-d H:00:00', time() - 3600 * 5),
			'end' => date('Y-m-d H:00:00', time() - 3600 * 4),
		);

		$model->saveSlotsFromSchedule($events);
		$this->assertEquals(1, SlotModel::model()->count());

		//создаем слот в прошлом, проверяем, что он не удален + добавлен новый слот
		$this->getFixtureManager()->truncateTable('slot');
		$model->saveSlotsFromSchedule($events);
		$this->assertEquals(1, SlotModel::model()->count());
	}


	/**
	 * Тест изменения продолжительности времени приема
	 *
	 */
	public function testChangeSlotDuration()
	{
		$this->getFixtureManager()->truncateTable('slot');
		$model = DoctorClinicModel::model()->findByPk(1);

		/************** кратные интервалы ***************/
		$events = array(
			array(
				'start' => date('Y-m-d H:00:00', time() + 3600),
				'end' => date('Y-m-d H:00:00', time() + 3600 * 4),
			),
		);
		$model->saveSlotsFromSchedule($events);

		//меняем с 60 на 30 мин
		$model->schedule_step = 30;
		$model->saveSlotsFromSchedule($events);

		$this->assertEquals(6, SlotModel::model()->count());

		//меняем с 30 на 45 мин
		$model->schedule_step = 45;
		$model->saveSlotsFromSchedule($events);
		$this->assertEquals(4, SlotModel::model()->count());

		//меняем с 45 на 90 мин
		$model->schedule_step = 90;
		$model->saveSlotsFromSchedule($events);
		$this->assertEquals(2, SlotModel::model()->count());


		//меняем с 90 на 30 мин
		$model->schedule_step = 30;
		$model->saveSlotsFromSchedule($events);
		$this->assertEquals(6, SlotModel::model()->count());

		/************** некратные интервалы ***************/
		$this->getFixtureManager()->truncateTable('slot');

		$events = array(
			array(
				'start' => date('Y-m-d H:00:00', time() + 3600),
				'end' => date('Y-m-d H:30:00', time() + 3600 * 4),
			),
		);
		$model->schedule_step = 30;
		$model->saveSlotsFromSchedule($events);
		$this->assertEquals(7, SlotModel::model()->count());

		$model->schedule_step = 45;
		$model->saveSlotsFromSchedule($events);
		$this->assertEquals(4, SlotModel::model()->count());

		$model->schedule_step = 23;
		$model->saveSlotsFromSchedule($events);
		$this->assertEquals(9, SlotModel::model()->count());
	}


	/**
	 * данные для создания записи
	 * @return array
	 */
	public function doctorClinicRulesProvider()
	{
		return array(
			array(
				'insert',
				array(
					'clinic_id' => 1,
				),
				function (CDbTestCase $test, DoctorClinicModel $model) {
					$test->assertTrue($model->hasErrors(), 'Создана запись при пустом doctor_id');
				},
			),
			array(
				'insert',
				array(
					'doctor_id' => 1,
				),
				function (CDbTestCase $test, DoctorClinicModel $model) {
					$test->assertTrue($model->hasErrors(), 'Создана запись при пустом clinic_id');
				},
			),
			array(
				'insert',
				array(
					'clinic_id' => 1,
					'doctor_id' => 1,
				),
				function (CDbTestCase $test, $model) {
					$test->assertNotNull($model->id, 'Ошибка при создании записи');
				},
			),
			array(
				'insert',
				array(
					'clinic_id' => 1,
					'doctor_id' => 1,
					'doc_external_id' => '1',
					'schedule_rule' => serialize(
						array(
							'item1'=>'aaa',
							'item2'=>'bbb'
						)
					),
				),
				function (CDbTestCase $test, $model, $attributes) {
					$test->assertNotNull($model->id, 'Ошибка при создании записи');

					foreach ($attributes as $k =>$v) {
						$test->assertEquals($model->$k, $v, "Аттрибут {$k}. {$model->$k} != {$v}");
					}
				},
			),
			//проверка XSS
			array(
				'insert',
				array(
					'clinic_id' => 1,
					'doctor_id' => 1,
					'schedule_rule' => '<b></b>',
					'doc_external_id' => '<b></b>',
				),
				function (CDbTestCase $test, $model, $attributes) {
					$test->assertNotEquals($model->schedule_rule, $attributes['schedule_rule'], 'schedule_rule. Не работает очистка от тегов');
					$test->assertNotEquals($model->doc_external_id, $attributes['doc_external_id'], 'doc_external_id. Не работает очистка от тегов');
				},
			),
			array(
				//проверка relations
				'insert',
				array(
					'clinic_id' => 1,
					'doctor_id' => 1,
				),
				function (CDbTestCase $test, $model) {
					$test->assertNotNull($model->clinic,  'Не найден clinic');
					$test->assertNotNull($model->doctor,  'Не найден doctor');
				},
			),
			array(
				//проверка запрета на изменение clinic_id,  doctor_id
				'update',
				array(
					'clinic_id' => 2,
					'doctor_id' => 2,
				),
				function (CDbTestCase $test, $model, $attributes) {
					foreach ($attributes as $k=>$v) {
						$test->assertNotEquals($model->$k, $v, "Нельзя изменить значение аттрибута {$k}. Значение изменено на {$v}");
					}
				},
			),
			array(
				//изменение записи
				'update',
				array(
					'doc_external_id' => 'new',
					'schedule_rule' => 'new',
					'schedule_step' => 2
				),
				function (CDbTestCase $test, $model, $attributes) {
					foreach ($attributes as $k =>$v) {
						$test->assertEquals($model->$k, $v, "Аттрибут {$k}. {$model->$k} != {$v}");
					}
				},
			),

		);

	}

	/**
	 * тест сохранения ID в МИС
	 */
	public function testSaveExternalId()
	{
		\Yii::app()
			->db
			->createCommand("update doctor_4_clinic set doc_external_id = null;")
			->query();

		$external_id = \Yii::app()
			->db
			->createCommand("SELECT id FROM api_doctor LIMIT 1")
			->queryScalar();

		$id = \Yii::app()
			->db
			->createCommand("SELECT id FROM doctor_4_clinic WHERE doc_external_id IS NULL LIMIT 1")
			->queryScalar();

		$doctorInClinic = DoctorClinicModel::model()->findByPk($id);
		$this->assertTrue($doctorInClinic->saveExternalId($external_id));
		$this->assertEquals($external_id, $doctorInClinic->doc_external_id);
	}

	/**
	 * тест получения идентификаторов клиник и врачей в МИС
	 */
	public function testGetBranchesExternalIds()
	{
		$docsWithId = DoctorClinicModel::model()->getDocExternalIds(9);
		$this->assertCount(1, $docsWithId);
	}

	/**
	 * проверка поиска в клинике по имени
	 */
	public function testInClinicByName()
	{
		$this->assertEquals(
			1,
			DoctorClinicModel::model()
				->inClinicByName(1, 'Грук Светлана Михайловна')
				->count()
		);

		$this->assertEquals(
			0,
			DoctorClinicModel::model()
				->inClinicByName(1, 'несуществующий доктор')
				->count()
		);
	}


	/**
	 * Тестирование сохранения слотов, полученных из API клиники
	 * @dataProvider saveSlotsFromApiData
	 *
	 * @param int $doctor_4_clinic_id
	 * @param \StdClass[] $slots
	 * @param callable $callback
	 */
	public function testSaveSlotsFromApi($doctor_4_clinic_id, $slots, $callback)
	{
		$this->getFixtureManager()->truncateTable('api_doctor');
		$this->getFixtureManager()->loadFixture('api_doctor');

		$this->getFixtureManager()->truncateTable('slot');

		DoctorClinicModel::model()->findByPk($doctor_4_clinic_id)->saveSlotsFromApi($slots);
		$callback($this, $slots);
	}

	/**
	 * dataProvider для testSaveSlotsFromApi
	 *
	 * @return array
	 */
	public function saveSlotsFromApiData()
	{
		return [
			[
				5,
				json_decode('[
					{
						"resourceId" : "external_clinic_id_2#external_id_1",
						"slotId"     : "external_clinic_id_2#slot_id_1",
						"attributes" : {
							"from" : "' . date('Y-m-d H:00:00', time() + 3600) .'",
							"to"   : "' . date('Y-m-d H:00:00', time() + 3600*1.5) .'"
						}
					},
					{
						"resourceId" : "external_clinic_id_2#external_id_1",
						"slotId"     : "external_clinic_id_2#slot_id_2",
						"attributes" : {
							"from" : "' . date('Y-m-d H:00:00', time() + 3600*2) .'",
							"to"   : "' .  date('Y-m-d H:00:00', time() + 3600*3) .'"
						}
					},
					{
						"resourceId" : "external_clinic_id_2#external_id_1",
						"slotId"     : "external_clinic_id_2#slot_id_3",
						"attributes" : {
							"from" : "' .  date('Y-m-d H:00:00', time() + 3600*3) .'",
							"to"   : "' .  date('Y-m-d H:00:00', time() + 3600*4.5) .'"
						}
					}
				]'),
				function (DoctorClinicModelTest $test, $slots) {
					$test->assertEquals(count($slots), SlotModel::model()->count());
				}
			],
		];

	}


	/**
	 * Тестирование загрузки слотов из API клиники
	 *
	 */
	public function testLoadSlotsFromApi()
	{
		$this->getFixtureManager()->truncateTable('api_doctor');
		$this->getFixtureManager()->loadFixture('api_doctor');

		$this->getFixtureManager()->truncateTable('slot');

		$api_url = ROOT_PATH . "/common/tests/unit/data/api/clinic/getSlots.json";
		$jsonRpcClient = new ClinicApiClient($api_url);
		$jsonRpcClient->setId('abc');
		$response = $jsonRpcClient->getSlots([]);

		DoctorClinicModel::model()->findByPk(5)->saveSlotsFromApi($response);

		$this->assertEquals(
			count($response),
			SlotModel::model()->count()
		);
	}
}
