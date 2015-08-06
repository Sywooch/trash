<?php

namespace dfs\tests\docdoc\objects;
use dfs\docdoc\objects\Phone;

/**
 * Class ObjectTest
 *
 * @package dfs\tests\docdoc\objects
 */
class PhoneTest extends \CTestCase
{
	/**
	 * Тест объекта телефон
	 */
	public function testPhone()
	{
		$phone = new Phone();

		$this->assertEquals(
			Phone::strToNumber('+7 (495) 123-45-67'),
			'74951234567'
		);

		$phone->setNumber('74951234567');
		$this->assertEquals(
			$phone->prettyFormat('+7 '),
			'+7 (495) 123-45-67'
		);
	}

	/**
	 * Тестируем телефоны в разных форматах
	 *
	 * @dataProvider validatorData
	 *
	 * @param string $phoneIn
	 * @param string $phoneOut
	 */
	public function testOutFormat($phoneIn, $phoneOut = '')
	{
		$phone = new Phone();
		$phone->setNumber($phoneIn);
		$this->assertEquals((bool) $phoneOut, $phone->isValid());
		if ($phoneOut) {
			$this->assertEquals($phoneOut, $phone->getNumber());
		}
	}

	/**
	 * Данные форматированных телефонов
	 *
	 * @return array
	 */
	public function validatorData()
	{
		return [
			['74951234567', '74951234567'],
			['84951234567', '74951234567'],
			['+7 (916) 811-25-64', '79168112564'],
			['8 (916) 123-45-66', '79161234566'],
			['8 916 1234566', '79161234566'],
			['84951234567', '74951234567'],
			['4951234567', '74951234567'],
			['(495) 123-45-67 ', '74951234567'],

			['12341234567'],
			['734951234567'],
		];
	}

}