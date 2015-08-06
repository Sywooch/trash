<?php
/**
 * Created by PhpStorm.
 * User: a.tyutyunnikov
 * Date: 15.07.14
 * Time: 10:44
 */

namespace dfs\tests\docdoc\models;

use dfs\docdoc\models\CallLogModel;
use dfs\docdoc\models\RequestRecordModel;

/**
 * Class CallLogModelTest
 *
 * @package dfs\tests\docdoc\models
 */
class CallLogModelTest extends \CDbTestCase
{
	/**
	 * грузим при старте каждого метода
	 */
	public function setUp()
	{
		$this->getFixtureManager()->truncateTable(CallLogModel::model()->tableName());
		$this->getFixtureManager()->loadFixture(CallLogModel::model()->tableName());
		parent::setUp();
	}
	/**
	 * Тест ошибки уникальности
	 */
	public function testUniqueRule()
	{

		$random = CallLogModel::model()->find();

		$copy = new CallLogModel('insert');
		$copy->attributes = $random->attributes;
		$copy->save();

		$this->assertEquals(count($copy->getErrors()), 1);
	}

	/**
	 * Тест поиска подменного телефона
	 */
	public function testGetReplacedPhoneForRecord()
	{
		$this->getFixtureManager()->truncateTable('request_record');
		$this->getFixtureManager()->loadFixture('request_record');

		$rr = RequestRecordModel::model()->findByPk(7);
		$phone = CallLogModel::getReplacedPhone($rr->getCallerPhone(), $rr->getDestinationPhone(), $rr->crDate);
		$this->assertEquals(strlen($phone), 11);
	}
}
