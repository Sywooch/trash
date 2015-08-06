<?php

namespace dfs\tests\docdoc\models;

use dfs\docdoc\models\RequestModel;
use dfs\docdoc\models\SmsQueryModel;
use dfs\docdoc\models\SmsRequestModel;
use CDbTestCase;
use dfs\docdoc\objects\Rejection;
use Yii;

/**
 * Class SmsQueryModelTest
 *
 * @package dfs\tests\docdoc\models
 */
class SmsRequestModelTest extends CDbTestCase
{

	/**
	 * Загрузка данных
	 *
	 * @throws \CException
	 */
	public function setUp()
	{
		$this->getFixtureManager()->checkIntegrity(false);
		$this->getFixtureManager()->truncateTable('request');
		$this->getFixtureManager()->truncateTable('SMSQuery');
		$this->getFixtureManager()->truncateTable('sms_4_request');
		$this->getFixtureManager()->loadFixture('request');
	}
	
	/**
	 * Проверка сообщения
	 *
	 * @param string $method
	 * @param array $params
	 * @param string $expected
	 * @dataProvider dataProvideMessage
	 */
	public function testAssertMessage($method, $params, $expected)
	{
		$this->getFixtureManager()->truncateTable('request');
		$this->getFixtureManager()->truncateTable('diagnostica4clinic');
		$this->getFixtureManager()->loadFixture('request');
		$this->getFixtureManager()->loadFixture('diagnostica4clinic');
		if (empty($params['req_id'])) {
			$request = new RequestModel();
			unset($params['req_id']);
		} else {
			$request = RequestModel::model()->findByPk($params['req_id']);
		}

		$request = $this->setDefaultAttr($request);
		foreach ($params as $key => $param) {
			$request->$key = $param;
		}

		$smsRequest = new SmsRequestModel();
		$message = null;
		if (call_user_func(array($smsRequest, $method), (new \CEvent($request)))) {
			$sms = SmsQueryModel::model()->findByPk($smsRequest->smsQueryMessageId);
			$message = $sms->message;
		}

		$this->assertEquals($expected, $message);
	}

