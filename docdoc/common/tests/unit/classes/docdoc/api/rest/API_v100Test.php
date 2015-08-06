<?php

namespace dfs\tests\docdoc\api\rest;

require_once ROOT_PATH . "/back/public/lib/php/validate.php";
require_once ROOT_PATH . "/back/public/lib/php/russianTextUtils.class.php";

use dfs\docdoc\api\components\ApiFactory;
use CDbTestCase;
use Yii;
use PHPUnit_Framework_Constraint_IsType;
use stdClass;

/**
 * Class API_v100Test
 *
 * @package dfs\tests\docdoc\api\rest
 */
class API_v100Test extends CDbTestCase {

	/**
	 * Массив для хранения признаков, что перед запуском тестов в классе была полностью очищена база
	 * @var array
	 */
	public static $fixturesLoaded = [];


	public function setUp()
	{
		//перед запуском теста на необходимо очистить целиком всю базу
		//для быстродействия неоьбходимо очищать базу всего один раз для каждого наследника этого класса
		//для этого проверка ниже
		if (!isset(self::$fixturesLoaded[get_class($this)])) {

			$fm = $this->getFixtureManager();
			$fm->basePath = ROOT_PATH . "/common/tests/fixtures/api";

			$fm->checkIntegrity(false);
			$fm->truncateTables();
			$fm->load([
					':area_moscow',
					':district',
					':closest_district',
					':request',
					':clinic',
					':clinic_partner_phone',
					':phone',
					':doctor_4_clinic',
					':doctor',
					':doctor_sector',
					':doctor_opinion',
					':sector',
					':diagnostica',
					':diagnostica4clinic',
					':underground_station',
					':underground_station_4_clinic',
					':underground_line',
					':city',
					':partner',
					':slot',
					':closest_station',
					':rating',
					':rating_strategy',
					':api_doctor',
					':api_clinic',
					':street_dict',
				]);

			self::$fixturesLoaded[get_class($this)] = true;

			$fm = $this->getFixtureManager();
			$fm->basePath = ROOT_PATH . "/common/tests/fixtures";
		}

	}

	/**
	 * Типы полей в обЪекте клиники
	 *
	 * @var array
	 */
	public $clinicTypes = array(
		'Id' => PHPUnit_Framework_Constraint_IsType::TYPE_INT,
		'Name' => PHPUnit_Framework_Constraint_IsType::TYPE_STRING,
		'ShortName' => PHPUnit_Framework_Constraint_IsType::TYPE_STRING,
		'RewriteName' => PHPUnit_Framework_Constraint_IsType::TYPE_STRING,
		'URL' => PHPUnit_Framework_Constraint_IsType::TYPE_STRING,
		'Longitude' => PHPUnit_Framework_Constraint_IsType::TYPE_STRING,
		'Latitude' => PHPUnit_Framework_Constraint_IsType::TYPE_STRING,
		'City' => PHPUnit_Framework_Constraint_IsType::TYPE_STRING,
		'Street' => PHPUnit_Framework_Constraint_IsType::TYPE_STRING,
		'House' => PHPUnit_Framework_Constraint_IsType::TYPE_STRING,
		'Description' => PHPUnit_Framework_Constraint_IsType::TYPE_STRING,
		'WeekdaysOpen' => PHPUnit_Framework_Constraint_IsType::TYPE_STRING,
		'WeekendOpen' => PHPUnit_Framework_Constraint_IsType::TYPE_STRING,
		'ShortDescription' => PHPUnit_Framework_Constraint_IsType::TYPE_STRING,
		'IsDiagnostic' => PHPUnit_Framework_Constraint_IsType::TYPE_STRING,
		'Phone' => PHPUnit_Framework_Constraint_IsType::TYPE_STRING,
		'PhoneAppointment' => PHPUnit_Framework_Constraint_IsType::TYPE_STRING,
		'logoPath' => PHPUnit_Framework_Constraint_IsType::TYPE_STRING,
		'ScheduleState' => PHPUnit_Framework_Constraint_IsType::TYPE_STRING,
		'Email' => PHPUnit_Framework_Constraint_IsType::TYPE_STRING,
		'Logo' => PHPUnit_Framework_Constraint_IsType::TYPE_STRING,
		'Diagnostics' => PHPUnit_Framework_Constraint_IsType::TYPE_ARRAY,
		'Stations' => PHPUnit_Framework_Constraint_IsType::TYPE_ARRAY,
	);

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

		$api = ApiFactory::getApi('/api/rest/1.0.0/json/clinic/list' . $request);
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
			// Получение только активных клиник
			array(
				'/start/0/count/10/city/1',
				4,
			),

			//  Тест проверка на получение клиник из Питера
			array(
				'/start/0/count/10/city/2',
				0,
			),

