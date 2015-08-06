<?php
/**
 * Created by PhpStorm.
 * User: a.tyutyunnikov
 * Date: 10.11.14
 * Time: 19:51
 */

namespace dfs\back\tests\ui\api\rest;

use CurlResponse;

/**
 * Class API_v101Test
 * @package dfs\back\tests\ui\api\rest
 */
class API_v101Test extends BaseApiTestCase
{
	protected $cityListStruct = ['Id', 'Name'];

	/**
	 * Структура специальности
	 *
	 * @var string[]
	 */
	protected $specialityListStruct = ['Id', 'Name'];

	protected $_diagnosticListStruct = ['Id', 'Name', 'SubDiagnosticList'];

	protected $_subdiagnosticsListStruct = ['Id', 'Name'];

	/**
	 * Версия апи для curl
	 *
	 * @return string
	 */
	protected function getVersion()
	{
		return '1.0.1';
	}

	/**
	 * Тест аутентификации
	 *
	 * @param array $authParams
	 * @param int $code
	 *
	 * @dataProvider authenticationDataProvider
	 */
	public function testAuthentication(array $authParams, $code)
	{
		$resp = $this->open('', [], $authParams);
		$this->assertStatusCode($code, $resp);
	}

	/**
	 * Данные для аутентификации
	 *
	 * @return array
	 */
	public function authenticationDataProvider()
	{
		return [
			//норм реквизиты
			[['test-partner', 'docdoc_test654'], 200],
			//левые реквиизты
			[['big brother', 'watching you'], 401],
			//без реквизитов
			[[], 401]
		];
	}

	/**
	 * Пример массива для /doctor/$id
	 *
	 * @return array
	 */
	protected function getDoctorStruct()
	{
		return [
			'Id' => 1,
			'Name' => 'name',
			'Rating' => 'rating',
			'Sex' => 'sex',
			'Img' => 'img',
			'AddPhoneNumber' => 'number',
			'Category' => 'cat',
			'Degree' => '',
			'Rank' => 'rank',
			'Description' => 'desc',
			'TextEducation' => 'textEducation',
			'TextAssociation' => 'assoc',
			'TextDegree' => 'degree',
			'TextSpec' => 'spec',
			'TextCourse' => 'course',
			'TextExperience' => 'exp',
			'ExperienceYear' => 'year',
			'Price' => 'price',
			'SpecialPrice' => 'spec_price',
			'Departure' => 'departure'
		];
	}

	/**
	 * Станция метро для /metro/*
	 *
	 * @return array
	 */
	public function getMetroStationStruct()
	{
		return [
			'Id' => 1,
			'Name' => 'name',
			'LineName' => 'line',
			'LineColor' => 'color',
			'CityId' => 'cityId'
		];
	}

	/**
	 * Пример массива для /doctor/list
	 *
	 * @return array
	 */
	public function getDoctorListStruct()
	{
		return [
			'Id' => 1,
			'Name' => 'name',
			'Alias' => 'alias',
			'Rating' => 'rating',
			'InternalRating' => 'internalRating',
			'Price' => 'price',
			'SpecialPrice' => 'spec_price',
			'Sex' => 'sex',
			'Img' => 'img',
			'OpinionCount' => 'opinion_count',
			'TextAbout' => 'text_about',
			'ExperienceYear' => 'yea',
			'Departure' => 'departure',
			'Category' => 'cat',
			'Degree' => 'degree',
			'Rank' => 'rank',
			'Specialities' => [],
			'Stations' => []
		];
	}