	/**
	 * Данные для теста отправки уведомлений
	 */
	public function dataProvideMessage()
	{
		return array(
			// сообщение после создания заявки в рабочее время
			array(
				'requestStatusChanged',
				array(
					'req_id'        => 0,
					'req_status'    => 0,
					'req_created'   => 1401955200,
					'client_name'   => 'test',
					'client_phone'  => 79261234567,
					'scenario'      => RequestModel::SCENARIO_SITE,
				),
				"Ваша заявка о записи на прием к врачу принята. Наши консультанты свяжутся с вами в течение 15 минут с 8:00 до 21:00 и запишут Вас на прием. Ваш DocDoc.ru"
			),
			// сообщение после создания заявки в нерабочее время
			array(
				'requestStatusChanged',
				array(
					'req_id'        => 0,
					'req_status'    => 0,
					'req_created'   => 1407865000,
					'client_name'   => 'test',
					'client_phone'  => 79261234567,
					'scenario'      => RequestModel::SCENARIO_SITE,
				),
				"Ваша заявка на подбор врача принята. Сервис работает с 8:00 до 21:00 Пн-Вс. Наш администратор свяжется с Вами до 09:00. Ваш DocDoc.ru"
			),
			// сообщение после создания заявки по диагностике в рабочее время
			array(
				'requestStatusChanged',
				array(
					'req_id'        => 0,
					'kind'          => 1,
					'req_status'    => 0,
					'req_created'   => 1401955200,
					'client_name'   => 'test',
					'client_phone'  => 79261234567,
					'date_admission'  => 1407865000,
					'scenario'      => RequestModel::SCENARIO_SITE,
				),
				'Вы записались в клинику "Клиника №1" на 12.08.2014 21:36. Оператор свяжется с Вами для подтверждения даты и времени посещения.'
			),
			// сообщение после создания заявки по диагностике онлайн
			array(
				'requestStatusChanged',
				array(
					'req_id'        => 0,
					'kind'          => 2,
					'req_status'    => 0,
					'req_created'   => 1401955200,
					'client_name'   => 'test',
					'client_phone'  => 79261234567,
					'date_admission'  => 1407865000,
					'diagnostics_id' => 21,
					'clinic_id' => 1,
					'req_type' => RequestModel::TYPE_ONLINE_RECORD,
					'scenario' => RequestModel::SCENARIO_DIAGNOSTIC_ONLINE,
				),
				'Вы записаны на МРТ (магнитно-резонансная томография) в клинику "Клиника №1" на 12.08.2014 21:36. Заявка №. Оператор свяжется с Вами для подтверждения даты и времени посещения.'
			),
			// сообщение после создания заявки по диагностике онлайн, если есть скидка
			array(
				'requestStatusChanged',
				array(
					'req_id'        => 0,
					'kind'          => 2,
					'req_status'    => 0,
					'req_created'   => 1401955200,
					'client_name'   => 'test',
					'client_phone'  => 79261234567,
					'date_admission'  => 1407865000,
					'diagnostics_id' => 71,
					'clinic_id' => 1,
					'req_type' => RequestModel::TYPE_ONLINE_RECORD,
					'scenario' => RequestModel::SCENARIO_DIAGNOSTIC_ONLINE,
				),
				'Вы записаны на УЗИ печени в клинику "Клиника №1" на 12.08.2014 21:36. Заявка №. Оператор свяжется с Вами для подтверждения даты и времени посещения. Дополнительная скидка на услугу составит 12%.'
			),
			// сообщение после создания заявки по диагностике онлайн, если есть скидка
			array(
				'requestStatusChanged',
				array(
					'req_id'        => 0,
					'kind'          => 2,
					'req_status'    => 0,
					'req_created'   => 1401955200,
					'client_name'   => 'test',
					'client_phone'  => 79261234567,
					'date_admission'  => 1407865000,
					'diagnostics_id' => 71,
					'clinic_id' => 1,
					'scenario' => RequestModel::SCENARIO_SITE,
				),
				'Вы записаны на услугу УЗИ печени в клинику "Клиника №1" на 12.08.2014 21:36. Оператор свяжется с Вами для подтверждения даты и времени посещения.'
			),
			// сообщение не отправлено - невалидный номер телефона
			array(
				'requestStatusChanged',
				array(
					'req_id'        => 6,
					'req_status'    => 0,
					'req_created'   => 1401955200,
					'client_phone'  => 74951234567
				),
				null
			),
			// сообщение после имзенения времени приема
			array(
				'requestDateAdmissionChanged',
				array(
					'req_id'           => 6,
					'req_status'       => 2,
					'date_admission'   => time() + 3600,
					'req_type'         => RequestModel::TYPE_CALL_TO_DOCTOR,
				),
				"Вы изменили время записи на " . \Yii::app()->dateFormatter->format("dd MMMM HH:mm", time() + 3600) . ". Врач: Грук Светлана Михайловна. Адрес клиники: Краснодарская улица, д. 52, корп. 2. Ваш DocDoc.ru"
			),
			// сообщение после имзенения времени приема
			array(
				'requestDateAdmissionChanged',
				array(
					'req_id'           => 6,
					'kind'             => 1,
					'req_status'       => 2,
					'date_admission'   => time() + 3600,
				),
				"Вы изменили время записи на " . \Yii::app()->dateFormatter->format("dd MMMM HH:mm", time() + 3600) . ". Диагностика: УЗИ (ультразвуковое исследование). Адрес клиники: Краснодарская улица, д. 52, корп. 2. Ваш DocDoc.ru"
			),

			// сообщение после имзенения времени приема. Лишняя точка в конце адреса убивается
			[
				'requestDateAdmissionChanged',
				[
					'req_id'         => 12,
					'kind'           => 1,
					'req_status'     => 2,
					'clinic_id'      => 12,
					'date_admission' => time() + 3600,
				],
				"Вы изменили время записи на " .
				\Yii::app()->dateFormatter->format("dd MMMM HH:mm", time() + 3600) .
				". Диагностика: УЗИ (ультразвуковое исследование). Адрес клиники: Ленина, д. 1. Ваш DocDoc.ru"
			],

			// сообщение не отправится, потому что время приема в прошлом
			array(
				'requestDateAdmissionChanged',
				array(
					'req_id'           => 6,
					'kind'             => 1,
					'req_status'       => 2,
					'date_admission'   => time(),
				),
				null,
			),
			// сообщение о недозвоне
			array(
				'requestRejectReasonChanged',
				array(
					'req_id'        => 6,
					'reject_reason' => Rejection::REASON_CLIENT_NOT_ANSWER,
				),
				"Вы оставляли заявку на подбор врача на портале DocDoc.ru. К сожалению, мы не смогли с Вами связаться. Перезвоните нам по тел. +74952367276 для записи к специалисту. Ваш DocDoc.ru",
			),
			// сообщение о недозвоне
			array(
				'requestRejectReasonChanged',
				array(
					'req_id'        => 6,
					'kind'          => 1,
					'reject_reason' => Rejection::REASON_CLIENT_NOT_ANSWER,
				),
				"Вы оставляли заявку о записи в клинику на портале DocDoc.ru. К сожалению, мы не смогли с Вами связаться. Перезвоните нам по тел. +74952367276 для записи к специалисту. Ваш DocDoc.ru",
			),

			// сообщение о недозвоне с телефоном формата 8800 в сообщении
			[
				'requestRejectReasonChanged',
				[
					'req_id'        => 12,
					'reject_reason' => Rejection::REASON_CLIENT_NOT_ANSWER,
				],
				"Вы оставляли заявку на подбор врача на портале DocDoc.ru. К сожалению, мы не смогли с Вами связаться. Перезвоните нам по тел. 88001112233 для записи к специалисту. Ваш DocDoc.ru",
			],

			// сообщение с контактами клиники для новых городов
			array(
				'requestRejectReasonChanged',
				array(
					'req_id'        => 6,
					'id_city'       => 4,
					'clinic_id'     => 11,
					'reject_reason' => Rejection::REASON_HAVE_CONTACTS,
					'req_type'      => RequestModel::TYPE_CALL_TO_DOCTOR,
				),
				"Благодарим Вас за использование сервиса DocDoc.ru. На данный момент услуга в Вашем городе предоставляется в тестовом режиме. Вы можете обратиться напрямую в клинику по телефону +74956410645. Ваш DocDoc.ru",
			),

			// сообщение с контактами клиники для новых городов с несколькими телефонами
			[
				'requestRejectReasonChanged',
				[
					'req_id'        => 6,
					'id_city'       => 4,
					'clinic_id'     => 12,
					'reject_reason' => Rejection::REASON_HAVE_CONTACTS,
					'req_type'      => RequestModel::TYPE_CALL_TO_DOCTOR,
				],
				"Благодарим Вас за использование сервиса DocDoc.ru. На данный момент услуга в Вашем городе предоставляется в тестовом режиме. Вы можете обратиться напрямую в клинику по телефону +75551234567. Ваш DocDoc.ru",
			],

			// проверяем, что не отправляется смс по заявкам с юискома
			array(
				'requestStatusChanged',
				array(
					'req_id'        => 0,
					'req_status'    => 0,
					'req_created'   => 1401955200,
					'client_name'   => 'test',
					'client_phone'  => 79261234567,
					'req_type'      => RequestModel::TYPE_CALL_TO_DOCTOR,
				),
				null
			),
		);
	}

