<?php

namespace dfs\tests\docdoc\asterisk;

use CDbTestCase;
use dfs\docdoc\objects\Phone;
use Exception;
use PHPUnit_Framework_Error_Warning;
use dfs\docdoc\asterisk\AsteriskApi;
use dfs\docdoc\models\RequestModel;

class AsteriskApiTest extends CDbTestCase
{
	/**
	 * Загружались фикстуры или нет
	 *
	 * @var bool
	 */
	public static $asteriskTestRun;

	/**
	 * Выполнять при запуске каждого теста
	 *
	 * @return void
	 */
	protected function setUp()
	{
		if (!self::$asteriskTestRun) {
			$this->getFixtureManager()->checkIntegrity(false);
			$this->getFixtureManager()->truncateTable('request');
			$this->getFixtureManager()->truncateTable('partner');
			$this->getFixtureManager()->truncateTable('phone');
			$this->getFixtureManager()->truncateTable('city');
			$this->getFixtureManager()->truncateTable('partner_phones');

			$this->getFixtureManager()->loadFixture('partner');
			$this->getFixtureManager()->loadFixture('partner_phones');
			$this->getFixtureManager()->loadFixture('phone');
			$this->getFixtureManager()->loadFixture('city');

			self::$asteriskTestRun = true;
		}

		parent::setUp();
	}

	/**
	 * Тестируем метод создания заявки
	 *
	 * @dataProvider asteriskCreateRequestProvider
	 *
	 * @param array  $params       Параметры запроса
	 * @param string $errorMessage Сообщение об ошибке
	 * @return bool
	 */
	public function testCreateRequest(array $params, $errorMessage = '')
	{
		global $confPhonesForNoMergedRequests;
		$confPhonesForNoMergedRequests = array('74991111111');

		//ставим ранее созданной заявке с тем же телефоном дату создания 2 недели назад
		if ($params['filename'] === 'тот же номер > 2 нед') {
			$request = RequestModel::model()
				->findByAttributes(
					array(
						'client_phone' => $params['phone']
					)
				);

			$request->req_created = $request->req_created - RequestModel::DIFF_TIME_FOR_MERGED_REQUEST - 1;
			$request->save();
		}

		try {
			$api        = new AsteriskApi();
			$request_id = $api->createRequest($params);
			$this->assertEmpty($errorMessage);
		} catch (PHPUnit_Framework_Error_Warning $e) {
			// Кейс с неправильным телефоном назначения
			// На самом деле ничего не проверяет
			return true;
		}catch (Exception $e) {
			$this->assertEquals($errorMessage, $e->getMessage());
			return true;
		}

		$request = RequestModel::model()->resetScope()->findByPk($request_id);

		//проверяем, что повторный звонок не создал повторную заявку
		if ($params['filename'] === 'тот же номер') {
			$this->assertEquals($request->last_call_id, '1399278272.3784');
			$this->assertEquals(1, $request->is_hot);
		} //а повторный звонок > чем через 2 недели создал новую заявку
		elseif ($params['filename'] === 'тот же номер > 2 нед') {
			$this->assertEquals($request->last_call_id, 'тот же номер > 2 нед');
		} // проверяем, что работает исключения для телефонов
		elseif ($params['filename'] === 'exceptPhone') {
			$count = \Yii::app()->db
				->createCommand("SELECT COUNT(*) FROM request WHERE client_phone='" . $params['phone'] . "'")
				->queryScalar();
			$this->assertEquals(2, $count);
		} elseif (!empty($params['clinicId'])) {
			// проверяем, что проставляется клиника
			$this->assertNotEmpty($request->clinic_id);
		} elseif ($params['filename'] === 'partner') {
			// проверяем, что проставляется партнер
			$this->assertNotNull($request->partner);
		} elseif ($params['filename'] === 'incorrect') {
			// некорректно определившийся номер телефона
			$this->assertEquals(1, $request->client_phone);
		} else {
			$this->assertEquals($request->req_type, RequestModel::TYPE_CALL);
			$this->assertEquals($request->req_status, RequestModel::STATUS_NEW);

			if ($params['city'] === '') {
				$this->assertEquals($request->city->rewrite_name, 'default');
			} else {
				$this->assertEquals($request->city->rewrite_name, mb_strtolower($params['city']));
			}

			$p = new Phone($params['phone']);

			$this->assertEquals($request->last_call_id, $params['filename']);
			$this->assertEquals($request->client_phone, $p->getNumber());
			$this->assertNotNull($request->phone);
		}

		//ставим, что с этой заявкой оператор отработал
		$request->setScenario('update');
		$request->is_hot = 0;
		$request->save();

		return true;
	}

