<?php
/**
 * Created by PhpStorm.
 * User: a.tyutyunnikov
 * Date: 17.09.14
 * Time: 16:38
 */

namespace dfs\tests\docdoc\api\rest;

use dfs\docdoc\api\components\ApiFactory;

class API_v105Test extends API_v104Test
{
	/**
	 * Тестируем все сервисы
	 *
	 * @dataProvider provideTestData
	 *
	 * @param string $request
	 * @param string $expected_json
	 * @param array $params
	 *
	 */
	public function testServices($request, $expected_json, array $params = [])
	{
		$api = ApiFactory::getApi('/api/rest/1.0.5/json' . $request, $params);

		$res = $api->getRowResult();
		$expected = json_decode($expected_json);
		$actual = json_decode($res);

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
			'{"Doctor":[{"Id":2,"Name":"Николаев Николай Николаевич","Rating":"4.75","Sex":0,"Img":"http://docdoc.ru/img/doctorsNew/2_small.jpg","AddPhoneNumber":"","Category":"","Degree":"","Rank":"","Description":"Занимается лечением пациентов, имеющих эндокринную патологию репродуктивной системы; ведением беременности и родов при наличии акушерской патологии, после вспомогательных репродуктивных технологий, в том числе после ЭКО. Автор более чем 30 научных публикаций.","TextEducation":"Текст","TextAssociation":"","TextDegree":"","TextSpec":"Текст","TextCourse":"Сертификаты","TextExperience":"","ExperienceYear":' . $this->getExperience(1977) . ',"Price":2300,"SpecialPrice":0,"Departure":0,"Clinics":[1,2,3],"Alias":"Nikolaev_Nikolai","Specialities":[{"Id":"1","Name":"Акушер-гинеколог","Alias":"akusher","NameGenitive":"Акушера-гинеколога","NamePlural":"Акушеры-гинекологи","NamePluralGenitive":"Акушеров-гинекологов","IsSimple":false}],"Stations":[{"Id":"1","Name":"Авиамоторная","LineName":"","LineColor":"","CityId":"","Alias":"aviamotornaya"},{"Id":"2","Name":"Автозаводская","LineName":"Замоскворецкая","LineColor":"0a6f20","CityId":"1","Alias":"avtozavodskaya"},{"Id":"3","Name":"Академическая","LineName":"","LineColor":"","CityId":"","Alias":"akademicheskaya"}],"BookingClinics":[],"isActive":true}]}',
			['partnerId' => 1],
		];

		$parentData['doctorList'] = [
			'/doctor/list/start/0/count/1/city/1/speciality/1',
			'{"Total":12,"DoctorList":[{"Id":2,"Name":"Николаев Николай Николаевич","Rating":"4.75","Sex":0,"Img":"http://docdoc.ru/img/doctorsNew/2_small.jpg","AddPhoneNumber":"","Category":"","Degree":"","Rank":"","Description":"Занимается лечением пациентов, имеющих эндокринную патологию репродуктивной системы; ведением беременности и родов при наличии акушерской патологии, после вспомогательных репродуктивных технологий, в том числе после ЭКО. Автор более чем 30 научных публикаций.","ExperienceYear":' . $this->getExperience(1977) . ',"Price":2300,"SpecialPrice":0,"Departure":0,"Clinics":[1,2,3],"Alias":"Nikolaev_Nikolai","Specialities":[{"Id":"1","Name":"Акушер-гинеколог","Alias":"akusher","NameGenitive":"Акушера-гинеколога","NamePlural":"Акушеры-гинекологи","NamePluralGenitive":"Акушеров-гинекологов","IsSimple":false}],"Stations":[{"Id":"1","Name":"Авиамоторная","LineName":"","LineColor":"","CityId":"","Alias":"aviamotornaya"},{"Id":"2","Name":"Автозаводская","LineName":"Замоскворецкая","LineColor":"0a6f20","CityId":"1","Alias":"avtozavodskaya"},{"Id":"3","Name":"Академическая","LineName":"","LineColor":"","CityId":"","Alias":"akademicheskaya"}],"BookingClinics":[],"TextAbout":"Занимается лечением пациентов, имеющих эндокринную патологию репродуктивной системы; ведением беременности и родов при наличии акушерской патологии, после вспомогательных репродуктивных технологий, в том числе после ЭКО. Автор более чем 30 научных публикаций.","InternalRating":"9.55","OpinionCount":2,"isActive":true,"Extra":null}]}',
			['partnerId' => 1],
		];

		$parentData['slotList'] = [
			'/slot/list/clinic/1/doctor/1/from/2014-01-01/to/2030-12-12',
			'{"SlotList":[{"Id":"uuid","StartTime":"2029-01-01 00:00:00","FinishTime":"2029-01-01 01:10:10"},{"Id":"uuid","StartTime":"2029-01-01 00:00:00","FinishTime":"2029-01-01 01:10:10"},{"Id":"uuid","StartTime":"2029-01-01 00:00:00","FinishTime":"2029-01-01 01:10:10"},{"Id":"uuid","StartTime":"2029-01-01 00:00:00","FinishTime":"2029-01-01 01:10:10"}]}',
			['partnerId' => 1],
		];

		$parentData['doctorListDeti1'] = [
			'/doctor/list/start/0/count/1/city/1/deti/1',
			'{"Total":3,"DoctorList":[{"Id":2,"Name":"Николаев Николай Николаевич","Rating":"4.75","Sex":0,"Img":"http://docdoc.ru/img/doctorsNew/2_small.jpg","AddPhoneNumber":"","Category":"","Degree":"","Rank":"","Description":"Занимается лечением пациентов, имеющих эндокринную патологию репродуктивной системы; ведением беременности и родов при наличии акушерской патологии, после вспомогательных репродуктивных технологий, в том числе после ЭКО. Автор более чем 30 научных публикаций.","ExperienceYear":' . $this->getExperience(1977) . ',"Price":2300,"SpecialPrice":0,"Departure":0,"Clinics":[1,2,3],"Alias":"Nikolaev_Nikolai","Specialities":[{"Id":"1","Name":"Акушер-гинеколог","Alias":"akusher","NameGenitive":"Акушера-гинеколога","NamePlural":"Акушеры-гинекологи","NamePluralGenitive":"Акушеров-гинекологов","IsSimple":false}],"Stations":[{"Id":"1","Name":"Авиамоторная","LineName":"","LineColor":"","CityId":"","Alias":"aviamotornaya"},{"Id":"2","Name":"Автозаводская","LineName":"Замоскворецкая","LineColor":"0a6f20","CityId":"1","Alias":"avtozavodskaya"},{"Id":"3","Name":"Академическая","LineName":"","LineColor":"","CityId":"","Alias":"akademicheskaya"}],"BookingClinics":[],"TextAbout":"Занимается лечением пациентов, имеющих эндокринную патологию репродуктивной системы; ведением беременности и родов при наличии акушерской патологии, после вспомогательных репродуктивных технологий, в том числе после ЭКО. Автор более чем 30 научных публикаций.","InternalRating":"9.55","OpinionCount":2,"isActive":true,"Extra":null}]}',
			['partnerId' => 1],
		];

		$parentData['doctorListDeti0'] = [
			'/doctor/list/start/0/count/1/city/1/deti/0',
			'{"Total":9,"DoctorList":[{"Id":5,"Name":"Николаев Николай Николаевич 5","Rating":"4.75555","Sex":0,"Img":"http://docdoc.ru/img/doctorsNew/5_small.jpg","AddPhoneNumber":"","Category":"","Degree":"","Rank":"","Description":"","ExperienceYear":' . $this->getExperience(1977) . ',"Price":1300,"SpecialPrice":0,"Departure":1,"Clinics":[2],"Alias":"Nikolaev_Nikolai_5","Specialities":[{"Id":"1","Name":"Акушер-гинеколог","Alias":"akusher","NameGenitive":"Акушера-гинеколога","NamePlural":"Акушеры-гинекологи","NamePluralGenitive":"Акушеров-гинекологов","IsSimple":false}],"Stations":[{"Id":"2","Name":"Автозаводская","LineName":"Замоскворецкая","LineColor":"0a6f20","CityId":"1","Alias":"avtozavodskaya"}],"BookingClinics":[],"TextAbout":"","InternalRating":"9.55","OpinionCount":0,"isActive":true,"Extra":null}]}',
			['partnerId' => 1],
		];