	/**
	 * Проверка отправки напоминия о приеме
	 */
	public function testRequestReminder()
	{
		$smsRequest = new SmsRequestModel();

		$request = RequestModel::model()->findByPk(6);
		$request = $this->setDefaultAttr($request);

		$this->assertEquals(true, $smsRequest->requestReminder($request));
		$sms = SmsQueryModel::model()->findByPk($smsRequest->smsQueryMessageId);
		$expected =
			"Напоминаем, что Вы записаны на приём к врачу на 05 июня 11:00. Врач: Грук Светлана Михайловна. Адрес клиники: Краснодарская улица, д. 52, корп. 2. При необходимости перенести приём звоните +74952367276. Ваш DocDoc.ru";
		$this->assertEquals($expected, $sms->message);

		$request->diagnostics_id = 1;
		$request->kind = RequestModel::KIND_DIAGNOSTICS;
		$request->req_doctor_id = null;
		$this->assertEquals(true, $smsRequest->requestReminder($request));
		$sms = SmsQueryModel::model()->findByPk($smsRequest->smsQueryMessageId);
		$expected =
			"Напоминаем, что Вы записаны на диагностику на 05 июня 11:00. Диагностика: УЗИ (ультразвуковое исследование). Адрес клиники: Краснодарская улица, д. 52, корп. 2. При необходимости перенести приём звоните +74956410606. Ваш DocDoc.ru";
		$this->assertEquals($expected, $sms->message);
	}

