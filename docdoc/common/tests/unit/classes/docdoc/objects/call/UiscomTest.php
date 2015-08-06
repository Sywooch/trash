<?php
/**
 * Created by PhpStorm.
 * User: atyutyunnikov
 * Date: 20.03.15
 * Time: 16:21
 */

namespace dfs\tests\docdoc\objects\call;

use dfs\docdoc\objects\call\Uiscom;

class UiscomTest extends \CTestCase
{
	/**
	 * @return Uiscom
	 */
	protected function getObject()
	{
		return new Uiscom();
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
				['caller_phone' => '79163000482', 'destination_phone' => '74955404656', 'partner_id' => null, 'ctime' => '2014-02-05 09:17:10']
			],
			[
				'2014-02-05_091710.758_79163000482_79132224433_74955404656.mp3',
				['caller_phone' => null, 'destination_phone' => null, 'partner_id' => null, 'ctime' => null]
			],
			[
				'2014-02-05_091710.758_79163000482_79132224433_74955404656_pid132.mp3',
				['caller_phone' => null, 'destination_phone' => null, 'partner_id' => null, 'ctime' => null]
			],
			[
				'1366705689.9666.mp3',
				['caller_phone' => null, 'destination_phone' => null, 'partner_id' => null, 'ctime' => null]
			],
			[
				'1366705689.9666.mp3',
				['caller_phone' => null, 'destination_phone' => null, 'partner_id' => null, 'ctime' => null]
			],
			[
				'162318_1425561798.31_79167509307_749912345678_74991234567_pid123.mp3',
				['caller_phone' => null, 'destination_phone' => null, 'partner_id' => null, 'ctime' => null]
			],
			[
				'091710.758_1391544000_79163000482_79132224433_74955404656.mp3',
				['caller_phone' => null, 'destination_phone' => null, 'partner_id' => null, 'ctime' => null]
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
	 *
	 * @dataProvider filesDataProvider
	 */
	public function testGetCallId($file)
	{
		$this->assertEquals($this->getObject()->getCallId($file), null);
	}
}
