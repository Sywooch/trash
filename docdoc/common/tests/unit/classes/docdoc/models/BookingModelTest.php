<?php
/**
 * Created by PhpStorm.
 * User: a.tyutyunnikov
 * Date: 22.08.14
 * Time: 11:36
 */

namespace dfs\tests\docdoc\models;

use dfs\docdoc\models\BookingModel;
use dfs\docdoc\models\RequestModel;
use dfs\docdoc\models\SlotModel;

class BookingModelTest extends \CDbTestCase
{
	/**
	 * Настраиваю окружение для букинга
	 */
	protected function setUp()
	{
		$this->getFixtureManager()->checkIntegrity(false);

		$this->getFixtureManager()->truncateTable('booking');
		$this->getFixtureManager()->loadFixture('booking');

		$this->getFixtureManager()->truncateTable('request');
		$this->getFixtureManager()->loadFixture('request');

		$this->getFixtureManager()->truncateTable('slot');
		$this->getFixtureManager()->loadFixture('slot');

		$this->getFixtureManager()->truncateTable('client');
		$this->getFixtureManager()->loadFixture('client');


	}

	/**
	 * Тестирую создание брони
	 *
	 * @param string   $scenario
	 * @param array    $attributes
	 * @param callable $checkFunction
	 *
	 * @dataProvider bookingDataProvider
	 */
	public function testBookingCreate($scenario, $attributes, \Closure $checkFunction)
	{
		if ($scenario == 'insert') {
			$model = new BookingModel();
		} else {
			$model = BookingModel::model()->findByPk(1);
		}

		$model->setScenario('test');
		$model->attributes = $attributes;
		$model->save();

		$checkFunction($model);
	}

	/**
	 * Провайдер для создания брони
	 *
	 * @return array
	 */
	public function bookingDataProvider()
	{
		return [
			//несуществующий слот
			[
				'insert',
				['request_id' => 1, 'slot_id' => -1], //(7, -1)
				function (BookingModel $booking) {
					$this->assertEquals(1, count($booking->getErrors()));
					$this->assertEquals(1, count($booking->getErrors('slot_id')));
				}
			],
			//создание новой брони, успех
			[
				'insert',
				['request_id' => 3, 'slot_id' => 'uuid2'],
				function (BookingModel $booking) {
					//инсерт всетаки прошел
					$this->assertNotNull($booking->id);
					//бронь долна быть в статусе нью
					$this->assertEquals(BookingModel::STATUS_NEW, $booking->status);
					//после брони заявки статус не должен измениться
					$this->assertEquals(RequestModel::STATUS_RECORD, $booking->request->req_status);
				}
			],
			//апдейт с ошибкой, нельзя менять заявку для брони
			[
				'update',
				['id' => 1, 'request_id' => 3, 'slot_id' => 'uuid1'],
				function (BookingModel $booking) {
					$this->assertEquals(1, count($booking->getErrors()));
					$this->assertEquals(1, count($booking->getErrors('request_id')));
				}
			],
			//апдейт с ошибкой, нельзя менять слот для брони
			[
				'update',
				['id' => 1, 'request_id' => 1, 'slot_id' => 'uuid3'],
				function (BookingModel $booking) {
					$this->assertEquals(1, count($booking->getErrors()));
					$this->assertEquals(1, count($booking->getErrors('slot_id')));
				}
			],
			//попытка повторно забронировать заявку на другое время
			[
				'insert',
				['request_id' => 6, 'slot_id' => 'uuid3'],
				function (BookingModel $booking) {
					$this->assertEquals(1, count($booking->getErrors()));
					$this->assertEquals(1, count($booking->getErrors('request_id')));
				}
			],
			//попытка cоздать бронь со статусом галимым
			[
				'insert',
				['request_id' => 3, 'slot_id' => 'uuid3', 'status' => BookingModel::STATUS_COME],
				function (BookingModel $booking) {
					$this->assertEquals(1, count($booking->getErrors()));
					$this->assertEquals(1, count($booking->getErrors('status')));
				}
			],
		];
	}