		$parentData['doctorListNaDom0'] = [
			'/doctor/list/start/0/count/1/city/1/na-dom/0',
			'{"Total":10,"DoctorList":[{"Id":2,"Name":"Николаев Николай Николаевич","Rating":"4.75","Sex":0,"Img":"http://docdoc.ru/img/doctorsNew/2_small.jpg","AddPhoneNumber":"","Category":"","Degree":"","Rank":"","Description":"Занимается лечением пациентов, имеющих эндокринную патологию репродуктивной системы; ведением беременности и родов при наличии акушерской патологии, после вспомогательных репродуктивных технологий, в том числе после ЭКО. Автор более чем 30 научных публикаций.","ExperienceYear":' . $this->getExperience(1977) . ',"Price":2300,"SpecialPrice":0,"Departure":0,"Clinics":[1,2,3],"Alias":"Nikolaev_Nikolai","Specialities":[{"Id":"1","Name":"Акушер-гинеколог","Alias":"akusher","NameGenitive":"Акушера-гинеколога","NamePlural":"Акушеры-гинекологи","NamePluralGenitive":"Акушеров-гинекологов","IsSimple":false}],"Stations":[{"Id":"1","Name":"Авиамоторная","LineName":"","LineColor":"","CityId":"","Alias":"aviamotornaya"},{"Id":"2","Name":"Автозаводская","LineName":"Замоскворецкая","LineColor":"0a6f20","CityId":"1","Alias":"avtozavodskaya"},{"Id":"3","Name":"Академическая","LineName":"","LineColor":"","CityId":"","Alias":"akademicheskaya"}],"BookingClinics":[],"TextAbout":"Занимается лечением пациентов, имеющих эндокринную патологию репродуктивной системы; ведением беременности и родов при наличии акушерской патологии, после вспомогательных репродуктивных технологий, в том числе после ЭКО. Автор более чем 30 научных публикаций.","InternalRating":"9.55","OpinionCount":2,"isActive":true,"Extra":null}]}',
			['partnerId' => 1],
		];

		$parentData['doctorListNaDom1'] = [
			'/doctor/list/start/0/count/1/city/1/na-dom/1',
			'{"Total":2,"DoctorList":[{"Id":5,"Name":"Николаев Николай Николаевич 5","Rating":"4.75555","Sex":0,"Img":"http://docdoc.ru/img/doctorsNew/5_small.jpg","AddPhoneNumber":"","Category":"","Degree":"","Rank":"","Description":"","ExperienceYear":' . $this->getExperience(1977) . ',"Price":1300,"SpecialPrice":0,"Departure":1,"Clinics":[2],"Alias":"Nikolaev_Nikolai_5","Specialities":[{"Id":"1","Name":"Акушер-гинеколог","Alias":"akusher","NameGenitive":"Акушера-гинеколога","NamePlural":"Акушеры-гинекологи","NamePluralGenitive":"Акушеров-гинекологов","IsSimple":false}],"Stations":[{"Id":"2","Name":"Автозаводская","LineName":"Замоскворецкая","LineColor":"0a6f20","CityId":"1","Alias":"avtozavodskaya"}],"BookingClinics":[],"TextAbout":"","InternalRating":"9.55","OpinionCount":0,"isActive":true,"Extra":null}]}',
			['partnerId' => 1],
		];

		$parentData['doctorListNaDom1Deti1'] = [
			'/doctor/list/start/0/count/1/city/1/na-dom/1/deti/1',
			'{"Total":1,"DoctorList":[{"Id":6,"Name":"Николаев Николай Николаевич 6","Rating":"4.75555","Sex":0,"Img":"http://docdoc.ru/img/doctorsNew/6_small.jpg","AddPhoneNumber":"","Category":"","Degree":"","Rank":"","Description":"","ExperienceYear":' . $this->getExperience(1977) . ',"Price":1300,"SpecialPrice":0,"Departure":1,"Clinics":[2],"Alias":"Nikolaev_Nikolai_6","Specialities":[{"Id":"1","Name":"Акушер-гинеколог","Alias":"akusher","NameGenitive":"Акушера-гинеколога","NamePlural":"Акушеры-гинекологи","NamePluralGenitive":"Акушеров-гинекологов","IsSimple":false}],"Stations":[{"Id":"2","Name":"Автозаводская","LineName":"Замоскворецкая","LineColor":"0a6f20","CityId":"1","Alias":"avtozavodskaya"}],"BookingClinics":[],"TextAbout":"","InternalRating":"9.55","OpinionCount":0,"isActive":true,"Extra":null}]}',
			['partnerId' => 1],
		];

		$parentData['doctorListInDistrict'] = [
			'/doctor/list/start/0/count/1/city/1/district/5',
			'{"Total":0,"DoctorList":[]}',
			['partnerId' => 1],
		];

