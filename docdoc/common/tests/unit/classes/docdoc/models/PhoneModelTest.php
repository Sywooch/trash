<?php

namespace dfs\tests\docdoc\models;

use dfs\docdoc\models\PhoneModel;
use CDbTestCase;
use Yii;

/**
 * Class PhoneModelTest
 *
 * @package dfs\tests\docdoc\models
 */
class PhoneModelTest extends CDbTestCase
{
	/**
	 * Тест на создание нового телефона
	 */
	public function testCreatePhone()
	{
		$this->getFixtureManager()->checkIntegrity(false);
		$this->getFixtureManager()->truncateTable(PhoneModel::model()->tableName());

		//новый телефон
		$phone = PhoneModel::model()->createPhone('74951234567');
		$this->assertNotNull($phone);

		//новый телефон
		$phone = PhoneModel::model()->createPhone('+7 (495) 765-43-21');
		$this->assertNotNull($phone);

		//некорректный телефон
		$phone = PhoneModel::model()->createPhone('7495123');
		$this->assertNull($phone);

		//повторно телефон не должен быть создан
		$phone = PhoneModel::model()->createPhone('+7 (495) 123-45-67');
		$this->assertEquals(2, $phone->count());

	}
}