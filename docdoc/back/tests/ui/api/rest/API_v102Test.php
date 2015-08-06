<?php
/**
 * Created by PhpStorm.
 * User: a.tyutyunnikov
 * Date: 12.11.14
 * Time: 19:34
 */

namespace dfs\back\tests\ui\api\rest;


class API_v102Test extends API_v101Test
{
	/**
	 * Версия апи для curl
	 *
	 * @return string
	 */
	protected function getVersion()
	{
		return '1.0.2';
	}

	/**
	 * Пример массива для /doctor/$id
	 *
	 * @return array
	 */
	protected function getDoctorStruct()
	{
		$struct = parent::getDoctorStruct();
		$struct['Clinics'] = [];
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
			'Clinics' => [],
			'Degree' => 'degree',
			'Rank' => 'rank',
			'Specialities' => [],
			'Stations' => []
		];
	}

	/**
	 * Получение полной информации о клинике
	 *
	 * @link https://docdoc.atlassian.net/wiki/pages/viewpage.action?pageId=6783036#id-Версия1.0.2-Получениеполнойинформацииоклинике
	 *
	 * @param $id
	 * @param callable $checkFunction
	 * @dataProvider clinicDataProvider
	 */
	public function testClinic($id, callable $checkFunction)
	{
		$path = '/clinic/' . $id;
		$resp = $this->openWithCheck($path);
		$checkFunction($resp->body);
	}

	public function clinicDataProvider()
	{
		return [
			//реальный
			[1, function ($resp) {
				$this->structTest($this->getClinicStruct(), json_decode($resp, true), 'Clinic');;
			}],
			//текст вместо числа
			['text_id', function ($resp) {
				$this->assertEquals('{"Clinic":[[]]}', $resp);
			}],
			//пусто
			['', function ($resp) {
				$this->assertEquals('{"Clinic":[[]]}', $resp);
			}],
			//не существует
			[3, function ($resp) {
				$this->assertEquals('{"Clinic":[[]]}', $resp);
			}],
			//отрицательный
			[-11, function ($resp) {
				$this->assertEquals('{"Clinic":[[]]}', $resp);
			}],
		];
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
			'Description' => 'description',
			'House'       => 'house',
			'Phone'       => 'phone',
			'Logo'        => 'logo',
			'Doctors'     => [1, 2],
			'Longitude'   => '44',
			'Latitude'    => '55',
		];
	}
} 
