<?php

namespace dfs\tests\docdoc\api\rest;

use dfs\docdoc\api\components\ApiFactory;
use PHPUnit_Framework_Constraint_IsType;

/**
 * Class API_v101Test
 *
 * @package dfs\tests\docdoc\api\rest
 */
class API_v101Test extends API_v100Test
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
		if (is_null($total)) {
			$total = $count;
		}
		$api = ApiFactory::getApi('/api/rest/1.0.1/json/clinic/list' . $request);
		$clinics = json_decode($api->getRowResult());

		$this->assertEquals($total, $clinics->Total);
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
	 * Тестируем создание заявок
	 *
	 * @dataProvider provideRequestCreate
	 *
	 * @param array $params
	 * @param string   $response
	 */
	public function testRequestCreate($params, $response)
	{
		$api = ApiFactory::getApi('/api/rest/1.0.1/json/request', $params);
		$this->assertEquals($response, $api->getRowResult());
	}

	/**
	 * Запросы на создание заявок
	 *
	 * @return array
	 */
	public function provideRequestCreate()
	{
		return array(
			// коректные заявки
			array(
				array(
					'dataType' => 'json',
					'partnerId' => 2,
					'rawData' => json_encode(array(
						'name' => 'Тест Тест',
						'phone' => '+79261234567',
						'doctor' => 1,
						'comment' => 'Запись к врачу',
					)),
				),
				'{"Response":{"status":"success","message":"Заявка принята"}}'
			),
			array(
				array(
					'dataType' => 'json',
					'partnerId' => 2,
					'rawData' => json_encode(array(
							'name' => 'Тест Тест',
							'phone' => '+79261234567',
							'clinic' => 1,
							'speciality' => 1,
							'comment' => 'Запись к клинику',
						)),
				),
				'{"Response":{"status":"success","message":"Заявка принята"}}'
			),
			array(
				array(
					'dataType' => 'json',
					'partnerId' => 2,
					'rawData' => json_encode(array(
							'name' => 'Тест Тест',
							'phone' => '79261234567',
							'clinic' => 1,
							'speciality' => 1,
							'stations' => array(1),
							'departure' => 1,
							'age' => 'child',
							'comment' => 'Подбор врача',
						)),
				),
				'{"Response":{"status":"success","message":"Заявка принята"}}'
			),
			array(
				array(
					'dataType' => 'json',
					'partnerId' => 2,
					'rawData' => json_encode(array(
						'name' => 'Тест Тест',
						'phone' => '+79261234567',
						'clinic' => 1,
						'comment' => 'Запись в клинику',
					)),
				),
				'{"Response":{"status":"success","message":"Заявка принята"}}'
			),

			// некоректные заявки
			array(
				array(
					'dataType' => 'json',
					'partnerId' => 1,
					'rawData' => '',
				),
				'{"Response":{"status":"error","message":"Не получены данные о заявке"}}'
			),
			array(
				array(
					'dataType' => 'json',
					'partnerId' => 1,
					'rawData' => json_encode(array(
							'name' => 'Тест Тест',
							'phone' => '123',
						)),
				),
				'{"Response":{"status":"error","message":"Некорректный формат номера телефона"}}'
			),
			// Запись к несуществующему врачу
			array(
				array(
					'dataType' => 'json',
					'partnerId' => 1,
					'rawData' => json_encode(array(
						'name' => 'Тест Тест',
						'phone' => '79261234567',
						'doctor' => 999999,
					)),
				),
				'{"Response":{"status":"error","message":"Нет такого врача в системе"}}'
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
	 *
	 */
	public function testServices($request, $expected_json, array $params = [])
	{
		$api = ApiFactory::getApi('/api/rest/1.0.1/json' . $request);
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
		return [
			'cityList' => [
				'/city',
				'{"CityList":[{"Id":"1","Name":"Москва"},{"Id":"2","Name":"Санкт-Петербург"}]}',

			],
			'metroList' => [
				'/metro/city/1',
				'{"MetroList":[{"Id":"2","Name":"Автозаводская","LineName":"Замоскворецкая","LineColor":"0a6f20","CityId":"1"},{"Id":"4","Name":"Александровский сад","LineName":"Филевская","LineColor":"0099cc","CityId":"1"}]}',

			],
			'diagnosticList' => [
				'/diagnostic',
				'{"DiagnosticList":[{"Id":"1","Name":"УЗИ (ультразвуковое исследование)","SubDiagnosticList":[{"Id":"71","Name":"печени"}]},{"Id":"19","Name":"КТ (компьютерная томография)","SubDiagnosticList":[]},{"Id":"138","Name":"Эндоскопические методы исследования","SubDiagnosticList":[{"Id":"139","Name":"Эзофагогастродуоденоскопия (ЭФГДС)"}]}]}',

			],
			'doctorView2' => [
				'/doctor/2',
				'{"Doctor":[{"Id":"2","Name":"Николаев Николай Николаевич","Rating":"4.75","Sex":0,"Img":"http://docdoc.ru/img/doctorsNew/2_small.jpg","AddPhoneNumber":"","Category":"","Degree":"","Rank":"","Description":"Занимается лечением пациентов, имеющих эндокринную патологию репродуктивной системы; ведением беременности и родов при наличии акушерской патологии, после вспомогательных репродуктивных технологий, в том числе после ЭКО. Автор более чем 30 научных публикаций.","TextEducation":"Текст","TextAssociation":"","TextDegree":"","TextSpec":"Текст","TextCourse":"Сертификаты","TextExperience":"","ExperienceYear":' . $this->getExperience(1977) . ',"Price":2300,"SpecialPrice":0,"Departure":0}]}',

			],
			'doctorList' => [
				'/doctor/list/start/0/count/1/city/1/speciality/1',
				'{"Total":12,"DoctorList":[{"Id":"2","Name":"Николаев Николай Николаевич","Alias":"Nikolaev_Nikolai","Rating":"4.75","InternalRating":"9.55","Price":"2300","SpecialPrice":null,"Sex":0,"Img":"http://docdoc.ru/img/doctorsNew/2_small.jpg","OpinionCount":2,"TextAbout":"Занимается лечением пациентов, имеющих эндокринную патологию репродуктивной системы; ведением беременности и родов при наличии акушерской патологии, после вспомогательных репродуктивных технологий, в том числе после ЭКО. Автор более чем 30 научных публикаций.","ExperienceYear":' . $this->getExperience(1977) . ',"Departure":"0","Category":null,"Degree":null,"Rank":null,"Specialities":[{"Id":"1","Name":"Акушер-гинеколог"}],"Stations":[{"doctor_id":"2","Id":"1","Name":"Авиамоторная","Alias":"aviamotornaya","LineId":null,"LineName":null,"LineColor":null,"CityId":null},{"doctor_id":"2","Id":"2","Name":"Автозаводская","Alias":"avtozavodskaya","LineId":"1","LineName":"Замоскворецкая","LineColor":"0a6f20","CityId":"1"},{"doctor_id":"2","Id":"3","Name":"Академическая","Alias":"akademicheskaya","LineId":null,"LineName":null,"LineColor":null,"CityId":null}]}]}',

			],
			'clinicList' => [
				'/clinic/list/start/0/count/1/city/1/type/1',
				'{"Total":3,"ClinicList":[{"Id":1,"Name":"Клиника \u21161","ShortName":"Клиника \u21161","RewriteName":"clinica_11","URL":"http://www.clinicanomer1.ru","Longitude":"55.675702","Latitude":"37.767699","City":"Москва","Street":"Краснодарская улица","House":"д. 52, корп. 2","Description":"Многопрофильный медицинский центр, специализирующийся на проведении диагностического обследования взрослых и детей от 14 лет. Клиника расположена в шаговой близости от метро Люблино (5-10 мин.) Прием происходит по предварительной записи по многоканальному телефону +7 (495) 988-01-64.","WeekdaysOpen":null,"WeekendOpen":null,"ShortDescription":"Многопрофильный медицинский центр, специализирующийся на проведении диагностического обследования взрослых и детей от 14 лет. Клиника расположена в шаговой близости от метро Люблино (5-10 мин.) Прием происходит по предварительной записи по многоканальному телефону +7 (495) 988-01-64.","IsDiagnostic":"yes","isClinic":"yes","IsDoctor":"no","Phone":"74956410606","PhoneAppointment":"+7 (495) 641-06-06","logoPath":"1.png","ScheduleState":"enable","Email":"","Logo":"http://docdoc.ru/upload/kliniki/logo/1.png","Diagnostics":[{"Id":"1","Name":"УЗИ (ультразвуковое исследование)","Price":"4500.00","SpecialPrice":"0.00"}],"Stations":[{"Id":"1","Name":"Авиамоторная","LineName":null,"LineColor":null,"CityId":null},{"Id":"2","Name":"Автозаводская","LineName":"Замоскворецкая","LineColor":"0a6f20","CityId":"1"},{"Id":"3","Name":"Академическая","LineName":null,"LineColor":null,"CityId":null}]}]}',
				['partnerId' => 1],
			],
		];

	}

}