	/**
	 * Пример массива для /clinic/list
	 *
	 * @return array
	 */
	public function getClinicListStruct($isDiagnostic = false)
	{
		$struct = [
			'Id' => 33,
			'Name' => 'name',
			'ShortName' => 'short name',
			'RewriteName' => 'rewrite name',
			'URL' => 'url',
			'Longitude' => 33,
			'Latitude' => 23,
			'City' => '23',
			'Street' => 'w23',
			'House' => 'house',
			'Description' => 'desc',
			'WeekdaysOpen' => 'weekdays',
			'WeekendOpen' => 'weekend',
			'ShortDescription' => 'shortdesk',
			'IsDiagnostic' => 'isDiagnostic',
			'isClinic' => 'yes',
			'IsDoctor' => 'no',
			'Phone' => 'phone',
			'PhoneAppointment' => 'f',
			'logoPath' => 'adf',
			'ScheduleState' => 'schedulestate',
			'Email' => 'asdf@asf.ru',
			'ReplacementPhone' => 'phone',
			'Logo' => 'logo',
		];

		$isDiagnostic && $struct['Diagnostics'] = [];

		$struct['Stations'] = [];

		return $struct;
	}

	/**
	 * Получение списка станций метро
	 *
	 * @param array $params
	 * @param bool $empty
	 * @param callable $checkFunction
	 *
	 * @dataProvider metroListDataProvider
	 * @link https://docdoc.atlassian.net/wiki/pages/viewpage.action?pageId=1310726#id-Версия1.0.1-Получениеспискастанцийметро
	 */
	public function testMetroList(array $params, $empty, callable $checkFunction = null)
	{
		$path = '/metro' . $this->build4pu($params);
		$resp = $this->openWithCheck($path);

		if($checkFunction){
			$checkFunction($resp);
		} else {
			$this->structListTest($this->getMetroStationStruct(), json_decode($resp, true), 'MetroList', $empty);
		}
	}

	/**
	 * Данные для testMetroList
	 *
	 * @return array
	 */
	public function metroListDataProvider()
	{
		return [
			//норм
			[['city' => 1], false],
			//не норм
			[['city' => 'Wrong_City_param'], true],
			//пусто
			[['city' => ''], null, function (CurlResponse $resp) {
				$this->assertEquals('{"status":"error","message":"Неправильная строка запроса"}', $resp->body);
			}],
			//без города
			[[], true],
		];
	}

	/**
	 * Получение списка городов
	 *
	 * @link https://docdoc.atlassian.net/wiki/pages/viewpage.action?pageId=1310726#id-Версия1.0.1-Получениеспискагородов
	 */
	public function testCityList()
	{
		$path = '/city';
		$resp = $this->openWithCheck($path);
		//проверка на формат ответа
		$r = json_decode($resp->body, true);
		$expected = $this->cityListStruct;
		$actual = array_keys(array_shift($r['CityList']));
		$this->assertEquals($expected, $actual);
	}

	/**
	 * Получение списка специальностей
	 *
	 * @link https://docdoc.atlassian.net/wiki/pages/viewpage.action?pageId=1310726#id-Версия1.0.1-Получениеспискаспециальностей
	 */
	public function testSpecialityList()
	{
		$path = '/speciality';
		$resp = $this->openWithCheck($path);
		//проверка на формат ответа
		$r = json_decode($resp->body, true);
		$expected = $this->specialityListStruct;
		$actual = array_keys(array_shift($r['SpecList']));
		$this->assertEquals($expected, $actual);
	}

	/**
	 * Получение списка городов
	 *
	 * @link https://docdoc.atlassian.net/wiki/pages/viewpage.action?pageId=1310726#id-Версия1.0.1-Получениеспискаспециальностей
	 */
	public function testDiagnosticList()
	{
		$path = '/diagnostic';
		$resp = $this->openWithCheck($path);
		//проверка на формат ответа
		$r = json_decode($resp->body, true);
		$expected = $this->_diagnosticListStruct;
		$actual = array_keys(array_shift($r['DiagnosticList']));
		$this->assertEquals($expected, $actual);


		//проверю формат субдиагностик
		$withSubDiagnostic = null;

		foreach ($r['DiagnosticList'] as $d) {
			if ($d['SubDiagnosticList']) {
				$withSubDiagnostic = $d['SubDiagnosticList'];
				break;
			}
		}

		$this->assertNotNull($withSubDiagnostic);

		$expected = $this->_subdiagnosticsListStruct;
		$actual = array_keys(array_shift($withSubDiagnostic));
		$this->assertEquals($expected, $actual);
	}

