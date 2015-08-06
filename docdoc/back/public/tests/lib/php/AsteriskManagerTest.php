<?php

require_once LIB_PATH . '/asterisk/AsteriskManager.php';

/**
 * Class AsteriskManagerTest
 *
 * Тестируем класс управление астериском
 */
class AsteriskManagerTest extends PHPUnit_Framework_TestCase
{

	/**
	 * Тестируем обработку ошибок астериска
	 *
	 * @dataProvider providerMessages
	 *
	 * @param $message
	 * @param $result
	 *
	 *
	 * @return bool
	 */
	public function testErrorHandler($message, $result)
	{
		$params = array('server' => 'localhost');
		$ast = new Net_AsteriskManager($params);

		try {
			$ast->errorHandler($message);
		} catch (Exception $e) {
			$this->assertEquals($result, $e->getMessage());
			return true;
		}

		$this->fail("Не было исключения");
	}

	/**
	 * Сообщения для теста
	 *
	 * @return array
	 */
	public function providerMessages()
	{
		return array(
			array(
				'Response: Error; Message: No channel specified; Server: localhost',
				'Monitoring of channel failed: No channel specified'
			),
			array(
				'Response: Error; Message: Originate failed; Server: localhost',
				'Unexpected error: Originate failed'
			),
			array(
				false,
				'Server didn\'t respond as expected',
			),
		);
	}

}