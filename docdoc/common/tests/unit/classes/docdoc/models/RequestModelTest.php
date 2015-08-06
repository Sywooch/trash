<?php

namespace dfs\tests\docdoc\models;

use dfs\docdoc\models\ClinicModel;
use dfs\docdoc\models\PartnerCostModel;
use dfs\docdoc\models\RequestModel;
use dfs\docdoc\models\RequestRecordModel;
use dfs\docdoc\models\RequestSpamModel;
use dfs\docdoc\models\SlotModel;
use dfs\docdoc\models\StationModel;
use CAdvancedArbehavior;
use CDbTestCase;
use dfs\docdoc\objects\Rejection;
use Yii;

/**
 * Class ClinicModelTest
 *
 * @package dfs\tests\docdoc\models
 */
class RequestModelTest extends CDbTestCase
{

	/**
	 * Выполнять при запуске каждого теста
	 *
	 * @return void
	 */
	protected function setUp()
	{
		PartnerCostModel::model()->clearCache();

		parent::setUp();
	}

	/**
	 * Флаг для теста testAsteriskCreateRequest, показывающий был этот тест запущен или нет
	 *
	 * @var bool
	 */
	static public $asteriskTestRun = false;

	/**
	 * Флаг для теста testSaveByRecord(), чтобы не загружать фикстуры повторно
	 *
	 * @var bool
	 */
	protected static $loadedSaveByRecordFixtures = false;

	/**
	 * Ошибки
	 *
	 * @var string[]
	 */
	private $errors = array();

	/**
	 * Отлов ошибок, созданных внутри моделей
	 *
	 * @param $errno
	 * @param $errstr
	 * @param $errfile
	 * @param $errline
	 * @param $errcontext
	 */
	public function errorHandler($errno, $errstr, $errfile, $errline, $errcontext)
	{
		$this->errors[] = compact(
			"errno",
			"errstr",
			"errfile",
			"errline",
			"errcontext"
		);
	}

	/**
	 * Проверка, есть ли среди пойманных ошибок ошибка с заданным текстом и уровнем
	 *
	 * @param string $errstr
	 * @param int    $errno
	 */
	public function assertError($errstr, $errno)
	{
		foreach ($this->errors as $error) {
			if ($error["errstr"] === $errstr
				&& $error["errno"] === $errno
			) {
				return;
			}
		}

		$this->fail(
			"Ошибка с уровнем {$errno} и сообщением '{$errstr}' не найдена",
			var_export($this->errors, true)
		);
	}

	/**
	 * Тест записи к конкретному врачу
	 * сценарий RequestModel::SCENARIO_SITE
	 *
	 */
	public function testToDoctor()
	{
		$this->loadFixtures();

		$request = new RequestModel();
		$request->setScenario(RequestModel::SCENARIO_SITE);

		$request->attributes = array(
			'client_name'     => 'Иван Иванович Иванов-Македонский',
			'client_phone'    => '+7 (916) 811-25-64',
			'age_selector'    => 'adult',
			'req_doctor_id'   => 1,
			'partner_id'      => 1,
			'clinic_id'       => 1,
			'req_departure'   => 1,
			'client_comments' => '<script>alert("XSS");</script>',
			'enter_point'     => RequestModel::ENTER_POINT_SHORT_FORM,
		);

		//отлавливаем ошибку Empty clinic id for doctor
		set_error_handler(array($this, "errorHandler"));

		if (!$request->save()) {
			$message = "";

			foreach ($request->getErrors() as $att => $msg) {
				$message .= sprintf("%s: %s\n", $att, join(', ', $msg));
			}

			$this->fail($message);
		}

		restore_error_handler();

		$this->assertCommon($request, 3);

		$this->assertEquals(RequestModel::ENTER_POINT_SHORT_FORM, $request->enter_point);

		$this->assertNotEmpty($request->req_sector_id, "Не получен сектор врача");

		$this->assertNotNull($request->clinic, "Не создана связь с клиникой");

	}

	/**
	 * Тест записи в конкретную клинику
	 *
	 * сценарий RequestModel::SCENARIO_CALL
	 *
	 */
	public function testToClinic()
	{
		$this->loadFixtures();

		$request = new RequestModel();
		$request->setScenario(RequestModel::SCENARIO_SITE);

		$request->attributes = array(
			'client_name'     => 'Иван Иванович Иванов-Македонский',
			'client_phone'    => '+7 (916) 811-25-64',
			'age_selector'    => 'adult',
			'kind'            => 1,
			'clinic_id'       => 1,
			'partner_id'      => 1,
			'req_departure'   => 1,
			'client_comments' => '<script>alert("XSS");</script>',
		);

		if (!$request->save()) {
			$message = "";

			foreach ($request->getErrors() as $att => $msg) {
				$message .= sprintf("%s: %s\n", $att, join(', ', $msg));
			}

			$this->fail($message);
		}

		$this->assertCommon($request, 3);

		$this->assertNull($request->doctor, "Некорректная связь с врачем");

		$this->assertNotNull($request->clinic, "Не создана связь с клиникой");

	}

	/**
	 * общие проверки заявки
	 *
	 * @param RequestModel $request
	 * @param int          $num
	 */
	private function assertCommon(RequestModel $request, $num = 4)
	{
		$this->assertEquals('Иван Иванович Иванов-Македонский', $request->client_name);

		$this->assertEquals('alert("XSS");', $request->client_comments, "Aктивная xss в request->client_comments");

		$this->assertEquals(
			11,
			strlen($request->client_phone),
			"Длина телефона request->client_phone не равна 11 символам"
		);

		$this->assertNotNull($request->partner, "Не создана связь с партнером");

		$this->assertNotEmpty($request->source_type);

		//при создании заявки делается запись о создании + установлен статус Новая + запись об отправке события в микспанель
		//для диагностики в микспанель не отправляется
		$this->assertEquals($num, count($request->request_history), "Не создан лог в request_history");

		$this->assertNotNull($request->request_partner, "Не создана запись в request_partner");

		$this->assertNotNull($request->city, "Не создана связь с городом");

		$this->assertEquals(1, $request->count(), 'Количество записей в таблице request != 1');

	}

	/**
	 * Тест на заказ звонка
	 *
	 * сценарий RequestModel::SCENARIO_CALL
	 *
	 */
	public function testScenarioCall()
	{
		$this->loadFixtures();

		$request = new RequestModel();
		$request->setScenario(RequestModel::SCENARIO_CALL);

		$request->attributes = array(
			'client_phone' => '+7 (916) 811-25-64',
			'partner_id'   => 1,
		);

		if (!$request->save()) {
			$message = "";

			foreach ($request->getErrors() as $att => $msg) {
				$message .= sprintf("%s: %s\n", $att, join(', ', $msg));
			}

			$this->fail($message);
		}

		$this->assertEquals(
			11,
			strlen($request->client_phone),
			"Длина телефона request->client_phone не равна 11 символам"
		);

		$this->assertNotNull($request->partner, "Не создана связь с партнером");

		$this->assertEquals(3, count($request->request_history), "Не создан лог в request_history");

		$this->assertNotNull($request->city, "Не создана связь с городом");

		$this->assertEquals(1, $request->count(), 'Количество записей в таблице request != 1');
	}

	/**
	 * тест сохранения данных в request_station
	 */
	public function testRequestStation()
	{

		$this->loadFixtures();
		$this->getFixtureManager()->truncateTable('request_station');
		$this->getFixtureManager()->truncateTable('underground_station');
		$this->getFixtureManager()->loadFixture('underground_station');
		$this->getFixtureManager()->loadFixture('request');

		$request = new RequestModel();
		$request->setScenario(RequestModel::SCENARIO_CALL);

		//создание
		$request->attributes = array(
			'client_phone' => '+7 (916) 811-25-64',
		);

		$request->stations = array(1, 2, 3);
		$request->save();

		$count = \Yii::app()->db->createCommand("SELECT COUNT(*) FROM request_station")->queryScalar();
		$this->assertEquals(3, $count);

		//обновление
		$request->stations = StationModel::model()->findAll(array('condition' => " id=2 OR id=3 "));
		$request->save();
		$count = \Yii::app()->db->createCommand("SELECT COUNT(*) FROM request_station")->queryScalar();
		$this->assertEquals(2, $count);

		//неинициализированный relation
		$request = RequestModel::model()->findByPk($request->req_id);

		$request->save();
		$count = \Yii::app()->db->createCommand("SELECT COUNT(*) FROM request_station")->queryScalar();
		$this->assertEquals(2, $count);

		//некорректные параметры
		$request->stations = array(1, 2, 'weqwe qeqweqw', "'--''", array("eqweqweqweqwe", "qwq"), "", "www" => array());
		$request->save();
		$count = \Yii::app()->db->createCommand("SELECT COUNT(*) FROM request_station")->queryScalar();
		//здесь небольшой баг в классе CAdvancedArbehavior. Будут записаны 3 строки с station_id = 1, 2, 0
		$this->assertEquals(3, $count);

	}

	/**
	 * подготовка базы для тестов
	 */
	private function loadFixtures()
	{
		//убираем проверку первичных ключей
		$this->getFixtureManager()->checkIntegrity(false);
		$this->getFixtureManager()->truncateTable('request');
		$this->getFixtureManager()->truncateTable('request_spam');
		$this->getFixtureManager()->truncateTable('doctor');
		$this->getFixtureManager()->truncateTable('clinic');
		$this->getFixtureManager()->truncateTable('partner');
		$this->getFixtureManager()->truncateTable('sector');
		$this->getFixtureManager()->truncateTable('doctor_sector');
		$this->getFixtureManager()->truncateTable('request_history');
		$this->getFixtureManager()->truncateTable('request_partner');
		$this->getFixtureManager()->truncateTable('city');
		$this->getFixtureManager()->truncateTable('doctor_4_clinic');
		$this->getFixtureManager()->truncateTable('contract_group');
		$this->getFixtureManager()->truncateTable('contract_group_service');
		$this->getFixtureManager()->loadFixture('clinic');
		$this->getFixtureManager()->loadFixture('doctor');
		$this->getFixtureManager()->loadFixture('doctor_4_clinic');
		$this->getFixtureManager()->loadFixture('partner');
		$this->getFixtureManager()->loadFixture('sector');
		$this->getFixtureManager()->loadFixture('doctor_sector');
		$this->getFixtureManager()->loadFixture('city');
		$this->getFixtureManager()->loadFixture('contract_group');
		$this->getFixtureManager()->loadFixture('contract_group_service');
	}

	/**
	 * Тест проверка сохранения пустого объекта при создании заявки с сайта
	 */
	public function testSiteCreateError()
	{
		$request = new RequestModel();
		$request->setScenario(RequestModel::SCENARIO_SITE);

		//пытаемся сохранить пустой объект
		if (!$request->save()) {

			//ожидаем три возможные ошибки при создании заявки
			$this->assertEquals(2, count($request->getErrors()));
		} else {
			$this->fail("Некорректно отработала валидация данных при создании заявки");
		}
	}

	/**
	 * Тест проверка имени клиента
	 */
	public function testClientName()
	{
		$request = new RequestModel(RequestModel::SCENARIO_SITE);

		$request->client_name = "";
		$request->validate();
		$this->assertGreaterThan(0, count($request->getErrors('client_name')));

		$request->client_name = "Иван Иванович Иванов-Македонский";
		$request->validate();
		$this->assertEquals(0, count($request->getErrors('client_name')));

	}

	/**
	 * Тест отработки события при изменении статуса заявки
	 */
	public function testRequestStatusChange()
	{
		$this->loadFixtures();
		$request = new RequestModel();
		$request->setScenario(RequestModel::SCENARIO_CALL);

		$request->attributes = array(
			'client_phone' => '+7 (916) 811-25-64',
			'partner_id'   => 1,
		);

		//Создание заявки с сайта (IP: )
		//MixPanel. Событие AppCreated. Event = {....
		$request->save();

		//Изменен статус -> 'Отказ'
		//MixPanel. Событие AppRefused. Event = {.....
		$request->req_status = RequestModel::STATUS_REJECT;
		$request->save();

		//Изменен статус -> 'Повторный звонок'
		$request->setRecall();
		$request->save();
		$this->assertEquals(8, count($request->request_history));

		//Поступил повторный звонок, без изменения статуса
		$request->setRecall();
		$request->save();

		$request = RequestModel::model()->findByPk($request->req_id);
		$this->assertEquals(9, count($request->request_history));
	}