		$parentData['doctorListInDistrict1'] = [
			'/doctor/list/start/0/count/10/city/1/district/1/near/extra',
			'{"Total":10,"DoctorList":[{"Id":2,"Name":"Николаев Николай Николаевич","Rating":"4.75","Sex":0,"Img":"http://docdoc.ru/img/doctorsNew/2_small.jpg","AddPhoneNumber":"","Category":"","Degree":"","Rank":"","Description":"Занимается лечением пациентов, имеющих эндокринную патологию репродуктивной системы; ведением беременности и родов при наличии акушерской патологии, после вспомогательных репродуктивных технологий, в том числе после ЭКО. Автор более чем 30 научных публикаций.","ExperienceYear":' . $this->getExperience(1977) . ',"Price":2300,"SpecialPrice":0,"Departure":0,"Clinics":[1,2,3],"Alias":"Nikolaev_Nikolai","Specialities":[{"Id":"1","Name":"Акушер-гинеколог","Alias":"akusher","NameGenitive":"Акушера-гинеколога","NamePlural":"Акушеры-гинекологи","NamePluralGenitive":"Акушеров-гинекологов","IsSimple":false}],"Stations":[{"Id":"1","Name":"Авиамоторная","LineName":"","LineColor":"","CityId":"","Alias":"aviamotornaya"},{"Id":"2","Name":"Автозаводская","LineName":"Замоскворецкая","LineColor":"0a6f20","CityId":"1","Alias":"avtozavodskaya"},{"Id":"3","Name":"Академическая","LineName":"","LineColor":"","CityId":"","Alias":"akademicheskaya"}],"BookingClinics":[],"TextAbout":"Занимается лечением пациентов, имеющих эндокринную патологию репродуктивной системы; ведением беременности и родов при наличии акушерской патологии, после вспомогательных репродуктивных технологий, в том числе после ЭКО. Автор более чем 30 научных публикаций.","InternalRating":"9.55","OpinionCount":2,"isActive":true,"Extra":null},{"Id":3,"Name":"dfgdsfgdsg sdfgdsfg Никsdfgsdfgолаевич","Rating":"4.75","Sex":0,"Img":"http://docdoc.ru/img/doctorsNew/3_small.jpg","AddPhoneNumber":"","Category":"","Degree":"","Rank":"","Description":"Занимается лечением пациентов, имеющих эндокринную патологию репродуктивной системы; ведением беременности и родов при наличии акушерской патологии, после вспомогательных репродуктивных технологий, в том числе после ЭКО. Автор более чем 30 научных публикаций.","ExperienceYear":' . $this->getExperience(1977) . ',"Price":2500,"SpecialPrice":0,"Departure":0,"Clinics":[1,3],"Alias":"Nikolaev_Nikolai","Specialities":[{"Id":"1","Name":"Акушер-гинеколог","Alias":"akusher","NameGenitive":"Акушера-гинеколога","NamePlural":"Акушеры-гинекологи","NamePluralGenitive":"Акушеров-гинекологов","IsSimple":false}],"Stations":[{"Id":"1","Name":"Авиамоторная","LineName":"","LineColor":"","CityId":"","Alias":"aviamotornaya"},{"Id":"2","Name":"Автозаводская","LineName":"Замоскворецкая","LineColor":"0a6f20","CityId":"1","Alias":"avtozavodskaya"},{"Id":"3","Name":"Академическая","LineName":"","LineColor":"","CityId":"","Alias":"akademicheskaya"}],"BookingClinics":[1],"TextAbout":"Занимается лечением пациентов, имеющих эндокринную патологию репродуктивной системы; ведением беременности и родов при наличии акушерской патологии, после вспомогательных репродуктивных технологий, в том числе после ЭКО. Автор более чем 30 научных публикаций.","InternalRating":"9.55","OpinionCount":0,"isActive":true,"Extra":null},{"Id":5,"Name":"Николаев Николай Николаевич 5","Rating":"4.75555","Sex":0,"Img":"http://docdoc.ru/img/doctorsNew/5_small.jpg","AddPhoneNumber":"","Category":"","Degree":"","Rank":"","Description":"","ExperienceYear":' . $this->getExperience(1977) . ',"Price":1300,"SpecialPrice":0,"Departure":1,"Clinics":[2],"Alias":"Nikolaev_Nikolai_5","Specialities":[{"Id":"1","Name":"Акушер-гинеколог","Alias":"akusher","NameGenitive":"Акушера-гинеколога","NamePlural":"Акушеры-гинекологи","NamePluralGenitive":"Акушеров-гинекологов","IsSimple":false}],"Stations":[{"Id":"2","Name":"Автозаводская","LineName":"Замоскворецкая","LineColor":"0a6f20","CityId":"1","Alias":"avtozavodskaya"}],"BookingClinics":[],"TextAbout":"","InternalRating":"9.55","OpinionCount":0,"isActive":true,"Extra":"geo"},{"Id":6,"Name":"Николаев Николай Николаевич 6","Rating":"4.75555","Sex":0,"Img":"http://docdoc.ru/img/doctorsNew/6_small.jpg","AddPhoneNumber":"","Category":"","Degree":"","Rank":"","Description":"","ExperienceYear":' . $this->getExperience(1977) . ',"Price":1300,"SpecialPrice":0,"Departure":1,"Clinics":[2],"Alias":"Nikolaev_Nikolai_6","Specialities":[{"Id":"1","Name":"Акушер-гинеколог","Alias":"akusher","NameGenitive":"Акушера-гинеколога","NamePlural":"Акушеры-гинекологи","NamePluralGenitive":"Акушеров-гинекологов","IsSimple":false}],"Stations":[{"Id":"2","Name":"Автозаводская","LineName":"Замоскворецкая","LineColor":"0a6f20","CityId":"1","Alias":"avtozavodskaya"}],"BookingClinics":[],"TextAbout":"","InternalRating":"9.55","OpinionCount":0,"isActive":true,"Extra":"geo"},{"Id":7,"Name":"Николаев Николай Николаевич 7","Rating":"4.75555","Sex":0,"Img":"http://docdoc.ru/img/doctorsNew/7_small.jpg","AddPhoneNumber":"","Category":"","Degree":"","Rank":"","Description":"","ExperienceYear":' . $this->getExperience(1977) . ',"Price":1300,"SpecialPrice":0,"Departure":0,"Clinics":[2],"Alias":"Nikolaev_Nikolai_7","Specialities":[{"Id":"1","Name":"Акушер-гинеколог","Alias":"akusher","NameGenitive":"Акушера-гинеколога","NamePlural":"Акушеры-гинекологи","NamePluralGenitive":"Акушеров-гинекологов","IsSimple":false}],"Stations":[{"Id":"2","Name":"Автозаводская","LineName":"Замоскворецкая","LineColor":"0a6f20","CityId":"1","Alias":"avtozavodskaya"}],"BookingClinics":[],"TextAbout":"","InternalRating":"9.55","OpinionCount":0,"isActive":true,"Extra":"geo"},{"Id":8,"Name":"Николаев Николай Николаевич 8","Rating":"4.75555","Sex":0,"Img":"http://docdoc.ru/img/doctorsNew/8_small.jpg","AddPhoneNumber":"","Category":"","Degree":"","Rank":"","Description":"","ExperienceYear":' . $this->getExperience(1977) . ',"Price":1300,"SpecialPrice":0,"Departure":0,"Clinics":[2],"Alias":"Nikolaev_Nikolai_8","Specialities":[{"Id":"1","Name":"Акушер-гинеколог","Alias":"akusher","NameGenitive":"Акушера-гинеколога","NamePlural":"Акушеры-гинекологи","NamePluralGenitive":"Акушеров-гинекологов","IsSimple":false}],"Stations":[{"Id":"2","Name":"Автозаводская","LineName":"Замоскворецкая","LineColor":"0a6f20","CityId":"1","Alias":"avtozavodskaya"}],"BookingClinics":[],"TextAbout":"","InternalRating":"9.55","OpinionCount":0,"isActive":true,"Extra":"geo"},{"Id":9,"Name":"Николаев Николай Николаевич 9","Rating":"4.75555","Sex":0,"Img":"http://docdoc.ru/img/doctorsNew/9_small.jpg","AddPhoneNumber":"","Category":"","Degree":"","Rank":"","Description":"","ExperienceYear":' . $this->getExperience(1977) . ',"Price":1300,"SpecialPrice":0,"Departure":0,"Clinics":[2],"Alias":"Nikolaev_Nikolai_9","Specialities":[{"Id":"1","Name":"Акушер-гинеколог","Alias":"akusher","NameGenitive":"Акушера-гинеколога","NamePlural":"Акушеры-гинекологи","NamePluralGenitive":"Акушеров-гинекологов","IsSimple":false}],"Stations":[{"Id":"2","Name":"Автозаводская","LineName":"Замоскворецкая","LineColor":"0a6f20","CityId":"1","Alias":"avtozavodskaya"}],"BookingClinics":[],"TextAbout":"","InternalRating":"9.55","OpinionCount":0,"isActive":true,"Extra":"geo"},{"Id":10,"Name":"Николаев Николай Николаевич 10","Rating":"4.75555","Sex":0,"Img":"http://docdoc.ru/img/doctorsNew/10_small.jpg","AddPhoneNumber":"","Category":"","Degree":"","Rank":"","Description":"","ExperienceYear":' . $this->getExperience(1977) . ',"Price":1300,"SpecialPrice":0,"Departure":0,"Clinics":[2],"Alias":"Nikolaev_Nikolai_10","Specialities":[{"Id":"1","Name":"Акушер-гинеколог","Alias":"akusher","NameGenitive":"Акушера-гинеколога","NamePlural":"Акушеры-гинекологи","NamePluralGenitive":"Акушеров-гинекологов","IsSimple":false}],"Stations":[{"Id":"2","Name":"Автозаводская","LineName":"Замоскворецкая","LineColor":"0a6f20","CityId":"1","Alias":"avtozavodskaya"}],"BookingClinics":[],"TextAbout":"","InternalRating":"9.55","OpinionCount":0,"isActive":true,"Extra":"geo"},{"Id":11,"Name":"Николаев Николай Николаевич 11","Rating":"4.75555","Sex":0,"Img":"http://docdoc.ru/img/doctorsNew/11_small.jpg","AddPhoneNumber":"","Category":"","Degree":"","Rank":"","Description":"","ExperienceYear":' . $this->getExperience(1977) . ',"Price":1300,"SpecialPrice":0,"Departure":0,"Clinics":[2],"Alias":"Nikolaev_Nikolai_11","Specialities":[{"Id":"1","Name":"Акушер-гинеколог","Alias":"akusher","NameGenitive":"Акушера-гинеколога","NamePlural":"Акушеры-гинекологи","NamePluralGenitive":"Акушеров-гинекологов","IsSimple":false}],"Stations":[{"Id":"2","Name":"Автозаводская","LineName":"Замоскворецкая","LineColor":"0a6f20","CityId":"1","Alias":"avtozavodskaya"}],"BookingClinics":[],"TextAbout":"","InternalRating":"9.55","OpinionCount":0,"isActive":true,"Extra":"geo"},{"Id":12,"Name":"Николаев Николай Николаевич 12","Rating":"4.75555","Sex":0,"Img":"http://docdoc.ru/img/doctorsNew/12_small.jpg","AddPhoneNumber":"","Category":"","Degree":"","Rank":"","Description":"","ExperienceYear":' . $this->getExperience(1977) . ',"Price":1300,"SpecialPrice":0,"Departure":0,"Clinics":[2,4],"Alias":"Nikolaev_Nikolai_12","Specialities":[{"Id":"1","Name":"Акушер-гинеколог","Alias":"akusher","NameGenitive":"Акушера-гинеколога","NamePlural":"Акушеры-гинекологи","NamePluralGenitive":"Акушеров-гинекологов","IsSimple":false}],"Stations":[{"Id":"2","Name":"Автозаводская","LineName":"Замоскворецкая","LineColor":"0a6f20","CityId":"1","Alias":"avtozavodskaya"}],"BookingClinics":[],"TextAbout":"","InternalRating":"9.55","OpinionCount":0,"isActive":true,"Extra":"geo"}]}',
		];

