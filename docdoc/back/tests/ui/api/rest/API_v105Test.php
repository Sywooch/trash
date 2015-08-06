<?php
/**
 * Created by PhpStorm.
 * User: a.tyutyunnikov
 * Date: 12.11.14
 * Time: 19:36
 */

namespace dfs\back\tests\ui\api\rest;


class API_v105Test extends API_v104Test
{

	/**
	 * Структура списка городов
	 *
	 * @var string[]
	 */
	protected $cityListStruct = ['Id', 'Name', 'Alias', 'Phone', 'Latitude', 'Longitude'];

	/**
	 * Структура специальности
	 *
	 * @var string[]
	 */
	protected $specialityListStruct = [
		'Id',
		'Name',
		'Alias',
		'NameGenitive',
		'NamePlural',
		'NamePluralGenitive',
		'IsSimple'
	];

	/**
	 * @var string[]
	 */
	protected $_streetListStruct = ['Id', 'CityId', 'Title', 'RewriteName'];

	/**
	 * @var string[]
	 */
	protected $_stationListStruct = ['Id', 'Name', 'LineName', 'LineColor', 'CityId', 'Alias'];

	/**
	 * Структура списка районов
	 *
	 * @var string[]
	 */
	protected $districtListStruct = ['Id', 'Alias', 'Name', 'Area'];

	/**
	 * Версия апи для curl
	 *
	 * @return string
	 */
	protected function getVersion()
	{
		return '1.0.5';
	}

	/**
	 * Пример массива для /doctor/$id
	 *
	 * @return array
	 */
	protected function getDoctorStruct()
	{
		$struct = parent::getDoctorStruct();
		$struct['BookingClinics'] = [];
		$struct['isActive'] = true;
		return $struct;
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
			'Rating' => 'rating',
			'Sex' => 'sex',
			'Img' => 'img',
			'AddPhoneNumber' => 'phone',
			'Category' => 'cat',
			'Degree' => 'degree',
			'Rank' => 'rank',
			'Description' => 'desc',
			'ExperienceYear' => 'yea',
			'Price' => 'price',
			'SpecialPrice' => 'spec_price',
			'Departure' => 'departure',
			'Clinics' => [],
			'Alias' => 'alias',
			'Specialities' => [],
			'Stations' => [],
			'BookingClinics' => [],
			'isActive' => true,
			'TextAbout' => 'text_about',
			'InternalRating' => 'internalRating',
			'OpinionCount' => 'opinion_count',
			'Extra' => false,
		];
	}

	/**
	 * Получение списка  (негатив)
	 *
	 * @param array $params
	 * @param callable $checkFunction
	 * @dataProvider slotListDataProvider
	 */
	public function testBadSlotList(array $params, callable $checkFunction)
	{
		$path = '/slot/list' . $this->build4pu($params);
		$resp = $this->openWithCheck($path);
		$data = json_decode($resp, true);

		$checkFunction($data);
	}

	/**
	 * Данные для testSlotList
	 * @return array
	 */
	public function slotListDataProvider()
	{
		return [
			[
				['doctor' => 1, 'clinic' => 1, 'from' => '2014-08-01', 'to' => '2014-08-01'],
				function($data){
					$this->assertEquals(['SlotList'], array_keys($data));
					$this->assertEquals(0, count($data['SlotList']));
				}
			],
			[
				['doctor' => -1, 'clinic' => 'text', 'from' => '2014-08-01', 'to' => '2014-08-01'],
				function($data){
					$this->assertEquals(['SlotList'], array_keys($data));
					$this->assertEquals(0, count($data['SlotList']));
				}
			],
			[
				['doctor' => -1, 'clinic' => 'text', 'from' => '', 'to' => 'badFormat'],
				function($data){
					$this->assertEquals(['status', 'message'], array_keys($data));
					$this->assertEquals('error', $data['status']);
					$this->assertEquals('Неверный формат даты', $data['message']);
				}
			],
		];
	}

	//todo пока все выключено невозможно затестить
	/*public function testSuccessSlotList()
	{

	}*/

	/**
	 * Получение списка улиц города
	 */
	public function testStreetList()
	{
		$path = '/street/city/1';
		$resp = $this->openWithCheck($path);
		//проверка на формат ответа
		$r = json_decode($resp->body, true);
		$expected = $this->_streetListStruct;
		$actual = array_keys(array_shift($r['StreetList']));
		$this->assertEquals($expected, $actual);
	}

	/**
	 * Получение списка улиц города
	 */
	public function testNearestStationList()
	{
		$path = '/nearestStation/id/1';
		$resp = $this->openWithCheck($path);
		//проверка на формат ответа
		$r = json_decode($resp->body, true);
		$expected = $this->_stationListStruct;
		$actual = array_keys(array_shift($r['StationList']));
		$this->assertEquals($expected, $actual);
	}

	/**
	 * Получение списка ближайших регионов
	 */
	public function testNearDistrictsList()
	{
		$path = '/nearDistricts/id/1';
		$resp = $this->openWithCheck($path);
		$r = json_decode($resp->body, true);
		$expected = $this->districtListStruct;
		$actual = array_keys(array_shift($r['DistrictList']));
		$this->assertEquals($expected, $actual);
	}

	/**
	 * Пример массива для /clinic/list
	 *
	 * @param bool $isDiagnostic
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
			'StreetId' => '1',
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
			'DistrictId' => '7',
			'Email' => 'asdf@asf.ru',
			'ReplacementPhone' => 'phone',
			'MinPrice' => 'min_price',
			'MaxPrice' => 'max_price',
			'Logo' => 'logo',
		];

		$isDiagnostic && $struct['Diagnostics'] = [];

		$struct['Stations'] = [];
		$struct['Specialities'] = [];

		return $struct;
	}

	/**
	 * @return array
	 */
	public function getClinicStruct()
	{
		return [
			'Id'          => 1,
			'Name'        => 'NAME',
			'ShortName'   => 'short_name',
			'RewriteName' => 'rewrite_name',
			'Url'         => 'url',
			'City'        => 'city',
			'Street'      => 'street',
			'StreetId'    => '1',
			'Description' => 'description',
			'House'       => 'house',
			'Phone'       => 'phone',
			'Logo'        => 'logo',
			'DistrictId'  => '1',
			'Doctors'     => [1, 2],
			'Longitude'   => '44',
			'Latitude'    => '55',
		];
	}
}
