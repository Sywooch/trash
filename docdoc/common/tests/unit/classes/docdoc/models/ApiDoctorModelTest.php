<?php

namespace dfs\tests\docdoc\models;

use dfs\docdoc\models\ApiDoctorModel;
use CDbTestCase;
use dfs\docdoc\models\DoctorClinicModel;
use Yii;
use dfs\docdoc\api\clinic\ClinicApiClient;

/**
 * Class ApiDoctorModelTest
 *
 * @package dfs\tests\docdoc\models
 */
class ApiDoctorModelTest extends CDbTestCase
{


	/**
	 * Подготовка данных
	 *
	 * @throws \CException
	 */
	public function setUp()
	{
		$fm = $this->getFixtureManager();
		$fm->checkIntegrity(false);
		$fm->truncateTable('api_doctor');
		$fm->truncateTable('clinic');
		$fm->truncateTable('doctor_4_clinic');
		$fm->truncateTable('doctor');

		$fm->loadFixture('api_doctor');
		$fm->loadFixture('clinic');
		$fm->loadFixture('doctor_4_clinic');
		$fm->loadFixture('doctor');
	}

	/**
	 * Тестирование соханения клиник, полученных из API клиники
	 */
	public function testMergeWithApiDoctor()
	{
		$apiDoctor = ApiDoctorModel::model()->findByPk(3);
		//есть такой доктор
		$this->assertTrue(DoctorClinicModel::model()->mergeWithApiDoctor($apiDoctor));
		$this->assertEquals(3, DoctorClinicModel::model()->findByPk(7)->doc_external_id);
		$this->assertNotNull(ApiDoctorModel::model()->findByPk(3)->doctorClinic);

		//нет такого доктора
		$apiDoctor = ApiDoctorModel::model()->findByPk(2);
		$this->assertFalse(DoctorClinicModel::model()->mergeWithApiDoctor($apiDoctor));
	}

	/**
	 * Тест выключения всех докторов в клинике
	 */
	public function testDisableAllByClinic()
	{
		$apiDoctors = ApiDoctorModel::model()->byClinic('external_clinic_id_2')->enabled()->findAll();
		$this->assertTrue(count($apiDoctors) > 0);

		ApiDoctorModel::model()->disableByClinic('external_clinic_id_2');

		$apiDoctors = ApiDoctorModel::model()->byClinic('external_clinic_id_2')->enabled()->findAll();
		$this->assertTrue(count($apiDoctors) == 0);
	}

	/**
	 * Тест выключения всех докторов в клинике
	 */
	public function testDisableAllByClinicInvert()
	{
		$apiDoctors = ApiDoctorModel::model()->byClinic('external_clinic_id_2')->enabled()->findAll();
		$this->assertTrue(count($apiDoctors) > 0);

		ApiDoctorModel::model()->disableByClinic('external_clinic_id_312312312312312312312312', true);

		$apiDoctors = ApiDoctorModel::model()->byClinic('external_clinic_id_2')->enabled()->findAll();
		$this->assertTrue(count($apiDoctors) == 0);
	}

	/**
	 * Тестирование соханения клиник, полученных из API клиники
	 */
	public function testSaveDoctorsFromApi()
	{
		$this->getFixtureManager()->truncateTable('api_doctor');

		//грузим докторов
		$api_url = ROOT_PATH . "/common/tests/unit/data/api/clinic/getDoctors.json";
		$jsonRpcClient = new ClinicApiClient($api_url);
		$jsonRpcClient->setId('abc');
		$doctors = $jsonRpcClient->getResources(['123']);

		ApiDoctorModel::model()->saveResourcesFromApi($doctors);

		$num = ApiDoctorModel::model()->count();
		$this->assertEquals(count($doctors), $num);

		//проверяем, что повторно одни и те же клиники не сохраняются
		ApiDoctorModel::model()->saveResourcesFromApi($doctors);
		$this->assertEquals($num, ApiDoctorModel::model()->count());

	}

}