		$parentData['doctorListInDistrict2'] = [
			'/doctor/list/start/0/count/1/city/1/district/2',
			'{"Total":10,"DoctorList":[{"Id":2,"Name":"Николаев Николай Николаевич","Rating":"4.75","Sex":0,"Img":"http://docdoc.ru/img/doctorsNew/2_small.jpg","AddPhoneNumber":"","Category":"","Degree":"","Rank":"","Description":"Занимается лечением пациентов, имеющих эндокринную патологию репродуктивной системы; ведением беременности и родов при наличии акушерской патологии, после вспомогательных репродуктивных технологий, в том числе после ЭКО. Автор более чем 30 научных публикаций.","ExperienceYear":' . $this->getExperience(1977) . ',"Price":2300,"SpecialPrice":0,"Departure":0,"Clinics":[1,2,3],"Alias":"Nikolaev_Nikolai","Specialities":[{"Id":"1","Name":"Акушер-гинеколог","Alias":"akusher","NameGenitive":"Акушера-гинеколога","NamePlural":"Акушеры-гинекологи","NamePluralGenitive":"Акушеров-гинекологов","IsSimple":false}],"Stations":[{"Id":"1","Name":"Авиамоторная","LineName":"","LineColor":"","CityId":"","Alias":"aviamotornaya"},{"Id":"2","Name":"Автозаводская","LineName":"Замоскворецкая","LineColor":"0a6f20","CityId":"1","Alias":"avtozavodskaya"},{"Id":"3","Name":"Академическая","LineName":"","LineColor":"","CityId":"","Alias":"akademicheskaya"}],"BookingClinics":[],"TextAbout":"Занимается лечением пациентов, имеющих эндокринную патологию репродуктивной системы; ведением беременности и родов при наличии акушерской патологии, после вспомогательных репродуктивных технологий, в том числе после ЭКО. Автор более чем 30 научных публикаций.","InternalRating":"9.55","OpinionCount":2,"isActive":true,"Extra":null}]}',
			['partnerId' => 1],
		];

		$parentData['doctorListInArea'] = [
			'/doctor/list/start/0/count/1/city/1/area/5',
			'{"Total":0,"DoctorList":[]}',
			['partnerId' => 1],
		];

		$parentData['doctorListInArea1'] = [
			'/doctor/list/start/0/count/1/city/1/area/1',
			'{"Total":11,"DoctorList":[{"Id":2,"Name":"Николаев Николай Николаевич","Rating":"4.75","Sex":0,"Img":"http://docdoc.ru/img/doctorsNew/2_small.jpg","AddPhoneNumber":"","Category":"","Degree":"","Rank":"","Description":"Занимается лечением пациентов, имеющих эндокринную патологию репродуктивной системы; ведением беременности и родов при наличии акушерской патологии, после вспомогательных репродуктивных технологий, в том числе после ЭКО. Автор более чем 30 научных публикаций.","ExperienceYear":' . $this->getExperience(1977) . ',"Price":2300,"SpecialPrice":0,"Departure":0,"Clinics":[1,2,3],"Alias":"Nikolaev_Nikolai","Specialities":[{"Id":"1","Name":"Акушер-гинеколог","Alias":"akusher","NameGenitive":"Акушера-гинеколога","NamePlural":"Акушеры-гинекологи","NamePluralGenitive":"Акушеров-гинекологов","IsSimple":false}],"Stations":[{"Id":"1","Name":"Авиамоторная","LineName":"","LineColor":"","CityId":"","Alias":"aviamotornaya"},{"Id":"2","Name":"Автозаводская","LineName":"Замоскворецкая","LineColor":"0a6f20","CityId":"1","Alias":"avtozavodskaya"},{"Id":"3","Name":"Академическая","LineName":"","LineColor":"","CityId":"","Alias":"akademicheskaya"}],"BookingClinics":[],"TextAbout":"Занимается лечением пациентов, имеющих эндокринную патологию репродуктивной системы; ведением беременности и родов при наличии акушерской патологии, после вспомогательных репродуктивных технологий, в том числе после ЭКО. Автор более чем 30 научных публикаций.","InternalRating":"9.55","OpinionCount":2,"isActive":true,"Extra":null}]}',
			['partnerId' => 1],
		];

		//где-то в районе северного полюса
		$parentData['doctorListByCoordinate'] = [
			'/doctor/list/start/0/count/10/city/1/lat/87.767699/lng/155.675702/radius/1',
			'{"Total":0,"DoctorList":[]}',
			['partnerId' => 1],
		];

		//300 метров от клиники 4
		$parentData['doctorListByCoordinate2'] = [
			'/doctor/list/start/0/count/10/city/1/lat/57.764699/lng/85.672702/radius/1',
			'{"Total":3,"DoctorList":[{"Id":12,"Name":"Николаев Николай Николаевич 12","Rating":"4.75555","Sex":0,"Img":"http://docdoc.ru/img/doctorsNew/12_small.jpg","AddPhoneNumber":"","Category":"","Degree":"","Rank":"","Description":"","ExperienceYear":' . $this->getExperience(1977) . ',"Price":1300,"SpecialPrice":0,"Departure":0,"Clinics":[2,4],"Alias":"Nikolaev_Nikolai_12","Specialities":[{"Id":"1","Name":"Акушер-гинеколог","Alias":"akusher","NameGenitive":"Акушера-гинеколога","NamePlural":"Акушеры-гинекологи","NamePluralGenitive":"Акушеров-гинекологов","IsSimple":false}],"Stations":[{"Id":"2","Name":"Автозаводская","LineName":"Замоскворецкая","LineColor":"0a6f20","CityId":"1","Alias":"avtozavodskaya"}],"BookingClinics":[],"TextAbout":"","InternalRating":"9.55","OpinionCount":0,"isActive":true,"Extra":null},{"Id":13,"Name":"Николаев Николай Николаевич 13","Rating":"4.75555","Sex":0,"Img":"http://docdoc.ru/img/doctorsNew/13_small.jpg","AddPhoneNumber":"","Category":"","Degree":"","Rank":"","Description":"","ExperienceYear":' . $this->getExperience(1977) . ',"Price":1300,"SpecialPrice":0,"Departure":0,"Clinics":[2,4],"Alias":"Nikolaev_Nikolai_13","Specialities":[{"Id":"1","Name":"Акушер-гинеколог","Alias":"akusher","NameGenitive":"Акушера-гинеколога","NamePlural":"Акушеры-гинекологи","NamePluralGenitive":"Акушеров-гинекологов","IsSimple":false}],"Stations":[{"Id":"2","Name":"Автозаводская","LineName":"Замоскворецкая","LineColor":"0a6f20","CityId":"1","Alias":"avtozavodskaya"}],"BookingClinics":[],"TextAbout":"","InternalRating":"9.55","OpinionCount":0,"isActive":true,"Extra":null},{"Id":14,"Name":"Николаев Николай Николаевич 13","Rating":"4.75555","Sex":0,"Img":"http://docdoc.ru/img/doctorsNew/14_small.jpg","AddPhoneNumber":"","Category":"","Degree":"","Rank":"","Description":"","ExperienceYear":' . $this->getExperience(1977) . ',"Price":1300,"SpecialPrice":0,"Departure":0,"Clinics":[4],"Alias":"Nikolaev_Nikolai_13","Specialities":[{"Id":"1","Name":"Акушер-гинеколог","Alias":"akusher","NameGenitive":"Акушера-гинеколога","NamePlural":"Акушеры-гинекологи","NamePluralGenitive":"Акушеров-гинекологов","IsSimple":false},{"Id":"3","Name":"Акушер","Alias":"akusher","NameGenitive":"Акушера","NamePlural":"Акушеры","NamePluralGenitive":"Акушеров","IsSimple":true}],"Stations":[{"Id":"3","Name":"Академическая","LineName":"","LineColor":"","CityId":"","Alias":"akademicheskaya"}],"BookingClinics":[],"TextAbout":"","InternalRating":"9.55","OpinionCount":0,"isActive":true,"Extra":null}]}',
			['partnerId' => 1],
		];