	/**
	 * Обработка запроса на изменение isAppointment у всех записей заявки
	 *
	 * @dataProvider dataSaveAppointmentByRecords
	 *
	 * @param int   $request_id
	 * @param array $appointments
	 * @param int   $clinic_id
	 *
	 */
	public function testSaveAppointmentByRecords($request_id, $appointments, $clinic_id)
	{
		$this->getFixtureManager()->checkIntegrity(false);
		$this->getFixtureManager()->truncateTable('request');
		$this->getFixtureManager()->truncateTable('request_record');
		$this->getFixtureManager()->truncateTable('request_history');
		$this->getFixtureManager()->loadFixture('request');
		$this->getFixtureManager()->loadFixture('request_record');

		$request = RequestModel::model()->findByPk($request_id);
		$request->saveAppointmentByRecords($appointments);

		foreach ($request->request_record as $r) {
			if (isset($appointments[$r->record_id])) {
				$this->assertEquals('yes', $r->isAppointment);
			} else {
				$this->assertEquals('no', $r->isAppointment);
			}
		}

		$this->assertEquals($clinic_id, RequestModel::model()->findByPk($request_id)->clinic_id);

	}

	/**
	 * dataprovider для testSaveAppointmentByRecords
	 *
	 * @return array
	 */
	public function dataSaveAppointmentByRecords()
	{
		return array(
			array(
				//сбрасываем isAppointment для всех записей (отправляем пустой массив)
				1,
				array(),
				null,
			),
			array(
				//установка yes для 1, сброс для 5,
				//clinic_id должно проставиться 1
				1,
				array(
					1 => 'yes'
				),
				1,
			),
			array(
				//всем yes
				//clinic id в заявку должно подставиться из request_record = 5
				1,
				array(
					1 => 'yes',
					5 => 'yes',
					6 => 'yes',
				),
				3,
			),
			array(
				//clinic id в заявку должно подставиться из request_record = 5
				1,
				array(
					6 => 'yes',
				),
				3
			),

		);

	}

	/**
	 * проверка сброса флага is_hot
	 */
	public function testIsHot()
	{
		$request = new RequestModel();
		$request->setScenario(RequestModel::SCENARIO_SITE);

		$request->attributes = array(
			'client_name'  => 'Иван Иванович',
			'client_phone' => '+7 (916) 811-25-64',
		);
		$request->save();

		$this->assertEquals(1, $request->is_hot);

		$exists_request = RequestModel::model()->findByPk($request->req_id);
		$exists_request->is_hot = 0;
		$exists_request->save();
		$this->assertEquals(0, $exists_request->is_hot);
	}

	/**
	 * проверка сохранения филиала клиники
	 *
	 * @dataProvider provideSaveClinicBranch
	 *
	 * @param array $params
	 * @param int   $clinicId
	 */
	public function testSaveClinicBranch($params, $clinicId)
	{
		$this->getFixtureManager()->checkIntegrity(false);
		$this->getFixtureManager()->truncateTable('clinic');
		$this->getFixtureManager()->truncateTable('request');
		$this->getFixtureManager()->truncateTable('request_record');
		$this->getFixtureManager()->loadFixture('clinic');
		$this->getFixtureManager()->loadFixture('request');
		$this->getFixtureManager()->loadFixture('request_record');

		$request = RequestModel::model()->findByPk($params['request_id']);

		$request->clinic_id = $params['clinic_id'];
		$request->save();

		$request = RequestModel::model()->findByPk($request->req_id);

		// Проверяем изменение клиники после сохранения записей
		$request->saveAppointmentByRecords(array(13 => 'yes'));
		$this->assertEquals($clinicId, $request->clinic_id);

	}

	/**
	 * данные для проверки сохранения филиала
	 */
	public function provideSaveClinicBranch()
	{
		return array(
			// Записывается выбранная клиника
			array(
				array(
					'request_id' => 5,
					'clinic_id'  => 10,
				),
				10
			),
			// Если нет клиники в заявке, каписывается клиника из аудиозаписи
			array(
				array(
					'request_id' => 5,
					'clinic_id'  => 0,
				),
				9
			),
			// Если выбран не филиал, то записывается клиника из аудиозаписи
			array(
				array(
					'request_id' => 5,
					'clinic_id'  => 1,
				),
				9
			),
		);
	}

	/**
	 * проверка с relation sector
	 */
	public function testSector()
	{
		$fm = $this->getFixtureManager();
		$fm->checkIntegrity(false);
		$fm->truncateTable('request');
		$fm->loadFixture('request');

		$request = RequestModel::model()->findByPk(1);
		$this->assertInstanceOf('dfs\docdoc\models\SectorModel', $request->sector);
	}

	public function testChangeStatus()
	{
		$fm = $this->getFixtureManager();
		$fm->checkIntegrity(false);
		$fm->truncateTable('request');
		$fm->truncateTable('request_history');
		$fm->loadFixture('request');

		$request = RequestModel::model()->findByPk(1);

		//исходные значения
		$src = $request->getAttributes();

		//меняем значения
		$request->client_name = 'qweqweqwe';
		$request->client_phone = '79168112564';
		$request->is_hot = 0;

		//проверка, что статус сохранился
		$this->assertTrue($request->saveStatus(RequestModel::STATUS_REJECT));

		//убеждаемся, что при сохранении статуса изменяется только значение столбца req_status и камент
		$src['req_status'] = RequestModel::STATUS_REJECT;

		$dst = RequestModel::model()->findByPk(1);
		//@todo КОСТЫЛЬ
		// так как изменение статусов сейчас полностью переведено на RequestModel,
		// чтобы всегда у нас сохранялся клиент, добавил возможность сохранения clientId сюда
		// когда сохранение заявки будет целиком переведено на модель, clientId нужно убрать
		$src['clientId'] = $dst->clientId;
		$this->assertEquals($src, $dst->getAttributes());

		$this->assertEquals(2, count($request->request_history));

	}

	/**
	 * тест функции back/public/api/service/createRequest.php
	 */
	public function testCreateRequestFunction()
	{
		$this->loadFixtures();
		require_once ROOT_PATH . "/back/public/api/service/createRequest.php";
		$params = [
			'partner'          => 1,
			'clinic'           => 1,
			'client'           => 'Белтадзе Давид',
			'callLater'        => '',
			'appointment'      => 1403528400,
			'dateRecord'       => '2014-06-23 11:05:00',
			'appStatus'        => 0,
			'doctor'           => '1',
			'sectorId'         => '1',
			'diagnosticsId'    => '0',
			'diagnosticsOther' => '',
			'isTransfer'       => '1',
			'departure'        => 0,
			'isReject'         => '1',
			'rejectReason'     => '28',
			'owner'            => '73',
			'phone'            => '79651037188',
			'addClientPhone'   => '',
			'kind'             => '0',
		];

		$response = createRequest($params);

		$this->assertEquals(
			[
				'Response' => [
					'status'  => 'success',
					'message' => 'Заявка принята'
				]
			],
			$response
		);

		$request = Yii::app()
			->db
			->createCommand("SELECT * FROM request ORDER BY req_id DESC LIMIT 1")
			->queryRow();

		$this->assertEquals(RequestModel::STATUS_NEW, $request['req_status']);
		$this->assertEquals(RequestModel::ENTER_POINT_PARTNER_DOCTOR, $request['enter_point']);

	}

	/**
	 * тест функции back/public/lib/php/models/DocRequest.php
	 */
	public function testDocRequest()
	{
		$this->loadFixtures();
		require_once ROOT_PATH . "/back/public/lib/php/models/DocRequest.php";

		$request = new \DocRequest();
		$request->clinic_id = 1;
		$request->client_name = 'Белтадзе Давид';
		$request->client_phone = '79168112564';
		$request->req_type = 3;
		$request->enter_point = RequestModel::ENTER_POINT_DIRECT_CALL;
		$this->assertTrue($request->save());

		$req = Yii::app()
			->db
			->createCommand("SELECT req_id, req_status FROM request ORDER BY req_id DESC LIMIT 1")
			->queryRow();

		$this->assertEquals(RequestModel::STATUS_NEW, $req['req_status']);

		$request = new \DocRequest($req['req_id']);
		$request->req_status = RequestModel::STATUS_CAME;
		$request->save();

		$model = RequestModel::model()->findByPk($req['req_id']);
		$this->assertEquals(RequestModel::STATUS_CAME, $model->req_status);

		$this->assertEquals(RequestModel::ENTER_POINT_DIRECT_CALL, $model->enter_point);

	}

	/**
	 * Тест сохранения пользователя заявки
	 */
	public function testSaveRequestUser()
	{
		$this->getFixtureManager()->checkIntegrity(false);
		$this->getFixtureManager()->truncateTable('request');
		$this->getFixtureManager()->loadFixture('request');

		$request = RequestModel::model()->findByPk(1);
		$src = $request->getAttributes();

		$request->client_name = 'aaaa';
		$request->client_phone = '';
		$request->saveRequestUser(1);

		$request2 = RequestModel::model()->findByPk(1);
		$dst = $request2->getAttributes();

		//должен измениться только req_user_id
		$src['req_user_id'] = 1;
		$this->assertEquals($src, $dst);
	}

	/**
	 * Закгружает фикстуры для теста
	 * @throws \CException
	 */
	protected function loadSaveByRecordFixtures()
	{
		$this->getFixtureManager()->checkIntegrity(false);

		$this->getFixtureManager()->truncateTable('request');
		$this->getFixtureManager()->loadFixture('request');

		$this->getFixtureManager()->truncateTable('request_history');

		if (!self::$loadedSaveByRecordFixtures) {
			$this->getFixtureManager()->truncateTable('request_record');
			$this->getFixtureManager()->loadFixture('request_record');

			$this->getFixtureManager()->truncateTable('clinic');
			$this->getFixtureManager()->loadFixture('clinic');

			$this->getFixtureManager()->truncateTable('clinic_partner_phone');
			$this->getFixtureManager()->loadFixture('clinic_partner_phone');

			$this->getFixtureManager()->truncateTable('phone');
			$this->getFixtureManager()->loadFixture('phone');

			$this->getFixtureManager()->truncateTable('partner');
			$this->getFixtureManager()->loadFixture('partner');

			self::$loadedSaveByRecordFixtures = true;
		}
	}


	/**
	 * @param array   $params
	 *
	 * @dataProvider saveByRecordProvider
	 */
	public function testSaveByRecord($params)
	{
		$this->loadSaveByRecordFixtures();

		$recordId = array_shift($params);
		$r = RequestRecordModel::model()->findByPk($recordId);

		$request = RequestModel::saveByRecord($r);

		foreach ($params as $key => $value) {
			if (is_null($value)) {
				if($key == 'id_city'){
					if($request->clinic){
						$this->assertEquals($request->clinic->city_id, $value);
					} else {
						$this->assertEquals(1, $request->id_city); //по дефолту москва на уровне sql
					}
				} else {
					$this->assertNull($request->$key, 'field ' . $key . ' with value ' . var_export($request->$key, true) . ' notEquals ' . var_export($value, true));
				}

			} else {
				$this->assertEquals(
					$request->$key,
					$value,
					'field ' .
					$key .
					' with value ' .
					var_export($request->$key, true) .
					' notEquals ' .
					var_export($value, true)
				);
			}
		}

		//проверяю не создаются ли дубли при аналогичных параметрах

		//1) добавится запись к существующей заявке
		$r1 = new RequestRecordModel('copy');
		$r1->attributes = $r->attributes;
		$r1->crDate = date('Y-m-d H:i:s', strtotime($r->crDate) - RequestModel::DIFF_TIME_FOR_MERGED_REQUEST - 1);
		$request1 = RequestModel::saveByRecord($r1);

		if (!$request->partner || !$request->partner->not_merged_requests) {
			$this->assertEquals($request->req_id, $request1->req_id);
		} else {
			$this->assertNotEquals($request->req_id, $request1->req_id);
		}

		//2) создастся новая заявка
		$r2 = new RequestRecordModel('copy');
		$r2->attributes = $r->attributes;
		$r2->crDate = date('Y-m-d H:i:s', strtotime($r->crDate) + RequestModel::DIFF_TIME_FOR_MERGED_REQUEST + 1);

		$request2 = RequestModel::saveByRecord($r2);
		$this->assertNotEquals($request->req_id, $request2->req_id);

	}