	/**
	 * Получение списка докторов
	 *
	 * @param array $params
	 * @param bool $empty
	 * @param callable $checkFunction
	 *
	 * @dataProvider doctorListDataProvider
	 * @link https://docdoc.atlassian.net/wiki/pages/viewpage.action?pageId=1310726#id-Версия1.0.1-Получениеспискаврачей
	 */
	public function testDoctorList(array $params, $empty = false, callable $checkFunction = null)
	{
		!isset($params['count']) && $params['count'] = 5; //всегда прошу по 5, ибо дефолт 500

		$path = '/doctor/list' . $this->build4pu($params);
		$resp = $this->openWithCheck($path);

		if($checkFunction){
			$checkFunction($resp);
		} else {
			$data = json_decode($resp, true);
			$this->assertTrue(array_key_exists('Total', $data));
			$this->assertEquals(!$empty, (bool)$data['Total']);
			unset($data['Total']);
			$this->structListTest($this->getDoctorListStruct(), $data, 'DoctorList', $empty);
		}
	}

	/**
	 * Данные для testDoctorList
	 *
	 * @return array
	 */
	public function doctorListDataProvider()
	{
		return [
			//норм
			[
				['start' => 0, 'count' => 5, 'city' => 1, 'speciality' => 67, 'stations' => '1,2', 'near' => 'mixed'],
				false
			],
			//без параметров, все параметры опционнальные
			[[], false],
			//city = ошибка
			[['city' => 'Uryupinsk'], true],
			//левая специальность
			[['speciality' => 'not_number'], false],
			[['stations' => '12, 0 , 0 , not_station,'], false],
			[['near' => 'first_born_unicorn'], false],
		];
	}

	/**
	 * Получение списка клиник
	 *
	 * @param array $params
	 * @param bool $empty
	 * @param callable $checkFunction
	 *
	 * @dataProvider clinicListDataProvider
	 * @link https://docdoc.atlassian.net/wiki/pages/viewpage.action?pageId=1310726#id-Версия1.0.1-Получениеспискаклиник
	 */
	public function testClinicList(array $params, $empty, callable $checkFunction = null)
	{
		!isset($params['count']) && $params['count'] = 5; //всегда прошу по 5, ибо дефолт 500

		$path = '/clinic/list' . $this->build4pu($params);
		$resp = $this->openWithCheck($path);

		if($checkFunction){
			$checkFunction($resp);
		} else {
			$data = json_decode($resp, true);
			$this->assertTrue(array_key_exists('Total', $data));
			$this->assertEquals(!$empty, (bool)$data['Total']);
			unset($data['Total']);
			$this->assertEquals(['ClinicList'], array_keys($data));

			foreach($data['ClinicList'] as $actual){
				$expected = $this->getClinicListStruct($actual['IsDiagnostic'] == 'yes');
				$this->structTest($expected, $actual, null, $empty);
			}
		}
	}

	/**
	 * Данные для testClinicList
	 *
	 * @return array
	 */
	public function clinicListDataProvider()
	{
		return [
			//норм
			[['start' => 0, 'count' => 5, 'city' => 1, 'stations' => '1,2', 'near' => 'mixed'], false],
			//без параметров, все параметры опционнальные
			[[], false],
			//city = ошибка
			[['city' => 'Uryupinsk'], true],
			[['stations' => '12, 0 , 0 , not_station, bubrovka'], false],
			[['near' => 'second_born_unicorn'], false],
		];
	}