		//клиники 2 и 4, сортировка по расстоянию
		$parentData['doctorListByCoordinate3'] = [
			'/doctor/list/start/0/count/5/city/1/lat/57.767699/lng/85.672702/radius/5/order/distance',
			'{"Total":11,"DoctorList":[{"Id":14,"Name":"Николаев Николай Николаевич 13","Rating":"4.75555","Sex":0,"Img":"http://docdoc.ru/img/doctorsNew/14_small.jpg","AddPhoneNumber":"","Category":"","Degree":"","Rank":"","Description":"","ExperienceYear":' . $this->getExperience(1977) . ',"Price":1300,"SpecialPrice":0,"Departure":0,"Clinics":[4],"Alias":"Nikolaev_Nikolai_13","Specialities":[{"Id":"1","Name":"Акушер-гинеколог","Alias":"akusher","NameGenitive":"Акушера-гинеколога","NamePlural":"Акушеры-гинекологи","NamePluralGenitive":"Акушеров-гинекологов","IsSimple":false},{"Id":"3","Name":"Акушер","Alias":"akusher","NameGenitive":"Акушера","NamePlural":"Акушеры","NamePluralGenitive":"Акушеров","IsSimple":true}],"Stations":[{"Id":"3","Name":"Академическая","LineName":"","LineColor":"","CityId":"","Alias":"akademicheskaya"}],"BookingClinics":[],"isActive":true,"TextAbout":"","InternalRating":"9.55","OpinionCount":0,"Extra":null},{"Id":2,"Name":"Николаев Николай Николаевич","Rating":"4.75","Sex":0,"Img":"http://docdoc.ru/img/doctorsNew/2_small.jpg","AddPhoneNumber":"","Category":"","Degree":"","Rank":"","Description":"Занимается лечением пациентов, имеющих эндокринную патологию репродуктивной системы; ведением беременности и родов при наличии акушерской патологии, после вспомогательных репродуктивных технологий, в том числе после ЭКО. Автор более чем 30 научных публикаций.","ExperienceYear":' . $this->getExperience(1977) . ',"Price":2300,"SpecialPrice":0,"Departure":0,"Clinics":[1,2,3],"Alias":"Nikolaev_Nikolai","Specialities":[{"Id":"1","Name":"Акушер-гинеколог","Alias":"akusher","NameGenitive":"Акушера-гинеколога","NamePlural":"Акушеры-гинекологи","NamePluralGenitive":"Акушеров-гинекологов","IsSimple":false}],"Stations":[{"Id":"1","Name":"Авиамоторная","LineName":"","LineColor":"","CityId":"","Alias":"aviamotornaya"},{"Id":"2","Name":"Автозаводская","LineName":"Замоскворецкая","LineColor":"0a6f20","CityId":"1","Alias":"avtozavodskaya"},{"Id":"3","Name":"Академическая","LineName":"","LineColor":"","CityId":"","Alias":"akademicheskaya"}],"BookingClinics":[],"isActive":true,"TextAbout":"Занимается лечением пациентов, имеющих эндокринную патологию репродуктивной системы; ведением беременности и родов при наличии акушерской патологии, после вспомогательных репродуктивных технологий, в том числе после ЭКО. Автор более чем 30 научных публикаций.","InternalRating":"9.55","OpinionCount":2,"Extra":null},{"Id":5,"Name":"Николаев Николай Николаевич 5","Rating":"4.75555","Sex":0,"Img":"http://docdoc.ru/img/doctorsNew/5_small.jpg","AddPhoneNumber":"","Category":"","Degree":"","Rank":"","Description":"","ExperienceYear":' . $this->getExperience(1977) . ',"Price":1300,"SpecialPrice":0,"Departure":1,"Clinics":[2],"Alias":"Nikolaev_Nikolai_5","Specialities":[{"Id":"1","Name":"Акушер-гинеколог","Alias":"akusher","NameGenitive":"Акушера-гинеколога","NamePlural":"Акушеры-гинекологи","NamePluralGenitive":"Акушеров-гинекологов","IsSimple":false}],"Stations":[{"Id":"2","Name":"Автозаводская","LineName":"Замоскворецкая","LineColor":"0a6f20","CityId":"1","Alias":"avtozavodskaya"}],"BookingClinics":[],"isActive":true,"TextAbout":"","InternalRating":"9.55","OpinionCount":0,"Extra":null},{"Id":6,"Name":"Николаев Николай Николаевич 6","Rating":"4.75555","Sex":0,"Img":"http://docdoc.ru/img/doctorsNew/6_small.jpg","AddPhoneNumber":"","Category":"","Degree":"","Rank":"","Description":"","ExperienceYear":' . $this->getExperience(1977) . ',"Price":1300,"SpecialPrice":0,"Departure":1,"Clinics":[2],"Alias":"Nikolaev_Nikolai_6","Specialities":[{"Id":"1","Name":"Акушер-гинеколог","Alias":"akusher","NameGenitive":"Акушера-гинеколога","NamePlural":"Акушеры-гинекологи","NamePluralGenitive":"Акушеров-гинекологов","IsSimple":false}],"Stations":[{"Id":"2","Name":"Автозаводская","LineName":"Замоскворецкая","LineColor":"0a6f20","CityId":"1","Alias":"avtozavodskaya"}],"BookingClinics":[],"isActive":true,"TextAbout":"","InternalRating":"9.55","OpinionCount":0,"Extra":null},{"Id":7,"Name":"Николаев Николай Николаевич 7","Rating":"4.75555","Sex":0,"Img":"http://docdoc.ru/img/doctorsNew/7_small.jpg","AddPhoneNumber":"","Category":"","Degree":"","Rank":"","Description":"","ExperienceYear":' . $this->getExperience(1977) . ',"Price":1300,"SpecialPrice":0,"Departure":0,"Clinics":[2],"Alias":"Nikolaev_Nikolai_7","Specialities":[{"Id":"1","Name":"Акушер-гинеколог","Alias":"akusher","NameGenitive":"Акушера-гинеколога","NamePlural":"Акушеры-гинекологи","NamePluralGenitive":"Акушеров-гинекологов","IsSimple":false}],"Stations":[{"Id":"2","Name":"Автозаводская","LineName":"Замоскворецкая","LineColor":"0a6f20","CityId":"1","Alias":"avtozavodskaya"}],"BookingClinics":[],"isActive":true,"TextAbout":"","InternalRating":"9.55","OpinionCount":0,"Extra":null}]}',
			['partnerId' => 1],
		];

		//клиники 2 и 4, сортировка по расстоянию (по убыванию)
		$parentData['doctorListByCoordinate4'] = [
			'/doctor/list/start/0/count/5/city/1/lat/57.767699/lng/85.672702/radius/5/order/-distance',
			'{"Total":11,"DoctorList":[{"Id":2,"Name":"Николаев Николай Николаевич","Rating":"4.75","Sex":0,"Img":"http://docdoc.ru/img/doctorsNew/2_small.jpg","AddPhoneNumber":"","Category":"","Degree":"","Rank":"","Description":"Занимается лечением пациентов, имеющих эндокринную патологию репродуктивной системы; ведением беременности и родов при наличии акушерской патологии, после вспомогательных репродуктивных технологий, в том числе после ЭКО. Автор более чем 30 научных публикаций.","ExperienceYear":' . $this->getExperience(1977) . ',"Price":2300,"SpecialPrice":0,"Departure":0,"Clinics":[1,2,3],"Alias":"Nikolaev_Nikolai","Specialities":[{"Id":"1","Name":"Акушер-гинеколог","Alias":"akusher","NameGenitive":"Акушера-гинеколога","NamePlural":"Акушеры-гинекологи","NamePluralGenitive":"Акушеров-гинекологов","IsSimple":false}],"Stations":[{"Id":"1","Name":"Авиамоторная","LineName":"","LineColor":"","CityId":"","Alias":"aviamotornaya"},{"Id":"2","Name":"Автозаводская","LineName":"Замоскворецкая","LineColor":"0a6f20","CityId":"1","Alias":"avtozavodskaya"},{"Id":"3","Name":"Академическая","LineName":"","LineColor":"","CityId":"","Alias":"akademicheskaya"}],"BookingClinics":[],"isActive":true,"TextAbout":"Занимается лечением пациентов, имеющих эндокринную патологию репродуктивной системы; ведением беременности и родов при наличии акушерской патологии, после вспомогательных репродуктивных технологий, в том числе после ЭКО. Автор более чем 30 научных публикаций.","InternalRating":"9.55","OpinionCount":2,"Extra":null},{"Id":5,"Name":"Николаев Николай Николаевич 5","Rating":"4.75555","Sex":0,"Img":"http://docdoc.ru/img/doctorsNew/5_small.jpg","AddPhoneNumber":"","Category":"","Degree":"","Rank":"","Description":"","ExperienceYear":' . $this->getExperience(1977) . ',"Price":1300,"SpecialPrice":0,"Departure":1,"Clinics":[2],"Alias":"Nikolaev_Nikolai_5","Specialities":[{"Id":"1","Name":"Акушер-гинеколог","Alias":"akusher","NameGenitive":"Акушера-гинеколога","NamePlural":"Акушеры-гинекологи","NamePluralGenitive":"Акушеров-гинекологов","IsSimple":false}],"Stations":[{"Id":"2","Name":"Автозаводская","LineName":"Замоскворецкая","LineColor":"0a6f20","CityId":"1","Alias":"avtozavodskaya"}],"BookingClinics":[],"isActive":true,"TextAbout":"","InternalRating":"9.55","OpinionCount":0,"Extra":null},{"Id":6,"Name":"Николаев Николай Николаевич 6","Rating":"4.75555","Sex":0,"Img":"http://docdoc.ru/img/doctorsNew/6_small.jpg","AddPhoneNumber":"","Category":"","Degree":"","Rank":"","Description":"","ExperienceYear":' . $this->getExperience(1977) . ',"Price":1300,"SpecialPrice":0,"Departure":1,"Clinics":[2],"Alias":"Nikolaev_Nikolai_6","Specialities":[{"Id":"1","Name":"Акушер-гинеколог","Alias":"akusher","NameGenitive":"Акушера-гинеколога","NamePlural":"Акушеры-гинекологи","NamePluralGenitive":"Акушеров-гинекологов","IsSimple":false}],"Stations":[{"Id":"2","Name":"Автозаводская","LineName":"Замоскворецкая","LineColor":"0a6f20","CityId":"1","Alias":"avtozavodskaya"}],"BookingClinics":[],"isActive":true,"TextAbout":"","InternalRating":"9.55","OpinionCount":0,"Extra":null},{"Id":7,"Name":"Николаев Николай Николаевич 7","Rating":"4.75555","Sex":0,"Img":"http://docdoc.ru/img/doctorsNew/7_small.jpg","AddPhoneNumber":"","Category":"","Degree":"","Rank":"","Description":"","ExperienceYear":' . $this->getExperience(1977) . ',"Price":1300,"SpecialPrice":0,"Departure":0,"Clinics":[2],"Alias":"Nikolaev_Nikolai_7","Specialities":[{"Id":"1","Name":"Акушер-гинеколог","Alias":"akusher","NameGenitive":"Акушера-гинеколога","NamePlural":"Акушеры-гинекологи","NamePluralGenitive":"Акушеров-гинекологов","IsSimple":false}],"Stations":[{"Id":"2","Name":"Автозаводская","LineName":"Замоскворецкая","LineColor":"0a6f20","CityId":"1","Alias":"avtozavodskaya"}],"BookingClinics":[],"isActive":true,"TextAbout":"","InternalRating":"9.55","OpinionCount":0,"Extra":null},{"Id":8,"Name":"Николаев Николай Николаевич 8","Rating":"4.75555","Sex":0,"Img":"http://docdoc.ru/img/doctorsNew/8_small.jpg","AddPhoneNumber":"","Category":"","Degree":"","Rank":"","Description":"","ExperienceYear":' . $this->getExperience(1977) . ',"Price":1300,"SpecialPrice":0,"Departure":0,"Clinics":[2],"Alias":"Nikolaev_Nikolai_8","Specialities":[{"Id":"1","Name":"Акушер-гинеколог","Alias":"akusher","NameGenitive":"Акушера-гинеколога","NamePlural":"Акушеры-гинекологи","NamePluralGenitive":"Акушеров-гинекологов","IsSimple":false}],"Stations":[{"Id":"2","Name":"Автозаводская","LineName":"Замоскворецкая","LineColor":"0a6f20","CityId":"1","Alias":"avtozavodskaya"}],"BookingClinics":[],"isActive":true,"TextAbout":"","InternalRating":"9.55","OpinionCount":0,"Extra":null}]}',
			['partnerId' => 1],
		];

