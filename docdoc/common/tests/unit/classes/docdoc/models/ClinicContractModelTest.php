<?php

namespace dfs\tests\docdoc\models;

use dfs\docdoc\models\ClinicContractModel;
use dfs\docdoc\models\ClinicModel;
use dfs\docdoc\models\RequestModel;
use CDbTestCase;
use Yii;

/**
 * Class ClinicContractModelTest
 *
 * @package dfs\tests\docdoc\models
 */
class ClinicContractModelTest extends CDbTestCase
{
	public function loadFixtures()
	{
		$this->getFixtureManager()->checkIntegrity(false);
		$this->getFixtureManager()->truncateTable('request');
		$this->getFixtureManager()->truncateTable('doctor');
		$this->getFixtureManager()->truncateTable('clinic');
		$this->getFixtureManager()->truncateTable('partner');
		$this->getFixtureManager()->truncateTable('sector');
		$this->getFixtureManager()->truncateTable('doctor_sector');
		$this->getFixtureManager()->truncateTable('request_history');
		$this->getFixtureManager()->truncateTable('request_partner');
		$this->getFixtureManager()->truncateTable('city');
		$this->getFixtureManager()->truncateTable('doctor_4_clinic');
		$this->getFixtureManager()->truncateTable('contract_group');
		$this->getFixtureManager()->truncateTable('contract_group_service');
		$this->getFixtureManager()->truncateTable('clinic_contract');
		$this->getFixtureManager()->truncateTable('contract_dict');
		$this->getFixtureManager()->loadFixture('clinic');
		$this->getFixtureManager()->loadFixture('doctor');
		$this->getFixtureManager()->loadFixture('doctor_4_clinic');
		$this->getFixtureManager()->loadFixture('partner');
		$this->getFixtureManager()->loadFixture('sector');
		$this->getFixtureManager()->loadFixture('doctor_sector');
		$this->getFixtureManager()->loadFixture('city');
		$this->getFixtureManager()->loadFixture('contract_group');
		$this->getFixtureManager()->loadFixture('contract_group_service');
		$this->getFixtureManager()->loadFixture('request');
		$this->getFixtureManager()->loadFixture('clinic_contract');
		$this->getFixtureManager()->loadFixture('contract_dict');
	}

	/**
	 * Тестирование получения клиник для этого контракта
	 */
	public function testGetClinicsForContract()
	{
		$this->loadFixtures();
		//клиника с дочерним филиалом
		$c = ClinicContractModel::model()->findByPk(1);
		$this->assertCount(3, $c->getClinicsForContract());

		//клиника с одним филиалом
		$c = ClinicContractModel::model()->findByPk(2);
		$this->assertCount(1, $c->getClinicsForContract());

	}

	/**
	 * Проверка, что сбросились статусы у заявок за указанный период
	 */
	public function testResetBilling()
	{
		$this->loadFixtures();

		$exp = (new RequestModel())
			->inClinic(1)
			->inBilling([RequestModel::BILLING_STATUS_YES, RequestModel::BILLING_STATUS_NO])
			->count();

		$clinic = ClinicContractModel::model()->findByPk(1);
		$clinic->resetBilling('2014-01-01');

		$actual = (new RequestModel())
			->inClinic(1)
			->inBilling(RequestModel::BILLING_STATUS_NO)
			->count();

		$this->assertEquals($exp, $actual);
	}

	/**
	 * Проверяем, что закрылись счета по заявкам за указанный период
	 */
	public function testCloseBilling()
	{
		$this->loadFixtures();

		$request = new RequestModel();
		$request->client_phone = '74951234567';
		$request->clinic_id = 1;
		$request->save();
		$request->updateByPk($request->req_id,
			['clinic_id' => 9999, 'billing_status' => RequestModel::BILLING_STATUS_YES]
		);

		$exp = (new RequestModel())
			->inClinic(1)
			->inBilling([RequestModel::BILLING_STATUS_YES, RequestModel::BILLING_STATUS_PAID])
			->count();

		$clinic = ClinicContractModel::model()->findByPk(1);

		$inOtherClinicsBefore = (new RequestModel())
			->inBilling([RequestModel::BILLING_STATUS_YES])
			->inClinic(9999)
			->count();

		$clinic->closeBilling(null, '2014-12-01');

		$actual = (new RequestModel())
			->inClinic(1)
			->inBilling(RequestModel::BILLING_STATUS_PAID)
			->count();

		$inOtherClinicsAfter = (new RequestModel())
			->inBilling([RequestModel::BILLING_STATUS_YES])
			->inClinic(9999)
			->count();

		$this->assertEquals($exp, $actual);

		//проверяем, что изменились только в филиалах данной клиники
		$this->assertEquals($inOtherClinicsBefore, $inOtherClinicsAfter);
	}
}