	/**
	 * Запросы на создание заявки
	 *
	 * @return array
	 */
	public function asteriskCreateRequestProvider()
	{
		return array(
			array(
				// phone=79522373930&filename=1399278272.3784&city=SPB&destination_phone=78123856652&channel=SIP/uiscommsc-00000c9b
				array(
					'filename'         => '1399278272.3784',
					'phone'            => '79522373930',
					'city'             => 'SPB',
					'channel'          => 'SIP/uiscommsc-00000c9b',
					'destinationPhone' => '78123856652',
					'requestId'        => null,
					'recordType'       => null,
					'sip'              => null,
					'queue'            => null,
				)
			),
			array(
				array(
					//phone=74991289294&filename=1399278544.3938&city=MSK&destination_phone=74952367276&channel=SIP/uiscommsc-00000d2c
					'filename'         => '1399278544.3938',
					'phone'            => '74991289294',
					'city'             => 'MSK',
					'channel'          => 'SIP/uiscommsc-00000d2c',
					'destinationPhone' => '74952367276',
					'requestId'        => null,
					'recordType'       => null,
					'sip'              => null,
					'queue'            => null,
				)
			),
			//повторная заявка с того же телефона
			array(
				array(
					'filename'         => 'тот же номер',
					'phone'            => '79522373930',
					'city'             => 'SPB',
					'channel'          => 'SIP/uiscommsc-00000c9b',
					'destinationPhone' => '78123856652',
					'requestId'        => null,
					'recordType'       => null,
					'sip'              => null,
					'queue'            => null,
				)
			),

			//повторная заявка с того же телефона, без ведущей семёрки
			array(
				array(
					'filename'         => 'тот же номер',
					'phone'            => '9522373930',
					'city'             => 'SPB',
					'channel'          => 'SIP/uiscommsc-00000c9b',
					'destinationPhone' => '78123856652',
					'requestId'        => null,
					'recordType'       => null,
					'sip'              => null,
					'queue'            => null,
				)
			),

			//повторная заявка с того же телефона
			array(
				array(
					'filename'         => 'тот же номер > 2 нед',
					'phone'            => '79522373930',
					'city'             => 'SPB',
					'channel'          => 'SIP/uiscommsc-00000c9b',
					'destinationPhone' => '78123856652',
					'requestId'        => null,
					'recordType'       => null,
					'sip'              => null,
					'queue'            => null,
				)
			),
			array(
				array(
					//телефон-исключение
					'filename'         => '1399278545',
					'phone'            => '74991111111',
					'city'             => 'MSK',
					'channel'          => 'SIP/uiscommsc-00000d2c',
					'destinationPhone' => '74952367276',
					'requestId'        => null,
					'recordType'       => null,
					'sip'              => null,
					'queue'            => null,
				)
			),
			array(
				array(
					//телефон-исключение
					'filename'         => 'exceptPhone',
					'phone'            => '74991111111',
					'city'             => 'MSK',
					'channel'          => 'SIP/uiscommsc-00000d2c',
					'destinationPhone' => '74952367276',
					'requestId'        => null,
					'recordType'       => null,
					'sip'              => null,
					'queue'            => null,
				)
			),
			array(
				array(
					//проверка clinic_id
					'filename'         => '1399278272.3784',
					'phone'            => '79522373930',
					'city'             => 'SPB',
					'channel'          => 'SIP/uiscommsc-00000c9b',
					'destinationPhone' => '78123856652',
					'clinicId'         => 1,
					'recordType'       => null,
					'sip'              => null,
					'queue'            => null,
				)
			),
			array(
				array(
					//партнерский телефон
					'filename'         => 'partner',
					'phone'            => '78123800000',
					'city'             => 'SPB',
					'channel'          => 'SIP/uiscommsc-00000c9b',
					'destinationPhone' => '74951234567',
					'requestId'        => null,
					'recordType'       => null,
					'sip'              => null,
					'queue'            => null,
				)
			),
			array(
				array(
					//некорректно определившийся номер телефона
					'filename'         => 'incorrect',
					'phone'            => '1 некорректно определившийся номер телефона',
					'city'             => 'SPB',
					'channel'          => 'SIP/uiscommsc-00000c9b',
					'destinationPhone' => '78123856652',
					'requestId'        => null,
					'recordType'       => null,
					'sip'              => null,
					'queue'            => null,
				)
			),
			array(
				array(
					//не определился город, значит 8800??
					'filename'         => '8800',
					'phone'            => '79526473930',
					'city'             => '',
					'channel'          => 'SIP/uiscommsc-00000c9b',
					'destinationPhone' => '78123626652',
					'requestId'        => null,
					'recordType'       => null,
					'sip'              => null,
					'queue'            => null,
				)
			),

			array(
				array(
					// не определился
					'filename'         => '1399278544.3938',
					'phone'            => '',
					'city'             => 'MSK',
					'channel'          => 'SIP/uiscommsc-00000d2c',
					'destinationPhone' => '74952367276',
					'requestId'        => null,
					'recordType'       => null,
					'sip'              => null,
					'queue'            => null,
				)
			),

			array(
				array(
					// телефон определился не правильно
					'filename'         => '1399278544.3938',
					'phone'            => 'abc',
					'city'             => 'MSK',
					'channel'          => 'SIP/uiscommsc-00000d2c',
					'destinationPhone' => '74952367276',
					'requestId'        => null,
					'recordType'       => null,
					'sip'              => null,
					'queue'            => null,
				),
				'Failed to create request'
			),

			array(
				array(
					// Телефоны без ведущей семёрки
					'filename'         => '1399278544.3938',
					'phone'            => '74991289294',
					'city'             => 'MSK',
					'channel'          => 'SIP/uiscommsc-00000d2c',
					'destinationPhone' => '4952367276',
					'requestId'        => null,
					'recordType'       => null,
					'sip'              => null,
					'queue'            => null,
				)
			),

			array(
				array(
					// Битый destinationPhone не должен мешать создавать заявку
					'filename'         => '1399278544.3938',
					'phone'            => '74991289294',
					'city'             => 'MSK',
					'channel'          => 'SIP/uiscommsc-00000d2c',
					'destinationPhone' => 'abc',
					'requestId'        => null,
					'recordType'       => null,
					'sip'              => null,
					'queue'            => null,
				)
			),
		);

	}