		// Поиск ближайших врачей без указания радиуса
		$parentData['doctorListByCoordinateWhithoutRadius'] = [
			'/doctor/list/start/0/count/1/city/1/lat/57.767699/lng/85.672702/order/distance',
			'{"Total":12,"DoctorList":[{"Id":14,"Name":"Николаев Николай Николаевич 13","Rating":"4.75555","Sex":0,"Img":"http://docdoc.ru/img/doctorsNew/14_small.jpg","AddPhoneNumber":"","Category":"","Degree":"","Rank":"","Description":"","ExperienceYear":' . $this->getExperience(1977) . ',"Price":1300,"SpecialPrice":0,"Departure":0,"Clinics":[4],"Alias":"Nikolaev_Nikolai_13","Specialities":[{"Id":"1","Name":"Акушер-гинеколог","Alias":"akusher","NameGenitive":"Акушера-гинеколога","NamePlural":"Акушеры-гинекологи","NamePluralGenitive":"Акушеров-гинекологов","IsSimple":false},{"Id":"3","Name":"Акушер","Alias":"akusher","NameGenitive":"Акушера","NamePlural":"Акушеры","NamePluralGenitive":"Акушеров","IsSimple":true}],"Stations":[{"Id":"3","Name":"Академическая","LineName":"","LineColor":"","CityId":"","Alias":"akademicheskaya"}],"BookingClinics":[],"isActive":true,"TextAbout":"","InternalRating":"9.55","OpinionCount":0,"Extra":null}]}',
			['partnerId' => 1],
		];

		$parentData['clinicList'] = [
			'/clinic/list/start/0/count/1/city/1/type/1',
			'{"Total":4,"ClinicList":[{"Id":1,"Name":"Клиника \u21161","ShortName":"Клиника \u21161","RewriteName":"clinica_11","URL":"http://www.clinicanomer1.ru","Longitude":"37.767699","Latitude":"55.675702","City":"Москва","Street":"Краснодарская улица","StreetId":null,"House":"д. 52, корп. 2","Description":"Многопрофильный медицинский центр, специализирующийся на проведении диагностического обследования взрослых и детей от 14 лет. Клиника расположена в шаговой близости от метро Люблино (5-10 мин.) Прием происходит по предварительной записи по многоканальному телефону +7 (495) 988-01-64.","WeekdaysOpen":null,"WeekendOpen":null,"ShortDescription":"Многопрофильный медицинский центр, специализирующийся на проведении диагностического обследования взрослых и детей от 14 лет. Клиника расположена в шаговой близости от метро Люблино (5-10 мин.) Прием происходит по предварительной записи по многоканальному телефону +7 (495) 988-01-64.","IsDiagnostic":"yes","isClinic":"yes","IsDoctor":"no","Phone":"74956410606","PhoneAppointment":"+7 (495) 641-06-06","logoPath":"1.png","ScheduleState":"enable","DistrictId":"1","Email":"","ReplacementPhone":"79000000777","MinPrice":"2300","MaxPrice":"2500","Logo":"http://docdoc.ru/upload/kliniki/logo/1.png","Diagnostics":[{"Id":"1","Name":"УЗИ (ультразвуковое исследование)","Price":"4500.00","SpecialPrice":"0.00"}],"Stations":[{"Id":"1","Name":"Авиамоторная","LineName":null,"LineColor":null,"CityId":null},{"Id":"2","Name":"Автозаводская","LineName":"Замоскворецкая","LineColor":"0a6f20","CityId":"1"},{"Id":"3","Name":"Академическая","LineName":null,"LineColor":null,"CityId":null}],"Specialities":[{"Id":"1","Name":"Акушер-гинеколог","Alias":"akusher"}]}]}',
			['partnerId' => 1],
		];

		$parentData['clinicListWithPartnerWithUseSpecialPrice'] = [
			'/clinic/list/start/0/count/1/city/1/type/1',
			'{"Total":4,"ClinicList":[{"Id":1,"Name":"Клиника \u21161","ShortName":"Клиника \u21161","RewriteName":"clinica_11","URL":"http://www.clinicanomer1.ru","Longitude":"37.767699","Latitude":"55.675702","City":"Москва","Street":"Краснодарская улица","StreetId":null,"House":"д. 52, корп. 2","Description":"Многопрофильный медицинский центр, специализирующийся на проведении диагностического обследования взрослых и детей от 14 лет. Клиника расположена в шаговой близости от метро Люблино (5-10 мин.) Прием происходит по предварительной записи по многоканальному телефону +7 (495) 988-01-64.","WeekdaysOpen":null,"WeekendOpen":null,"ShortDescription":"Многопрофильный медицинский центр, специализирующийся на проведении диагностического обследования взрослых и детей от 14 лет. Клиника расположена в шаговой близости от метро Люблино (5-10 мин.) Прием происходит по предварительной записи по многоканальному телефону +7 (495) 988-01-64.","IsDiagnostic":"yes","isClinic":"yes","IsDoctor":"no","Phone":"74956410606","PhoneAppointment":"+7 (495) 641-06-06","logoPath":"1.png","ScheduleState":"enable","DistrictId":"1","Email":"","ReplacementPhone":null,"MinPrice":"100","MaxPrice":"2300","Logo":"http://docdoc.ru/upload/kliniki/logo/1.png","Diagnostics":[{"Id":"1","Name":"УЗИ (ультразвуковое исследование)","Price":"4500.00","SpecialPrice":"0.00"}],"Stations":[{"Id":"1","Name":"Авиамоторная","LineName":null,"LineColor":null,"CityId":null},{"Id":"2","Name":"Автозаводская","LineName":"Замоскворецкая","LineColor":"0a6f20","CityId":"1"},{"Id":"3","Name":"Академическая","LineName":null,"LineColor":null,"CityId":null}],"Specialities":[{"Id":"1","Name":"Акушер-гинеколог","Alias":"akusher"}]}]}',
			['partnerId' => 2],
		];


