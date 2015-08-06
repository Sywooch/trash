<?php
/**
 * Created by PhpStorm.
 * User: a.tyutyunnikov
 * Date: 12.11.14
 * Time: 19:35
 */

namespace dfs\back\tests\ui\api\rest;

use CurlResponse;
use dfs\docdoc\models\DoctorModel;

class API_v104Test extends API_v103Test
{
	protected $cityListStruct = ['Id', 'Name', 'Alias', 'Phone'];

	/**
	 * Структура специальности
	 *
	 * @var string[]
	 */
	protected $specialityListStruct = ['Id', 'Name', 'Alias'];

	protected $_diagnosticListStruct = ['Id', 'Name', 'Alias', 'SubDiagnosticList'];

	protected $_subdiagnosticsListStruct = ['Id', 'Name', 'Alias'];

	protected $_clinicListStruct2 = ['Stations', 'Specialities'];

	/**
	 * Версия апи для curl
	 *
	 * @return string
	 */
	protected function getVersion()
	{
		return '1.0.4';
	}

	/**
	 * Пример массива для /doctor/$id
	 *
	 * @return array
	 */
	protected function getDoctorStruct()
	{
		$struct = parent::getDoctorStruct();
		$struct['Alias'] = 'alias';
		$struct['Specialities'] = [];
		$struct['Stations'] = [];
		return $struct;
	}

	/**
	 * Станция метро для /metro/*
	 *
	 * @return array
	 */
	public function getMetroStationStruct()
	{
		$struct = parent::getMetroStationStruct();
		$struct['Alias'] = 'alias';
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
			'TextAbout' => 'text_about',
			'InternalRating' => 'internalRating',
			'OpinionCount' => 'opinion_count'
		];
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
			'MinPrice' => 'min_price',
			'MaxPrice' => 'max_price',
			'Logo' => 'logo',
		];

		$isDiagnostic && $struct['Diagnostics'] = [];

		$struct['Stations'] = [];
		$struct['Specialities'] = [];

		return $struct;
	}

	public function requestCreateDataProvider()
	{
		$data = parent::requestCreateDataProvider();

		$data['wrong_doctor'] = [
			['name' => 'Тест1', 'phone' => '89898988989', 'doctor' => 9151, 'comment' => 'коментарий'],
			function (CurlResponse $resp) {
				$this->assertEquals('{"Response":{"status":"error","message":"Не передана клиника"}}', $resp->body);
			}
		];

		$data['request_to_doctor'] = [
			['name' => 'Тест1', 'phone' => '89898988989', 'doctor' => 9151, 'clinic' => '13', 'comment' => 'коментарий'],
			function (CurlResponse $resp) {
				$this->assertEquals('{"Response":{"status":"success","message":"Заявка принята"}}', $resp->body);
			}
		];

		return $data;
	}

	/**
	 * Получение статистики
	 *
	 * @param array $params
	 * @dataProvider getStatDataProvider
	 */
	public function testGetStat(array $params)
	{
		$path = '/stat' . $this->build4pu($params);
		$resp = $this->openWithCheck($path);
		$data = json_decode($resp, true);
		$actual = array_keys($data);
		$this->assertEquals(['Requests', 'Doctors', 'Reviews'], $actual);
	}

	/**
	 * Данные для статистики
	 *
	 * @return array
	 */
	public function getStatDataProvider()
	{
		return [
			[[]],
			[['city' => 1]],
			[['city' => 1231]],
			[['city' => 'Moscow']],
		];
	}

	/**
	 * Округа москвы
	 */
	public function testAreaList()
	{
		$path = '/area';
		$resp = $this->openWithCheck($path);
		//проверка на формат ответа
		$r = json_decode($resp->body, true);
		$this->structListTest(['Id' => 1, 'Alias' => 'alias', 'Name' => 'name', 'FullName' => 'fullName'], $r, 'AreaList');
	}

	/**
	 * Получение списка районов
	 *
	 * @param array $params
	 * @param bool $empty
	 * @param callable $checkFunction
	 * @dataProvider districtListDataProvider
	 */
	public function testDistrictList(array $params, $empty = false, callable $checkFunction = null)
	{
		$path = '/district' . $this->build4pu($params);
		$resp = $this->openWithCheck($path);
		$data = json_decode($resp, true);
		if($checkFunction){
			$checkFunction($data);
		} else {
			$this->structListTest(['Id' => 1, 'Alias' => 'alias', 'Name' => 'name', 'Area' => []], $data, 'DistrictList', $empty);
		}

	}

	/**
	 * Данные для testDistrictList
	 * @return array
	 */
	public function districtListDataProvider()
	{
		return [
			[['city' => 1, 'area' => 1]],
			[['city' => 'Moscow', 'area' => 1], true],
			[['city' => 1, 'area' => 'area'], true],
			[
				['city' => 2, 'area' => 'area'],
				false,
				function($data){
					$this->assertTrue(in_array('DistrictList', array_keys($data)));
					foreach($data['DistrictList'] as $d){
						if($res = array_diff(array_keys(['Id' => 1, 'Alias' => 'alias', 'Name' => 'name', 'Area' => []]), array_keys($d))){
							if(count($res) !== 1 && array_values($res) !==['Area'] ){
								$this->fail('Структруа /district/area/area не сходится');
							}
						}
					}
				}
			],
			[['city' => 1]],
			[['city' => 'Moscow'], true],
			[
				['area' => 'area'],
				false,
				function($data){
					$this->assertTrue(in_array('DistrictList', array_keys($data)));
					foreach($data['DistrictList'] as $d){
						if($res = array_diff(array_keys(['Id' => 1, 'Alias' => 'alias', 'Name' => 'name', 'Area' => []]), array_keys($d))){
							if(count($res) !== 1 && array_values($res) !==['Area'] ){
								$this->fail('Структруа /district/area/area не сходится');
							}
						}
					}
				}
			],
		];
	}

	/**
	 * @param string $alias
	 * @param bool $empty
	 *
	 * @dataProvider doctorByAliasDataProvider
	 */
	public function testDoctorByAlias($alias, $empty)
	{
		if($alias == 'real'){
			$doctor = DoctorModel::model()->active()->find();
			$alias = $doctor->rewrite_name;
		}
		$path = '/doctor/by/alias/' . $alias;
		$resp = $this->openWithCheck($path);
		$this->structTest($this->getDoctorStruct(), json_decode($resp, true), 'Doctor', $empty);
	}

	/**
	 * Данные для testDoctorView
	 *
	 * @return array
	 */
	public function doctorByAliasDataProvider()
	{
		return [
			[123, true],
			['Gadya_Hrenova', true],
			['real', false],
			['', false],
			[-11, true],
		];
	}
} 