	/**
	 * Добавляем запись к заявке
	 */
	public function testAddRecord()
	{
		$api = new AsteriskApi();
		$requestId = $api->createRequest($this->asteriskCreateRequestProvider()[0][0]);

		$request = RequestModel::model()->findByPk($requestId);
		$request->req_status = RequestModel::STATUS_ACCEPT;
		$request->save();

		$params = [
			'filename'   => '1408000478.25527',
			'recordType' => 'OUT',
			'requestId'  => $requestId,
		];

		$this->assertEquals(1, $api->addRecord($params));

		// Проверяем что стутус не переходит из статуса принята -> в обработке
		$request = RequestModel::model()->findByPk($requestId);
		$this->assertEquals(RequestModel::STATUS_ACCEPT, $request->req_status);
	}

	/**
	 * Добавляем запись к заявке
	 *
	 * @expectedException Exception
	 * @expectedExceptionMessage Incorrect requestId or filename
	 */
	public function testAddRecordWrongRequestId()
	{
		$api = new AsteriskApi();

		$params = [
			'filename'   => '1408000478.25527',
			'recordType' => 'OUT',
			'requestId'  => '',
		];

		$api->addRecord($params);
	}

	/**
	 * Тестируем метод добавления канала
	 */
	public function testAddChanel()
	{
		$params = [
			'sip'     => '118',
			'channel' => 'SIP/118-00005beb',
			'requestId'  => '1',
		];

		$api = new AsteriskApi();
		$this->assertEquals(1, $api->addChannel($params));
	}

	/**
	 * Тестируем неудачные попытки добавления канала
	 *
	 * @dataProvider addChannelsData
	 * @param array  $params  Параметры запроса
	 * @param string $message Сообщение об ошибке
	 */
	public function testAddChanelFails(array $params, $message)
	{
		$api = new AsteriskApi();
		try {
			$api->addChannel($params);
			$this->fail("Должна произойти ошибка в добавлении канала");
		} catch (Exception $e) {
			$this->assertEquals($message, $e->getMessage());
		}
	}

	/**
	 * @return array
	 */
	public function addChannelsData()
	{
		return [
			[
				[
					'sip'     => 'a1',
					'channel' => 'SIP/118-00005beb',
					'requestId'  => '1',
				],
				'Failed to add new channel'
			],
			[
				[
					'sip'     => '',
					'channel' => 'SIP/118-00005beb',
					'requestId'  => '1',
				],
				'Incorrect sip or channel'
			],
			[
				[
					'sip'     => '118',
					'channel' => '',
					'requestId'  => '1',
				],
				'Incorrect sip or channel'
			],
		];
	}