		$parentData['clinicListWithReplacementPhone'] = [
			'/clinic/list/start/0/count/1/city/1/type/1/partnerId/1',
			'{"Total":4,"ClinicList":[{"Id":1,"Name":"Клиника \u21161","ShortName":"Клиника \u21161","RewriteName":"clinica_11","URL":"http://www.clinicanomer1.ru","Longitude":"37.767699","Latitude":"55.675702","City":"Москва","Street":"Краснодарская улица","StreetId":null,"House":"д. 52, корп. 2","Description":"Многопрофильный медицинский центр, специализирующийся на проведении диагностического обследования взрослых и детей от 14 лет. Клиника расположена в шаговой близости от метро Люблино (5-10 мин.) Прием происходит по предварительной записи по многоканальному телефону +7 (495) 988-01-64.","WeekdaysOpen":null,"WeekendOpen":null,"ShortDescription":"Многопрофильный медицинский центр, специализирующийся на проведении диагностического обследования взрослых и детей от 14 лет. Клиника расположена в шаговой близости от метро Люблино (5-10 мин.) Прием происходит по предварительной записи по многоканальному телефону +7 (495) 988-01-64.","IsDiagnostic":"yes","isClinic":"yes","IsDoctor":"no","Phone":"74956410606","PhoneAppointment":"+7 (495) 641-06-06","logoPath":"1.png","ScheduleState":"enable","DistrictId":"1","Email":"","ReplacementPhone":"79000000777","MinPrice":"2300","MaxPrice":"2500","Logo":"http://docdoc.ru/upload/kliniki/logo/1.png","Diagnostics":[{"Id":"1","Name":"УЗИ (ультразвуковое исследование)","Price":"4500.00","SpecialPrice":"0.00"}],"Stations":[{"Id":"1","Name":"Авиамоторная","LineName":null,"LineColor":null,"CityId":null},{"Id":"2","Name":"Автозаводская","LineName":"Замоскворецкая","LineColor":"0a6f20","CityId":"1"},{"Id":"3","Name":"Академическая","LineName":null,"LineColor":null,"CityId":null}],"Specialities":[{"Id":"1","Name":"Акушер-гинеколог","Alias":"akusher"}]}]}',
			['partnerId' => 1],
		];

		$parentData['clinicListWithReplacementPhoneAsNull'] = [
			'/clinic/list/start/0/count/1/city/1/type/1/partnerId/999',
			'{"Total":4,"ClinicList":[{"Id":1,"Name":"Клиника \u21161","ShortName":"Клиника \u21161","RewriteName":"clinica_11","URL":"http://www.clinicanomer1.ru","Longitude":"37.767699","Latitude":"55.675702","City":"Москва","Street":"Краснодарская улица","StreetId":null,"House":"д. 52, корп. 2","Description":"Многопрофильный медицинский центр, специализирующийся на проведении диагностического обследования взрослых и детей от 14 лет. Клиника расположена в шаговой близости от метро Люблино (5-10 мин.) Прием происходит по предварительной записи по многоканальному телефону +7 (495) 988-01-64.","WeekdaysOpen":null,"WeekendOpen":null,"ShortDescription":"Многопрофильный медицинский центр, специализирующийся на проведении диагностического обследования взрослых и детей от 14 лет. Клиника расположена в шаговой близости от метро Люблино (5-10 мин.) Прием происходит по предварительной записи по многоканальному телефону +7 (495) 988-01-64.","IsDiagnostic":"yes","isClinic":"yes","IsDoctor":"no","Phone":"74956410606","PhoneAppointment":"+7 (495) 641-06-06","logoPath":"1.png","ScheduleState":"enable","DistrictId":"1","Email":"","ReplacementPhone":null,"MinPrice":"2300","MaxPrice":"2500","Logo":"http://docdoc.ru/upload/kliniki/logo/1.png","Diagnostics":[{"Id":"1","Name":"УЗИ (ультразвуковое исследование)","Price":"4500.00","SpecialPrice":"0.00"}],"Stations":[{"Id":"1","Name":"Авиамоторная","LineName":null,"LineColor":null,"CityId":null},{"Id":"2","Name":"Автозаводская","LineName":"Замоскворецкая","LineColor":"0a6f20","CityId":"1"},{"Id":"3","Name":"Академическая","LineName":null,"LineColor":null,"CityId":null}],"Specialities":[{"Id":"1","Name":"Акушер-гинеколог","Alias":"akusher"}]}]}',
			['partnerId' => 1],

		];

		$parentData['specialityList'] = [
			'/speciality',
			'{"SpecList":[{"Id":3,"Name":"Акушер","Alias":"akusher","NameGenitive":"Акушера","NamePlural":"Акушеры","NamePluralGenitive":"Акушеров","IsSimple":true}]}',
		];

		$parentData['specialityListOnlySimple'] = [
			'/speciality/onlySimple/1',
			'{"SpecList":[{"Id":3,"Name":"Акушер","Alias":"akusher","NameGenitive":"Акушера","NamePlural":"Акушеры","NamePluralGenitive":"Акушеров","IsSimple":true}]}',
		];

		$parentData['specialityListNotOnlySimple'] = [
			'/speciality/onlySimple/0',
			'{"SpecList":[{"Id":3,"Name":"Акушер","Alias":"akusher","NameGenitive":"Акушера","NamePlural":"Акушеры","NamePluralGenitive":"Акушеров","IsSimple":true},{"Id":"1","Name":"Акушер-гинеколог","Alias":"akusher","NameGenitive":"Акушера-гинеколога","NamePlural":"Акушеры-гинекологи","NamePluralGenitive":"Акушеров-гинекологов","IsSimple":false}]}',
		];

		$parentData['streetList'] = [
			'/street/city/1',
			'{"StreetList":[{"Id":"1","CityId":"1","Title":"Абельмановская","RewriteName":"abelmanovskaya"},{"Id":"2","CityId":"1","Title":"Авиамоторная","RewriteName":"aviamotornaya"}]}',
		];

		$parentData['cityList'] = [
			'/city',
			'{"CityList":[{"Id":"1","Name":"Москва","Alias":"msk","Phone":"74952367276","Latitude":"55.755826","Longitude":"37.6173"},{"Id":"2","Name":"Санкт-Петербург","Alias":"spb","Phone":"78123856652","Latitude":"59.9342802","Longitude":"30.3350986"}]}',
		];

		$parentData['nearestStations1'] = [
			'/nearestStation/id/2',
			'{"StationList":[{"Id":"3","Name":"Академическая","LineName":"","LineColor":"","CityId":"","Alias":"akademicheskaya"}, {"Id":"1","Name":"Авиамоторная","LineName":"","LineColor":"","CityId":"","Alias":"aviamotornaya"}]}',
		];

		$parentData['doctorListWithoutWatermark'] = [
			'/doctor/list/start/0/count/1/city/1/speciality/1',
			'{"Total":12,"DoctorList":[{"Id":2,"Name":"Николаев Николай Николаевич","Rating":"4.75","Sex":0,"Img":"http://' . \Yii::app()->params['hosts']['front'] . '/img/doctorsNew/2.110x150.jpg","AddPhoneNumber":"","Category":"","Degree":"","Rank":"","Description":"Занимается лечением пациентов, имеющих эндокринную патологию репродуктивной системы; ведением беременности и родов при наличии акушерской патологии, после вспомогательных репродуктивных технологий, в том числе после ЭКО. Автор более чем 30 научных публикаций.","ExperienceYear":38,"Price":2300,"SpecialPrice":0,"Departure":0,"Clinics":[1,2,3],"Alias":"Nikolaev_Nikolai","Specialities":[{"Id":"1","Name":"Акушер-гинеколог","Alias":"akusher","NameGenitive":"Акушера-гинеколога","NamePlural":"Акушеры-гинекологи","NamePluralGenitive":"Акушеров-гинекологов","IsSimple":false}],"Stations":[{"Id":"1","Name":"Авиамоторная","LineName":"","LineColor":"","CityId":"","Alias":"aviamotornaya"},{"Id":"2","Name":"Автозаводская","LineName":"Замоскворецкая","LineColor":"0a6f20","CityId":"1","Alias":"avtozavodskaya"},{"Id":"3","Name":"Академическая","LineName":"","LineColor":"","CityId":"","Alias":"akademicheskaya"}],"BookingClinics":[],"isActive":true,"TextAbout":"Занимается лечением пациентов, имеющих эндокринную патологию репродуктивной системы; ведением беременности и родов при наличии акушерской патологии, после вспомогательных репродуктивных технологий, в том числе после ЭКО. Автор более чем 30 научных публикаций.","InternalRating":"9.55","OpinionCount":2,"Extra":null}]}',
			['partnerId' => 14],
		];