	/**
	 * Дата провайдер для testSaveByRecord
	 * Подставляет request_record с разными параметрами
	 *
	 * @return array
	 */
	public function saveByRecordProvider()
	{
		return [
			[
				[
					'record_id'  => 1,
					'id_city'    => 1,
					'partner_id' => null,
					'kind'       => RequestModel::KIND_DOCTOR,
					'clinic_id'  => 1
				],
				true
			],
			[
				[
					'record_id'  => 2,
					'id_city'    => 1,
					'partner_id' => null,
					'kind'       => RequestModel::KIND_DOCTOR,
					'clinic_id'  => 2
				],
				true
			],
			[
				[
					'record_id'  => 3,
					'id_city'    => 1,
					'partner_id' => null,
					'kind'       => RequestModel::KIND_DIAGNOSTICS,
					'clinic_id'  => 3
				],
				true
			],
			[
				[
					'record_id'  => 7,
					'id_city'    => 1,
					'partner_id' => 1,
					'kind'       => RequestModel::KIND_DOCTOR,
					'clinic_id'  => 1
				],
				false
			],
			[
				[
					'record_id'  => 8,
					'id_city'    => 1,
					'partner_id' => 2,
					'kind'       => RequestModel::KIND_DIAGNOSTICS,
					'clinic_id'  => 3
				],
				false
			],
			[
				[
					'record_id'  => 9,
					'id_city'    => 1,
					'partner_id' => null,
					'kind'       => RequestModel::KIND_DIAGNOSTICS,
					'clinic_id'  => 5
				],
				false
			], //diagnostics
			[
				[
					'record_id'  => 10,
					'id_city'    => 2,
					'partner_id' => null,
					'kind'       => RequestModel::KIND_DOCTOR,
					'clinic_id'  => 149
				],
				false
			], //landing
			[
				[
					'record_id'  => 11,
					'id_city'    => 1,
					'partner_id' => null,
					'kind'       => RequestModel::KIND_DOCTOR,
					'clinic_id'  => 1
				],
				false
			],
			[
				[
					'record_id'  => 12,
					'id_city'    => null,
					'partner_id' => null,
					'kind'       => RequestModel::KIND_DOCTOR,
					'clinic_id'  => 0
				]
			],
			// Проверка, что для zoon создается всегда новая заявка
			[
				[
					'record_id'  => 14,
					'id_city'    => 1,
					'partner_id' => 16,
					'kind'       => RequestModel::KIND_DOCTOR,
					'clinic_id'  => 1
				]
			],
			[
				[
					'record_id'  => 15,
				]
			],
			[
				[
					'record_id'  => 16,
				]
			],

		];
	}

	/**
	 * Дата провайдер для testChangeNotSuccessfulPartnerRequest
	 *
	 * @return array
	 */
	public function partnerCostTestDataProvider()
	{
		return [
			['kind' => RequestModel::KIND_DOCTOR, 'diagnostics_id' => null, 'sector_id' => null],
			['kind' => RequestModel::KIND_DOCTOR, 'diagnostics_id' => 1, 'sector_id' => null],
			['kind' => RequestModel::KIND_DOCTOR, 'diagnostics_id' => null, 'sector_id' => 1],
			['kind' => RequestModel::KIND_DOCTOR, 'diagnostics_id' => 1, 'sector_id' => 1],
			['kind' => RequestModel::KIND_DIAGNOSTICS, 'diagnostics_id' => null, 'sector_id' => null],
			['kind' => RequestModel::KIND_DIAGNOSTICS, 'diagnostics_id' => 1, 'sector_id' => null],
			['kind' => RequestModel::KIND_DIAGNOSTICS, 'diagnostics_id' => null, 'sector_id' => 1],
			['kind' => RequestModel::KIND_DIAGNOSTICS, 'diagnostics_id' => 1, 'sector_id' => 1],
		];
	}

	/**
	 * Проставляю доктора или диагностику заявке, не успешно, после апдейта partner_cost должно быть = 0
	 *
	 * @dataProvider partnerCostTestDataProvider
	 */
	public function testChangeNotSuccessfulPartnerRequest($kind, $diagnostics_id, $sector_id)
	{
		$this->getFixtureManager()->truncateTable('partner_cost');
		$this->getFixtureManager()->loadFixture('partner_cost');

		$r = RequestModel::model()->findByPk(4);
		$r->kind = $kind;
		$r->diagnostics_id = $diagnostics_id;
		$r->req_sector_id = $sector_id;
		$r->save();

		$this->assertEquals(0, $r->partner_cost);
	}

	/**
	 * Изменение заявки с измением биллинг статуса должно менять partner_cost
	 *
	 * @param $kind
	 * @param $diagnostics_id
	 * @param $sector_id
	 *
	 * @dataProvider partnerCostTestDataProvider
	 */
	public function testChangeSuccessfulPartnerRequest($kind, $diagnostics_id, $sector_id)
	{
		$this->getFixtureManager()->checkIntegrity(false);

		$this->getFixtureManager()->truncateTable('request');
		$this->getFixtureManager()->truncateTable('partner_cost');
		$this->getFixtureManager()->truncateTable('clinic_contract_cost');
		$this->getFixtureManager()->truncateTable('clinic_contract');
		$this->getFixtureManager()->truncateTable('contract_dict');

		$this->getFixtureManager()->loadFixture('request');
		$this->getFixtureManager()->loadFixture('partner_cost');
		$this->getFixtureManager()->loadFixture('contract_dict');
		$this->getFixtureManager()->loadFixture('clinic_contract');
		$this->getFixtureManager()->loadFixture('clinic_contract_cost');

		if (
			($kind == RequestModel::KIND_DOCTOR && !is_null($sector_id))
			||
			($kind == RequestModel::KIND_DIAGNOSTICS && !is_null($diagnostics_id))
		) {
			$r = RequestModel::model()->findByPk(5);
			$this->assertNotEquals(RequestModel::PARTNER_STATUS_ACCEPT, $r->partner_status);

			$r->kind = $kind;
			$r->diagnostics_id = $diagnostics_id;
			$r->req_sector_id = $sector_id;
			$r->billing_status = RequestModel::BILLING_STATUS_YES;
			$r->save();

			$this->assertEquals(RequestModel::PARTNER_STATUS_ACCEPT, $r->partner_status);
			$this->assertNotEquals(0, (int)$r->partner_cost);
		}
	}

	/**
	 * Проверка правильности подсчета суммы
	 */
	public function testGetSumOfPartnerCost()
	{
		$this->getFixtureManager()->checkIntegrity(false);

		$this->getFixtureManager()->truncateTable('partner_cost');
		$this->getFixtureManager()->loadFixture('partner_cost');

		$this->getFixtureManager()->truncateTable('request');
		$this->getFixtureManager()->loadFixture('request');

		$partnerId = 1;

		$requests = RequestModel::model()
			->byPartner(1)
			->findAll();

		$mySumm = 0;

		foreach($requests as $r){
			$i = rand(1, 100);
			$r->updateByPk($r->req_id, [
				'partner_cost' => $i,
				'partner_status' => RequestModel::PARTNER_STATUS_ACCEPT,
			]);
			$mySumm += $i;
		}


		$criteria = new \CDbCriteria();
		$criteria->scopes = ['byPartner' => [$partnerId]];
		$result = RequestModel::model()->getPartnerSumAndCount($criteria);

		$this->assertEquals($mySumm, $result['cost']);

	}

	/**
	 * создание заявки партнером. запись на диагностику
	 * сценарий RequestModel::SCENARIO_DIAGNOSTIC_ONLINE
	 *
	 */
	public function testRecordInClinic()
	{
		$this->loadFixtures();

		$request = new RequestModel();
		$request->setScenario(RequestModel::SCENARIO_DIAGNOSTIC_ONLINE);

		$request->attributes = array(
			'client_name'     => 'Иван Иванович Иванов-Македонский',
			'client_phone'    => '+7 (916) 811-25-64',
			'clinic_id'       => 1,
			'diagnostics_id'  => 1,
			'partner_id'      => 1,
			'client_comments' => '<script>alert("XSS");</script>',
			'enter_point'     => RequestModel::ENTER_POINT_CLINIC_CALL,
			'date_admission'  => 1410339545,
		);

		if (!$request->save()) {
			$message = "";

			foreach ($request->getErrors() as $att => $msg) {
				$message .= sprintf("%s: %s\n", $att, join(', ', $msg));
			}

			$this->fail($message);
		}
		$this->assertCommon($request);

		$this->assertEquals(RequestModel::ENTER_POINT_CLINIC_CALL, $request->enter_point);

		$this->assertEquals(RequestModel::TYPE_ONLINE_RECORD, $request->req_type);

		$this->assertEquals(RequestModel::KIND_DIAGNOSTICS, $request->kind);

		$this->assertNotEmpty($request->diagnostics_id, "Не получено исследование");

		$this->assertNotNull($request->clinic, "Не создана связь с клиникой");

	}

	/**
	 * Тест сохранения заявки оператором в БО
	 *
	 * @dataProvider saveOperatorData
	 */
	public function testSaveOperator($id, $attributes, $callback)
	{
		$this->getFixtureManager()->truncateTable('request');
		$this->getFixtureManager()->loadFixture('request');

		$request = ($id > 0) ? RequestModel::model()->findByPk(1) : new RequestModel();

		$request->scenario = RequestModel::SCENARIO_OPERATOR;
		$request->setAttributes($attributes);
		if (!$request->save()) {
			$this->fail(print_r($request->getErrors(), 1));
		}

		$reqId = $request->req_id;
		$checkRequest = RequestModel::model()->findByPk($reqId);

		foreach ($attributes as $k => $v) {
			$this->assertEquals(
				$v,
				$checkRequest->$k,
				"Свойство {$k} изменило свое значение при сохранении с {$v} на {$checkRequest->$k}"
			);
		}

		$callback($this, $request);
	}

	/**
	 * Данные для testSaveOperator
	 *
	 * @return array
	 */
	public function saveOperatorData()
	{
		return [
			//создание непустой заявки
			[
				0,
				[
					'id_city'           => '1',
					'clinic_id'         => '46',
					'client_name'       => 'Анжелика',
					'date_admission'    => '',
					'date_record'       => '0000-00-00 00:00:00',
					'req_status'        => 0,
					'req_doctor_id'     => 0,
					'diagnostics_other' => '',
					'is_transfer'       => '',
					'req_departure'     => 0,
					'reject_reason'     => 0,
					'req_user_id'       => '0',
					'client_phone'      => '79150370391',
					'add_client_phone'  => '',
					'call_later_time'   => '',
				],
				function (CDbTestCase $test, RequestModel $request) {
					$test->assertEquals('Создана заявка в БО', $request->request_history[0]->text);
				}
			],
			//создание пустой заявки
			[
				0,
				[
					'id_city'           => '1',
					'clinic_id'         => 0,
					'client_name'       => '',
					'date_admission'    => '',
					'date_record'       => '0000-00-00 00:00:00',
					'req_status'        => 0,
					'req_doctor_id'     => 0,
					'diagnostics_other' => '',
					'is_transfer'       => '',
					'req_departure'     => 0,
					'reject_reason'     => 0,
					'req_user_id'       => '0',
					'client_phone'      => '',
					'add_client_phone'  => '',
					'call_later_time'   => '',
				],
				function (CDbTestCase $test, RequestModel $request) {
					$test->assertEquals('Создана заявка в БО', $request->request_history[0]->text);
				}
			],
			//изменение заявки
			[
				1,
				[
					'id_city'           => '1',
					'clinic_id'         => '46',
					'client_name'       => 'Анжелика',
					'date_admission'    => '',
					'date_record'       => '0000-00-00 00:00:00',
					'req_status'        => 0,
					'req_doctor_id'     => 0,
					'diagnostics_other' => '',
					'is_transfer'       => '',
					'req_departure'     => 0,
					'reject_reason'     => 0,
					'req_user_id'       => '0',
					'client_phone'      => '79150370391',
					'add_client_phone'  => '',
					'call_later_time'   => '',
				],
				function (CDbTestCase $test, RequestModel $request) {
					$test->assertStringStartsWith('значение clientId изменено', $request->request_history[0]->text);
				}
			],

		];
	}

