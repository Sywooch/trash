<?php

namespace dfs\tests\docdoc\models;

use dfs\docdoc\models\ClinicBillingModel;
use dfs\docdoc\models\ClinicContractModel;
use dfs\docdoc\models\ClinicModel;
use dfs\docdoc\models\ClinicPaymentModel;
use dfs\docdoc\models\RequestModel;
use CDbTestCase;
use Yii;

/**
 * Class ClinicPaymentModelTest
 *
 * @package dfs\tests\docdoc\models
 */
class ClinicPaymentModelTest extends CDbTestCase
{
	public function setUp()
	{
		$this->getFixtureManager()->checkIntegrity(false);
		$this->getFixtureManager()->truncateTable('clinic_billing');
		$this->getFixtureManager()->truncateTable('clinic_payment');
		$this->getFixtureManager()->loadFixture('clinic_billing');
		$this->getFixtureManager()->loadFixture('clinic_payment');
	}

	/**
	 * новые поступления поступления
	 *
	 * @dataProvider paymentsData
	 */
	public function testCreatePayments($billingId, $payments, $checkAttr)
	{
		foreach ($payments as $p) {
			$payment = new ClinicPaymentModel();
			$p['clinic_billing_id'] = $billingId;
			$payment->setAttributes($p);
			$this->assertTrue($payment->save());
		}

		$billing = ClinicBillingModel::model()->findByPk($billingId);

		foreach ($checkAttr as $k => $v) {
			$this->assertEquals($v, $billing->$k);
		}
	}

	public function paymentsData()
	{
		return [
			//поступление полная сумма
			[
				1,
				[
					['sum' => 1000]
				],
				[
					'recieved_sum' => 1000,
					'status' => ClinicBillingModel::STATUS_CLOSED,
				]
			],
			//поступление нескольких сумм
			[
				1,
				[
					['sum' => 500.01],
					['sum' => 499.99],
				],
				[
					'recieved_sum' => 1000,
					'status' => ClinicBillingModel::STATUS_CLOSED,
				]
			],
			//неполное поступление
			[
				1,
				[
					['sum' => 500.01],
				],
				[
					'recieved_sum' => 500.01,
					'status' => ClinicBillingModel::STATUS_WAITING_PAYMENT,
				]
			],
		];
	}

	/**
	 * изменение поступления
	 *
	 * @dataProvider paymentsData
	 */
	public function testUpdatePayments()
	{
		$payment = ClinicPaymentModel::model()->findByPk(2);
		$payment->sum = 1000;
		$payment->save();

		$billing = ClinicBillingModel::model()->findByPk(2);

		$this->assertEquals($billing->recieved_sum, 1000);
		$this->assertEquals($billing->status, ClinicBillingModel::STATUS_CLOSED);
	}

	/**
	 * удаление поступления
	 *
	 * @dataProvider paymentsData
	 */
	public function testDeletePayments()
	{
		$payment = ClinicPaymentModel::model()->findByPk(2);
		$payment->delete();

		$billing = ClinicBillingModel::model()->findByPk(2);

		$this->assertEquals($billing->recieved_sum, 0);
		$this->assertEquals($billing->status, ClinicBillingModel::STATUS_WAITING_PAYMENT);
	}

}
