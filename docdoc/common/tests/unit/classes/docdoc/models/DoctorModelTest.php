<?php

namespace dfs\tests\docdoc\models;

use dfs\docdoc\models\ClinicModel;
use dfs\docdoc\models\DoctorModel;
use dfs\docdoc\models\RatingModel;
use dfs\docdoc\models\RatingStrategyModel;
use dfs\docdoc\models\RequestModel;
use dfs\docdoc\models\DoctorOpinionModel;
use dfs\docdoc\models\DoctorSectorModel;
use dfs\docdoc\models\DoctorClinicModel;
use dfs\docdoc\models\EducationDoctorModel;
use dfs\docdoc\models\LogBackUserModel;
use CDbTestCase;
use Yii;
use PHPUnit_Framework_Constraint_IsType;

/**
 * Class DoctorModelTest
 *
 * @package dfs\tests\docdoc\models
 */
class DoctorModelTest extends CDbTestCase
{
	/**
	 * при запуске каждого теста
	 */
	function setUp()
	{
		$this->getFixtureManager()->checkIntegrity(false);
		$this->getFixtureManager()->truncateTable('log');
		$this->getFixtureManager()->truncateTable('doctor');
		$this->getFixtureManager()->truncateTable('clinic');
		$this->getFixtureManager()->truncateTable('doctor_4_clinic');
		$this->getFixtureManager()->truncateTable('doctor_opinion');
		$this->getFixtureManager()->truncateTable('request');
		$this->getFixtureManager()->truncateTable('sector');
		$this->getFixtureManager()->truncateTable('doctor_sector');
		$this->getFixtureManager()->truncateTable('education_4_doctor');
		$this->getFixtureManager()->loadFixture('doctor');
		$this->getFixtureManager()->loadFixture('clinic');
		$this->getFixtureManager()->loadFixture('doctor_4_clinic');
		$this->getFixtureManager()->loadFixture('doctor_opinion');
		$this->getFixtureManager()->loadFixture('request');
		$this->getFixtureManager()->loadFixture('sector');
		$this->getFixtureManager()->loadFixture('doctor_sector');
		$this->getFixtureManager()->loadFixture('education_4_doctor');
	}

	/**
	 * Поиск врача по имени
	 */
	public function testByName()
	{
		$this->assertEquals(1, DoctorModel::model()->byName('Грук Светлана Михайловна')->count());
	}

	/**
	 * Поиск врача по имени
	 */
	public function testDoctorClinics()
	{
		$this->assertEquals(
			\Yii::app()->db->createCommand("SELECT COUNT(*) FROM doctor_4_clinic WHERE doctor_id = 1 and type = " . DoctorClinicModel::TYPE_DOCTOR)->queryScalar(),
			count(DoctorModel::model()->findByPk(1)->doctorClinics)
		);
	}

	/**
	 * Поиск врача в клинике
	 */
	public function testInClinics()
	{
		$this->assertEquals(
			\Yii::app()->db->createCommand("SELECT COUNT(DISTINCT doctor_id) FROM doctor_4_clinic WHERE clinic_id IN (1,2) and type = " . DoctorClinicModel::TYPE_DOCTOR)->queryScalar(),
			DoctorModel::model()->inClinics([1,2])->count()
		);

		$this->assertEquals(
			0,
			DoctorModel::model()->inClinics([])->count()
		);

		$this->assertEquals(
			\Yii::app()->db->createCommand("SELECT COUNT(DISTINCT doctor_id) FROM doctor_4_clinic WHERE clinic_id = 1 and type = " . DoctorClinicModel::TYPE_DOCTOR)->queryScalar(),
			DoctorModel::model()->inClinics([1])->count()
		);
	}

	/**
	 * Получение кол-ва опубликованных отзывов
	 */
	public function testGetOpinionCount()
	{
		$opinions = DoctorModel::model()
			->findByPk(2)
			->getOpinionCount();
		$this->assertEquals(2, $opinions);
		$this->assertInternalType(PHPUnit_Framework_Constraint_IsType::TYPE_INT, $opinions);
	}

	/**
	 * активные врачи
	 */
	public function testActive()
	{
		$docs = DoctorModel::model()->active()->findAll();
		$this->assertEquals(1, count($docs));

		foreach ($docs as $d) {
			$this->assertEquals(DoctorModel::STATUS_ACTIVE, $d->status);
		}
	}

	/**
	 * врачи в городе
	 */
	public function testInCity()
	{
		$docs = DoctorModel::model()->inCity(1)->findAll();
		$this->assertEquals(5, count($docs));

		$docs = DoctorModel::model()->inCity(999)->findAll();
		$this->assertEquals(0, count($docs));

	}