	/**
	 * @param int $id
	 * @param bool $empty
	 *
	 * @dataProvider doctorViewDataProvider
	 */
	public function testDoctorView($id, $empty)
	{
		$path = '/doctor/' . $id;
		$resp = $this->openWithCheck($path);
		$this->structTest($this->getDoctorStruct(), json_decode($resp, true), 'Doctor', $empty);
	}

	/**
	 * Данные для testDoctorView
	 *
	 * @return array
	 */
	public function doctorViewDataProvider()
	{
		return [
			//реальный
			[123, false],
			//текст вместо числа
			['text_id', true],
			//пусто
			['', true],
			//не существует
			[1, true],
			//отрицательный
			[-11, true],
		];
	}

	/**
	 * @param int $id
	 * @param callable $checkFunction
	 *
	 * @dataProvider doctorReviewListDataProvider
	 */
	public function testDoctorReviewList($id, callable $checkFunction)
	{
		$path = '/review/doctor/' . $id;
		$resp = $this->openWithCheck($path);
		$checkFunction($resp);
	}

	/**
	 * Данные для testDoctorReviewList
	 *
	 * @return array
	 */
	public function doctorReviewListDataProvider()
	{
		return [
			//реальный
			[123, function (CurlResponse $resp) {
				$this->doctorReviewListStructTest(json_decode($resp->body, true), false);
			}],
			//текст вместо числа
			['text_id', function (CurlResponse $resp) {
				$this->doctorReviewListStructTest(json_decode($resp->body, true), true);
			}],
			//пусто
			['', function (CurlResponse $resp) {
				$this->badRequestTest(json_decode($resp->body, true));
			}],
			//не существует
			[1, function (CurlResponse $resp) {
				$this->doctorReviewListStructTest(json_decode($resp->body, true), true);
			}],
			//отрицательный
			[-11, function (CurlResponse $resp) {
				$this->doctorReviewListStructTest(json_decode($resp->body, true), true);
			}],
		];
	}

	protected function doctorReviewListStructTest($array, $isEmpty)
	{
		$this->assertEquals(['ReviewList'], array_keys($array));

		if (!$isEmpty) {
			$expected = ['Id', 'Client', 'RatingQlf', 'RatingAtt', 'RatingRoom', 'Text', 'Date', 'DoctorId'];
			$actual = array_keys(array_shift($array['ReviewList']));
			$this->assertEquals($expected, $actual);
		}
	}

	/**
	 * Неправильный запрос
	 *
	 * @param $array
	 */
	protected function badRequestTest($array)
	{
		$expected = ['status', 'message'];
		$actual = array_keys($array);
		$this->assertEquals($expected, $actual);

		$this->assertEquals("error", $array['status']);
		$this->assertEquals("Неправильная строка запроса", $array['message']);
	}

	/**
	 * Создание заявки
	 *
	 * @param array $params
	 * @param callable $checkFunction
	 * @dataProvider requestCreateDataProvider
	 */
	public function testRequestCreate(array $params, callable $checkFunction)
	{
		$path = '/request';
		$json = json_encode($params);
		$resp = $this->openWithCheck($path, $json);
		$checkFunction($resp);
	}