	/**
	 * Проверяю реакцию на изменение заявки
	 */
	public function testAfterRequestChange()
	{
		$request = RequestModel::model()->findByPk(1);
		$this->assertNotEquals(0, count($request->booking));

		$bookingStatuses = [];

		foreach($request->booking as $b){
			$bookingStatuses[$b->id] = $b->status;
		}

		$requestStatuses = array_keys(RequestModel::getStatusList());

		foreach($requestStatuses as $status){
			$request->saveStatus($status);

			foreach($request->booking as $b){
				$b->refresh();
				$this->assertEquals($bookingStatuses[$b->id], $b->status);
			}
		}
	}

	/**
	 * @param int $bookingId
	 * @param boolean $return
	 *
	 * @dataProvider reloadSlotsDataProvider
	 */
	public function testCheckSlots($bookingId, $return)
	{
		$this->getFixtureManager()->checkIntegrity(false);

		$this->getFixtureManager()->truncateTable('clinic');
		$this->getFixtureManager()->loadFixture('clinic');

		$this->getFixtureManager()->truncateTable('api_doctor');
		$this->getFixtureManager()->loadFixture('api_doctor');


		$bookingConfig = \Yii::app()->params['booking'];
		$bookingConfig['apiUrl'] = ROOT_PATH . "/common/tests/unit/data/api/clinic/getSlots/1.json";

		\Yii::app()->setParams(['booking' => $bookingConfig]);

		if(!$return){
			$this->setExpectedException('PHPUnit_Framework_Error');
		}

		$model = BookingModel::model()->findByPk($bookingId);
		$res = $model->checkSlot();

		$this->assertEquals($return, $res);
	}

	/**
	 * Данные для релоада слотов
	 *
	 * @return array
	 */
	public function reloadSlotsDataProvider()
	{
		return [
			[3, true],
			[2, false],
		];
	}

	/**
	 * @param $success
	 *
	 * @dataProvider bookInApiDataProvider
	 */
	public function testBookInApi($success)
	{
		$this->getFixtureManager()->checkIntegrity(false);

		$this->getFixtureManager()->truncateTable('doctor_4_clinic');
		$this->getFixtureManager()->loadFixture('doctor_4_clinic');

		$this->getFixtureManager()->truncateTable('doctor');
		$this->getFixtureManager()->loadFixture('doctor');

		$bookingConfig = \Yii::app()->params['booking'];

		if($success){
			$bookingConfig['apiUrl'] = ROOT_PATH . "/common/tests/unit/data/api/clinic/book/success.json";
		} else {
			$bookingConfig['apiUrl'] = ROOT_PATH . "/common/tests/unit/data/api/clinic/book/error.json";
			$this->setExpectedException('PHPUnit_Framework_Error');
		}

		\Yii::app()->setParams(['booking' => $bookingConfig]);

		$booking = BookingModel::model()->findByPk(1);

		$res = $booking->bookInClinic();

		$this->assertEquals($success, $res);
	}

	/**
	 * @return array
	 */
	public function bookInApiDataProvider()
	{
		return [
			[true],
			[false],
		];
	}