	/**
	 * Проверка отправки сообщения о недозвоне при сборе отзывов
	 */
	public function testOpinionClientNotAvailableMessage()
	{
		$smsRequest = new SmsRequestModel();

		$request = RequestModel::model()->findByPk(6);
		$request = $this->setDefaultAttr($request);
		$request->req_status = RequestModel::STATUS_RECORD;
		$request->save();

		$this->assertEquals(true, $smsRequest->opinionClientNotAvailableMessage($request));
		$sms = SmsQueryModel::model()->findByPk($smsRequest->smsQueryMessageId);
		$expected =
			"Благодарим за использование сервиса DocDoc.ru для записи к врачу на 05.06.2014. Перезвоните, пожалуйста, нам по телефону +74952367271 для оценки качества сервиса. Ваш DocDoc.ru";
		$this->assertEquals($expected, $sms->message);
	}

	/**
	 * Проверка отправки СМС о новой заявке клинике
	 *
	 * @param array    $requestParams
	 * @param callable $checkFunction
	 *
	 * @dataProvider addMessageToClinicDataProvider
	 * @throws \CException
	 */
	public function testAddMessageToClinic(array $requestParams, callable $checkFunction)
	{
		$smsRequest = new SmsRequestModel();
		$request = new RequestModel();

		foreach ($requestParams as $k => $v) {
			$request->$k = $v;
		}

		$this->getFixtureManager()->truncateTable('SMSQuery');
		$this->assertEquals(true, $smsRequest->requestStatusChanged(new \CEvent($request)));

		$checkFunction();
	}

	/**
	 * Данные для тестирования смс в клинику
	 *
	 * @return array
	 */
	public function addMessageToClinicDataProvider()
	{
		return [
			[
				//req_typ null, смс в клинику не идет
				[
					'client_name'  => 'test',
					'client_phone' => '79261234567',
					'kind'         => RequestModel::KIND_DIAGNOSTICS,
					'clinic_id'    => '1',
					'source_type'  => RequestModel::SOURCE_SITE,
					'scenario'      => RequestModel::SCENARIO_SITE,
					'req_status'   => RequestModel::STATUS_RECORD,
				],
				function () {
					$this->assertEquals(1, SmsQueryModel::model()->count());
					$sms = SmsQueryModel::model()->find();
					$expected = 'Вы записались в клинику "Клиника №1". Оператор свяжется с Вами для подтверждения даты и времени посещения.';
					$this->assertEquals($expected, $sms->message);
				}
			],
			[

				//req_type для диагностики онлайн,смс в клинику идет
				[
					'req_id'       => 777,
					'client_name'  => 'test',
					'client_phone' => '79261234567',
					'kind'         => RequestModel::KIND_DIAGNOSTICS,
					'clinic_id'    => '1',
					'source_type'  => RequestModel::SOURCE_SITE,
					'req_type'     => RequestModel::TYPE_ONLINE_RECORD,
					'scenario'     => RequestModel::SCENARIO_DIAGNOSTIC_ONLINE,
					'req_status'   => RequestModel::STATUS_RECORD,
				],
				function () {
					$this->assertEquals(2, SmsQueryModel::model()->count());
					$smsList = SmsQueryModel::model()->findAll();

					$expected = 'Поступила заявка №777. Зайдите в ЛК DocDoc.';
					$this->assertEquals($expected, $smsList[0]->message);

					$expected = 'Вы записались в клинику "Клиника №1". Оператор свяжется с Вами для подтверждения даты и времени посещения.';
					$this->assertEquals($expected, $smsList[1]->message);
				}
			],

		];
	}

	/**
	 * Установка дефолтных атрибутов
	 *
	 * @param RequestModel $request
	 *
	 * @return mixed
	 */
	public function setDefaultAttr($request)
	{
		$request->req_status = RequestModel::STATUS_RECORD;
		$request->req_doctor_id = 1;
		$request->clinic_id = 1;
		$request->source_type = RequestModel::SOURCE_SITE;

		return $request;
	}


}
