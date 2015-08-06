<?php
namespace dfs\tests\docdoc\models;

use dfs\docdoc\models\ClinicPartnerPhoneModel;

/**
 * Class ClinicPartnerPhoneModelTest
 *
 * @package dfs\tests\docdoc\models
 */
class ClinicPartnerPhoneModelTest extends \CDbTestCase
{

	/**
	 * Поиск партнера по подменному телефону
	 *
	 * @dataProvider phoneDataProvider
	 */
	public function testGetPartnerIdByPhone($phone, $result)
	{
		$this->getFixtureManager()->truncateTable('clinic_partner_phone');
		$this->getFixtureManager()->truncateTable('phone');

		$this->getFixtureManager()->loadFixture('phone');
		$this->getFixtureManager()->loadFixture('clinic_partner_phone');

		$clinicPartnerPhone = ClinicPartnerPhoneModel::model()->byPhone($phone)->find();

		$partnerId = $clinicPartnerPhone instanceof ClinicPartnerPhoneModel ? $clinicPartnerPhone->partner_id : null;

		$this->assertEquals($partnerId, $result);
	}

	/**
	 * Дата провайдер для testGetPartnerIdByPhone
	 *
	 * @return array
	 */
	public function phoneDataProvider()
	{
		return [
			['74951234567', 1],
			['74991634567', 2],
			['23423423423', null],
		];
	}
}