	/**
	 * тестирование метода отслеживания изменения свойств заявки
	 */
	public function testIsChanged()
	{
		$request = new RequestModel();
		$request->date_admission = '';
		$request->date_record = '';
		$this->assertFalse($request->isChanged('date_admission'));
		$this->assertFalse($request->isChanged('date_record'));

		$attrs = [
			'date_admission' => time(),
			'date_record'    => new \CDbExpression('NOW()'),
			'client_name'    => 'eeee',
		];
		foreach ($attrs as $k => $v) {
			$request->$k = $v;
			$this->assertTrue($request->isChanged($k));
		}

		$request->save();

		//после сохранения должны сброситься все флаги изменения
		$request->client_name = 'eeee';
		$this->assertTrue($request->isChanged('client_name'));

	}

	/**
 * тестыы изменения даты записи
 */
	public function testDateAdmissionChange()
	{
		$request = new RequestModel(RequestModel::SCENARIO_OPERATOR);
		$request->date_admission = time();
		$request->client_name = 'иванов иван';
		$request->clinic_id = 1;
		$request->req_doctor_id = 1;
		$request->client_phone = '79168112564';

		$request->save();

		$date_record = $request->date_record;
		$this->assertNotEmpty((int)$date_record);

		//при смене даты визита не должна меняться date_record
		$request->date_admission = time()+1000;
		$request->save();
		$this->assertEquals($date_record, $request->date_record);

	}

	/**
	 * тестирование статусов заявки в ЛК
	 */
	public function testInBillingState()
	{
		$this->loadFixtures();
		$this->getFixtureManager()->loadFixture('request');

		//новые заявки
		$this->assertEquals(
			5,
			RequestModel::model()
				->inBillingState(RequestModel::BILLING_STATE_NEW)
				->count()
		);

		$this->assertEquals(
			6,
			RequestModel::model()
				->inBillingState(RequestModel::BILLING_STATE_RECORD)
				->count()
		);

		$this->assertEquals(
			1,
			RequestModel::model()
				->inBillingState(RequestModel::BILLING_STATE_REJECT)
				->count()
		);

		$this->assertEquals(
			11,
			RequestModel::model()
				->inBillingState([RequestModel::BILLING_STATE_RECORD, RequestModel::BILLING_STATE_NEW])
				->count()
		);

		$this->assertEquals(
			12,
			RequestModel::model()
				->inBillingState([RequestModel::BILLING_STATE_REJECT, RequestModel::BILLING_STATE_RECORD, RequestModel::BILLING_STATE_NEW])
				->count()
		);


		$this->assertEquals(
			6,
			RequestModel::model()
				->inClinic(1)
				->inBillingState([RequestModel::BILLING_STATE_REJECT, RequestModel::BILLING_STATE_RECORD, RequestModel::BILLING_STATE_NEW])
				->count()
		);


	}



	/**
	 * тест billing'a
	 */
	public function testBilling()
	{
		//оплата за запись clinic_id=2
		$request = new RequestModel(RequestModel::SCENARIO_OPERATOR);
		$request->client_name = 'иванов иван';
		$request->clinic_id = 2;
		$request->client_phone = '79168112564';
		$request->kind = RequestModel::KIND_DOCTOR;
		$request->save();

		//даты записи нет
		$this->assertEquals(RequestModel::BILLING_STATUS_NO, $request->billing_status);

		//Оплата за запись
		$request->date_admission = time() + 3600;
		$request->save();
		$this->assertEquals(RequestModel::BILLING_STATUS_YES, $request->billing_status);

		//для тарифа на запись клиника не может зафиксировать отказ по заявке на которую есть запись
		$this->assertFalse($request->setBillingState(RequestModel::BILLING_STATE_REJECT));

		//клиника сказала, что была запись по заявке
		$request->setBillingState(RequestModel::BILLING_STATE_RECORD, ['date_admission' => time()]);
		$request->save();
		$this->assertEquals(RequestModel::BILLING_STATUS_YES, $request->billing_status);

		//изменяем клинику на оплату за дошедших
		$request->clinic_id = 1;
		$request->date_admission = time() + 3600;
		$request->save();
		$this->assertEquals(RequestModel::BILLING_STATUS_NO, $request->billing_status);

		//меняем обратно другим способом
		$request->clinic_id = 2;
		$request->save();
		$this->assertEquals(RequestModel::BILLING_STATUS_YES, $request->billing_status);

		//изменяем тип заявки
		//оплата за дошедших на диагностику clinic_id = 3
		$request->kind = RequestModel::KIND_DIAGNOSTICS;
		$request->clinic_id = 3;
		$request->save();
		$this->assertEquals(RequestModel::BILLING_STATUS_NO, $request->billing_status);

		//клиника, онлайн-запись по диагностике
		//нет записи для диагностики по звонкам, в биллинг не попадет
		$request->clinic_id = 1;
		$request->req_type = RequestModel::TYPE_ONLINE_RECORD;
		$request->save();
		$this->assertEquals(RequestModel::BILLING_STATUS_NO, $request->billing_status);

		//переводим на клинику по дошедшим на диагностику
		$request->clinic_id = 3;
		$request->req_type = RequestModel::TYPE_CALL;
		$request->date_admission = time() + 3600;
		$request->save();
		$this->assertEquals(RequestModel::BILLING_STATUS_NO, $request->billing_status);

		//клиника сказала, что был визит
		$request->setBillingState(RequestModel::BILLING_STATE_CAME, ['date_admission' => time()]);
		$request->save();
		$this->assertEquals(RequestModel::BILLING_STATUS_YES, $request->billing_status);

		//клиника отклонила заявку
		$request->setBillingState(RequestModel::BILLING_STATE_REFUSED);
		$request->save();
		$this->assertEquals(RequestModel::BILLING_STATUS_REFUSED, $request->billing_status);
		$this->assertEquals(false, $request->isCame());
		$this->assertEquals(true, $request->isRefused());

		//клиника подтвердила заявку
		$request->setBillingState(RequestModel::BILLING_STATE_CAME, ['date_admission' => time()]);
		$request->save();
		$this->assertEquals(RequestModel::BILLING_STATUS_YES, $request->billing_status);
		$this->assertEquals(true, $request->isCame());
		$this->assertEquals(false, $request->isRefused());

		//изменяем время записи
		$request->date_admission = time() + 3600;
		$request->save();
		$this->assertEquals(RequestModel::BILLING_STATUS_NO, $request->billing_status);
		$this->assertEquals(false, $request->isCame());
		$this->assertEquals(false, $request->isRefused());
	}

	/**
	 * тест стоимости заявки
	 */
	public function testCost()
	{
		$this->getFixtureManager()->checkIntegrity(false);
		$this->getFixtureManager()->truncateTable('request');
		$this->getFixtureManager()->truncateTable('diagnostica');
		$this->getFixtureManager()->loadFixture('diagnostica');

		$totalCost = 0;

		//##################### clinic_id = 1 за дошедших + диагностика онлайн-запись + звонки####################//
		//произвольный врач 1-я заявка
		$request = $this->createTestRequest(
			['clinic_id' => 1, 'kind' => RequestModel::KIND_DOCTOR, 'date_admission' => time()],
			RequestModel::BILLING_STATE_CAME
		);
		$totalCost += $request->request_cost;
		$this->assertEquals(500, $request->request_cost);

		//к окулисту 1-я заявка
		$request = $this->createTestRequest(
			['clinic_id' => 1, 'kind' => RequestModel::KIND_DOCTOR, 'date_admission' => time(), 'req_sector_id' => 84],
			RequestModel::BILLING_STATE_CAME
		);
		$totalCost += $request->request_cost;
		$this->assertEquals(600, $request->request_cost);

		//произвольный врач 2-я заявка
		$request = $this->createTestRequest(
			['clinic_id' => 1, 'kind' => RequestModel::KIND_DOCTOR, 'date_admission' => time()],
			RequestModel::BILLING_STATE_CAME
		);
		$totalCost += $request->request_cost;
		$this->assertEquals(500, $request->request_cost);

		//к окулисту 2-я заявка уже по 800
		$request = $this->createTestRequest(
			['clinic_id' => 1, 'kind' => RequestModel::KIND_DOCTOR, 'date_admission' => time(), 'req_sector_id' => 84],
			RequestModel::BILLING_STATE_CAME
		);
		$totalCost += $request->request_cost;
		$this->assertEquals(800, $request->request_cost);

		//произвольный врач 3-я заявка уже по 700
		$request = $this->createTestRequest(
			['clinic_id' => 1, 'kind' => RequestModel::KIND_DOCTOR, 'date_admission' => time()],
			RequestModel::BILLING_STATE_CAME
		);
		$totalCost += $request->request_cost;
		$this->assertEquals(700, $request->request_cost);


		//к окулисту 3-я заявка по 800
		$request = $this->createTestRequest(
			['clinic_id' => 1, 'kind' => RequestModel::KIND_DOCTOR, 'date_admission' => time(), 'req_sector_id' => 84],
			RequestModel::BILLING_STATE_CAME
		);
		$totalCost += $request->request_cost;
		$this->assertEquals(800, $request->request_cost);

		//для клиники 1 было создано 6 заявок в биллинге
		$tariff = $request->clinic->getRequestContract($request);
		$this->assertEquals(6, $tariff->getTotalRequestNumInBilling(date('Y-m') . "-01", date('Y-m-t') . " 23:59:59"));
		$this->assertEquals($totalCost, $tariff->getTotalRequestCostInBilling(date('Y-m') . "-01", date('Y-m-t') . " 23:59:59"));

		//смена врача на произвольного
		$request->req_sector_id = 0;
		$request->save();
		$this->assertEquals(700, $request->request_cost);

		//меняем на клинику, у которой нет информации о контракте
		$request->saveClinic(11);
		$this->assertEquals(null, $request->request_cost);

		//меняем обратно
		$request->saveClinic(1);
		$this->assertEquals(700, $request->request_cost);

		//неактивная клиника, стоимость не должна меняться
		$request->clinic->status = ClinicModel::STATUS_NEW;
		$request->request_cost = null;
		$request->clinic->save();
		$request->save();
		$this->assertEquals(null, $request->request_cost);

		//меняем обратно
		$request->clinic->status = ClinicModel::STATUS_ACTIVE;
		$request->clinic->save();
		$request->save();
		$this->assertEquals(700, $request->request_cost);

		//ставим, что визита не было
		$request->setBillingState(RequestModel::BILLING_STATE_RECORD, ['date_admission' => time()+1000]);
		$request->save();
		$this->assertEquals(null, $request->request_cost);

		//меняем на договор за звонки на диагностику
		$rr = new RequestRecordModel();
		$rr->request_id = $request->req_id;
		$rr->duration = 31;
		$rr->clinic_id = 1;
		$rr->save();

		$request->req_type = RequestModel::TYPE_CALL;
		$request->kind = RequestModel::KIND_DIAGNOSTICS;
		$request->save();
		$this->assertEquals(50, $request->request_cost);

		//меняем на онлайн-запись на диагностику
		$request->req_type = RequestModel::TYPE_ONLINE_RECORD;
		$request->kind = RequestModel::KIND_DIAGNOSTICS;
		$request->save();
		$this->assertEquals(50, $request->request_cost);

		//это первая заявка на диагностику-онлайн  - по 150
		$request = $this->createTestRequest(
			['clinic_id' => 3, 'kind' => RequestModel::KIND_DIAGNOSTICS, 'req_type' => RequestModel::TYPE_ONLINE_RECORD, 'date_admission' => time()],
			RequestModel::BILLING_STATE_CAME
		);
		$this->assertEquals(150, $request->request_cost);

		//на онлайн-запись на диагностику 2-я заявка уже по 200
		$request = $this->createTestRequest(
			['clinic_id' => 3, 'kind' => RequestModel::KIND_DIAGNOSTICS, 'req_type' => RequestModel::TYPE_ONLINE_RECORD, 'date_admission' => time()],
			RequestModel::BILLING_STATE_CAME
		);
		$this->assertEquals(200, $request->request_cost);

		//онлайн-запись на диагностику на КТ/МРТ 1-я заявка
		$request = $this->createTestRequest(
			['clinic_id' => 3, 'kind' => RequestModel::KIND_DIAGNOSTICS, 'diagnostics_id' => 11, 'req_type' => RequestModel::TYPE_ONLINE_RECORD, 'date_admission' => time()],
			RequestModel::BILLING_STATE_CAME
		);
		$this->assertEquals(250, $request->request_cost);


		//онлайн-запись на диагностику на КТ/МРТ 2-я заявка
		$request = $this->createTestRequest(
			['clinic_id' => 3, 'kind' => RequestModel::KIND_DIAGNOSTICS, 'diagnostics_id' => 21, 'req_type' => RequestModel::TYPE_ONLINE_RECORD, 'date_admission' => time()],
			RequestModel::BILLING_STATE_CAME
		);

		$this->assertEquals(300, $request->request_cost);


		//онлайн-запись на диагностику на КТ/МРТ 3-я заявка уже по 400
		$request = $this->createTestRequest(
			['clinic_id' => 3, 'kind' => RequestModel::KIND_DIAGNOSTICS, 'diagnostics_id' => 11, 'req_type' => RequestModel::TYPE_ONLINE_RECORD, 'date_admission' => time()],
			RequestModel::BILLING_STATE_CAME
		);
		$this->assertEquals(400, $request->request_cost);

		//##################### clinic_id = 2 за дошедших к произвольному врачу ####################//

		$request = $this->createTestRequest(
			['clinic_id' => 2, 'kind' => RequestModel::KIND_DOCTOR, 'date_admission' => time(), 'req_sector_id' => 84],
			RequestModel::BILLING_STATE_RECORD
		);
		$this->assertEquals(400, $request->request_cost);

		//##################### clinic_id = 3 за дошедших на диагностику ####################//
		$request = $this->createTestRequest(
			['clinic_id' => 3, 'kind' => RequestModel::KIND_DIAGNOSTICS, 'date_admission' => time(), 'diagnostics_id' => 11],
			RequestModel::BILLING_STATE_CAME
		);
		$this->assertEquals(400, $request->request_cost);
		//для клиники 3 было создано 1 заявка общей стоимость 175
		$tariff = $request->clinic->getRequestContract($request);
		$this->assertEquals(6, $tariff->getTotalRequestNumInBilling(date('Y-m') . "-01", date('Y-m-t') . " 23:59:59"));
		$this->assertEquals(1700, $tariff->getTotalRequestCostInBilling(date('Y-m') . "-01", date('Y-m-t') . " 23:59:59"));


	}

