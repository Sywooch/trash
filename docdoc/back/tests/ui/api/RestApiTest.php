<?php
namespace dfs\back\tests\ui\api;

use CTestCase;
use Curl;

class RestApiTest extends CTestCase
{
	/**
	 * Версии API
	 *
	 * @var array
	 */
	private $_versions = ['1.0.0', '1.0.1', '1.0.2', '1.0.3', '1.0.4', '1.0.5'];

	/**
	 * Главный домен
	 *
	 * @return string
	 */
	private function getHost()
	{
		return \Yii::app()->getParams()['hosts']['back'];
	}

	/**
	 * Ссылка
	 *
	 * @return string
	 */
	private function getUrl()
	{
		return "https://test-partner:docdoc_test654@{$this->getHost()}/api/rest/";
	}

	/**
	 * Получение ответа на запрос
	 *
	 * @param string $requestPath
	 * @return \CurlResponse
	 */
	public function getResponse($requestPath)
	{
		$curl = new Curl;
		$curl->setAuth('test-partner', 'docdoc_test654');
		$curl->options = [
			'CURLOPT_SSL_VERIFYHOST' => false,
			'CURLOPT_SSL_VERIFYPEER' => false,
		];

		return $curl->get($this->getUrl() . $requestPath);
	}

	/**
	 * Отправка запроса методом post
	 *
	 * @param $requestPath
	 * @param $data
	 *
	 * @return mixed
	 */
	public function sendPostRequest($requestPath, $data)
	{
		$ch = curl_init();
		curl_setopt_array(
			$ch,
			[
				CURLOPT_URL            => $this->getUrl() . $requestPath,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_HEADER         => false,
				CURLOPT_SSL_VERIFYHOST => false,
				CURLOPT_SSL_VERIFYPEER => false,
				CURLOPT_HTTPHEADER     => array('Content-type: application/json'),
				CURLOPT_POSTFIELDS     => $data,
			]
		);

		return $response = curl_exec($ch);
	}

	/**
	 * Проверка создания заявок
	 *
	 * @param string $data
	 * @param string $result
	 * @dataProvider provideRequestCreate
	 */
	public function testRequestCreate($data, $result)
	{
		$response = $this->sendPostRequest("1.0.5/json/request", $data);
		$this->assertEquals($result, $response);
	}

	/**
	 * Запросы на создание заявок
	 *
	 * @return array
	 */
	public function provideRequestCreate()
	{
		return [
			// коректные заявки
			[
				json_encode([
						'name' => 'Тест Тест',
						'phone' => '79261234567',
						'clinic' => 1,
						'speciality' => 1,
						'stations' => [1],
						'departure' => 1,
						'age' => 'child',
						'comment' => 'Подбор врача',
				]),
				'{"Response":{"status":"success","message":"Заявка принята"}}'
			],
			[
				json_encode([
						'name' => 'Тест Тест',
						'phone' => '+79261234567',
						'clinic' => 1,
						'comment' => 'Запись в клинику',
				]),
				'{"Response":{"status":"success","message":"Заявка принята"}}'
			],

			// некоректные заявки
			[
				'',
				'{"Response":{"status":"error","message":"Не получены данные о заявке"}}'
			],
		];
	}

	/**
	 * Проверка работы api всех версий
	 *
	 * @param string $requestPath
	 * @param string $attr
	 * @param array $versions
	 * @dataProvider providerApiVersions
	 */
	public function testApiVersions($requestPath, $attr, $versions)
	{
		foreach ($this->_versions as $version) {
			if (empty($versions) || in_array($version, $versions)) {
				$response = $this->getResponse("{$version}/json{$requestPath}");
				$this->assertEquals(200, $response->headers['Status-Code']);
				$data = json_decode($response->body);
				if (!is_null($attr)) {
					$this->assertObjectHasAttribute($attr, $data);
				} else {
					$this->assertObjectHasAttribute('status', $data);
					$this->assertEquals('error', $data->status);
				}
			}
		}
	}

	/**
	 * Провайдер для проверки апи
	 *
	 * @return array
	 */
	public function providerApiVersions()
	{
		return [
			[
				'/city',
				'CityList',
				[],
			],
			[
				'/metro/city/1',
				'MetroList',
				[],
			],
			[
				'/speciality',
				'SpecList',
				[],
			],
			[
				'/diagnostic',
				'DiagnosticList',
				[],
			],
			[
				'/doctor/list/start/0/count/5/city/1/speciality/67/stations/1,2/near/mixed/',
				'DoctorList',
				[],
			],
			[
				'/doctor/list/start/0/count/5/city/10/',
				'DoctorList',
				[],
			],
			[
				'/clinic/list/start/1/count/5/city/1/stations/1,2/near/mixed/',
				'ClinicList',
				[],
			],
			[
				'/doctor/1652',
				'Doctor',
				[],
			],
			[
				'/clinic/10',
				'Clinic',
				[],
			],
			[
				'/review/doctor/167',
				'ReviewList',
				[],
			],
			[
				'/doctor/list/start/0/count/5/city/10/order/',
				null,
				['1.0.4'],
			],
			[
				'/stat/city/1',
				'Requests',
				['1.0.4'],
			],
			[
				'/doctor/by/alias/Zharuk_Elena',
				'Doctor',
				['1.0.4'],
			],
			[
				'/area',
				'AreaList',
				['1.0.4'],
			],
			[
				'/district',
				'DistrictList',
				['1.0.4'],
			],
		];
	}

	/**
	 * Проверка на произвольный порядок элементов
	 *
	 * @param $path1
	 * @param $path2
	 * @param $attr
	 * @dataProvider providerCountElements
	 */
	public function testCountElements($path1, $path2, $attr)
	{
		$obj1 = json_decode($this->getResponse("1.0.5/json{$path1}")->body);
		$obj2 = json_decode($this->getResponse("1.0.5/json{$path2}")->body);

		$this->assertObjectHasAttribute($attr, $obj1);
		$this->assertObjectHasAttribute($attr, $obj2);

		$count1 = count($obj1->$attr);
		$count2 = count($obj2->$attr);

		$this->assertEquals($count1, $count2);
	}

	/**
	 * @return array
	 */
	public function providerCountElements()
	{
		return [
			[
				'/doctor/list/start/0/count/5/city/1/speciality/67/stations/1,2/near/mixed/',
				'/doctor/list/start/0/count/5/city/1/speciality/67/stations/1,2/near/mixed/',
				'DoctorList',
			]
		];
	}

}