	/**
	 * Тестирование 5 кейсов:
	 *
	 * Если звонок распознается как повторный:
	 * исходная заявка не партнерская, звонок не с номера патрнера — заявка склеивается с исходной
	 * исходная заявка не партнерская, звонок с номера патрнера — создается новая заявка, она прикрепляется к партнеру
	 * исходная заявка партнерская, звонок не с номера патрнера — заявка создается без партнера
	 * исходная заявка партнерская, звонок с номера того же партнера — заявка склеивается с исходной партнерской
	 * исходная заявка партнерская, звонок с номера другого патрнера — создается новая заявка, она прикрепляется к новому партнеру
	 *
	 * @dataProvider createPartnerRequestDataProvider
	 */
	public function testCreatePartnerRequest($exitsParams, $creatingParams, \Closure $checkFunction)
	{
		$this->getFixtureManager()->truncateTable('partner');
		$this->getFixtureManager()->truncateTable('phone');
		$this->getFixtureManager()->loadFixture('partner');
		$this->getFixtureManager()->loadFixture('phone');

		$this->getFixtureManager()->truncateTable('request');

		$api = new AsteriskApi();

		$checkFunction($api, $exitsParams, $creatingParams);
	}

	public function createPartnerRequestDataProvider()
	{
		return [
			//исходная заявка не партнерская, звонок не с номера патрнера — заявка склеивается с исходной
			[
				[
					'filename'         => 'тут могла быть ваша реклама',
					'phone'            => '79522373930',
					'city'             => 'SPB',
					'channel'          => 'SIP/uiscommsc-00000c9b',
					'destinationPhone' => '78123856652',
					'requestId'        => null,
					'recordType'       => null,
					'sip'              => null,
					'queue'            => null,
				],
				[
					'filename'         => 'тут могла быть ваша реклама',
					'phone'            => '79522373930',
					'city'             => 'SPB',
					'channel'          => 'SIP/uiscommsc-00000c9b',
					'destinationPhone' => '78123856652',
					'requestId'        => null,
					'recordType'       => null,
					'sip'              => null,
					'queue'            => null,
				],
				function (AsteriskApi $api, $exitsParams, $creatingParams) {
					$ex_id = $api->createRequest($exitsParams);
					$cr_id = $api->createRequest($creatingParams);

					$this->assertNotNull($ex_id);
					$this->assertNotNull($cr_id);
					$this->assertEquals($ex_id, $cr_id);
				}
			],
			//исходная заявка не партнерская, звонок с номера патрнера — создается новая заявка, она прикрепляется к партнеру
			[
				[
					'filename'         => 'тут могла быть ваша реклама',
					'phone'            => '79522373930',
					'city'             => 'SPB',
					'channel'          => 'SIP/uiscommsc-00000c9b',
					'destinationPhone' => '78123856652',
					'requestId'        => null,
					'recordType'       => null,
					'sip'              => null,
					'queue'            => null,
				],
				[
					'filename'         => 'тут могла быть ваша реклама',
					'phone'            => '79522373930',
					'city'             => 'SPB',
					'channel'          => 'SIP/uiscommsc-00000c9b',
					'destinationPhone' => '74991634567',
					'requestId'        => null,
					'recordType'       => null,
					'sip'              => null,
					'queue'            => null,
				],
				function (AsteriskApi $api, $exitsParams, $creatingParams) {
					$ex_id = $api->createRequest($exitsParams);
					$cr_id = $api->createRequest($creatingParams);

					$this->assertNotNull($ex_id);
					$this->assertNotNull($cr_id);
					$this->assertNotEquals($ex_id, $cr_id);

					$req1 = RequestModel::model()->findByPk($ex_id);
					$this->assertFalse((bool)$req1->partner_id); //ебаные нули

					$req2 = RequestModel::model()->findByPk($cr_id);
					$this->assertTrue((bool)$req2->partner_id);
				}
			],
			//исходная заявка партнерская, звонок не с номера патрнера — заявка создается без партнера
			[
				[
					'filename'         => 'тут могла быть ваша реклама',
					'phone'            => '79522373930',
					'city'             => 'SPB',
					'channel'          => 'SIP/uiscommsc-00000c9b',
					'destinationPhone' => '74991634567',
					'requestId'        => null,
					'recordType'       => null,
					'sip'              => null,
					'queue'            => null,
				],
				[
					'filename'         => 'тут могла быть ваша реклама',
					'phone'            => '79522373930',
					'city'             => 'SPB',
					'channel'          => 'SIP/uiscommsc-00000c9b',
					'destinationPhone' => '78123856652',
					'requestId'        => null,
					'recordType'       => null,
					'sip'              => null,
					'queue'            => null,
				],
				function (AsteriskApi $api, $exitsParams, $creatingParams) {
					$ex_id = $api->createRequest($exitsParams);
					$cr_id = $api->createRequest($creatingParams);

					$this->assertNotNull($ex_id);
					$this->assertNotNull($cr_id);
					$this->assertNotEquals($ex_id, $cr_id);

					$req1 = RequestModel::model()->findByPk($ex_id);
					$this->assertTrue((bool)$req1->partner_id);

					$req2 = RequestModel::model()->findByPk($cr_id);
					$this->assertFalse((bool)$req2->partner_id);
				}
			],
			//исходная заявка партнерская, звонок с номера того же партнера — заявка склеивается с исходной партнерской
			[
				[
					'filename'         => 'тут могла быть ваша реклама',
					'phone'            => '79522373930',
					'city'             => 'SPB',
					'channel'          => 'SIP/uiscommsc-00000c9b',
					'destinationPhone' => '74991634567',
					'requestId'        => null,
					'recordType'       => null,
					'sip'              => null,
					'queue'            => null,
				],
				[
					'filename'         => 'тут могла быть ваша реклама',
					'phone'            => '79522373930',
					'city'             => 'SPB',
					'channel'          => 'SIP/uiscommsc-00000c9b',
					'destinationPhone' => '74991634567',
					'requestId'        => null,
					'recordType'       => null,
					'sip'              => null,
					'queue'            => null,
				],
				function (AsteriskApi $api, $exitsParams, $creatingParams) {
					$ex_id = $api->createRequest($exitsParams);
					$cr_id = $api->createRequest($creatingParams);

					$this->assertNotNull($ex_id);
					$this->assertNotNull($cr_id);
					$this->assertEquals($ex_id, $cr_id);

					$req = RequestModel::model()->findByPk($ex_id);
					$this->assertTrue((bool)$req->partner_id);
				}
			],
			//исходная заявка партнерская, звонок с номера другого патрнера — создается новая заявка, она прикрепляется к новому партнеру
			[
				[
					'filename'         => 'тут могла быть ваша реклама',
					'phone'            => '79522373930',
					'city'             => 'SPB',
					'channel'          => 'SIP/uiscommsc-00000c9b',
					'destinationPhone' => '74991634567',
					'requestId'        => null,
					'recordType'       => null,
					'sip'              => null,
					'queue'            => null,
				],
				[
					'filename'         => 'тут могла быть ваша реклама',
					'phone'            => '79522373930',
					'city'             => 'SPB',
					'channel'          => 'SIP/uiscommsc-00000c9b',
					'destinationPhone' => '74951234567',
					'requestId'        => null,
					'recordType'       => null,
					'sip'              => null,
					'queue'            => null,
				],
				function (AsteriskApi $api, $exitsParams, $creatingParams) {
					$ex_id = $api->createRequest($exitsParams);
					$cr_id = $api->createRequest($creatingParams);

					$this->assertNotNull($ex_id);
					$this->assertNotNull($cr_id);
					$this->assertNotEquals($ex_id, $cr_id);

					$req1 = RequestModel::model()->findByPk($ex_id);
					$this->assertTrue((bool)$req1->partner_id);

					$req2 = RequestModel::model()->findByPk($cr_id);
					$this->assertTrue((bool)$req2->partner_id);
				}
			],
			//исходная заявка партнерская, звонок с номера того же партнера
			//у партнера стоит, что заявки не склеивать  — заявка не склеивается с исходной партнерской
			//todo
			[
				[
					'filename'         => 'тут могла быть ваша реклама',
					'phone'            => '79522373930',
					'city'             => 'SPB',
					'channel'          => 'SIP/uiscommsc-00000c9b',
					'destinationPhone' => '74999876543',
					'requestId'        => null,
					'recordType'       => null,
					'sip'              => null,
					'queue'            => null,
				],
				[
					'filename'         => 'тут могла быть ваша реклама',
					'phone'            => '79522373930',
					'city'             => 'SPB',
					'channel'          => 'SIP/uiscommsc-00000c9b',
					'destinationPhone' => '74999876543',
					'requestId'        => null,
					'recordType'       => null,
					'sip'              => null,
					'queue'            => null,
				],
				function (AsteriskApi $api, $exitsParams, $creatingParams) {
					$ex_id = $api->createRequest($exitsParams);
					$cr_id = $api->createRequest($creatingParams);

					$this->assertNotNull($ex_id);
					$this->assertNotNull($cr_id);
					$this->assertNotEquals($ex_id, $cr_id);

					$req = RequestModel::model()->findByPk($ex_id);
					$this->assertTrue((bool)$req->partner_id);
				}
			],
		];
	}
}
