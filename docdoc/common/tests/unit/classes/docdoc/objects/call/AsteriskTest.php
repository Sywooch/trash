<?php
/**
 * Created by PhpStorm.
 * User: atyutyunnikov
 * Date: 20.03.15
 * Time: 15:58
 */
namespace dfs\tests\docdoc\objects\call;

use dfs\docdoc\objects\call\Asterisk;

class AsteriskTest extends \CTestCase
{
	/**
	 * @return Asterisk
	 */
	protected function getObject()
	{
		return new Asterisk();
	}

	/**
	 * Данные для тестирования гетеров телефонов
	 *
	 * @return array
	 */
	public function filesDataProvider()
	{
		return [
			[
				'2014-02-05_09.17.10,758_from_79163000482_to_74955404656_conv.mp3',
				['caller_phone' => null, 'destination_phone' => null, 'partner_id' => null, 'ctime' => null, 'call_id' => null]
			],
			[
				'2014-02-05_091710.758_79163000482_79132224433_74955404656.mp3',
				['caller_phone' => '79163000482', 'destination_phone' => '74955404656', 'partner_id' => null, 'ctime' => '2014-02-05 9:17:10', 'call_id' => null]
			],
			[
				'2014-02-05_091710.758_79163000482_79132224433_74955404656_pid132.mp3',
				['caller_phone' => '79163000482', 'destination_phone' => '74955404656', 'partner_id' => 132, 'ctime' => '2014-02-05 9:17:10', 'call_id' => null]
			],
			[
				'1366705689.9666.mp3',
				['caller_phone' => null, 'destination_phone' => null, 'partner_id' => null, 'ctime' => null, 'call_id' => null]
			],
			[
				'1366705689.9666.mp3',
				['caller_phone' => null, 'destination_phone' => null, 'partner_id' => null, 'ctime' => null, 'call_id' => null]
			],
			[
				'162318_1425561798.31_79167509307_749912345678_74991234567_pid123.mp3',
				['caller_phone' => '79167509307', 'destination_phone' => '74991234567', 'partner_id' => 123, 'ctime' => '2015-03-05 16:23:18', 'call_id' => '1425561798.31']
			],
			[
				'091710.758_1391544000_79163000482_79132224433_74955404656.mp3',
				['caller_phone' => '79163000482', 'destination_phone' => '74955404656', 'partner_id' => null, 'ctime' => '2014-02-05 00:00:00', 'call_id' => '1391544000']
			],
		];
	}

	/**
	 * @param string $file
	 * @param array $params
	 *
	 * @dataProvider filesDataProvider
	 */
	public function testGetCallerPhone($file, $params)
	{
		$this->assertEquals($this->getObject()->getCallerPhone($file), $params['caller_phone']);
	}

	/**
	 * @param string $file
	 * @param array $params
	 *
	 * @dataProvider filesDataProvider
	 */
	public function testGetDestinationPhone($file, $params)
	{
		$this->assertEquals($this->getObject()->getDestinationPhone($file), $params['destination_phone']);
	}

	/**
	 * @param $file
	 * @param array $params
	 *
	 * @dataProvider filesDataProvider
	 */
	public function testGetPartnerId($file, $params)
	{
		$this->assertEquals($this->getObject()->getPartnerId($file), $params['partner_id']);
	}

	/**
	 * @param string $file
	 * @param string $params
	 *
	 * @dataProvider filesDataProvider
	 */
	public function testGetCreatedTime($file, $params)
	{
		$this->assertEquals($this->getObject()->getCreatedTime($file), $params['ctime']);
	}

	/**
	 * @param string $file
	 * @param string $params
	 *
	 * @dataProvider filesDataProvider
	 */
	public function testGetCallId($file, $params)
	{
		$this->assertEquals($this->getObject()->getCallId($file), $params['call_id']);
	}
}
