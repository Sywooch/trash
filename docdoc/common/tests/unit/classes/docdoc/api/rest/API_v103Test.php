<?php

namespace dfs\tests\docdoc\api\rest;

use dfs\docdoc\api\components\ApiFactory;
use PHPUnit_Framework_Constraint_IsType;

/**
 * Class API_v103Test
 *
 * @package dfs\tests\docdoc\api\rest
 */
class API_v103Test extends API_v102Test
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
		$api = ApiFactory::getApi('/api/rest/1.0.3/json/clinic/list' . $request, ["partnerId" => 1]);
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
	 * Запросы на количество
	 *
	 * @return array
	 */
	public function provideClinicRequest()
	{
		return array(
			// Проверяем получение клиники по умолчанию, все активные клиники
			array(
				'/start/0/count/10/city/1',
				4,
			),

			// Проверяем, что выбирается только клиника
			array(
				'/start/0/count/10/city/1/clinicType/1',
				3,
			),

			// Проверка что выбирается только диагностический центр
			array(
				'/start/0/count/10/city/1/clinicType/2',
				2,
			),

			// Проверка что выбирается только частный врач
			array(
				'/start/0/count/10/city/1/clinicType/3',
				1,
			),

			// Проверка что выбираются клиника и частный врач
			array(
				'/start/0/count/10/city/1/clinicType/1,3',
				4,
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
		$api = ApiFactory::getApi('/api/rest/1.0.3/json' . $request, $params);
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

		$parentData['clinicList'] = [
			'/clinic/list/start/0/count/1/city/1/type/1',
			'{"Total":4,"ClinicList":[{"Id":1,"Name":"Клиника \u21161","ShortName":"Клиника \u21161","RewriteName":"clinica_11","URL":"http://www.clinicanomer1.ru","Longitude":"55.675702","Latitude":"37.767699","City":"Москва","Street":"Краснодарская улица","House":"д. 52, корп. 2","Description":"Многопрофильный медицинский центр, специализирующийся на проведении диагностического обследования взрослых и детей от 14 лет. Клиника расположена в шаговой близости от метро Люблино (5-10 мин.) Прием происходит по предварительной записи по многоканальному телефону +7 (495) 988-01-64.","WeekdaysOpen":null,"WeekendOpen":null,"ShortDescription":"Многопрофильный медицинский центр, специализирующийся на проведении диагностического обследования взрослых и детей от 14 лет. Клиника расположена в шаговой близости от метро Люблино (5-10 мин.) Прием происходит по предварительной записи по многоканальному телефону +7 (495) 988-01-64.","IsDiagnostic":"yes","isClinic":"yes","IsDoctor":"no","Phone":"74956410606","PhoneAppointment":"+7 (495) 641-06-06","logoPath":"1.png","ScheduleState":"enable","Email":"","ReplacementPhone":"79000000777","Logo":"http://docdoc.ru/upload/kliniki/logo/1.png","Diagnostics":[{"Id":"1","Name":"УЗИ (ультразвуковое исследование)","Price":"4500.00","SpecialPrice":"0.00"}],"Stations":[{"Id":"1","Name":"Авиамоторная","LineName":null,"LineColor":null,"CityId":null},{"Id":"2","Name":"Автозаводская","LineName":"Замоскворецкая","LineColor":"0a6f20","CityId":"1"},{"Id":"3","Name":"Академическая","LineName":null,"LineColor":null,"CityId":null}]}]}',
			['partnerId' => 1]
		];

		return $parentData;

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
		$api = ApiFactory::getApi('/api/rest/1.0.3/json/request', $params);
		$this->assertEquals($response, $api->getRowResult());
	}

} 