	/**
	 * проверка расчета стоимости заявок, когда тариф прописан у головного филиала.
	 * Для дочерних филиалов тарифы должны барться с головы
	 */
	public function testCostInBranches()
	{
		$this->getFixtureManager()->truncateTable('request');
		$this->getFixtureManager()->truncateTable('diagnostica');
		$this->getFixtureManager()->loadFixture('diagnostica');

		$totalCost = 0;

		//##################### clinic_id = 1 за дошедших + диагностика онлайн-запись + звонки####################//
		//произвольный врач 1-я заявка дочерняя клиника
		$request = $this->createTestRequest(
			['clinic_id' => 125, 'kind' => RequestModel::KIND_DOCTOR, 'date_admission' => time()],
			RequestModel::BILLING_STATE_CAME
		);
		$totalCost += $request->request_cost;
		$this->assertEquals(500, $request->request_cost);

		//произвольный врач 2-я заявка дочерняя клиника
		$request = $this->createTestRequest(
			['clinic_id' => 125, 'kind' => RequestModel::KIND_DOCTOR, 'date_admission' => time()],
			RequestModel::BILLING_STATE_CAME
		);
		$totalCost += $request->request_cost;
		$this->assertEquals(500, $request->request_cost);


		//произвольный врач 3-я заявка в головную клинику уже по 700
		$request = $this->createTestRequest(
			['clinic_id' => 1, 'kind' => RequestModel::KIND_DOCTOR, 'date_admission' => time()],
			RequestModel::BILLING_STATE_CAME
		);
		$totalCost += $request->request_cost;
		$this->assertEquals(700, $request->request_cost);


		//для клиники 1 и ее филиалов было создано 3 заявки в биллинге
		$tariff = $request->clinic->getRequestContract($request);
		$this->assertEquals(3, $tariff->getTotalRequestNumInBilling(date('Y-m') . "-01", date('Y-m-t') . " 23:59:59"));
		$this->assertEquals($totalCost, $tariff->getTotalRequestCostInBilling(date('Y-m') . "-01", date('Y-m-t') . " 23:59:59"));
	}

	/**
	 * создание заявки с заданными аттрибутами и в состоянии
	 *
	 * @param array $attr
	 * @param int $billingState
	 *
	 * @return RequestModel
	 */
	private function createTestRequest($attr, $billingState)
	{
		$request = new RequestModel(RequestModel::SCENARIO_OPERATOR);
		$request->client_name = 'иванов иван';
		$request->client_phone = '79168112564';

		foreach ($attr as $k => $v) {
			$request->$k = $v;
		}

		$request->setBillingState($billingState, $attr);
		$request->save();

		return $request;
	}

	/**
	 * Создание заявок на диагностику с различными парметрами
	 *
	 * @dataProvider requestCreateForDiagnosticsDataProvider
	 */
	public function testRequestCreateForDiagnostics($attributes, \Closure $checkFunction)
	{
		$this->getFixtureManager()->checkIntegrity(false);

		$this->getFixtureManager()->truncateTable('clinic');
		$this->getFixtureManager()->truncateTable('doctor');
		$this->getFixtureManager()->truncateTable('clinic_contract');
		$this->getFixtureManager()->truncateTable('diagnostica4clinic');
		$this->getFixtureManager()->truncateTable('request');

		$this->getFixtureManager()->loadFixture('clinic');
		$this->getFixtureManager()->loadFixture('doctor');
		$this->getFixtureManager()->loadFixture('clinic_contract');
		$this->getFixtureManager()->loadFixture('diagnostica4clinic');

		$r = new RequestModel(RequestModel::SCENARIO_DIAGNOSTIC_ONLINE);

		foreach($attributes as $k => $v){
			$r->$k = $v;
		}

		$r->save();

		$checkFunction($r);
	}

	/**
	 * Дата провайдер для создания заявок для диагностики
	 *
	 * @return array
	 */
	public function requestCreateForDiagnosticsDataProvider()
	{
		return [
			[
				//валидная заявка
				[
					'client_name' => 'Тест Тест',
					'client_phone' => '79261234567',
					'diagnostics_id' => 1,
					'clinic_id' => 1,
					'date_admission' => '1410339545',
				],
				function(RequestModel $r){
					$this->assertEquals(0, count($r->getErrors()));
					$this->assertEquals(RequestModel::TYPE_ONLINE_RECORD, $r->req_type);
				}
			],
			[
				//заявка с не всеми обязательными полями
				[
					'client_name' => 'Тест Тест',
					'client_phone' => '79261234567',
					'diagnostics_id' => 1,
					'clinic_id' => 1,
				],
				function(RequestModel $r){
					$this->assertEquals(1, count($r->getErrors('date_admission')));
				}
			],
			[
				//у клиники нет тарифа диагностики и нет доктора
				[
					'client_name' => 'Тест Тест',
					'client_phone' => '79261234567',
					'diagnostics_id' => 1,
					'clinic_id' => 2,
					'date_admission' => '1410339545',
				],
				function(RequestModel $r){
					$this->assertEquals(0, count($r->getErrors()));
					$this->assertEquals(RequestModel::TYPE_WRITE_TO_DOCTOR, $r->req_type);
				}
			],
			[
				//у клиники нет тарифа диагностики но есть доктор
				[
					'client_name' => 'Тест Тест',
					'client_phone' => '79261234567',
					'req_doctor_id' => 1,
					'diagnostics_id' => 123,
					'date_admission' => '1410339545',
					'clinic_id' => 2,
				],
				function(RequestModel $r){
					$this->assertEquals(0, count($r->getErrors()));
					$this->assertEquals(RequestModel::TYPE_WRITE_TO_DOCTOR, $r->req_type);
				}
			],
		];
	}

	/**
	 * Создание заявок на диагностику с различными парметрами
	 *
	 * @param array $attributes
	 * @param \Closure $checkFunction
	 *
	 * @dataProvider requestCreateForDiagnosticsOnlineDataProvider
	 */
	public function testRequestCreateForDiagnosticsOnline($attributes, \Closure $checkFunction)
	{
		$this->getFixtureManager()->checkIntegrity(false);

		$this->getFixtureManager()->truncateTable('clinic');
		$this->getFixtureManager()->truncateTable('doctor');
		$this->getFixtureManager()->truncateTable('clinic_contract');
		$this->getFixtureManager()->truncateTable('diagnostica4clinic');
		$this->getFixtureManager()->truncateTable('request');

		$this->getFixtureManager()->loadFixture('clinic');
		$this->getFixtureManager()->loadFixture('doctor');
		$this->getFixtureManager()->loadFixture('clinic_contract');
		$this->getFixtureManager()->loadFixture('diagnostica4clinic');

		$r = new RequestModel(RequestModel::SCENARIO_DIAGNOSTIC_ONLINE);

		foreach($attributes as $k => $v){
			$r->$k = $v;
		}

		$r->save();

		$checkFunction($r);
	}

	/**
	 * Дата провайдер для создания заявок для диагностики
	 *
	 * @return array
	 */
	public function requestCreateForDiagnosticsOnlineDataProvider()
	{
		return [
			[
				//валидная заявка
				[
					'client_name' => 'Тест1',
					'client_phone' => '79261234567',
					'diagnostics_id' => 1,
					'clinic_id' => 1,
					'date_admission' => '2236204800',
				],
				function(RequestModel $r){
					$this->assertEquals(0, count($r->getErrors()));
					$this->assertEquals(RequestModel::TYPE_ONLINE_RECORD, $r->req_type);
					$this->assertFalse((bool)$r->is_hot);
					$this->assertEquals(RequestModel::STATUS_RECORD, $r->req_status);
				}
			],
			[
				//заявка с не всеми обязательными полями
				[
					'client_phone' => '79261234567',
					'diagnostics_id' => 1,
				],
				function(RequestModel $r){
					$this->assertEquals(3, count($r->getErrors()));
					$this->assertEquals(1, count($r->getErrors('date_admission')));
					$this->assertEquals(1, count($r->getErrors('client_name')));
					$this->assertEquals(1, count($r->getErrors('clinic_id')));
				}
			],
			[
				//клиники не существует
				[
					'client_name' => 'Тест3',
					'client_phone' => '79261234567',
					'diagnostics_id' => 1,
					'clinic_id' => 1123333,
					'date_admission' => '2236204800',
				],
				function(RequestModel $r){
					$this->assertEquals(1, count($r->getErrors()));
					$this->assertEquals(1, count($r->getErrors('clinic_id')));
				}
			],
			[
				//у клиники нет тарифа диагностики онлайн
				[
					'client_name' => 'Тест4',
					'client_phone' => '79261234567',
					'req_doctor_id' => 1,
					'diagnostics_id' => 123,
					'date_admission' => '2236204800',
					'clinic_id' => 2,
				],
				function(RequestModel $r){
					$this->assertEquals(0, count($r->getErrors()));
					$this->assertEquals(RequestModel::TYPE_WRITE_TO_DOCTOR, $r->req_type);
				}
			],
		];
	}

