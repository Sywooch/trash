<?php

namespace dfs\tests\docdoc\api\rest;

use dfs\docdoc\api\components\ApiFactory;
use dfs\docdoc\models\DoctorClinicModel;
use dfs\docdoc\models\RequestHistoryModel;
use dfs\docdoc\models\RequestModel;

/**
 * Class API_v103Test
 *
 * @package dfs\tests\docdoc\api\rest
 */
class API_v104Test extends API_v103Test
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
		$api = ApiFactory::getApi('/api/rest/1.0.4/json' . $request, $params);
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

		$parentData['cityList'] = [
			'/city',
			'{"CityList":[{"Id":"1","Name":"Москва","Alias":"msk","Phone":"74952367276"},{"Id":"2","Name":"Санкт-Петербург","Alias":"spb","Phone":"78123856652"}]}',
		];

		$parentData['metroList'] = [
			'/metro/city/1',
			'{"MetroList":[{"Id":"2","Name":"Автозаводская","LineName":"Замоскворецкая","LineColor":"0a6f20","CityId":"1","Alias":"avtozavodskaya"},{"Id":"4","Name":"Александровский сад","LineName":"Филевская","LineColor":"0099cc","CityId":"1","Alias":"aleksandrovskiy_sad"}]}',
		];

		$parentData['specialityList'] = [
			'/speciality',
			'{"SpecList":[{"Id":3,"Name":"Акушер","Alias":"akusher"}]}',
		];

		$parentData['diagnosticList'] = [
			'/diagnostic',
			'{"DiagnosticList":[{"Id":"1","Name":"УЗИ (ультразвуковое исследование)","Alias":"uzi","SubDiagnosticList":[{"Id":"71","Name":"печени","Alias":"uzi-pecheni"}]},{"Id":"19","Name":"КТ (компьютерная томография)","Alias":"komputernaya-tomografiya","SubDiagnosticList":[]},{"Id":"138","Name":"Эндоскопические методы исследования","Alias":"endoskopicheskie-issledovaniya","SubDiagnosticList":[{"Id":"139","Name":"Эзофагогастродуоденоскопия (ЭФГДС)","Alias":"efgdc"}]}]}',
		];

		$parentData['statView'] = [
			'/stat',
			'{"Requests":0,"Doctors":14,"Reviews":21602}'
		];

		$parentData['districtList'] = [
			'/district/city/1/area/1',
			'{"DistrictList":[{"Id":"1","Alias":"arbat","Name":"Арбат","Area":{"Id":"1","Alias":"cao","Name":"ЦАО","FullName":"Центральный Округ"}},{"Id":"2","Alias":"basmannyj","Name":"Басманный","Area":{"Id":"1","Alias":"cao","Name":"ЦАО","FullName":"Центральный Округ"}},{"Id":"3","Alias":"district3","Name":"Район3","Area":{"Id":"1","Alias":"cao","Name":"ЦАО","FullName":"Центральный Округ"}}]}'
		];

		$parentData['areaList'] = [
			'/area',
			'{"AreaList":[{"Id":"1","Alias":"cao","Name":"ЦАО","FullName":"Центральный Округ"},{"Id":"2","Alias":"sao","Name":"САО","FullName":"Северный Округ"}]}'
		];

		$parentData['doctorView2'] = [
			'/doctor/2',
			'{"Doctor":[{"Id":2,"Name":"Николаев Николай Николаевич","Rating":"4.75","Sex":0,"Img":"http://docdoc.ru/img/doctorsNew/2_small.jpg","AddPhoneNumber":"","Category":"","Degree":"","Rank":"","Description":"Занимается лечением пациентов, имеющих эндокринную патологию репродуктивной системы; ведением беременности и родов при наличии акушерской патологии, после вспомогательных репродуктивных технологий, в том числе после ЭКО. Автор более чем 30 научных публикаций.","TextEducation":"Текст","TextAssociation":"","TextDegree":"","TextSpec":"Текст","TextCourse":"Сертификаты","TextExperience":"","ExperienceYear":' . $this->getExperience(1977) . ',"Price":2300,"SpecialPrice":0,"Departure":0,"Clinics":[1,2,3],"Alias":"Nikolaev_Nikolai","Specialities":[{"Id":"1","Name":"Акушер-гинеколог","Alias":"akusher"}],"Stations":[{"Id":"1","Name":"Авиамоторная","LineName":"","LineColor":"","CityId":"","Alias":"aviamotornaya"},{"Id":"2","Name":"Автозаводская","LineName":"Замоскворецкая","LineColor":"0a6f20","CityId":"1","Alias":"avtozavodskaya"},{"Id":"3","Name":"Академическая","LineName":"","LineColor":"","CityId":"","Alias":"akademicheskaya"}]}]}',

		];

		$parentData['doctorList'] = [
			'/doctor/list/start/0/count/1/city/1/speciality/1',
			'{"Total":12,"DoctorList":[{"Id":2,"Name":"Николаев Николай Николаевич","Rating":"4.75","Sex":0,"Img":"http://docdoc.ru/img/doctorsNew/2_small.jpg","AddPhoneNumber":"","Category":"","Degree":"","Rank":"","Description":"Занимается лечением пациентов, имеющих эндокринную патологию репродуктивной системы; ведением беременности и родов при наличии акушерской патологии, после вспомогательных репродуктивных технологий, в том числе после ЭКО. Автор более чем 30 научных публикаций.","ExperienceYear":' . $this->getExperience(1977) . ',"Price":2300,"SpecialPrice":0,"Departure":0,"Clinics":[1,2,3],"Alias":"Nikolaev_Nikolai","Specialities":[{"Id":"1","Name":"Акушер-гинеколог","Alias":"akusher"}],"Stations":[{"Id":"1","Name":"Авиамоторная","LineName":"","LineColor":"","CityId":"","Alias":"aviamotornaya"},{"Id":"2","Name":"Автозаводская","LineName":"Замоскворецкая","LineColor":"0a6f20","CityId":"1","Alias":"avtozavodskaya"},{"Id":"3","Name":"Академическая","LineName":"","LineColor":"","CityId":"","Alias":"akademicheskaya"}],"TextAbout":"Занимается лечением пациентов, имеющих эндокринную патологию репродуктивной системы; ведением беременности и родов при наличии акушерской патологии, после вспомогательных репродуктивных технологий, в том числе после ЭКО. Автор более чем 30 научных публикаций.","InternalRating":"9.55","OpinionCount":2}]}',
		];

		$parentData['doctorListInDistrict'] = [
			'/doctor/list/start/0/count/1/city/1/district/5',
			'{"Total":0,"DoctorList":[]}',
		];

		$parentData['doctorListInDistrict1'] = [
			'/doctor/list/start/0/count/10/city/1/district/1/near/extra',
			'{"Total":10,"DoctorList":[{"Id":2,"Name":"Николаев Николай Николаевич","Rating":"4.75","Sex":0,"Img":"http://docdoc.ru/img/doctorsNew/2_small.jpg","AddPhoneNumber":"","Category":"","Degree":"","Rank":"","Description":"Занимается лечением пациентов, имеющих эндокринную патологию репродуктивной системы; ведением беременности и родов при наличии акушерской патологии, после вспомогательных репродуктивных технологий, в том числе после ЭКО. Автор более чем 30 научных публикаций.","ExperienceYear":' . $this->getExperience(1977) . ',"Price":2300,"SpecialPrice":0,"Departure":0,"Clinics":[1,2,3],"Alias":"Nikolaev_Nikolai","Specialities":[{"Id":"1","Name":"Акушер-гинеколог","Alias":"akusher"}],"Stations":[{"Id":"1","Name":"Авиамоторная","LineName":"","LineColor":"","CityId":"","Alias":"aviamotornaya"},{"Id":"2","Name":"Автозаводская","LineName":"Замоскворецкая","LineColor":"0a6f20","CityId":"1","Alias":"avtozavodskaya"},{"Id":"3","Name":"Академическая","LineName":"","LineColor":"","CityId":"","Alias":"akademicheskaya"}],"TextAbout":"Занимается лечением пациентов, имеющих эндокринную патологию репродуктивной системы; ведением беременности и родов при наличии акушерской патологии, после вспомогательных репродуктивных технологий, в том числе после ЭКО. Автор более чем 30 научных публикаций.","InternalRating":"9.55","OpinionCount":2},{"Id":3,"Name":"dfgdsfgdsg sdfgdsfg Никsdfgsdfgолаевич","Rating":"4.75","Sex":0,"Img":"http://docdoc.ru/img/doctorsNew/3_small.jpg","AddPhoneNumber":"","Category":"","Degree":"","Rank":"","Description":"Занимается лечением пациентов, имеющих эндокринную патологию репродуктивной системы; ведением беременности и родов при наличии акушерской патологии, после вспомогательных репродуктивных технологий, в том числе после ЭКО. Автор более чем 30 научных публикаций.","ExperienceYear":' . $this->getExperience(1977) . ',"Price":2500,"SpecialPrice":0,"Departure":0,"Clinics":[1,3],"Alias":"Nikolaev_Nikolai","Specialities":[{"Id":"1","Name":"Акушер-гинеколог","Alias":"akusher"}],"Stations":[{"Id":"1","Name":"Авиамоторная","LineName":"","LineColor":"","CityId":"","Alias":"aviamotornaya"},{"Id":"2","Name":"Автозаводская","LineName":"Замоскворецкая","LineColor":"0a6f20","CityId":"1","Alias":"avtozavodskaya"},{"Id":"3","Name":"Академическая","LineName":"","LineColor":"","CityId":"","Alias":"akademicheskaya"}],"TextAbout":"Занимается лечением пациентов, имеющих эндокринную патологию репродуктивной системы; ведением беременности и родов при наличии акушерской патологии, после вспомогательных репродуктивных технологий, в том числе после ЭКО. Автор более чем 30 научных публикаций.","InternalRating":"9.55","OpinionCount":0},{"Id":5,"Name":"Николаев Николай Николаевич 5","Rating":"4.75555","Sex":0,"Img":"http://docdoc.ru/img/doctorsNew/5_small.jpg","AddPhoneNumber":"","Category":"","Degree":"","Rank":"","Description":"","ExperienceYear":' . $this->getExperience(1977) . ',"Price":1300,"SpecialPrice":0,"Departure":1,"Clinics":[2],"Alias":"Nikolaev_Nikolai_5","Specialities":[{"Id":"1","Name":"Акушер-гинеколог","Alias":"akusher"}],"Stations":[{"Id":"2","Name":"Автозаводская","LineName":"Замоскворецкая","LineColor":"0a6f20","CityId":"1","Alias":"avtozavodskaya"}],"TextAbout":"","InternalRating":"9.55","OpinionCount":0},{"Id":6,"Name":"Николаев Николай Николаевич 6","Rating":"4.75555","Sex":0,"Img":"http://docdoc.ru/img/doctorsNew/6_small.jpg","AddPhoneNumber":"","Category":"","Degree":"","Rank":"","Description":"","ExperienceYear":' . $this->getExperience(1977) . ',"Price":1300,"SpecialPrice":0,"Departure":1,"Clinics":[2],"Alias":"Nikolaev_Nikolai_6","Specialities":[{"Id":"1","Name":"Акушер-гинеколог","Alias":"akusher"}],"Stations":[{"Id":"2","Name":"Автозаводская","LineName":"Замоскворецкая","LineColor":"0a6f20","CityId":"1","Alias":"avtozavodskaya"}],"TextAbout":"","InternalRating":"9.55","OpinionCount":0},{"Id":7,"Name":"Николаев Николай Николаевич 7","Rating":"4.75555","Sex":0,"Img":"http://docdoc.ru/img/doctorsNew/7_small.jpg","AddPhoneNumber":"","Category":"","Degree":"","Rank":"","Description":"","ExperienceYear":' . $this->getExperience(1977) . ',"Price":1300,"SpecialPrice":0,"Departure":0,"Clinics":[2],"Alias":"Nikolaev_Nikolai_7","Specialities":[{"Id":"1","Name":"Акушер-гинеколог","Alias":"akusher"}],"Stations":[{"Id":"2","Name":"Автозаводская","LineName":"Замоскворецкая","LineColor":"0a6f20","CityId":"1","Alias":"avtozavodskaya"}],"TextAbout":"","InternalRating":"9.55","OpinionCount":0},{"Id":8,"Name":"Николаев Николай Николаевич 8","Rating":"4.75555","Sex":0,"Img":"http://docdoc.ru/img/doctorsNew/8_small.jpg","AddPhoneNumber":"","Category":"","Degree":"","Rank":"","Description":"","ExperienceYear":' . $this->getExperience(1977) . ',"Price":1300,"SpecialPrice":0,"Departure":0,"Clinics":[2],"Alias":"Nikolaev_Nikolai_8","Specialities":[{"Id":"1","Name":"Акушер-гинеколог","Alias":"akusher"}],"Stations":[{"Id":"2","Name":"Автозаводская","LineName":"Замоскворецкая","LineColor":"0a6f20","CityId":"1","Alias":"avtozavodskaya"}],"TextAbout":"","InternalRating":"9.55","OpinionCount":0},{"Id":9,"Name":"Николаев Николай Николаевич 9","Rating":"4.75555","Sex":0,"Img":"http://docdoc.ru/img/doctorsNew/9_small.jpg","AddPhoneNumber":"","Category":"","Degree":"","Rank":"","Description":"","ExperienceYear":' . $this->getExperience(1977) . ',"Price":1300,"SpecialPrice":0,"Departure":0,"Clinics":[2],"Alias":"Nikolaev_Nikolai_9","Specialities":[{"Id":"1","Name":"Акушер-гинеколог","Alias":"akusher"}],"Stations":[{"Id":"2","Name":"Автозаводская","LineName":"Замоскворецкая","LineColor":"0a6f20","CityId":"1","Alias":"avtozavodskaya"}],"TextAbout":"","InternalRating":"9.55","OpinionCount":0},{"Id":10,"Name":"Николаев Николай Николаевич 10","Rating":"4.75555","Sex":0,"Img":"http://docdoc.ru/img/doctorsNew/10_small.jpg","AddPhoneNumber":"","Category":"","Degree":"","Rank":"","Description":"","ExperienceYear":' . $this->getExperience(1977) . ',"Price":1300,"SpecialPrice":0,"Departure":0,"Clinics":[2],"Alias":"Nikolaev_Nikolai_10","Specialities":[{"Id":"1","Name":"Акушер-гинеколог","Alias":"akusher"}],"Stations":[{"Id":"2","Name":"Автозаводская","LineName":"Замоскворецкая","LineColor":"0a6f20","CityId":"1","Alias":"avtozavodskaya"}],"TextAbout":"","InternalRating":"9.55","OpinionCount":0},{"Id":11,"Name":"Николаев Николай Николаевич 11","Rating":"4.75555","Sex":0,"Img":"http://docdoc.ru/img/doctorsNew/11_small.jpg","AddPhoneNumber":"","Category":"","Degree":"","Rank":"","Description":"","ExperienceYear":' . $this->getExperience(1977) . ',"Price":1300,"SpecialPrice":0,"Departure":0,"Clinics":[2],"Alias":"Nikolaev_Nikolai_11","Specialities":[{"Id":"1","Name":"Акушер-гинеколог","Alias":"akusher"}],"Stations":[{"Id":"2","Name":"Автозаводская","LineName":"Замоскворецкая","LineColor":"0a6f20","CityId":"1","Alias":"avtozavodskaya"}],"TextAbout":"","InternalRating":"9.55","OpinionCount":0},{"Id":12,"Name":"Николаев Николай Николаевич 12","Rating":"4.75555","Sex":0,"Img":"http://docdoc.ru/img/doctorsNew/12_small.jpg","AddPhoneNumber":"","Category":"","Degree":"","Rank":"","Description":"","ExperienceYear":' . $this->getExperience(1977) . ',"Price":1300,"SpecialPrice":0,"Departure":0,"Clinics":[2,4],"Alias":"Nikolaev_Nikolai_12","Specialities":[{"Id":"1","Name":"Акушер-гинеколог","Alias":"akusher"}],"Stations":[{"Id":"2","Name":"Автозаводская","LineName":"Замоскворецкая","LineColor":"0a6f20","CityId":"1","Alias":"avtozavodskaya"}],"TextAbout":"","InternalRating":"9.55","OpinionCount":0}]}',
		];

		$parentData['doctorListInDistrict2'] = [
			'/doctor/list/start/0/count/1/city/1/district/2',
			'{"Total":10,"DoctorList":[{"Id":2,"Name":"Николаев Николай Николаевич","Rating":"4.75","Sex":0,"Img":"http://docdoc.ru/img/doctorsNew/2_small.jpg","AddPhoneNumber":"","Category":"","Degree":"","Rank":"","Description":"Занимается лечением пациентов, имеющих эндокринную патологию репродуктивной системы; ведением беременности и родов при наличии акушерской патологии, после вспомогательных репродуктивных технологий, в том числе после ЭКО. Автор более чем 30 научных публикаций.","ExperienceYear":' . $this->getExperience(1977) . ',"Price":2300,"SpecialPrice":0,"Departure":0,"Clinics":[1,2,3],"Alias":"Nikolaev_Nikolai","Specialities":[{"Id":"1","Name":"Акушер-гинеколог","Alias":"akusher"}],"Stations":[{"Id":"1","Name":"Авиамоторная","LineName":"","LineColor":"","CityId":"","Alias":"aviamotornaya"},{"Id":"2","Name":"Автозаводская","LineName":"Замоскворецкая","LineColor":"0a6f20","CityId":"1","Alias":"avtozavodskaya"},{"Id":"3","Name":"Академическая","LineName":"","LineColor":"","CityId":"","Alias":"akademicheskaya"}],"TextAbout":"Занимается лечением пациентов, имеющих эндокринную патологию репродуктивной системы; ведением беременности и родов при наличии акушерской патологии, после вспомогательных репродуктивных технологий, в том числе после ЭКО. Автор более чем 30 научных публикаций.","InternalRating":"9.55","OpinionCount":2}]}',
		];

		$parentData['doctorListInArea'] = [
			'/doctor/list/start/0/count/1/city/1/area/5',
			'{"Total":0,"DoctorList":[]}',
		];

		$parentData['doctorListInArea1'] = [
			'/doctor/list/start/0/count/1/city/1/area/1',
			'{"Total":11,"DoctorList":[{"Id":2,"Name":"Николаев Николай Николаевич","Rating":"4.75","Sex":0,"Img":"http://docdoc.ru/img/doctorsNew/2_small.jpg","AddPhoneNumber":"","Category":"","Degree":"","Rank":"","Description":"Занимается лечением пациентов, имеющих эндокринную патологию репродуктивной системы; ведением беременности и родов при наличии акушерской патологии, после вспомогательных репродуктивных технологий, в том числе после ЭКО. Автор более чем 30 научных публикаций.","ExperienceYear":' . $this->getExperience(1977) . ',"Price":2300,"SpecialPrice":0,"Departure":0,"Clinics":[1,2,3],"Alias":"Nikolaev_Nikolai","Specialities":[{"Id":"1","Name":"Акушер-гинеколог","Alias":"akusher"}],"Stations":[{"Id":"1","Name":"Авиамоторная","LineName":"","LineColor":"","CityId":"","Alias":"aviamotornaya"},{"Id":"2","Name":"Автозаводская","LineName":"Замоскворецкая","LineColor":"0a6f20","CityId":"1","Alias":"avtozavodskaya"},{"Id":"3","Name":"Академическая","LineName":"","LineColor":"","CityId":"","Alias":"akademicheskaya"}],"TextAbout":"Занимается лечением пациентов, имеющих эндокринную патологию репродуктивной системы; ведением беременности и родов при наличии акушерской патологии, после вспомогательных репродуктивных технологий, в том числе после ЭКО. Автор более чем 30 научных публикаций.","InternalRating":"9.55","OpinionCount":2}]}',
		];

		$parentData['clinicList'] = [
			'/clinic/list/start/0/count/1/city/1/type/1/partnerId/1',
			'{"Total":4,"ClinicList":[{"Id":1,"Name":"Клиника \u21161","ShortName":"Клиника \u21161","RewriteName":"clinica_11","URL":"http://www.clinicanomer1.ru","Longitude":"55.675702","Latitude":"37.767699","City":"Москва","Street":"Краснодарская улица","House":"д. 52, корп. 2","Description":"Многопрофильный медицинский центр, специализирующийся на проведении диагностического обследования взрослых и детей от 14 лет. Клиника расположена в шаговой близости от метро Люблино (5-10 мин.) Прием происходит по предварительной записи по многоканальному телефону +7 (495) 988-01-64.","WeekdaysOpen":null,"WeekendOpen":null,"ShortDescription":"Многопрофильный медицинский центр, специализирующийся на проведении диагностического обследования взрослых и детей от 14 лет. Клиника расположена в шаговой близости от метро Люблино (5-10 мин.) Прием происходит по предварительной записи по многоканальному телефону +7 (495) 988-01-64.","IsDiagnostic":"yes","isClinic":"yes","IsDoctor":"no","Phone":"74956410606","PhoneAppointment":"+7 (495) 641-06-06","logoPath":"1.png","ScheduleState":"enable","Email":"","ReplacementPhone":"79000000777","MinPrice":"2300","MaxPrice":"2500","Logo":"http://docdoc.ru/upload/kliniki/logo/1.png","Diagnostics":[{"Id":"1","Name":"УЗИ (ультразвуковое исследование)","Price":"4500.00","SpecialPrice":"0.00"}],"Stations":[{"Id":"1","Name":"Авиамоторная","LineName":null,"LineColor":null,"CityId":null},{"Id":"2","Name":"Автозаводская","LineName":"Замоскворецкая","LineColor":"0a6f20","CityId":"1"},{"Id":"3","Name":"Академическая","LineName":null,"LineColor":null,"CityId":null}],"Specialities":[{"Id":"1","Name":"Акушер-гинеколог","Alias":"akusher"}]}]}',
			['partnerId' => 1],
		];

		$parentData['clinicListWithReplacementPhone'] = [
			'/clinic/list/start/0/count/1/city/1/type/1/partnerId/1',
			'{"Total":4,"ClinicList":[{"Id":1,"Name":"Клиника \u21161","ShortName":"Клиника \u21161","RewriteName":"clinica_11","URL":"http://www.clinicanomer1.ru","Longitude":"55.675702","Latitude":"37.767699","City":"Москва","Street":"Краснодарская улица","House":"д. 52, корп. 2","Description":"Многопрофильный медицинский центр, специализирующийся на проведении диагностического обследования взрослых и детей от 14 лет. Клиника расположена в шаговой близости от метро Люблино (5-10 мин.) Прием происходит по предварительной записи по многоканальному телефону +7 (495) 988-01-64.","WeekdaysOpen":null,"WeekendOpen":null,"ShortDescription":"Многопрофильный медицинский центр, специализирующийся на проведении диагностического обследования взрослых и детей от 14 лет. Клиника расположена в шаговой близости от метро Люблино (5-10 мин.) Прием происходит по предварительной записи по многоканальному телефону +7 (495) 988-01-64.","IsDiagnostic":"yes","isClinic":"yes","IsDoctor":"no","Phone":"74956410606","PhoneAppointment":"+7 (495) 641-06-06","logoPath":"1.png","ScheduleState":"enable","Email":"","ReplacementPhone":"79000000777","MinPrice":"2300","MaxPrice":"2500","Logo":"http://docdoc.ru/upload/kliniki/logo/1.png","Diagnostics":[{"Id":"1","Name":"УЗИ (ультразвуковое исследование)","Price":"4500.00","SpecialPrice":"0.00"}],"Stations":[{"Id":"1","Name":"Авиамоторная","LineName":null,"LineColor":null,"CityId":null},{"Id":"2","Name":"Автозаводская","LineName":"Замоскворецкая","LineColor":"0a6f20","CityId":"1"},{"Id":"3","Name":"Академическая","LineName":null,"LineColor":null,"CityId":null}],"Specialities":[{"Id":"1","Name":"Акушер-гинеколог","Alias":"akusher"}]}]}',
			['partnerId' => 1],
		];

		$parentData['clinicListWithReplacementPhoneAsNull'] = [
			'/clinic/list/start/0/count/1/city/1/type/1/partnerId/999',
			'{"Total":4,"ClinicList":[{"Id":1,"Name":"Клиника \u21161","ShortName":"Клиника \u21161","RewriteName":"clinica_11","URL":"http://www.clinicanomer1.ru","Longitude":"55.675702","Latitude":"37.767699","City":"Москва","Street":"Краснодарская улица","House":"д. 52, корп. 2","Description":"Многопрофильный медицинский центр, специализирующийся на проведении диагностического обследования взрослых и детей от 14 лет. Клиника расположена в шаговой близости от метро Люблино (5-10 мин.) Прием происходит по предварительной записи по многоканальному телефону +7 (495) 988-01-64.","WeekdaysOpen":null,"WeekendOpen":null,"ShortDescription":"Многопрофильный медицинский центр, специализирующийся на проведении диагностического обследования взрослых и детей от 14 лет. Клиника расположена в шаговой близости от метро Люблино (5-10 мин.) Прием происходит по предварительной записи по многоканальному телефону +7 (495) 988-01-64.","IsDiagnostic":"yes","isClinic":"yes","IsDoctor":"no","Phone":"74956410606","PhoneAppointment":"+7 (495) 641-06-06","logoPath":"1.png","ScheduleState":"enable","Email":"","ReplacementPhone":null,"MinPrice":"2300","MaxPrice":"2500","Logo":"http://docdoc.ru/upload/kliniki/logo/1.png","Diagnostics":[{"Id":"1","Name":"УЗИ (ультразвуковое исследование)","Price":"4500.00","SpecialPrice":"0.00"}],"Stations":[{"Id":"1","Name":"Авиамоторная","LineName":null,"LineColor":null,"CityId":null},{"Id":"2","Name":"Автозаводская","LineName":"Замоскворецкая","LineColor":"0a6f20","CityId":"1"},{"Id":"3","Name":"Академическая","LineName":null,"LineColor":null,"CityId":null}],"Specialities":[{"Id":"1","Name":"Акушер-гинеколог","Alias":"akusher"}]}]}',
			['partnerId' => 1],

		];

		return $parentData;
	}

	/**
	 * Тест сортировки
	 */
	public function testSort()
	{
		list($request, $response) = $this->provideTestData()['doctorList'];

		$expected = json_decode($response, true);

		$api = ApiFactory::getApi('/api/rest/1.0.4/json' . $request);
		$res = $api->getRowResult();
		$actual = json_decode($res, true);
		$this->assertEquals($expected, $actual);

		$api = ApiFactory::getApi('/api/rest/1.0.4/json' . $request . '/order/-rating');
		$res = $api->getRowResult();
		$actual = json_decode($res, true);
		$this->assertNotEquals($expected, $actual);

	}

	/**
	 * Запросы для проверки списка докторов
	 *
	 * @return array
	 */
	public function provideTestDoctorList()
	{
		return [
			[
				'data' => [
					'city' => 1,
					'speciality' => null,
					'stations' => null,
					'near' => null,
					'start' => '0',
					'count' => '5',
				],
			],
			[
				'data' => [
					'city' => 1,
					'count' => '3',
				],
			],
			[
				'data' => [
					'city' => 1,
					'speciality' => '1',
				],
			],
			[
				'data' => [
					'city' => 1,
					'speciality' => '1',
					'count' => '5',
				],
			],
			[
				'data' => [
					'city' => 1,
					'speciality' => '1',
					'stations' => '4',
				],
				'countFind' => 0,
			],
			[
				'data' => [
					'city' => 1,
					'speciality' => '1',
					'stations' => '2',
					'near' => 'mixed',
				],
				'countFind' => 12,
			],
			[
				'data' => [
					'city' => 1,
					'speciality' => '1',
					'stations' => '4',
					'near' => 'extra',
				],
				'countFind' => 10,
			],
		];
	}

	/**
	 * Тест получения списка докторов
	 *
	 * @dataProvider provideTestDoctorList
	 *
	 * @param array $data
	 * @param int $countFind
	 */
	public function testDoctorListCount($data, $countFind = null)
	{
		$start = isset($data['start']) ? $data['start'] : null;
		$count = isset($data['count']) ? $data['count'] : null;
		$near = isset($data['near']) ? $data['near'] : null;

		$params = [
			'dataType' => 'json',
			'rawData'   => '',
			'start' => $start,
			'count' => $count,
			'near' => $near,
		];

		$where = [];

		if (isset($data['city'])) {
			$params['city'] = $data['city'];
			$where[] = 'c.city_id = ' . intval($data['city']);
		}
		if (isset($data['speciality'])) {
			$params['speciality'] = $data['speciality'];
			$where[] = 'ds.sector_id = ' . intval($data['speciality']);
		}
		if (isset($data['stations'])) {
			$params['stations'] = $data['stations'];
			$where[] = 'us4c.undegraund_station_id = ' . intval($data['stations']);
		}

		if ($countFind === null) {
			$sql = 'SELECT COUNT(DISTINCT(d.id))
				FROM doctor as d
					INNER JOIN doctor_4_clinic as d4c ON (d.id = d4c.doctor_id and d4c.type = ' . DoctorClinicModel::TYPE_DOCTOR . ')
					INNER JOIN clinic as c ON (d4c.clinic_id = c.id)
					LEFT JOIN underground_station_4_clinic as us4c ON (us4c.clinic_id = c.id)
					LEFT JOIN doctor_sector as ds ON (ds.doctor_id = d.id)
				WHERE d.status = 3 AND c.status = 3 AND (c.isClinic = "yes" OR c.isPrivatDoctor = "yes")';

			if ($where) {
				$sql .= ' AND ' . implode(' AND ', $where);
			}

			$countFind = \Yii::app()->getDb()->createCommand($sql)->queryScalar();
		}

		//$api = new API_v104('doctor', 'list', $params);
		$api = ApiFactory::getApi('/api/rest/1.0.4/json/doctor/list', $params);
		$result = json_decode($api->getRowResult(), true);

		$this->assertEquals($result['Total'], $countFind);
		$this->assertEquals(count($result['DoctorList']), $count === null ? $result['Total'] : ($count < $countFind ? $count : $countFind));
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
		$this->getFixtureManager()->truncateTable('request');
		$this->getFixtureManager()->truncateTable('request_history');

		$api = ApiFactory::getApi('/api/rest/1.0.4/json/request', $params);
		$this->assertEquals($response, $api->getRowResult());

		if(isset($api->result['Response']['status']) && $api->result['Response']['status'] === 'success'){
			$requestId = RequestModel::model()->find()->req_id;
			$this->assertEquals(3, RequestHistoryModel::model()->count("request_id = :request_id", [':request_id' => $requestId]));
		}
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
						'clinic' => 1,
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
						'clinic' => 1,
						'doctor' => 999999,
					)),
				),
				'{"Response":{"status":"error","message":"Нет такого врача в системе"}}'
			),
			array(
				array(
					'dataType' => 'json',
					'partnerId' => 1,
					'rawData' => json_encode(array(
						'name' => 'Тест Тест',
						'phone' => '79261234567',
						'clinic' => 1,
						'doctor' => 91511111111111111111111111111111,
					)),
				),
				'{"Response":{"status":"error","message":"Нет такого врача в системе"}}'
			),
			array(
				//несуществующая клиника. клиника возмется с врача
				array(
					'dataType' => 'json',
					'partnerId' => 1,
					'rawData' => json_encode(array(
						'name' => 'Тест Тест',
						'phone' => '79261234567',
						'clinic' => 12544544444444444444444444444444,
						'doctor' => 1,
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
						'doctor' => 1,
						'comment' => 'Запись к врачу',
					)),
				),
				'{"Response":{"status":"error","message":"Не передана клиника"}}'
			),
		);
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

		$api = ApiFactory::getApi('/api/rest/1.0.4/json/doctor/by/alias' . $request);

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
				'{"Doctor":[{"Id":2,"Name":"Николаев Николай Николаевич","Rating":"4.75","Sex":0,"Img":"http://docdoc.ru/img/doctorsNew/2_small.jpg","AddPhoneNumber":"","Category":"","Degree":"","Rank":"","Description":"Занимается лечением пациентов, имеющих эндокринную патологию репродуктивной системы; ведением беременности и родов при наличии акушерской патологии, после вспомогательных репродуктивных технологий, в том числе после ЭКО. Автор более чем 30 научных публикаций.","TextEducation":"Текст","TextAssociation":"","TextDegree":"","TextSpec":"Текст","TextCourse":"Сертификаты","TextExperience":"","ExperienceYear":' . $this->getExperience(1977) . ',"Price":2300,"SpecialPrice":0,"Departure":0,"Clinics":[1,2,3],"Alias":"Nikolaev_Nikolai","Specialities":[{"Id":"1","Name":"Акушер-гинеколог","Alias":"akusher"}],"Stations":[{"Id":"1","Name":"Авиамоторная","LineName":"","LineColor":"","CityId":"","Alias":"aviamotornaya"},{"Id":"2","Name":"Автозаводская","LineName":"Замоскворецкая","LineColor":"0a6f20","CityId":"1","Alias":"avtozavodskaya"},{"Id":"3","Name":"Академическая","LineName":"","LineColor":"","CityId":"","Alias":"akademicheskaya"}]}]}'
			],

			// неактивный доктор (статус = 4)
			[
				'/Gruk_Svetlana',
				'{"Doctor":[{"Id":1,"Name":"Грук Светлана Михайловна","Rating":"4.1","Sex":1,"Img":"http://docdoc.ru/img/doctorsNew/1_small.jpg","AddPhoneNumber":"","Category":"","Degree":"","Rank":"","Description":"Специализируется на лечении гастритов, ГЭРБ, язвенной болезни, энтероколитов, дисбактериозов, панкреатитов и других расстройств желудочно-кишечного тракта.","TextEducation":" ордена Дружбы народов медицинский университет (2004 г.)","TextAssociation":"Участник конференции","TextDegree":"Врач первой категории.","TextSpec":"&lt;div&gt;&lt;strong&gt;Профилактика и лечение=>&lt;/strong&gt;&lt;/div&gt;","TextCourse":"Первичная специализация &amp;quot;Терапия&amp;quot; (2006 г.)","TextExperience":"","ExperienceYear":' . $this->getExperience(2005) . ',"Price":800,"SpecialPrice":0,"Departure":0,"Clinics":[1],"Alias":"Gruk_Svetlana","Specialities":[{"Id":"1","Name":"Акушер-гинеколог","Alias":"akusher"}],"Stations":[{"Id":"1","Name":"Авиамоторная","LineName":"","LineColor":"","CityId":"","Alias":"aviamotornaya"},{"Id":"2","Name":"Автозаводская","LineName":"Замоскворецкая","LineColor":"0a6f20","CityId":"1","Alias":"avtozavodskaya"},{"Id":"3","Name":"Академическая","LineName":"","LineColor":"","CityId":"","Alias":"akademicheskaya"}]}]}'
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