	/**
	 * Проверка удаления врача
	 */
	public function testDelete()
	{
		$this->getFixtureManager()->truncateTable('log_back_user');
		$this->getFixtureManager()->loadFixture('log_back_user');

		$doctor = DoctorModel::model()->findByPk(1);
		$this->assertEquals(true, $doctor->delete());
		$log = LogBackUserModel::model()->findAllByAttributes(array(
			'message' => 'Удаление врача id = 1',
		));
		$this->assertEquals(1, count($log));

		// Пробуем удалить активного врача
		$doctor = DoctorModel::model()->findByPk(2);
		$this->assertEquals(false, $doctor->delete());
	}

	/**
	 * Проверка удаления дублирующегося врача
	 */
	public function testDeleteAsDublicate()
	{
		$dublDoctorId = 1;
		$doctorId = 2;
		$doctor = DoctorModel::model()->findByPk($dublDoctorId);
		$doctor->deleteAsDublicate($doctorId);

		// Проверяем, что удалился врач и связанные данные
		$doctor = DoctorModel::model()->findByPk($dublDoctorId);
		$this->assertNull($doctor);
		$params = array('doctor_id' => $dublDoctorId);
		$this->assertEquals(0, count(DoctorClinicModel::model()->findAllByAttributes(array('doctor_id' => $dublDoctorId, 'type' => DoctorClinicModel::TYPE_DOCTOR))));
		$this->assertEquals(0, count(DoctorSectorModel::model()->findAllByAttributes($params)));
		$this->assertEquals(0, count(EducationDoctorModel::model()->findAllByAttributes($params)));

		// Проверяем перенос заявок
		$this->assertEquals(0, RequestModel::model()->byDoctor($dublDoctorId)->count());
		$this->assertEquals(2, RequestModel::model()->byDoctor($doctorId)->count());

		// Проверяем перенос отзывов
		$this->assertEquals(0, DoctorOpinionModel::model()->byDoctor($dublDoctorId)->count());
		$this->assertEquals(6, DoctorOpinionModel::model()->byDoctor($doctorId)->count());
	}

	/**
	 * Обновление рейтинга после сохранения
	 *
	 * @throws \CException
	 */
	public function testRecalcRatingAfterSave()
	{
		$this->getFixtureManager()->truncateTable('clinic');
		$this->getFixtureManager()->loadFixture('clinic');
		$this->getFixtureManager()->truncateTable('doctor');
		$this->getFixtureManager()->loadFixture('doctor');
		$this->getFixtureManager()->truncateTable('rating');
		$this->getFixtureManager()->loadFixture('rating');
		$this->getFixtureManager()->truncateTable('rating_strategy');
		$this->getFixtureManager()->loadFixture('rating_strategy');

		$doctor = DoctorModel::model()->findByPk(1);
		$ratingStrategy = RatingStrategyModel::model()->findByPk(1);

		foreach($doctor->doctorClinics as $object){
			$rating = RatingModel::model()->byObject($object->id, RatingModel::TYPE_DOCTOR)
				->byStrategy($ratingStrategy->id)
				->find();

			$expected = $rating->rating_value;

			$object->doctor->rating_internal += 1;
			$object->doctor->save();

			$newRating = RatingModel::model()->byObject($object->id, RatingModel::TYPE_DOCTOR)
				->byStrategy($ratingStrategy->id)
				->find();

			$actual = $newRating->rating_value;

			$this->assertNotEquals($expected, $actual);
		}


	}

	/**
	 * Загрузка фикстур
	 */
	public function loadFixtures()
	{
		$fm = $this->getFixtureManager();
		$fm->basePath = ROOT_PATH . '/common/tests/fixtures/doctor';

		$fm->checkIntegrity(false);
		$fm->truncateTable('clinic');
		$fm->truncateTable('doctor');
		$fm->truncateTable('doctor_4_clinic');
		$fm->truncateTable('doctor_sector');
		$fm->truncateTable('sector');
		$fm->truncateTable('underground_station_4_clinic');
		$fm->truncateTable('closest_station');
		$fm->truncateTable('rating');
		$fm->truncateTable('rating_strategy');
		$fm->loadFixture('clinic');
		$fm->loadFixture('doctor');
		$fm->loadFixture('doctor_4_clinic');
		$fm->loadFixture('doctor_sector');
		$fm->loadFixture('sector');
		$fm->loadFixture('underground_station_4_clinic');
		$fm->loadFixture('closest_station');
		$fm->loadFixture('rating');
		$fm->loadFixture('rating_strategy');

		$fm = $this->getFixtureManager();
		$fm->basePath = ROOT_PATH . "/common/tests/fixtures";
	}

