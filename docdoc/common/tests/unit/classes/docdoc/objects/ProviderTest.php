<?php
/**
 * Created by PhpStorm.
 * User: a.tyutyunnikov
 * Date: 22.07.14
 * Time: 15:26
 */

namespace dfs\tests\docdoc\models;

use dfs\docdoc\objects\call\Provider;
use dfs\docdoc\objects\call\Uiscom;

class ProviderTest extends \CDbTestCase
{
	/**
	 * @var bool
	 */
	private static $configured = false;

	/**
	 * грузим один раз
	 */
	public function setUp()
	{
		if(!self::$configured){
			$fm =$this->getFixtureManager();

			$fm->checkIntegrity(false);
			$fm->truncateTable('clinic_partner_phone');
			$fm->truncateTable('request_record');
			$fm->truncateTable('clinic');
			$fm->truncateTable('partner');
			$fm->truncateTable('phone');
			$fm->truncateTable('call_log');

			$fm->loadFixture('clinic_partner_phone');
			$fm->loadFixture('request_record');
			$fm->loadFixture('clinic');
			$fm->loadFixture('partner');
			$fm->loadFixture('phone');
			$fm->loadFixture('call_log');

			self::$configured = true;
		}

	}

	/**
	 * @dataProvider createRecordDataProvider
	 */
	public function testCreateRecord($file, $data)
	{
		$provider = Provider::findById($data['pid']);
		$record = $provider->createRecord($file);

		$this->assertEquals($record->replaced_phone, $data['replaced_phone']);

		$this->assertEquals($record->clinic_id, $data['clinic_id']);
	}

	/**
	 * @return array
	 */
	public function createRecordDataProvider()
	{
		return [
			['2014-02-05_09.50.43,940_from_79163000482_to_74956410606_conv.mp3', ['replaced_phone' => '74951234567', 'clinic_id' => 1, 'pid' => 10]],
			['2015-03-19_105811.411611095_74953681090_74997050794_74956728787_pid16.mp3', ['replaced_phone' => '74997050794', 'clinic_id' => 2, 'pid' => 13]],
		];
	}

	/**
	 * Тест ошибки если подменный телефон не найден
	 *
	 * @expectedException \PHPUnit_Framework_Error
	 *
	 * @dataProvider triggerErrorDataProvider
	 */
	public function testTriggerErrorWhenReplacedPhoneIdNotFound($file)
	{
		$provider = Provider::findById(10);
		$provider->createRecord($file);
	}

	/**
	 * @return array
	 */
	public function triggerErrorDataProvider()
	{
		return [
			['2013-01-05_09.50.43,940_from_74951234567_to_74955404656_conv.mp3', ['replaced_phone' => null, 'clinic_id' => null]],
			['2014-02-05_09.50.43,940_from_79163001234_to_74956410606_conv.mp3', ['replaced_phone' => null, 'clinic_id' => 1]],
			['p_2014-12-05_09.50.43,940_from_79163000482_to_74955404656_conv.mp3', ['replaced_phone' => null, 'clinic_id' => null]],
			['1401889564.20777.mp3', ['replaced_phone' => null, 'clinic_id' => null]],
		];
	}

	/**
	 * Тест определения продолжительности аудиозаписи
	 */
	public function testGetDuration()
	{
		$provider = new Uiscom();
		// Запись из заявки #199236 с неверной продолжительностью 6 сек, верное значение - 152
		$duration = $provider->getDuration('common/tests/unit/files/p_2014-07-14_16.52.27,149_from_79262569996_to_74954530353_conv.mp3');
		$this->assertEquals(152, $duration);
	}
}