	/**
	 * Тест смены статусов брони
	 * Проверяется переход с каждого из возможных статусов в каждый возможный
	 *
	 * @throws \CException
	 */
	public function testChangeStatus()
	{
		$this->getFixtureManager()->checkIntegrity(false);

		$this->getFixtureManager()->truncateTable('booking');
		$this->getFixtureManager()->truncateTable('request_history');
		$this->getFixtureManager()->truncateTable('doctor_4_clinic');

		$this->getFixtureManager()->loadFixture('doctor_4_clinic');

		$statuses = array_keys(BookingModel::$localToRequestStatuses);

		foreach ($statuses as $key => $status) {
			$temp_statuses = $statuses;
			unset($temp_statuses[$key]);

			foreach ($temp_statuses as $st) {
				//$this->getFixtureManager()->truncateTable('booking');
				(isset($b) && $b instanceof BookingModel) && $b->delete();

				//создаю базовую бронь
				$b = new BookingModel();
				$b->scenario = BookingModel::SCENARIO_SKIP_VALIDATION;
				$b->request_id = 3;
				$b->slot_id = 2;
				$b->status = $status;
				$b->save();

				//меняю ей статус
				$b->status = $st;
				$b->save();

				if ($b->canChangeStatus($st)) {
					list($requestStatus,) = BookingModel::$localToRequestStatuses[$st];
					$this->assertEquals($requestStatus, $b->request->req_status);

					if($st != $status && $st == BookingModel::STATUS_CANCELED_BY_ORGANIZATION){
						$this->assertEquals($b->request->billing_status, RequestModel::BILLING_STATUS_REFUSED);
						$this->assertEquals($b->request->req_status, RequestModel::STATUS_REJECT);
					}
				} else {
					$this->assertEquals(1, count($b->getErrors('status')));
				}
			}
		}
	}

	/**
	 * Резерв
	 *
	 * @param int $requestId
	 * @param int $slotId
	 * @param bool $expected
	 *
	 * @dataProvider reserveDataProvider
	 */
	public function testReserve($requestId, $slotId, $expected)
	{
		$bookingConfig = \Yii::app()->params['booking'];
		$bookingConfig['apiUrl'] = ROOT_PATH . "/common/tests/unit/data/api/clinic/getSlots/2.json";
		\Yii::app()->setParams(['booking' => $bookingConfig]);

		$booking = new BookingModel();
		$booking->request_id = $requestId;
		$slot = SlotModel::model()->findByPk($slotId);
		$res = $booking->reserve($slot->external_id);

		$this->assertEquals($expected, (bool)$res);
	}

	/**
	 * Данный для резерва
	 *
	 * @return array
	 */
	public function reserveDataProvider()
	{
		return [
			[10, 6, true], //успех
			[6, 3, false],//бронь уже существует
		];
	}

	/**
	 * Тест обновления из апи брони
	 *
	 * @param string $url
	 * @param callable $checkFunction
	 *
	 * @dataProvider reloadFromApiDataProvider
	 */
	public function testReloadFromApi($url, callable $checkFunction)
	{
		$bookingConfig = \Yii::app()->params['booking'];
		$bookingConfig['apiUrl'] = $url;
		\Yii::app()->setParams(['booking' => $bookingConfig]);

		$booking = BookingModel::model()->findByPk(4);
		$checkFunction($booking);
	}

	/**
	 * @return array
	 */
	public function reloadFromApiDataProvider()
	{
		return [
			[
				ROOT_PATH . "/common/tests/unit/data/api/clinic/getBookStatus/only_status.json",
				function (BookingModel $b) {
					$this->assertTrue($b->reloadFromApi());
				}
			],
			[
				ROOT_PATH . "/common/tests/unit/data/api/clinic/getBookStatus/change_dates.json",
				function (BookingModel $b) {
					$oldStartTime = $b->start_time;
					$oldFinishTime = $b->finish_time;

					$this->assertTrue($b->reloadFromApi());

					$this->assertNotEquals($oldStartTime, $b->start_time);
					$this->assertNotEquals($oldFinishTime, $b->finish_time);
				}
			],
		];
	}

	/**
	 * Ошибка данных при обновлении брони из апи
	 *
	 * @throws \CException
	 */
	public function testReloadFromApiError()
	{
		$bookingConfig = \Yii::app()->params['booking'];
		$bookingConfig['apiUrl'] = ROOT_PATH . "/common/tests/unit/data/api/clinic/getBookStatus/error.json";
		\Yii::app()->setParams(['booking' => $bookingConfig]);

		$booking = BookingModel::model()->findByPk(1);
		$this->setExpectedException(\CException::class);
		$booking->reloadFromApi();
	}
}
