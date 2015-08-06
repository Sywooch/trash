<?php

namespace dfs\tests\docdoc\models;

use dfs\docdoc\api\clinic\ClinicApiClient;
use dfs\docdoc\models\ApiClinicModel;
use dfs\docdoc\models\ApiDoctorModel;
use CDbTestCase;
use Yii;

/**
 * Class ApiClinicModelTest
 *
 * @package dfs\tests\docdoc\models
 */
class ApiClinicModelTest extends CDbTestCase
{
	/**
	 * Подготовка данных
	 *
	 * @throws \CException
	 */
	public function setUp()
	{
		$this->getFixtureManager()->checkIntegrity(false);
		$this->getFixtureManager()->truncateTable('api_clinic');
		$this->getFixtureManager()->truncateTable('api_doctor');
	}

	/**
	 * Тестирование соханения клиник, полученных из API клиники
	 */
	public function testSaveClinicFromApi()
	{
		$api_url = ROOT_PATH . "/common/tests/unit/data/api/clinic/getClinics.json";
		$jsonRpcClient = new ClinicApiClient($api_url);
		$jsonRpcClient->setId('abc');
		$clinics = $jsonRpcClient->getClinics([]);

		ApiClinicModel::model()->saveClinicsFromApi($clinics);

		$num = ApiClinicModel::model()->count();
		$this->assertEquals(count($clinics), $num);

		//проверяем, что повторно одни и те же клиники не сохраняются
		ApiClinicModel::model()->saveClinicsFromApi($clinics);
		$this->assertEquals($num, ApiClinicModel::model()->count());
	}
}