	/**
	 * Тест сценария SCENARIO_VALIDATE_PHONE
	 *
	 * @param array $attributes
	 * @param callable $checkFunction
	 * @throws \CException
	 *
	 * @dataProvider validationPhoneScenarioDataProvider
	 */
	public function testValidationPhoneScenario($attributes, \Closure $checkFunction)
	{
		$this->getFixtureManager()->checkIntegrity(false);
		$this->getFixtureManager()->truncateTable('request');

		$r = new RequestModel(RequestModel::SCENARIO_VALIDATE_PHONE);
		$r->attributes = $attributes;
		$r->save();

		$checkFunction($r);
	}

	/**
	 * Данные для testValidationPhoneScenario
	 *
	 * @return array
	 */
	public function validationPhoneScenarioDataProvider()
	{
		return [
			[
				//валидная заявка
				[
					'client_name' => 'Тест1',
					'client_phone' => '79261234567',
					'diagnostics_id' => 1,
					'clinic_id' => 1,
					'date_admission' => '2236204800',
				],
				function(RequestModel $r){
					$this->assertEquals(0, count($r->getErrors()));
					$this->assertFalse((bool)$r->is_hot);
					$this->assertEquals(RequestModel::STATUS_PRE_CREATED, $r->req_status);
					$this->assertNotNull($r->validation_code);

					//date_admission unsafe
					$this->assertNull($r->date_admission);
				}
			],
			[

				//ошибка, нужен телефон
				[
					'diagnostics_id' => 1,
					'clinic_id' => 1,
				],
				function(RequestModel $r){
					$this->assertEquals(1, count($r->getErrors()));
					$this->assertEquals(1, count($r->getErrors('client_phone')));
				}
			],
		];
	}

	/**
	 * Проверка сохранения источника
	 *
	 * @dataProvider saveSourceTypeDataProvider
	 */
	public function testSaveSourceType($partnerId, $result)
	{
		$this->getFixtureManager()->checkIntegrity(false);
		$this->getFixtureManager()->truncateTable('request');

		$request = new RequestModel();
		$request->client_name = 'иванов иван';
		$request->client_phone = '79168112564';
		$request->partner_id = $partnerId;
		$request->save();

		$this->assertEquals($result, $request->source_type);
	}

	/**
	 * @return array
	 */
	public function saveSourceTypeDataProvider()
	{
		return [
			[
				1,
				RequestModel::SOURCE_PARTNER,
			],
			[
				8,
				RequestModel::SOURCE_YANDEX,
			],
			[
				12,
				RequestModel::SOURCE_IPHONE,
			],
		];
	}

	/**
	 * Тест сохранения доктора, клиники и даты записи при броне
	 *
	 * @throws \CException
	 */
	public function testBook()
	{
		$this->getFixtureManager()->truncateTable('clinic');
		$this->getFixtureManager()->truncateTable('request');
		$this->getFixtureManager()->truncateTable('booking');
		$this->getFixtureManager()->truncateTable('slot');

		$this->getFixtureManager()->loadFixture('clinic');
		$this->getFixtureManager()->loadFixture('request');
		$this->getFixtureManager()->loadFixture('slot');

		//у клиники разрешена online_record
		$slotId = 'uuid1';
		$request = RequestModel::model()->findByPk(2);

		//пофиг что бронь не пройдет. тест фиксации доктора, клиники и даты записи
		$request->book($slotId, true);
		$slot = SlotModel::model()->byExternalId($slotId)->find();

		$this->assertEquals(strtotime($slot->start_time), $request->date_admission);
		$this->assertEquals($slot->doctorClinic->doctor_id, $request->req_doctor_id);
		$this->assertEquals($slot->doctorClinic->clinic_id, $request->clinic_id);

		$this->getFixtureManager()->truncateTable('request');
		$this->getFixtureManager()->truncateTable('booking');
		$this->getFixtureManager()->loadFixture('request');

		//у клиники запрещена online_record, дата записи не должна проставиться
		$slotId = 'uuid4';
		$request =(new RequestModel())->findByPk(2);

		$request->book($slotId, true);
		$slot = SlotModel::model()->byExternalId($slotId)->find();

		$this->assertNotEquals(strtotime($slot->start_time), $request->date_admission);
		$this->assertEquals($slot->doctorClinic->doctor_id, $request->req_doctor_id);
		$this->assertEquals($slot->doctorClinic->clinic_id, $request->clinic_id);
	}

	/**
	 * Расчет kind
	 *
	 * @param int|null $clinicId
	 * @param int|null $partnerId
	 * @param string|null $scenario
	 * @param string|null $replacedPhone
	 * @param int $expected
	 *
	 * @dataProvider calculateKindDataProvider
	 */
	public function testCalculateKind($clinicId, $partnerId, $scenario, $replacedPhone, $expected)
	{
		$this->getFixtureManager()->checkIntegrity(false);

		$this->getFixtureManager()->truncateTable('clinic');
		$this->getFixtureManager()->loadFixture('clinic');

		$this->getFixtureManager()->truncateTable('partner');
		$this->getFixtureManager()->loadFixture('partner');

		$request = new RequestModel();
		$request->clinic_id = $clinicId;
		$request->partner_id = $partnerId;
		$scenario && $request->setScenario($scenario);

		$actual = $request->calculateKind($replacedPhone);

		$this->assertEquals($expected, $actual);
	}

	/**
	 * Данные для testCalculateKind
	 *
	 * @return array
	 */
	public function calculateKindDataProvider()
	{
		return [
			[1, null, RequestModel::SCENARIO_PARTNER, null, RequestModel::KIND_DOCTOR], //сценарий рулит
			[1, null, RequestModel::SCENARIO_DIAGNOSTIC_ONLINE, null, RequestModel::KIND_DIAGNOSTICS], //сценарий рулит
			[4, null, null, '74959880166', RequestModel::KIND_DIAGNOSTICS],  //клиника с подменным номером на диагностику
			[149, null, null, '74949992239', RequestModel::KIND_DOCTOR], //клиника с подменным номером на врача(лендинг)
			[3, null, null, null, RequestModel::KIND_DIAGNOSTICS], //клиника но не диагностика
			[2, null, null, null, RequestModel::KIND_DOCTOR], //диагностика но не клиника
			[83, 12, null, null, RequestModel::KIND_DIAGNOSTICS], //клиника и диагн и с партнером
			[null, 12, null, null, RequestModel::KIND_DIAGNOSTICS], //без клиники но с партнером
			[null, null, null, null, RequestModel::KIND_DOCTOR], //дефолт
		];
	}

	/**
	 * Тест проверки заявки на спам
	 *
	 * @dataProvider requestIsSpamProvider
	 *
	 * @param int $scenario
	 * @param int $requests
	 * @param int $spam
	 */
	public function testRequestIsSpam($scenario, $requests, $spam)
	{
		$this->loadFixtures();

		$_REQUEST['USER_AGENT'] = 'Mozilla';
		$_REQUEST['REMOTE_ADDR'] = '127.0.0.1';

		for ($i = 0; $i <= RequestModel::SPAM_NUM_REQUESTS; $i++) {
			$request = new RequestModel($scenario);
			$request->client_name = 'Spam';
			$request->client_phone = 71234567890;
			try {
				$request->save();
			} catch (\CException $e) {
				$this->assertEquals('Заявка будет помещена в спам', $e->getMessage());
			}
		}

		$requestCount = RequestModel::model()->count();
		$spamCount = RequestSpamModel::model()->count();

		if (!\Yii::app()->getParams()['antispamEnabled']) {
			$spam = 0;
			$requests = $requestCount;
		}

		$this->assertEquals($requests, $requestCount);
		$this->assertEquals($spam, $spamCount);
	}

	/**
	 * Данные для testRequestIsSpam
	 *
	 * @return array
	 */
	public function requestIsSpamProvider()
	{
		return [
			[RequestModel::SCENARIO_SITE, 2, 1],
			[RequestModel::SCENARIO_CALL, 2, 1],
			[RequestModel::SCENARIO_VALIDATE_PHONE, 2, 1],
			[RequestModel::SCENARIO_ASTERISK, 3, 0],
			[RequestModel::SCENARIO_OPERATOR, 3, 0],
		];
	}

	/**
	 * Тест определения req_type
	 *
	 * @throws \CException
	 */
	public function testSetReqType()
	{
		$fm = $this->getFixtureManager();
		$fm->checkIntegrity(false);

		$fm->truncateTable('clinic');
		$fm->loadFixture('clinic');
		$fm->truncateTable('clinic_contract');
		$fm->loadFixture('clinic_contract');


		$request = new RequestModel();

		//запись на диагностику, нет клиники
		//врача и диагностики нет
		$request->setScenario(RequestModel::SCENARIO_DIAGNOSTIC_ONLINE);
		$request->setReqType();
		$this->assertEquals(RequestModel::TYPE_PICK_DOCTOR, $request->req_type);

		//клиника с тарифом онлайн запись на диагностику
		$request->clinic_id = 1;
		$request->setKind();
		$request->setReqType();
		$this->assertEquals(RequestModel::TYPE_ONLINE_RECORD, $request->req_type);

		$request = new RequestModel();
		$request->req_doctor_id = 1;
		$request->scenario = RequestModel::SCENARIO_PARTNER;
		$request->setReqType();
		$this->assertEquals(RequestModel::TYPE_WRITE_TO_DOCTOR, $request->req_type);

		$request = new RequestModel(RequestModel::SCENARIO_CALL);
		$request->setReqType();
		$this->assertEquals(RequestModel::TYPE_PICK_DOCTOR, $request->req_type);

		$request->scenario = RequestModel::SCENARIO_ASTERISK;
		$request->setReqType();
		$this->assertEquals(RequestModel::TYPE_CALL, $request->req_type);
	}

	/**
	 * Данные для testOldStatusChange
	 *
	 * @return array
	 */
	public function oldStatusChangeData()
	{
		$c = [
			'isTransfer'   => ['isTransfer' => 1],
			'!isTransfer'  => ['isTransfer' => 0],
			'isReject'     => ['isReject' => 1],
			'callLater'    => ['callLater' => 1],
			'!callLater'   => [],
			'appointment'  => ['AppointmentDate' => date('Y-m-d H:i'), 'appointment' => time()],
			'!appointment' => ['AppointmentDate' => null, 'appointment' => null],
			'appStatus'    => ['appStatus' => 1],
			'!appStatus'    => ['appStatus' => 0],
		];

		return $this->statusChangeData($c);
	}

	/**
	 * Изменение статуса заявки новым классом
	 *
	 * @param array $attr
	 * @param $params
	 * @param $resultAttr
	 * @param array $additionalParams
	 *
	 * @dataProvider newStatusChangeData
	 */
	public function testNewStatusChange($attr, $params, $resultAttr, array $additionalParams = [])
	{
		$r = new RequestModel();
		$r->setScenario(RequestModel::SCENARIO_OPERATOR);
		foreach ($attr as $k => $v) {
			$r->$k = $v;
		}
		$r->save();

		$r = RequestModel::model()->findByPk($r->req_id);
		foreach ($params as $k => $v) {
			$r->$k = $v;
		}

		$status = $r->getStatusForOperatorAction($additionalParams);
		$r->setScenario(RequestModel::SCENARIO_OPERATOR);
		$r->req_status = $status;
		$r->save();

		$r = RequestModel::model()->findByPk($r->req_id);
		foreach ($resultAttr as $k => $v) {
			$this->assertEquals($r->$k, $v);
		}
	}

