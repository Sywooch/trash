<?php

namespace dfs\tests\docdoc\api\rest;

use dfs\docdoc\api\components\ApiFactory;
use PHPUnit_Framework_Constraint_IsType;

/**
 * Class API_v102Test
 *
 * @package dfs\tests\docdoc\api\rest
 */
class API_v102Test extends API_v101Test
{

	/**
	 * Тестируем получение клиник
	 *
	 * @dataProvider provideClinicRequest
	 *
	 * @param string $request
	 * @param int   $count
	 * @param int   $total
	 */
	public function testClinicList($request, $count, $total = null)
	{
		if ( is_null($total)) {
			$total = $count;
		}
		$api = ApiFactory::getApi('/api/rest/1.0.2/json/clinic/list' . $request);
		$clinics = json_decode($api->getRowResult());

		if (!is_null($total)) {
			$this->assertEquals($total, $clinics->Total);
		}
		$this->assertInternalType(PHPUnit_Framework_Constraint_IsType::TYPE_INT, $clinics->Total);
		$this->assertInternalType(PHPUnit_Framework_Constraint_IsType::TYPE_ARRAY, $clinics->ClinicList);
		$this->assertCount($count, $clinics->ClinicList);
		foreach($clinics->ClinicList as $clinic) {
			$this->assertClinic($clinic);
		}
	}

	/**
	 * Запросы на колличество
	 *
	 * @return array
	 */
	public function provideClinicRequest()
	{
		return array(
			// Получение только активных клиник с активными врачами
			array(
				'/start/0/count/10/city/1',
				3,
			),

			//  Тест проверка на получение клиник из Питера
			array(
				'/start/0/count/10/city/2',
				0,
			),

			// Тест проверка на получение клиник по станциям метро
			array(
				'/start/0/count/10/city/1/stations/1,2,3/near/strict',
				2,
			),

			// Тест start
			array(
				'/start/1/count/10/city/1/stations/1,2,3/near/strict',
				1, 2
			),

			// Тест count
			array(
				'/start/0/count/1/city/1/stations/1,2,3/near/strict',
				1, 2
			),
		);
	}


	/**
	 * Тестируем все сервисы
	 *
	 * @dataProvider provideTestData
	 *
	 * @param string $request
	 * @param string   $expected_json
	 * @param array $params
	 */
	public function testServices($request, $expected_json, array $params = [])
	{
		$api = ApiFactory::getApi('/api/rest/1.0.2/json' . $request);
		$res = $api->getRowResult();
		$actual = json_decode($res, true);
		$expected = json_decode($expected_json, true);

		$this->assertEquals($expected, $actual);
	}

	/**
	 * Запросы на количество
	 *
	 * @return array
	 */
	public function provideTestData()
	{
		$parentData = parent::provideTestData();

		$parentData['doctorView2'] = [
			'/doctor/2',
			'{"Doctor":[{"Id":2,"Name":"Николаев Николай Николаевич","Rating":"4.75","Sex":0,"Img":"http://docdoc.ru/img/doctorsNew/2_small.jpg","AddPhoneNumber":"","Category":"","Degree":"","Rank":"","Description":"Занимается лечением пациентов, имеющих эндокринную патологию репродуктивной системы; ведением беременности и родов при наличии акушерской патологии, после вспомогательных репродуктивных технологий, в том числе после ЭКО. Автор более чем 30 научных публикаций.","TextEducation":"Текст","TextAssociation":"","TextDegree":"","TextSpec":"Текст","TextCourse":"Сертификаты","TextExperience":"","ExperienceYear":' . $this->getExperience(1977) . ',"Price":2300,"SpecialPrice":0,"Departure":0,"Clinics":[1,2,3]}]}',

		];

		$parentData['doctorList'] = [
			'/doctor/list/start/0/count/1/city/1/speciality/1',
			'{"Total":12,"DoctorList":[{"Id":"2","Name":"Николаев Николай Николаевич","Alias":"Nikolaev_Nikolai","Rating":4.75,"InternalRating":"9.55","Price":"2300","SpecialPrice":null,"Sex":0,"Img":"http://docdoc.ru/img/doctorsNew/2_small.jpg","OpinionCount":2,"TextAbout":"Занимается лечением пациентов, имеющих эндокринную патологию репродуктивной системы; ведением беременности и родов при наличии акушерской патологии, после вспомогательных репродуктивных технологий, в том числе после ЭКО. Автор более чем 30 научных публикаций.","ExperienceYear":' . $this->getExperience(1977) . ',"Departure":"0","Category":null,"Clinics":[1,2,3],"Degree":null,"Rank":null,"Specialities":[{"Id":"1","Name":"Акушер-гинеколог"}],"Stations":[{"doctor_id":"2","Id":"1","Name":"Авиамоторная","Alias":"aviamotornaya","LineId":null,"LineName":null,"LineColor":null,"CityId":null},{"doctor_id":"2","Id":"2","Name":"Автозаводская","Alias":"avtozavodskaya","LineId":"1","LineName":"Замоскворецкая","LineColor":"0a6f20","CityId":"1"},{"doctor_id":"2","Id":"3","Name":"Академическая","Alias":"akademicheskaya","LineId":null,"LineName":null,"LineColor":null,"CityId":null}]}]}',

		];

		return $parentData;
	}

	/**
	 * Проверка структуры списка врачей и рейтинга
	 */
	public function testDoctorList()
	{
		$params = array(
			'dataType'  => 'json',
			'rawData'   => '',
			'start'     => 0,
			'count'     => 5,
			'city'      => 1,
		);

		$api = ApiFactory::getApi('/api/rest/1.0.2/json/doctor/list/start/0/count/5/city/1');
		$data = json_decode($api->getRowResult());

		$this->assertInternalType(PHPUnit_Framework_Constraint_IsType::TYPE_INT, $data->Total);
		$this->assertInternalType(PHPUnit_Framework_Constraint_IsType::TYPE_ARRAY, $data->DoctorList);
		foreach($data->DoctorList as $doctor) {
			$this->assertInternalType(PHPUnit_Framework_Constraint_IsType::TYPE_FLOAT, $doctor->Rating);
			$this->assertLessThanOrEqual(5, $doctor->Rating);
			$this->assertLessThan(5, strlen($doctor->Rating));
		}
	}

	/**
	 * Тестируем создание заявок
	 *
	 * @dataProvider provideRequestCreate
	 *
	 * @param array $params
	 * @param string   $response
	 */
	public function testRequestCreate($params, $response)
	{
		$api = ApiFactory::getApi('/api/rest/1.0.2/json/request', $params);
		$this->assertEquals($response, $api->getRowResult());
	}

} 
