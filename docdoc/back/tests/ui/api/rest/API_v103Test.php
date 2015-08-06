<?php
/**
 * Created by PhpStorm.
 * User: a.tyutyunnikov
 * Date: 12.11.14
 * Time: 19:35
 */

namespace dfs\back\tests\ui\api\rest;


class API_v103Test extends API_v102Test
{
	/**
	 * Версия апи для curl
	 *
	 * @return string
	 */
	protected function getVersion()
	{
		return '1.0.3';
	}

	/**
	 * Кол-во клиник
	 *
	 * @param array $params
	 * @dataProvider clinicCountDataProvider
	 */
	public function testClinicCount(array $params)
	{
		$path = '/clinic/count' . $this->build4pu($params);
		$resp = $this->openWithCheck($path);
		$data = json_decode($resp, true);
		$actual = array_keys($data);
		$this->assertEquals(['Total', 'ClinicSelected'], $actual);
	}

	/**
	 * Данные для кол-ва клиник
	 *
	 * @return array
	 */
	public function clinicCountDataProvider()
	{
		return [
			[[]],
			[['city' => 0, 'type' => '1,3', 'stations' => '1,2']],
			[['type' => '1,3', 'stations' => '1,2']],
			[['type' => '1,3,letter', 'stations' => '1,2']],
			[['stations' => '1,2']],
			[['stations' => '1,2,letter']],
			[['city' => 0, 'type' => '1,3']],
		];
	}
} 