	/**
	 * Данные для testOldStatusChange
	 *
	 * @return array
	 */
	public function newStatusChangeData()
	{
		$c = [
			'isTransfer'   => ['is_transfer' => 1],
			'!isTransfer'  => ['is_transfer' => 0],
			'isReject'     => ['req_status' => RequestModel::STATUS_REJECT, 'reject_reason' => Rejection::REASON_CLIENT_NOT_ANSWER],
			'callLater'    => ['call_later_time' => time()],
			'!callLater'   => ['call_later_time' => null],
			'appointment'  => ['date_admission' => date('Y-m-d H:i'), ],
			'!appointment' => ['date_admission' => null, ],
			'appStatus'    => ['appointment_status' => 1],
			'!appStatus'   => ['appointment_status' => 0],
		];

		return $this->statusChangeData($c);
	}

	/**
	 * Данные для changeStatusTest
	 *
	 * @param array $c
	 * @return array
	 */
	public function statusChangeData($c)
	{
		return [
			//новая заявка 0
			[
				['req_status' => RequestModel::STATUS_NEW, 'req_user_id' => 1 ],
				[],
				['req_status' => RequestModel::STATUS_ACCEPT, 'req_user_id' => 1, ],
			],
			//новая заявка при создании нескольких заявок сразу
			[
				['req_status' => RequestModel::STATUS_NEW, 'req_user_id' => 1 ],
				[],
				['req_status' => RequestModel::STATUS_RECORD, 'req_user_id' => 1, ],
				['multiply_create' => 1],
			],
			//новая заявка при создании нескольких заявок сразу
			[
				['req_status' => RequestModel::STATUS_NEW, 'req_user_id' => 1 ],
				[],
				['req_status' => RequestModel::STATUS_ACCEPT, 'req_user_id' => 1, ],
				['multiply_create' => 0],
			],

			//############################# ПРИНЯТА ##########################
			//Принята 6 -> в обработке 1
			[
				['req_status' => RequestModel::STATUS_ACCEPT, ],
				$c['isTransfer'],
				['req_status' => RequestModel::STATUS_PROCESS, ],
			],
			//Принята 6 -> перезвонить 7
			[
				['req_status' => RequestModel::STATUS_ACCEPT, ],
				$c['callLater'],
				['req_status' => RequestModel::STATUS_CALL_LATER, ],
			],
			//Принята 6 -> отказ 5
			[
				['req_status' => RequestModel::STATUS_ACCEPT, ],
				$c['isReject'],
				['req_status' => RequestModel::STATUS_REJECT, ],
			],
			//isTransfer +  callLater
			[
				['req_status' => RequestModel::STATUS_ACCEPT, ],
				$c['callLater'] + $c['isTransfer'],
				['req_status' => RequestModel::STATUS_CALL_LATER, ],
			],
			//isTransfer +  callLater + isReject
			[
				['req_status' => RequestModel::STATUS_ACCEPT, ],
				$c['callLater'] + $c['isTransfer'] + $c['isReject'],
				['req_status' => RequestModel::STATUS_REJECT, ],
			],
			//ни одно из правил не сработало
			[
				['req_status' => RequestModel::STATUS_ACCEPT, ],
				[],
				['req_status' => RequestModel::STATUS_ACCEPT, ],
			],

			//############################# В ОБРАБОТКЕ ##########################
			//В обработке 1 -> Принята 6
			[
				['req_status' => RequestModel::STATUS_PROCESS, ],
				$c['!isTransfer'],
				['req_status' => RequestModel::STATUS_ACCEPT, ],
			],
			[
				['req_status' => RequestModel::STATUS_PROCESS, ],
				$c['isTransfer'],
				['req_status' => RequestModel::STATUS_PROCESS, ],
			],
			//В обработке 1 -> обработана 2
			[
				['req_status' => RequestModel::STATUS_PROCESS, ],
				$c['appointment'] + $c['isTransfer'],
				['req_status' => RequestModel::STATUS_RECORD, ],
			],
			//В обработке 1 -> перезвонить 7
			[
				['req_status' => RequestModel::STATUS_PROCESS, ],
				$c['callLater'],
				['req_status' => RequestModel::STATUS_CALL_LATER, ],
			],
			//В обработке 1 -> отказ 5
			[
				['req_status' => RequestModel::STATUS_PROCESS, ],
				$c['isReject'],
				['req_status' => RequestModel::STATUS_REJECT, ],
			],
			//!isTransfer + appointment
			[
				['req_status' => RequestModel::STATUS_PROCESS, ],
				$c['appointment'] + $c['!isTransfer'],
				['req_status' => RequestModel::STATUS_ACCEPT, ],
			],
			//isTransfer + callLater
			[
				['req_status' => RequestModel::STATUS_PROCESS, ],
				$c['isTransfer'] + $c['callLater'],
				['req_status' => RequestModel::STATUS_CALL_LATER, ],
			],
			//isTransfer + callLater + isReject
			[
				['req_status' => RequestModel::STATUS_PROCESS, ],
				$c['isTransfer'] + $c['callLater'] + $c['isReject'],
				['req_status' => RequestModel::STATUS_REJECT, ],
			],
			//ни одно из правил не сработало
			[
				['req_status' => RequestModel::STATUS_PROCESS, ],
				[],
				['req_status' => RequestModel::STATUS_ACCEPT, ],
			],


			//############################# ОБРАБОТАНА ##########################
			//обработана - принята
			[
				['req_status' => RequestModel::STATUS_RECORD, ],
				$c['!isTransfer'],
				['req_status' => RequestModel::STATUS_ACCEPT, ],
			],
			//обработана - в обработке
			[
				['req_status' => RequestModel::STATUS_RECORD, ],
				$c['isTransfer'] + $c['!appointment'] ,
				['req_status' => RequestModel::STATUS_PROCESS, ],
			],
			//обработана - завершена
			[
				['req_status' => RequestModel::STATUS_RECORD, ],
				$c['isTransfer'] + $c['appointment'] + $c['appStatus'],
				['req_status' => RequestModel::STATUS_CAME, ],
			],
			[
				['req_status' => RequestModel::STATUS_RECORD, 'appointment_status' => 1],
				$c['isTransfer'] + $c['appointment'],
				['req_status' => RequestModel::STATUS_CAME, ],
			],
			//обработана - перезвонить
			[
				['req_status' => RequestModel::STATUS_RECORD, ],
				$c['callLater'],
				['req_status' => RequestModel::STATUS_CALL_LATER, ],
			],
			//обработана - отказ
			[
				['req_status' => RequestModel::STATUS_RECORD, ],
				$c['isReject'],
				['req_status' => RequestModel::STATUS_REJECT, ],
			],
			//ни одно из правил не сработало
			[
				['req_status' => RequestModel::STATUS_RECORD, ],
				[],
				['req_status' => RequestModel::STATUS_ACCEPT, ],
			],
			//isTransfer + callLater
			[
				['req_status' => RequestModel::STATUS_RECORD, ],
				$c['isTransfer'] + $c['callLater'],
				['req_status' => RequestModel::STATUS_CALL_LATER, ],
			],
			//isTransfer + callLater + isReject
			[
				['req_status' => RequestModel::STATUS_RECORD, ],
				$c['isTransfer'] + $c['callLater'] + $c['isReject'],
				['req_status' => RequestModel::STATUS_REJECT, ],
			],
			//isTransfer + appStatus
			[
				['req_status' => RequestModel::STATUS_RECORD, ],
				$c['isTransfer'] + $c['appStatus'],
				['req_status' => RequestModel::STATUS_PROCESS, ],
			],
			/**
			 * @link https://docdoc.atlassian.net/browse/DD-681
			 */
			//############################# ЗАВЕРШЕНА ##########################
			//завершена - принята ИГНОР
			[
				['req_status' => RequestModel::STATUS_CAME, ],
				$c['!isTransfer'],
				['req_status' => RequestModel::STATUS_CAME, ],
			],
			//завершена - В обработке ИГНОР
			[
				['req_status' => RequestModel::STATUS_CAME, ],
				$c['isTransfer'] + $c['!appointment'],
				['req_status' => RequestModel::STATUS_CAME, ],
			],
			//завершена - Обработана ИГНОР
			[
				['req_status' => RequestModel::STATUS_CAME, 'appointment_status' => 0],
				$c['isTransfer'] + $c['appointment'],
				['req_status' => RequestModel::STATUS_CAME, ],
			],
			//завершена - Перезвонить ИГНОР
			[
				['req_status' => RequestModel::STATUS_CAME, ],
				$c['callLater'],
				['req_status' => RequestModel::STATUS_CAME, ],
			],
			//завершена - отказ  ИГНОР
			[
				['req_status' => RequestModel::STATUS_CAME, ],
				$c['isReject'],
				['req_status' => RequestModel::STATUS_CAME, ],
			],
			//ни одно из правил не сработало  ИГНОР
			[
				['req_status' => RequestModel::STATUS_CAME, ],
				[],
				['req_status' => RequestModel::STATUS_CAME, ],
			],
			//isTransfer + callLater  ИГНОР
			[
				['req_status' => RequestModel::STATUS_CAME, ],
				$c['isTransfer'] + $c['callLater'],
				['req_status' => RequestModel::STATUS_CAME, ],
			],
			//isTransfer + appointment + appStatus  ИГНОР
			[
				['req_status' => RequestModel::STATUS_CAME, ],
				$c['isTransfer'] + $c['appointment'] + $c['appStatus'],
				['req_status' => RequestModel::STATUS_CAME, ],
			],
			[
				['req_status' => RequestModel::STATUS_CAME, 'appointment_status' => 1],
				$c['isTransfer'] + $c['appointment'],
				['req_status' => RequestModel::STATUS_CAME, ],
			],
			//isTransfer  ИГНОР
			[
				['req_status' => RequestModel::STATUS_CAME, ],
				$c['isTransfer'],
				['req_status' => RequestModel::STATUS_CAME, ],
			],

			//############################# ОТКАЗ ##########################
			//отказ - в обработке


			//############################# ПЕРЕЗВОНИТЬ ##########################
			//Перезвонить - Принята
			[
				['req_status' => RequestModel::STATUS_CALL_LATER, ],
				$c['!callLater'] + $c['!isTransfer'],
				['req_status' => RequestModel::STATUS_ACCEPT, ],
			],
			//Перезвонить - В обработке
			[
				['req_status' => RequestModel::STATUS_CALL_LATER, ],
				$c['!callLater'] + $c['!appointment'] + $c['isTransfer'],
				['req_status' => RequestModel::STATUS_PROCESS, ],
			],
			//Перезвонить - Обработана
			[
				['req_status' => RequestModel::STATUS_CALL_LATER, 'appointment_status' => 0],
				$c['!callLater'] + $c['appointment'] + $c['isTransfer'],
				['req_status' => RequestModel::STATUS_RECORD, ],
			],
			//Перезвонить - Завершена
			[
				['req_status' => RequestModel::STATUS_CALL_LATER, ],
				$c['!callLater'] + $c['appointment'] + $c['isTransfer'] + $c['appStatus'],
				['req_status' => RequestModel::STATUS_CAME, ],
			],
			//Перезвонить - Отказ
			[
				['req_status' => RequestModel::STATUS_CALL_LATER, ],
				$c['!callLater'] + $c['isReject'],
				['req_status' => RequestModel::STATUS_REJECT, ],
			],
			//Перезвонить - Перезвонить
			[
				['req_status' => RequestModel::STATUS_CALL_LATER, ],
				$c['callLater'],
				['req_status' => RequestModel::STATUS_CALL_LATER, ],
			],
			[
				['req_status' => RequestModel::STATUS_CALL_LATER, ],
				$c['callLater'] + $c['isReject'],
				['req_status' => RequestModel::STATUS_CALL_LATER, ],
			],

			//############################# ПОВТОРНЫЙ ЗВОНОК ##########################
			//Повторный звонок - Принята
			[
				['req_status' => RequestModel::STATUS_RECALL, ],
				$c['!isTransfer'],
				['req_status' => RequestModel::STATUS_ACCEPT, ],
			],
			//Повторный звонок - В обработке
			[
				['req_status' => RequestModel::STATUS_RECALL, ],
				$c['isTransfer'] + $c['!appointment'],
				['req_status' => RequestModel::STATUS_PROCESS, ],
			],
			//Повторный звонок - Обработана
			[
				['req_status' => RequestModel::STATUS_RECALL, 'appointment_status' => 0],
				$c['appointment'] + $c['isTransfer'],
				['req_status' => RequestModel::STATUS_RECORD, ],
			],
			//Повторный звонок - Завершена
			[
				['req_status' => RequestModel::STATUS_RECALL, ],
				$c['appointment'] + $c['isTransfer'] + $c['appStatus'],
				['req_status' => RequestModel::STATUS_CAME, ],
			],
			//Повторный звонок - Отказ
			[
				['req_status' => RequestModel::STATUS_RECALL, ],
				$c['isReject'],
				['req_status' => RequestModel::STATUS_REJECT, ],
			],

			/**
			 * @link https://docdoc.atlassian.net/browse/DD-681
			 */
			//############################# НЕ ПРИШЕЛ ##########################
			//не пришел -> принята
			[
				['req_status' => RequestModel::STATUS_NOT_CAME, ],
				$c['!isTransfer'],
				['req_status' => RequestModel::STATUS_NOT_CAME, ],
			],
			//не пришел -> В обработке
			[
				['req_status' => RequestModel::STATUS_NOT_CAME, ],
				$c['isTransfer'] + $c['!appointment'],
				['req_status' => RequestModel::STATUS_NOT_CAME, ],
			],
			//не пришел -> Обработана
			[
				['req_status' => RequestModel::STATUS_NOT_CAME, 'appointment_status' => 0],
				$c['isTransfer'] + $c['appointment'],
				['req_status' => RequestModel::STATUS_NOT_CAME, ],
			],
			//не пришел -> Перезвонить
			[
				['req_status' => RequestModel::STATUS_NOT_CAME, ],
				$c['callLater'],
				['req_status' => RequestModel::STATUS_NOT_CAME, ],
			],
			//не пришел -> отказ
			[
				['req_status' => RequestModel::STATUS_NOT_CAME, ],
				$c['isReject'],
				['req_status' => RequestModel::STATUS_NOT_CAME, ],
			],
			//ни одно из правил не сработало
			[
				['req_status' => RequestModel::STATUS_NOT_CAME, ],
				[],
				['req_status' => RequestModel::STATUS_NOT_CAME, ],
			],
			//не пришел -> isTransfer + callLater
			[
				['req_status' => RequestModel::STATUS_NOT_CAME, ],
				$c['isTransfer'] + $c['callLater'],
				['req_status' => RequestModel::STATUS_NOT_CAME, ],
			],
			//не пришел -> isTransfer + appointment + appStatus
			[
				['req_status' => RequestModel::STATUS_NOT_CAME, ],
				$c['isTransfer'] + $c['appointment'] + $c['appStatus'],
				['req_status' => RequestModel::STATUS_NOT_CAME, ],
			],
			[
				['req_status' => RequestModel::STATUS_NOT_CAME, 'appointment_status' => 1],
				$c['isTransfer'] + $c['appointment'],
				['req_status' => RequestModel::STATUS_NOT_CAME, ],
			],
			//не пришел -> isTransfer
			[
				['req_status' => RequestModel::STATUS_NOT_CAME, ],
				$c['isTransfer'],
				['req_status' => RequestModel::STATUS_NOT_CAME, ],
			],


			//############################# УСЛОВНО ЗАВЕРШЕНА ##########################
			//условно завершена -> принята
			[
				['req_status' => RequestModel::STATUS_CAME_UNDEFINED, ],
				$c['!isTransfer'],
				['req_status' => RequestModel::STATUS_ACCEPT, ],
			],
			//условно завершена -> В обработке
			[
				['req_status' => RequestModel::STATUS_CAME_UNDEFINED, ],
				$c['isTransfer'] + $c['!appointment'],
				['req_status' => RequestModel::STATUS_PROCESS, ],
			],
			//условно завершена -> Обработана
			[
				['req_status' => RequestModel::STATUS_CAME_UNDEFINED, 'appointment_status' => 0],
				$c['isTransfer'] + $c['appointment'],
				['req_status' => RequestModel::STATUS_RECORD, ],
			],
			//условно завершена -> Перезвонить
			[
				['req_status' => RequestModel::STATUS_CAME_UNDEFINED, ],
				$c['callLater'],
				['req_status' => RequestModel::STATUS_CALL_LATER, ],
			],
			//условно завершена -> отказ
			[
				['req_status' => RequestModel::STATUS_CAME_UNDEFINED, ],
				$c['isReject'],
				['req_status' => RequestModel::STATUS_REJECT, ],
			],
			//ни одно из правил не сработало
			[
				['req_status' => RequestModel::STATUS_CAME_UNDEFINED, ],
				[],
				['req_status' => RequestModel::STATUS_ACCEPT, ],
			],
			//условно завершена -> isTransfer + callLater
			[
				['req_status' => RequestModel::STATUS_CAME_UNDEFINED, ],
				$c['isTransfer'] + $c['callLater'],
				['req_status' => RequestModel::STATUS_CALL_LATER, ],
			],
			//условно завершена -> isTransfer + appointment + appStatus
			[
				['req_status' => RequestModel::STATUS_CAME_UNDEFINED, ],
				$c['isTransfer'] + $c['appointment'] + $c['appStatus'],
				['req_status' => RequestModel::STATUS_CAME, ],
			],
			[
				['req_status' => RequestModel::STATUS_CAME_UNDEFINED, 'appointment_status' => 1],
				$c['isTransfer'] + $c['appointment'],
				['req_status' => RequestModel::STATUS_CAME, ],
			],
			//условно завершена -> isTransfer
			[
				['req_status' => RequestModel::STATUS_CAME_UNDEFINED, ],
				$c['isTransfer'],
				['req_status' => RequestModel::STATUS_PROCESS, ],
			],

		];
	}