	/**
	 * Тест метода получения докторов
	 *
	 * @dataProvider doctorDataProvider
	 * @param array $params
	 * @param array $expected
	 */
	public function testGetItems(array $params, array $expected)
	{
		$this->loadFixtures();

		$items = DoctorModel::model()->getItems($params);
		$ids = [];
		foreach ($items as $item) {
			$ids[] = (int)$item->id;
		}

		$this->assertEquals($expected, $ids);
	}

	/**
	 * @return array
	 */
	public function doctorDataProvider()
	{
		return [
			// в Москве
			[
				['city' => 1],
				[9, 6, 2, 5, 4, 8, 7, 14, 15, 13, 12, 10, 11],
			],
			// в Питере
			[
				['city' => 2],
				[2, 3],
			],
			// в Москве с сортировкой по внутреннему рейтингу и лимитом 10
			[
				['city' => 1, 'order' => 'rating_internal DESC', 'count' => 10],
				[9, 6, 2, 5, 4, 8, 7, 14, 15, 13],
			],
			// в Москве с сортировкой по внутреннему рейтингу и лимитом 10, начиная со 2
			[
				['city' => 1, 'order' => 'rating_internal DESC', 'count' => 10, 'start' => 2],
				[2, 5, 4, 8, 7, 14, 15, 13, 12, 10],
			],
			// в Москве по специальности
			[
				['city' => 1, 'speciality' => 2, 'order' => 'rating_internal DESC', 'count' => 10],
				[8, 7, 14, 15],
			],
			// в Москве по станциям метро, включая ближайшие
			[
				['city' => 1, 'stations' => [1, 2], 'order' => 'rating_internal DESC', 'count' => 10, 'nearest' => true],
				[2, 5, 4, 14, 15, 13, 9, 6, 8, 7],
			],
			// в Москве по станциям метро
			[
				['city' => 1, 'stations' => [1, 2], 'nearest' => false, 'order' => 'rating_internal DESC', 'count' => 10],
				[2, 5, 4, 14, 15, 13],
			],
			// в Москве по выбранным клиникам
			[
				['city' => 1, 'speciality' => 1, 'clinics' => [1], 'order' => 'rating_internal DESC', 'count' => 10],
				[2, 5, 4, 13],
			],
			// в Москве по специальности и по станции, включая лучшие
			[
				['city' => 1, 'speciality' => 1, 'stations' => [2], 'order' => 'rating_internal DESC', 'count' => 10, 'best' => true],
				[2, 5, 4, 13, 6, 9, 10, 11, 12],
			],
		];
	}

	/**
	 * @throws \CException
	 */
	public function testSetClinics()
	{
		$fm = $this->getFixtureManager();
		$fm->checkIntegrity(false);
		$fm->truncateTable('doctor');
		$fm->truncateTable('clinic');
		$fm->truncateTable('doctor_4_clinic');
		$fm->loadFixture('doctor');
		$fm->loadFixture('clinic');
		$fm->loadFixture('doctor_4_clinic');

		$doctor = DoctorModel::model()->findByPk(2);

		$clinicsId = array_map(
			function (ClinicModel $x){
				return $x->id;
			},
			$doctor->clinics
		);

		//для теста нужно больше одной клиники
		$this->assertTrue(count($clinicsId) > 1);

		$doctor4clinicsId = array_map(
			function(DoctorClinicModel $x){
				return $x->id;
			},
			$doctor->doctorClinics
		);

		$cr = new \CDbCriteria();
		$cr->select = 'id';
		$cr->addNotInCondition('id', $clinicsId);
		$cr->limit = 5;

		$anotherClinicIds = array_map(
			function(ClinicModel $x){
				return $x->id;
			},
			ClinicModel::model()->findAll($cr)
		);

		$clinicToDelete = array_shift($clinicsId);
		$newAllClinicsId = array_merge($anotherClinicIds, $clinicsId);

		$doctor->setClinics($newAllClinicsId);

		$newClinicsId = array_map(
			function (ClinicModel $x){
				return $x->id;
			},
			$doctor->clinics
		);

		$newDoctor4clinicsId = array_map(
			function(DoctorClinicModel $x){
				return $x->id;
			},
			$doctor->doctorClinics
		);

		//все сохраниились?
		$this->assertEquals(count($doctor->clinics), count($newAllClinicsId));

		//одного удалили. остальные связи должны остаться бо на них висит уже много чего
		$this->assertEquals(count($doctor4clinicsId) - 1, count(array_intersect($doctor4clinicsId, $newDoctor4clinicsId)));

		//клинику то удалили
		$this->assertFalse(in_array($clinicToDelete, $newClinicsId));

	}

}