	/**
	 * Данные для testRequestCreate
	 *
	 * @return array
	 */
	public function requestCreateDataProvider()
	{
		return [
			//пусто
			'empty'             => [
				[],
				function (CurlResponse $resp) {
					$this->assertEquals('{"Response":{"status":"error","message":"Не получены данные о заявке"}}', $resp->body);
				}
			],
			//пустое имя
			'empty_name'        => [
				['name' => '', 'phone' => '89898988989', 'doctor' => null, 'comment' => 'коментарий'],
				function (CurlResponse $resp) {
					$this->assertEquals('{"Response":{"status":"error","message":"Необходимо ввести имя"}}', $resp->body);
				}
			],
			//без имени
			'without_name'      => [
				['phone' => '89898988989', 'doctor' => null, 'comment' => 'коментарий'],
				function (CurlResponse $resp) {
					$this->assertEquals('{"Response":{"status":"error","message":"Необходимо ввести имя"}}', $resp->body);
				}
			],

			//пустой телефон
			'empty_phone'       => [
				['name' => 'фыва', 'phone' => '', 'doctor' => null, 'comment' => 'коментарий'],
				function (CurlResponse $resp) {
					$this->assertEquals('{"Response":{"status":"error","message":"Введите номер телефона"}}', $resp->body);
				}
			],

			//без телефона
			'without_phone'     => [
				['name' => 'dafasf', 'doctor' => null, 'comment' => 'коментарий'],
				function (CurlResponse $resp) {
					$this->assertEquals('{"Response":{"status":"error","message":"Введите номер телефона"}}', $resp->body);
				}
			],

			//левый врач
			'wrong_doctor'      => [
				['name' => 'Тест1', 'phone' => '89898988989', 'doctor' => 1, 'comment' => 'коментарий'],
				function (CurlResponse $resp) {
					$this->assertEquals('{"Response":{"status":"error","message":"Нет такого врача в системе"}}', $resp->body);
				}
			],

			//Запись к врачу
			'request_to_doctor' => [
				['name' => 'Тест1', 'phone' => '89898988989', 'doctor' => 9151, 'comment' => 'коментарий'],
				function (CurlResponse $resp) {
					$this->assertEquals('{"Response":{"status":"success","message":"Заявка принята"}}', $resp->body);
				}
			],

			//Запись к клинику
			[
				['name' => 'Тест2', 'phone' => '89898988989', 'clinic' => 1, 'speciality' => 1, 'comment' => 'коментарий'],
				function (CurlResponse $resp) {
					$this->assertEquals('{"Response":{"status":"success","message":"Заявка принята"}}', $resp->body);
				}
			],
			//Запись к клинику с левыми данными
			[
				['name' => 'Тест22', 'phone' => '89898988989', 'clinic' => -1, 'speciality' => -11, 'comment' => 'коментарий'],
				function (CurlResponse $resp) {
					$this->assertEquals('{"Response":{"status":"success","message":"Заявка принята"}}', $resp->body);
				}
			],
			//Запись на онлайн диагностику
			[
				['name' => 'Тест3', 'phone' => '89898988989', 'clinic' => 1, 'diagnostics' => 1, 'subdiagnostics' => 1, 'dateAdmission' => '2014-11-11', 'comment' => 'коментарий'],
				function (CurlResponse $resp) {
					$this->assertEquals('{"Response":{"status":"success","message":"Заявка принята"}}', $resp->body);
				}
			],
			//Запись на онлайн диагностику с левыми данными
			[
				['name' => 'Тест3', 'phone' => '89898988989', 'clinic' => 1, 'diagnostics' => -1, 'subdiagnostics' => '', 'dateAdmission' => '', 'comment' => 'коментарий'],
				function (CurlResponse $resp) {
					$this->assertEquals('{"Response":{"status":"success","message":"Заявка принята"}}', $resp->body);
				}
			],
			//Подбор врача
			[
				['name' => 'Тест4', 'phone' => '89898988989', 'speciality' => 1, 'city' => 1, 'stations' => [1, 2, 3], 'departure' => 1, 'age' => 13, 'comment' => 'коментарий'],
				function (CurlResponse $resp) {
					$this->assertEquals('{"Response":{"status":"success","message":"Заявка принята"}}', $resp->body);
				}
			],
			//Подбор врача с левыми данными
			[
				['name' => 'Тест44', 'phone' => '89898988989', 'speciality' => '', 'city' => '', 'stations' => [1, -2, 'df'], 'departure' => -1, 'age' => 4, 'comment' => 'коментарий'],
				function (CurlResponse $resp) {
					$this->assertEquals('{"Response":{"status":"success","message":"Заявка принята"}}', $resp->body);
				}
			],
		];
	}
} 