	/**
	 * Проставление даты, до которой заявку должны обратать
	 *
	 * @param string|null $scenario
	 * @param array $attributes
	 * @param callable $checkFunction
	 *
	 * @dataProvider setExpireTimeDataProvider
	 */
	public function testSetExpireTime($scenario, array $attributes, callable $checkFunction)
	{
		$this->getFixtureManager()->checkIntegrity(false);

		$this->getFixtureManager()->truncateTable('clinic');
		$this->getFixtureManager()->truncateTable('doctor');
		$this->getFixtureManager()->truncateTable('clinic_contract');
		$this->getFixtureManager()->truncateTable('diagnostica4clinic');
		$this->getFixtureManager()->truncateTable('request');

		$this->getFixtureManager()->loadFixture('clinic');
		$this->getFixtureManager()->loadFixture('doctor');
		$this->getFixtureManager()->loadFixture('clinic_contract');
		$this->getFixtureManager()->loadFixture('diagnostica4clinic');

		if(isset($attributes['req_id'])){
			$request = RequestModel::model()->findByPk($attributes['req_id']);
		} else {
			$request = new RequestModel();
		}

		$scenario && $request->setScenario($scenario);

		foreach($attributes as $k => $v){
			$request->$k = $v;
		}

		$request->save();

		$checkFunction($request);

	}

	/**
	 * @return array
	 */
	public function setExpireTimeDataProvider()
	{
		return [
			[
				RequestModel::SCENARIO_DIAGNOSTIC_ONLINE,
				[
					'client_name' => 'Тест1',
					'client_phone' => '79261234567',
					'diagnostics_id' => 1,
					'clinic_id' => 1,
					'date_admission' => '2236204800',
				],
				function(RequestModel $r){
					$this->assertEquals(0, count($r->getErrors()));
					$this->assertNotNull($r->expire_time);
				}
			],
			[
				null,
				[
					'client_name' => 'Тест2',
					'client_phone' => '79261234567',
					'diagnostics_id' => 1,
					'clinic_id' => 1,
					'date_admission' => '2236204800',
				],
				function(RequestModel $r){
					$this->assertEquals(0, count($r->getErrors()));
					$this->assertNull($r->expire_time);
				}
			],
		];
	}

	/**
	 * Высчитывает время до которого клиники должны обработать заявку
	 *
	 * @throws \CException
	 */
	public function testCalculateExpireTime()
	{
		$this->getFixtureManager()->checkIntegrity(false);

		$this->getFixtureManager()->truncateTable('clinic');
		$this->getFixtureManager()->truncateTable('doctor');
		$this->getFixtureManager()->truncateTable('request');

		$this->getFixtureManager()->loadFixture('clinic');
		$this->getFixtureManager()->loadFixture('doctor');
		$this->getFixtureManager()->loadFixture('request');

		$request = RequestModel::model()->findByPk(10); //clinic_id == 1
		$this->assertNotNull($request);

		$this->assertEquals(date('c', strtotime('2014-12-04 09:20:00')), $request->calculateExpireTime(strtotime('2014-12-04 05:30:30')));
		$this->assertEquals(date('c', strtotime('2014-12-04 09:20:00')), $request->calculateExpireTime(strtotime('2014-12-03 21:00:00')));
		$this->assertEquals(date('c', strtotime('2014-12-08 09:20:00')), $request->calculateExpireTime(strtotime('2014-12-06 21:10:30')));
		$this->assertEquals(date('c', strtotime('2014-12-06 18:20:30')), $request->calculateExpireTime(strtotime('2014-12-06 18:00:30')));
	}

	/**
	 *
	 *
	 * @param $date
	 * @param $count
	 *
	 * @dataProvider needToNotifyByAsteriskDataProvider
	 */
	public function testNeedToNotifyByAsterisk($date, $count)
	{
		$this->getFixtureManager()->checkIntegrity(false);
		$this->getFixtureManager()->truncateTable('request');
		$this->getFixtureManager()->loadFixture('request');

		$requests = RequestModel::model()->needToNotifyByAsterisk($date, 300)->findAll();
		$this->assertEquals($count, count($requests));
	}

	/**
	 * @return array
	 */
	public function needToNotifyByAsteriskDataProvider()
	{
		return [
			['2014-01-01 13:00', 0],
			['2014-01-01 13:50', 1],
			['2014-01-01 13:55', 2],
			['2014-01-01 14:00', 1],
			['2014-01-01 14:05', 0],
		];
	}

	/**
	 * Фикс бага с расчетом партнерского биллинга
	 *
	 */
	public function testFixPartnerBilling()
	{
		$this->loadFixtures();
		$this->getFixtureManager()->truncateTable('partner');
		$this->getFixtureManager()->truncateTable('partner_cost');
		$this->getFixtureManager()->truncateTable('clinic_contract_cost');
		$this->getFixtureManager()->truncateTable('clinic_contract');
		$this->getFixtureManager()->truncateTable('contract_dict');
		$this->getFixtureManager()->loadFixture('partner');
		$this->getFixtureManager()->loadFixture('partner_cost');
		$this->getFixtureManager()->loadFixture('clinic_contract_cost');
		$this->getFixtureManager()->loadFixture('clinic_contract');
		$this->getFixtureManager()->loadFixture('contract_dict');


		$request = new RequestModel();
		$attr = [
			'city_id' => '1',
			'client_name' => 'Хадикова Нина Яновна',
			'client_phone' => '74996382921',
			'req_created' => '1426847324',
			'date_admission' => 1429088400,
			'req_status' => 6,
			'req_sector_id' => 81,
			'source_type' => 3,
			'diagnostics_id' => 56,
			'kind' => 0,
			'req_type' => 2,
			'clinic_id' => 2,
			'partner_id' => 1,
			'partner_status' => 0,
		];

		$request->setAttributes($attr, false);
		//в баге устанавливался, но не сохранялся partner_status
		$request->save();
		$this->assertEquals(RequestModel::PARTNER_STATUS_ACCEPT, $request->partner_status);

		$request = RequestModel::model()->findByPk($request->req_id);
		$this->assertEquals(RequestModel::PARTNER_STATUS_ACCEPT, $request->partner_status);
	}
}