			// Тест проверка на получение клиник по станциям метро
			array(
				'/start/0/count/10/city/1/stations/1,2,3/near/strict',
				3,
			),

			// Тест start
			array(
				'/start/1/count/10/city/1/stations/1,2,3/near/strict',
				2, 3
			),

			// Тест count
			array(
				'/start/0/count/1/city/1/stations/1,2,3/near/strict',
				1, 3
			),
		);
	}

	/**
	 * Проверяем обьект клиники
	 *
	 * @param stdClass $clinic
	 * @param string $msg
	 */
	public function assertClinic(stdClass $clinic, $msg = null)
	{
		foreach($this->clinicTypes as $name=>$type) {
			if ( in_array($name, array('Diagnostics'))
				&& !array_key_exists($name, (array)$clinic)
			) {
				continue;
			}

			$this->objectHasAttribute($clinic, $name, $msg);
			$value = $clinic->$name;

			if ( in_array($name, array('WeekdaysOpen', 'WeekendOpen', 'Phone'))
				&& is_null($value)
			) {
				continue;
			}
			$this->assertInternalType($type, $value, "{$msg}; attr: {$name}");

			if ( in_array($name, array('Email'))
				&& empty($value)
			) {
				continue;
			}
			if ($type === PHPUnit_Framework_Constraint_IsType::TYPE_INT) {
				$this->assertGreaterThan(0, $value, "{$msg}; attr: {$name}");
			} elseif ($type === PHPUnit_Framework_Constraint_IsType::TYPE_STRING) {
				$this->assertGreaterThan(0, strlen($value), "{$msg}; attr: {$name}");
			}
		}
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
		$api = ApiFactory::getApi('/api/rest/1.0.0/json' . $request, $params);
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
				'{"CityList":[{"Id":"1","Name":"\u041c\u043e\u0441\u043a\u0432\u0430"},{"Id":"2","Name":"\u0421\u0430\u043d\u043a\u0442-\u041f\u0435\u0442\u0435\u0440\u0431\u0443\u0440\u0433"}]}',
				['partnerId' => 1],

			],
			'metroList' => [
				'/metro/city/1',
				'{"MetroList":[{"Id":"2","Name":"\u0410\u0432\u0442\u043e\u0437\u0430\u0432\u043e\u0434\u0441\u043a\u0430\u044f","LineName":"\u0417\u0430\u043c\u043e\u0441\u043a\u0432\u043e\u0440\u0435\u0446\u043a\u0430\u044f","LineColor":"0a6f20","CityId":"1"},{"Id":"4","Name":"\u0410\u043b\u0435\u043a\u0441\u0430\u043d\u0434\u0440\u043e\u0432\u0441\u043a\u0438\u0439 \u0441\u0430\u0434","LineName":"\u0424\u0438\u043b\u0435\u0432\u0441\u043a\u0430\u044f","LineColor":"0099cc","CityId":"1"}]}',

			],
			'specialityList' => [
				'/speciality',
				'{"SpecList":[{"Id":3,"Name":"Акушер"}]}',
			],
			'diagnosticList' => [
				'/diagnostic',
				'{"DiagnosticList":[{"Id":"1","Name":"\u0423\u0417\u0418 (\u0443\u043b\u044c\u0442\u0440\u0430\u0437\u0432\u0443\u043a\u043e\u0432\u043e\u0435 \u0438\u0441\u0441\u043b\u0435\u0434\u043e\u0432\u0430\u043d\u0438\u0435)","SubDiagnosticList":[{"Id":"71","Name":"\u043f\u0435\u0447\u0435\u043d\u0438"}]},{"Id":"19","Name":"\u041a\u0422 (\u043a\u043e\u043c\u043f\u044c\u044e\u0442\u0435\u0440\u043d\u0430\u044f \u0442\u043e\u043c\u043e\u0433\u0440\u0430\u0444\u0438\u044f)","SubDiagnosticList":[]},{"Id":"138","Name":"\u042d\u043d\u0434\u043e\u0441\u043a\u043e\u043f\u0438\u0447\u0435\u0441\u043a\u0438\u0435 \u043c\u0435\u0442\u043e\u0434\u044b \u0438\u0441\u0441\u043b\u0435\u0434\u043e\u0432\u0430\u043d\u0438\u044f","SubDiagnosticList":[{"Id":"139","Name":"\u042d\u0437\u043e\u0444\u0430\u0433\u043e\u0433\u0430\u0441\u0442\u0440\u043e\u0434\u0443\u043e\u0434\u0435\u043d\u043e\u0441\u043a\u043e\u043f\u0438\u044f (\u042d\u0424\u0413\u0414\u0421)"}]}]}',

			],
			//неактивный врач
			'doctorView' => [
				'/doctor/1',
				'{"Doctor":[{"Id":"1","Name":"Грук Светлана Михайловна","Rating":"4.1","Sex":1,"Img":"http://docdoc.ru/img/doctorsNew/1_small.jpg","AddPhoneNumber":"","Category":"","Degree":"","Rank":"","Description":"Специализируется на лечении гастритов, ГЭРБ, язвенной болезни, энтероколитов, дисбактериозов, панкреатитов и других расстройств желудочно-кишечного тракта.","TextEducation":"ордена Дружбы народов медицинский университет (2004 г.)","TextAssociation":"Участник конференции","TextDegree":"Врач первой категории.","TextSpec":"&amp;lt;div&amp;gt;&amp;lt;strong&amp;gt;Профилактика и лечение=&gt;&amp;lt;/strong&amp;gt;&amp;lt;/div&amp;gt;","TextCourse":"Первичная специализация &amp;amp;quot;Терапия&amp;amp;quot; (2006 г.)","TextExperience":"","ExperienceYear":' . $this->getExperience(2005) . ',"Price":800,"SpecialPrice":0,"Departure":0}]}',

			],
			'doctorView2' => [
				'/doctor/2',
				'{"Doctor":[{"Id":"2","Name":"\u041d\u0438\u043a\u043e\u043b\u0430\u0435\u0432 \u041d\u0438\u043a\u043e\u043b\u0430\u0439 \u041d\u0438\u043a\u043e\u043b\u0430\u0435\u0432\u0438\u0447","Rating":"4.75","Sex":0,"Img":"http://docdoc.ru/img/doctorsNew/2_small.jpg","AddPhoneNumber":"","Category":"","Degree":"","Rank":"","Description":"\u0417\u0430\u043d\u0438\u043c\u0430\u0435\u0442\u0441\u044f \u043b\u0435\u0447\u0435\u043d\u0438\u0435\u043c \u043f\u0430\u0446\u0438\u0435\u043d\u0442\u043e\u0432, \u0438\u043c\u0435\u044e\u0449\u0438\u0445 \u044d\u043d\u0434\u043e\u043a\u0440\u0438\u043d\u043d\u0443\u044e \u043f\u0430\u0442\u043e\u043b\u043e\u0433\u0438\u044e \u0440\u0435\u043f\u0440\u043e\u0434\u0443\u043a\u0442\u0438\u0432\u043d\u043e\u0439 \u0441\u0438\u0441\u0442\u0435\u043c\u044b; \u0432\u0435\u0434\u0435\u043d\u0438\u0435\u043c \u0431\u0435\u0440\u0435\u043c\u0435\u043d\u043d\u043e\u0441\u0442\u0438 \u0438 \u0440\u043e\u0434\u043e\u0432 \u043f\u0440\u0438 \u043d\u0430\u043b\u0438\u0447\u0438\u0438 \u0430\u043a\u0443\u0448\u0435\u0440\u0441\u043a\u043e\u0439 \u043f\u0430\u0442\u043e\u043b\u043e\u0433\u0438\u0438, \u043f\u043e\u0441\u043b\u0435 \u0432\u0441\u043f\u043e\u043c\u043e\u0433\u0430\u0442\u0435\u043b\u044c\u043d\u044b\u0445 \u0440\u0435\u043f\u0440\u043e\u0434\u0443\u043a\u0442\u0438\u0432\u043d\u044b\u0445 \u0442\u0435\u0445\u043d\u043e\u043b\u043e\u0433\u0438\u0439, \u0432 \u0442\u043e\u043c \u0447\u0438\u0441\u043b\u0435 \u043f\u043e\u0441\u043b\u0435 \u042d\u041a\u041e. \u0410\u0432\u0442\u043e\u0440 \u0431\u043e\u043b\u0435\u0435 \u0447\u0435\u043c 30 \u043d\u0430\u0443\u0447\u043d\u044b\u0445 \u043f\u0443\u0431\u043b\u0438\u043a\u0430\u0446\u0438\u0439.","TextEducation":"\u0422\u0435\u043a\u0441\u0442","TextAssociation":"","TextDegree":"","TextSpec":"\u0422\u0435\u043a\u0441\u0442","TextCourse":"\u0421\u0435\u0440\u0442\u0438\u0444\u0438\u043a\u0430\u0442\u044b","TextExperience":"","ExperienceYear":' . $this->getExperience(1977) . ',"Price":2300,"SpecialPrice":0,"Departure":0}]}',
			],
			'doctorList' => [
				'/doctor/list/start/0/count/1/city/1/speciality/1',
				'{"Total":12,"DoctorList":[{"Id":"2","Name":"\u041d\u0438\u043a\u043e\u043b\u0430\u0435\u0432 \u041d\u0438\u043a\u043e\u043b\u0430\u0439 \u041d\u0438\u043a\u043e\u043b\u0430\u0435\u0432\u0438\u0447","Alias":"Nikolaev_Nikolai","Rating":"4.75","InternalRating":"9.55","Price":"2300","SpecialPrice":null,"Sex":0,"Img":"http://docdoc.ru/img/doctorsNew/2_small.jpg","OpinionCount":2,"TextAbout":"\u0417\u0430\u043d\u0438\u043c\u0430\u0435\u0442\u0441\u044f \u043b\u0435\u0447\u0435\u043d\u0438\u0435\u043c \u043f\u0430\u0446\u0438\u0435\u043d\u0442\u043e\u0432, \u0438\u043c\u0435\u044e\u0449\u0438\u0445 \u044d\u043d\u0434\u043e\u043a\u0440\u0438\u043d\u043d\u0443\u044e \u043f\u0430\u0442\u043e\u043b\u043e\u0433\u0438\u044e \u0440\u0435\u043f\u0440\u043e\u0434\u0443\u043a\u0442\u0438\u0432\u043d\u043e\u0439 \u0441\u0438\u0441\u0442\u0435\u043c\u044b; \u0432\u0435\u0434\u0435\u043d\u0438\u0435\u043c \u0431\u0435\u0440\u0435\u043c\u0435\u043d\u043d\u043e\u0441\u0442\u0438 \u0438 \u0440\u043e\u0434\u043e\u0432 \u043f\u0440\u0438 \u043d\u0430\u043b\u0438\u0447\u0438\u0438 \u0430\u043a\u0443\u0448\u0435\u0440\u0441\u043a\u043e\u0439 \u043f\u0430\u0442\u043e\u043b\u043e\u0433\u0438\u0438, \u043f\u043e\u0441\u043b\u0435 \u0432\u0441\u043f\u043e\u043c\u043e\u0433\u0430\u0442\u0435\u043b\u044c\u043d\u044b\u0445 \u0440\u0435\u043f\u0440\u043e\u0434\u0443\u043a\u0442\u0438\u0432\u043d\u044b\u0445 \u0442\u0435\u0445\u043d\u043e\u043b\u043e\u0433\u0438\u0439, \u0432 \u0442\u043e\u043c \u0447\u0438\u0441\u043b\u0435 \u043f\u043e\u0441\u043b\u0435 \u042d\u041a\u041e. \u0410\u0432\u0442\u043e\u0440 \u0431\u043e\u043b\u0435\u0435 \u0447\u0435\u043c 30 \u043d\u0430\u0443\u0447\u043d\u044b\u0445 \u043f\u0443\u0431\u043b\u0438\u043a\u0430\u0446\u0438\u0439.","ExperienceYear":' . $this->getExperience(1977) . ',"Departure":"0","Category":null,"Degree":null,"Rank":null,"Specialities":[{"Id":"1","Name":"\u0410\u043a\u0443\u0448\u0435\u0440-\u0433\u0438\u043d\u0435\u043a\u043e\u043b\u043e\u0433"}],"Stations":[{"doctor_id":"2","Id":"1","Name":"\u0410\u0432\u0438\u0430\u043c\u043e\u0442\u043e\u0440\u043d\u0430\u044f","Alias":"aviamotornaya","LineId":null,"LineName":null,"LineColor":null,"CityId":null},{"doctor_id":"2","Id":"2","Name":"\u0410\u0432\u0442\u043e\u0437\u0430\u0432\u043e\u0434\u0441\u043a\u0430\u044f","Alias":"avtozavodskaya","LineId":"1","LineName":"\u0417\u0430\u043c\u043e\u0441\u043a\u0432\u043e\u0440\u0435\u0446\u043a\u0430\u044f","LineColor":"0a6f20","CityId":"1"},{"doctor_id":"2","Id":"3","Name":"\u0410\u043a\u0430\u0434\u0435\u043c\u0438\u0447\u0435\u0441\u043a\u0430\u044f","Alias":"akademicheskaya","LineId":null,"LineName":null,"LineColor":null,"CityId":null}]}]}',

			],
			'clinicList' => [
				'/clinic/list/start/0/count/1/city/1/type/1',
				'{"Total":4,"ClinicList":[{"Id":1,"Name":"\u041a\u043b\u0438\u043d\u0438\u043a\u0430 \u21161","ShortName":"\u041a\u043b\u0438\u043d\u0438\u043a\u0430 \u21161","RewriteName":"clinica_11","URL":"http://www.clinicanomer1.ru","Longitude":"55.675702","Latitude":"37.767699","City":"\u041c\u043e\u0441\u043a\u0432\u0430","Street":"\u041a\u0440\u0430\u0441\u043d\u043e\u0434\u0430\u0440\u0441\u043a\u0430\u044f \u0443\u043b\u0438\u0446\u0430","House":"\u0434. 52, \u043a\u043e\u0440\u043f. 2","Description":"\u041c\u043d\u043e\u0433\u043e\u043f\u0440\u043e\u0444\u0438\u043b\u044c\u043d\u044b\u0439 \u043c\u0435\u0434\u0438\u0446\u0438\u043d\u0441\u043a\u0438\u0439 \u0446\u0435\u043d\u0442\u0440, \u0441\u043f\u0435\u0446\u0438\u0430\u043b\u0438\u0437\u0438\u0440\u0443\u044e\u0449\u0438\u0439\u0441\u044f \u043d\u0430 \u043f\u0440\u043e\u0432\u0435\u0434\u0435\u043d\u0438\u0438 \u0434\u0438\u0430\u0433\u043d\u043e\u0441\u0442\u0438\u0447\u0435\u0441\u043a\u043e\u0433\u043e \u043e\u0431\u0441\u043b\u0435\u0434\u043e\u0432\u0430\u043d\u0438\u044f \u0432\u0437\u0440\u043e\u0441\u043b\u044b\u0445 \u0438 \u0434\u0435\u0442\u0435\u0439 \u043e\u0442 14 \u043b\u0435\u0442. \u041a\u043b\u0438\u043d\u0438\u043a\u0430 \u0440\u0430\u0441\u043f\u043e\u043b\u043e\u0436\u0435\u043d\u0430 \u0432 \u0448\u0430\u0433\u043e\u0432\u043e\u0439 \u0431\u043b\u0438\u0437\u043e\u0441\u0442\u0438 \u043e\u0442 \u043c\u0435\u0442\u0440\u043e \u041b\u044e\u0431\u043b\u0438\u043d\u043e (5-10 \u043c\u0438\u043d.) \u041f\u0440\u0438\u0435\u043c \u043f\u0440\u043e\u0438\u0441\u0445\u043e\u0434\u0438\u0442 \u043f\u043e \u043f\u0440\u0435\u0434\u0432\u0430\u0440\u0438\u0442\u0435\u043b\u044c\u043d\u043e\u0439 \u0437\u0430\u043f\u0438\u0441\u0438 \u043f\u043e \u043c\u043d\u043e\u0433\u043e\u043a\u0430\u043d\u0430\u043b\u044c\u043d\u043e\u043c\u0443 \u0442\u0435\u043b\u0435\u0444\u043e\u043d\u0443 +7 (495) 988-01-64.","WeekdaysOpen":null,"WeekendOpen":null,"ShortDescription":"\u041c\u043d\u043e\u0433\u043e\u043f\u0440\u043e\u0444\u0438\u043b\u044c\u043d\u044b\u0439 \u043c\u0435\u0434\u0438\u0446\u0438\u043d\u0441\u043a\u0438\u0439 \u0446\u0435\u043d\u0442\u0440, \u0441\u043f\u0435\u0446\u0438\u0430\u043b\u0438\u0437\u0438\u0440\u0443\u044e\u0449\u0438\u0439\u0441\u044f \u043d\u0430 \u043f\u0440\u043e\u0432\u0435\u0434\u0435\u043d\u0438\u0438 \u0434\u0438\u0430\u0433\u043d\u043e\u0441\u0442\u0438\u0447\u0435\u0441\u043a\u043e\u0433\u043e \u043e\u0431\u0441\u043b\u0435\u0434\u043e\u0432\u0430\u043d\u0438\u044f \u0432\u0437\u0440\u043e\u0441\u043b\u044b\u0445 \u0438 \u0434\u0435\u0442\u0435\u0439 \u043e\u0442 14 \u043b\u0435\u0442. \u041a\u043b\u0438\u043d\u0438\u043a\u0430 \u0440\u0430\u0441\u043f\u043e\u043b\u043e\u0436\u0435\u043d\u0430 \u0432 \u0448\u0430\u0433\u043e\u0432\u043e\u0439 \u0431\u043b\u0438\u0437\u043e\u0441\u0442\u0438 \u043e\u0442 \u043c\u0435\u0442\u0440\u043e \u041b\u044e\u0431\u043b\u0438\u043d\u043e (5-10 \u043c\u0438\u043d.) \u041f\u0440\u0438\u0435\u043c \u043f\u0440\u043e\u0438\u0441\u0445\u043e\u0434\u0438\u0442 \u043f\u043e \u043f\u0440\u0435\u0434\u0432\u0430\u0440\u0438\u0442\u0435\u043b\u044c\u043d\u043e\u0439 \u0437\u0430\u043f\u0438\u0441\u0438 \u043f\u043e \u043c\u043d\u043e\u0433\u043e\u043a\u0430\u043d\u0430\u043b\u044c\u043d\u043e\u043c\u0443 \u0442\u0435\u043b\u0435\u0444\u043e\u043d\u0443 +7 (495) 988-01-64.","IsDiagnostic":"yes","isClinic":"yes","IsDoctor":"no","Phone":"74956410606","PhoneAppointment":"+7 (495) 641-06-06","logoPath":"1.png","ScheduleState":"enable","Email":"","Logo":"http://docdoc.ru/upload/kliniki/logo/1.png","Diagnostics":[{"Id":"1","Name":"\u0423\u0417\u0418 (\u0443\u043b\u044c\u0442\u0440\u0430\u0437\u0432\u0443\u043a\u043e\u0432\u043e\u0435 \u0438\u0441\u0441\u043b\u0435\u0434\u043e\u0432\u0430\u043d\u0438\u0435)","Price":"4500.00","SpecialPrice":"0.00"}],"Stations":[{"Id":"1","Name":"\u0410\u0432\u0438\u0430\u043c\u043e\u0442\u043e\u0440\u043d\u0430\u044f","LineName":null,"LineColor":null,"CityId":null},{"Id":"2","Name":"\u0410\u0432\u0442\u043e\u0437\u0430\u0432\u043e\u0434\u0441\u043a\u0430\u044f","LineName":"\u0417\u0430\u043c\u043e\u0441\u043a\u0432\u043e\u0440\u0435\u0446\u043a\u0430\u044f","LineColor":"0a6f20","CityId":"1"},{"Id":"3","Name":"\u0410\u043a\u0430\u0434\u0435\u043c\u0438\u0447\u0435\u0441\u043a\u0430\u044f","LineName":null,"LineColor":null,"CityId":null}]}]}',
			],
			'clinicViewWithPartnerWithUseSpecialPrice' => [
				'/clinic/3',
				'{"Clinic":[{"Id":"3","Name":"\u0414\u0438\u0430\u0433\u043d\u043e\u0441\u0442\u0438\u0447\u0435\u0441\u043a\u0438\u0439 \u0446\u0435\u043d\u0442\u0440 \u21163","ShortName":"\u0414\u0438\u0430\u0433\u043d\u043e\u0441\u0442\u0438\u0447\u0435\u0441\u043a\u0438\u0439 \u0446\u0435\u043d\u0442\u0440 \u21163","RewriteName":"clinic_3","Url":"http://www.clinicanomer3.ru","Longitude":"55.675702","Latitude":"37.767699","City":"\u041c\u043e\u0441\u043a\u0432\u0430","Street":"\u041a\u0440\u0430\u0441\u043d\u043e\u0434\u0430\u0440\u0441\u043a\u0430\u044f \u0443\u043b\u0438\u0446\u0430","Description":"\u041c\u043d\u043e\u0433\u043e\u043f\u0440\u043e\u0444\u0438\u043b\u044c\u043d\u044b\u0439 \u043c\u0435\u0434\u0438\u0446\u0438\u043d\u0441\u043a\u0438\u0439 \u0446\u0435\u043d\u0442\u0440, \u0441\u043f\u0435\u0446\u0438\u0430\u043b\u0438\u0437\u0438\u0440\u0443\u044e\u0449\u0438\u0439\u0441\u044f \u043d\u0430 \u043f\u0440\u043e\u0432\u0435\u0434\u0435\u043d\u0438\u0438 \u0434\u0438\u0430\u0433\u043d\u043e\u0441\u0442\u0438\u0447\u0435\u0441\u043a\u043e\u0433\u043e \u043e\u0431\u0441\u043b\u0435\u0434\u043e\u0432\u0430\u043d\u0438\u044f \u0432\u0437\u0440\u043e\u0441\u043b\u044b\u0445 \u0438 \u0434\u0435\u0442\u0435\u0439 \u043e\u0442 14 \u043b\u0435\u0442. \u041a\u043b\u0438\u043d\u0438\u043a\u0430 \u0440\u0430\u0441\u043f\u043e\u043b\u043e\u0436\u0435\u043d\u0430 \u0432 \u0448\u0430\u0433\u043e\u0432\u043e\u0439 \u0431\u043b\u0438\u0437\u043e\u0441\u0442\u0438 \u043e\u0442 \u043c\u0435\u0442\u0440\u043e \u041b\u044e\u0431\u043b\u0438\u043d\u043e (5-10 \u043c\u0438\u043d.) \u041f\u0440\u0438\u0435\u043c \u043f\u0440\u043e\u0438\u0441\u0445\u043e\u0434\u0438\u0442 \u043f\u043e \u043f\u0440\u0435\u0434\u0432\u0430\u0440\u0438\u0442\u0435\u043b\u044c\u043d\u043e\u0439 \u0437\u0430\u043f\u0438\u0441\u0438 \u043f\u043e \u043c\u043d\u043e\u0433\u043e\u043a\u0430\u043d\u0430\u043b\u044c\u043d\u043e\u043c\u0443 \u0442\u0435\u043b\u0435\u0444\u043e\u043d\u0443 +7 (495) 988-01-64.","House":"\u0434. 52, \u043a\u043e\u0440\u043f. 2","WeekdaysOpen":null,"WeekendOpen":null,"Phone":"+74959880166","Logo":"http://docdoc.ru/upload/kliniki/logo/3.png","Diagnostics":[{"Id":"139","Name":"\u042d\u043d\u0434\u043e\u0441\u043a\u043e\u043f\u0438\u0447\u0435\u0441\u043a\u0438\u0435 \u043c\u0435\u0442\u043e\u0434\u044b \u0438\u0441\u0441\u043b\u0435\u0434\u043e\u0432\u0430\u043d\u0438\u044f \u042d\u0437\u043e\u0444\u0430\u0433\u043e\u0433\u0430\u0441\u0442\u0440\u043e\u0434\u0443\u043e\u0434\u0435\u043d\u043e\u0441\u043a\u043e\u043f\u0438\u044f (\u042d\u0424\u0413\u0414\u0421)","Price":"4500.00","SpecialPrice":"1123.00"}]}]}',
				['partnerId' => 2]
			],
			'clinicViewWithPartnerWithoutUseSpecialPrice' => [
				'/clinic/3',
				'{"Clinic":[{"Id":"3","Name":"\u0414\u0438\u0430\u0433\u043d\u043e\u0441\u0442\u0438\u0447\u0435\u0441\u043a\u0438\u0439 \u0446\u0435\u043d\u0442\u0440 \u21163","ShortName":"\u0414\u0438\u0430\u0433\u043d\u043e\u0441\u0442\u0438\u0447\u0435\u0441\u043a\u0438\u0439 \u0446\u0435\u043d\u0442\u0440 \u21163","RewriteName":"clinic_3","Url":"http://www.clinicanomer3.ru","Longitude":"55.675702","Latitude":"37.767699","City":"\u041c\u043e\u0441\u043a\u0432\u0430","Street":"\u041a\u0440\u0430\u0441\u043d\u043e\u0434\u0430\u0440\u0441\u043a\u0430\u044f \u0443\u043b\u0438\u0446\u0430","Description":"\u041c\u043d\u043e\u0433\u043e\u043f\u0440\u043e\u0444\u0438\u043b\u044c\u043d\u044b\u0439 \u043c\u0435\u0434\u0438\u0446\u0438\u043d\u0441\u043a\u0438\u0439 \u0446\u0435\u043d\u0442\u0440, \u0441\u043f\u0435\u0446\u0438\u0430\u043b\u0438\u0437\u0438\u0440\u0443\u044e\u0449\u0438\u0439\u0441\u044f \u043d\u0430 \u043f\u0440\u043e\u0432\u0435\u0434\u0435\u043d\u0438\u0438 \u0434\u0438\u0430\u0433\u043d\u043e\u0441\u0442\u0438\u0447\u0435\u0441\u043a\u043e\u0433\u043e \u043e\u0431\u0441\u043b\u0435\u0434\u043e\u0432\u0430\u043d\u0438\u044f \u0432\u0437\u0440\u043e\u0441\u043b\u044b\u0445 \u0438 \u0434\u0435\u0442\u0435\u0439 \u043e\u0442 14 \u043b\u0435\u0442. \u041a\u043b\u0438\u043d\u0438\u043a\u0430 \u0440\u0430\u0441\u043f\u043e\u043b\u043e\u0436\u0435\u043d\u0430 \u0432 \u0448\u0430\u0433\u043e\u0432\u043e\u0439 \u0431\u043b\u0438\u0437\u043e\u0441\u0442\u0438 \u043e\u0442 \u043c\u0435\u0442\u0440\u043e \u041b\u044e\u0431\u043b\u0438\u043d\u043e (5-10 \u043c\u0438\u043d.) \u041f\u0440\u0438\u0435\u043c \u043f\u0440\u043e\u0438\u0441\u0445\u043e\u0434\u0438\u0442 \u043f\u043e \u043f\u0440\u0435\u0434\u0432\u0430\u0440\u0438\u0442\u0435\u043b\u044c\u043d\u043e\u0439 \u0437\u0430\u043f\u0438\u0441\u0438 \u043f\u043e \u043c\u043d\u043e\u0433\u043e\u043a\u0430\u043d\u0430\u043b\u044c\u043d\u043e\u043c\u0443 \u0442\u0435\u043b\u0435\u0444\u043e\u043d\u0443 +7 (495) 988-01-64.","House":"\u0434. 52, \u043a\u043e\u0440\u043f. 2","WeekdaysOpen":null,"WeekendOpen":null,"Phone":"+74959880166","Logo":"http://docdoc.ru/upload/kliniki/logo/3.png","Diagnostics":[{"Id":"139","Name":"\u042d\u043d\u0434\u043e\u0441\u043a\u043e\u043f\u0438\u0447\u0435\u0441\u043a\u0438\u0435 \u043c\u0435\u0442\u043e\u0434\u044b \u0438\u0441\u0441\u043b\u0435\u0434\u043e\u0432\u0430\u043d\u0438\u044f \u042d\u0437\u043e\u0444\u0430\u0433\u043e\u0433\u0430\u0441\u0442\u0440\u043e\u0434\u0443\u043e\u0434\u0435\u043d\u043e\u0441\u043a\u043e\u043f\u0438\u044f (\u042d\u0424\u0413\u0414\u0421)","Price":"4500.00","SpecialPrice":0}]}]}',
				['partnerId' => 1]
			],
		];

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
		$api = ApiFactory::getApi('/api/rest/1.0.0/json/request', $params);
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
				'{"Response":{"status":"success","message":"\u0417\u0430\u044f\u0432\u043a\u0430 \u043f\u0440\u0438\u043d\u044f\u0442\u0430"}}'
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
				'{"Response":{"status":"success","message":"\u0417\u0430\u044f\u0432\u043a\u0430 \u043f\u0440\u0438\u043d\u044f\u0442\u0430"}}'
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
				'{"Response":{"status":"success","message":"\u0417\u0430\u044f\u0432\u043a\u0430 \u043f\u0440\u0438\u043d\u044f\u0442\u0430"}}'
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
				'{"Response":{"status":"success","message":"\u0417\u0430\u044f\u0432\u043a\u0430 \u043f\u0440\u0438\u043d\u044f\u0442\u0430"}}'
			),

			// некоректные заявки
			array(
				array(
					'dataType' => 'json',
					'partnerId' => 1,
					'rawData' => '',
				),
				'{"Response":{"status":"error","message":"\u041d\u0435 \u043f\u043e\u043b\u0443\u0447\u0435\u043d\u044b \u0434\u0430\u043d\u043d\u044b\u0435 \u043e \u0437\u0430\u044f\u0432\u043a\u0435"}}'
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
				'{"Response":{"status":"error","message":"\u041d\u0435\u043a\u043e\u0440\u0440\u0435\u043a\u0442\u043d\u044b\u0439 \u0444\u043e\u0440\u043c\u0430\u0442 \u043d\u043e\u043c\u0435\u0440\u0430 \u0442\u0435\u043b\u0435\u0444\u043e\u043d\u0430"}}'
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
				'{"Response":{"status":"error","message":"\u041d\u0435\u0442 \u0442\u0430\u043a\u043e\u0433\u043e \u0432\u0440\u0430\u0447\u0430 \u0432 \u0441\u0438\u0441\u0442\u0435\u043c\u0435"}}'
			),
			// Онлайн запись на диагностику
			array(
				array(
					'dataType' => 'json',
					'partnerId' => 1,
					'rawData' => json_encode(array(
							'name' => 'Тест Тест',
							'phone' => '79261234567',
							'doctor' => 999999,
							'diagnostics' => 123,
							'dateAdmission' => '2014-12-12',
						)),
				),
				'{"Response":{"status":"error","message":"\u041a\u043b\u0438\u043d\u0438\u043a\u0430 \u043d\u0435 \u043d\u0430\u0439\u0434\u0435\u043d\u0430"}}'
			),
			// Онлайн запись на диагностику
			array(
				array(
					'dataType' => 'json',
					'partnerId' => 1,
					'rawData' => json_encode(array(
							'name' => 'Тест Тест',
							'phone' => '79261234567',
							'doctor' => 999999,
							'diagnostics' => 123,
							'clinic' => 1,
							'dateAdmission' => '2014-12-12',
						)),
				),
				'{"Response":{"status":"success","message":"\u0417\u0430\u044f\u0432\u043a\u0430 \u043f\u0440\u0438\u043d\u044f\u0442\u0430"}}'
			),
		);
	}

	/**
	 * Возвращает стаж доктора
	 *
	 * @param $year
	 * @return bool|string
	 */
	public function getExperience($year) {

		return date("Y") - $year;
	}

}