		$parentData['doctorVieWithoutWatermark'] = [
			'/doctor/2',
			'{"Doctor":[{"Id":2,"Name":"Николаев Николай Николаевич","Rating":"4.75","Sex":0,"Img":"http://' . \Yii::app()->params['hosts']['front'] . '/img/doctorsNew/2.110x150.jpg","AddPhoneNumber":"","Category":"","Degree":"","Rank":"","Description":"Занимается лечением пациентов, имеющих эндокринную патологию репродуктивной системы; ведением беременности и родов при наличии акушерской патологии, после вспомогательных репродуктивных технологий, в том числе после ЭКО. Автор более чем 30 научных публикаций.","TextEducation":"Текст","TextAssociation":"","TextDegree":"","TextSpec":"Текст","TextCourse":"Сертификаты","TextExperience":"","ExperienceYear":' . $this->getExperience(1977) . ',"Price":2300,"SpecialPrice":0,"Departure":0,"Clinics":[1,2,3],"Alias":"Nikolaev_Nikolai","Specialities":[{"Id":"1","Name":"Акушер-гинеколог","Alias":"akusher","NameGenitive":"Акушера-гинеколога","NamePlural":"Акушеры-гинекологи","NamePluralGenitive":"Акушеров-гинекологов","IsSimple":false}],"Stations":[{"Id":"1","Name":"Авиамоторная","LineName":"","LineColor":"","CityId":"","Alias":"aviamotornaya"},{"Id":"2","Name":"Автозаводская","LineName":"Замоскворецкая","LineColor":"0a6f20","CityId":"1","Alias":"avtozavodskaya"},{"Id":"3","Name":"Академическая","LineName":"","LineColor":"","CityId":"","Alias":"akademicheskaya"}],"BookingClinics":[],"isActive":true}]}',
			['partnerId' => 14]
		];

		$parentData['nearDistrictsWithLimit'] = [
			'/nearDistricts/id/1/limit/1',
			'{"DistrictList":[{"Id":"2","Alias":"basmannyj","Name":"Басманный","Area":{"Id":"1","Alias":"cao","Name":"ЦАО","FullName":"Центральный Округ"}}]}',
		];

		$parentData['nearDistrictsWithoutLimit'] = [
			'/nearDistricts/id/2',
			'{"DistrictList":[{"Id":"1","Alias":"arbat","Name":"Арбат","Area":{"Id":"1","Alias":"cao","Name":"ЦАО","FullName":"Центральный Округ"}},{"Id":"3","Alias":"district3","Name":"Район3","Area":{"Id":"1","Alias":"cao","Name":"ЦАО","FullName":"Центральный Округ"}}]}',
		];

		$parentData['nearDistrictsWithLimit2'] = [
			'/nearDistricts/id/2/limit/1',
			'{"DistrictList":[{"Id":"1","Alias":"arbat","Name":"Арбат","Area":{"Id":"1","Alias":"cao","Name":"ЦАО","FullName":"Центральный Округ"}}]}',
		];

		return $parentData;
	}


	/**
	 * Тест сортировки
	 */
	public function testSort()
	{
		list($request, $response) = $this->provideTestData()['doctorList'];

		$api = ApiFactory::getApi('/api/rest/1.0.5/json/doctor/list' . $request);
		$expected = json_decode($response);
		$actual = json_decode($api->getRowResult());
		$this->assertEquals($expected, $actual);

		$params['order'] = '-price';
		$api = ApiFactory::getApi('/api/rest/1.0.5/json/doctor/list' . $request . '/order/-price');
		$actual = json_decode($api->getRowResult());
		$this->assertNotEquals($expected, $actual);
	}

	/**
	 * @param $partnerId
	 * @param $showSpecPrice
	 * @throws \CException
	 *
	 * @dataProvider showSpecialPriceDataProvider
	 */
	public function testShowSpecialPrice($partnerId, $showSpecPrice)
	{
		$api = ApiFactory::getApi('/api/rest/1.0.5/json/doctor/list/start/0/count/10/city/1', ['partnerId' => $partnerId]);
		$json = $api->getRowResult();
		$res = json_decode($json, true);

		$actual = array_filter(
			$res['DoctorList'],
			function($x){
				return $x['SpecialPrice'] > 0;
			}
		);

		if($showSpecPrice){
			$this->assertTrue(count($actual) > 0);
		} else {
			$this->assertFalse(count($actual) > 0);
		}
	}

	/**
	 * Данные для testShowSpecialPrice
	 * @return array
	 */
	public function showSpecialPriceDataProvider()
	{
		return [
			[2, true],  //у партнера use_special_price =1
			[1, false], //use_special_price = 0
		];
	}

	/**
	 * @param string $request
	 * @param string $response
	 *
	 * @dataProvider doctorByAliasDataProvider
	 */
	public function testDoctorByAlias($request, $response)
	{
		$fm = $this->getFixtureManager();
		$fm->checkIntegrity(false);
		$fm->basePath = ROOT_PATH . "/common/tests/fixtures/api";
		$fm->truncateTable('doctor');
		$fm->loadFixture('doctor');
		$fm->basePath = ROOT_PATH . "/common/tests/fixtures";

		$api = ApiFactory::getApi('/api/rest/1.0.5/json/doctor/by/alias' . $request);

		$this->assertEquals($api->getRowResult(), $response);
	}

	/**
	 * Провайдер для поиска врача по альясу
	 *
	 * @return array
	 */
	public function doctorByAliasDataProvider()
	{
		return [
			// активный доктор (статус = 3)
			[
				'/Nikolaev_Nikolai',
				'{"Doctor":[{"Id":2,"Name":"Николаев Николай Николаевич","Rating":"4.75","Sex":0,"Img":"http://docdoc.ru/img/doctorsNew/2_small.jpg","AddPhoneNumber":"","Category":"","Degree":"","Rank":"","Description":"Занимается лечением пациентов, имеющих эндокринную патологию репродуктивной системы; ведением беременности и родов при наличии акушерской патологии, после вспомогательных репродуктивных технологий, в том числе после ЭКО. Автор более чем 30 научных публикаций.","TextEducation":"Текст","TextAssociation":"","TextDegree":"","TextSpec":"Текст","TextCourse":"Сертификаты","TextExperience":"","ExperienceYear":' . $this->getExperience(1977) . ',"Price":2300,"SpecialPrice":0,"Departure":0,"Clinics":[1,2,3],"Alias":"Nikolaev_Nikolai","Specialities":[{"Id":"1","Name":"Акушер-гинеколог","Alias":"akusher","NameGenitive":"Акушера-гинеколога","NamePlural":"Акушеры-гинекологи","NamePluralGenitive":"Акушеров-гинекологов","IsSimple":false}],"Stations":[{"Id":"1","Name":"Авиамоторная","LineName":"","LineColor":"","CityId":"","Alias":"aviamotornaya"},{"Id":"2","Name":"Автозаводская","LineName":"Замоскворецкая","LineColor":"0a6f20","CityId":"1","Alias":"avtozavodskaya"},{"Id":"3","Name":"Академическая","LineName":"","LineColor":"","CityId":"","Alias":"akademicheskaya"}],"BookingClinics":[],"isActive":true}]}'
			],

			// неактивный доктор (статус = 4)
			[
				'/Gruk_Svetlana',
				'{"Doctor":[{"Id":1,"Name":"Грук Светлана Михайловна","Rating":"4.1","Sex":1,"Img":"http://docdoc.ru/img/doctorsNew/1_small.jpg","AddPhoneNumber":"","Category":"","Degree":"","Rank":"","Description":"Специализируется на лечении гастритов, ГЭРБ, язвенной болезни, энтероколитов, дисбактериозов, панкреатитов и других расстройств желудочно-кишечного тракта.","TextEducation":" ордена Дружбы народов медицинский университет (2004 г.)","TextAssociation":"Участник конференции","TextDegree":"Врач первой категории.","TextSpec":"&lt;div&gt;&lt;strong&gt;Профилактика и лечение=>&lt;/strong&gt;&lt;/div&gt;","TextCourse":"Первичная специализация &amp;quot;Терапия&amp;quot; (2006 г.)","TextExperience":"","ExperienceYear":' . $this->getExperience(2005) . ',"Price":800,"SpecialPrice":0,"Departure":0,"Clinics":[1],"Alias":"Gruk_Svetlana","Specialities":[{"Id":"1","Name":"Акушер-гинеколог","Alias":"akusher","NameGenitive":"Акушера-гинеколога","NamePlural":"Акушеры-гинекологи","NamePluralGenitive":"Акушеров-гинекологов","IsSimple":false}],"Stations":[{"Id":"1","Name":"Авиамоторная","LineName":"","LineColor":"","CityId":"","Alias":"aviamotornaya"},{"Id":"2","Name":"Автозаводская","LineName":"Замоскворецкая","LineColor":"0a6f20","CityId":"1","Alias":"avtozavodskaya"},{"Id":"3","Name":"Академическая","LineName":"","LineColor":"","CityId":"","Alias":"akademicheskaya"}],"BookingClinics":[1],"isActive":false}]}'
			],

			// несуществующий доктор
			[
				'/not_found',
				'{"Doctor":[]}'
			],

			// другой врач (статус = 7)
			[
				'/Nikolaev_Nikolai4',
				'{"Doctor":[]}'
			]
		];
	}
}
