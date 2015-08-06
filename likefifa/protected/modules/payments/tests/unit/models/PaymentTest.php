<?php
namespace dfs\modules\payment\tests\unit\models;
use dfs\modules\payments\models\Payment;
use dfs\modules\payments\models\PaymentsAccount;

/**
 * Class PaymentsInvoiceTest
 *
 * Тестируем работу переводом
 *
 * @author  Aleksey Parshukov <parshukovag@gmail.com>
 * @date    16.10.2013
 *
 * @see     https://docdoc.atlassian.net/wiki/pages/viewpage.action?pageId=1310733
 * @package dfs\modules\payments
 *
 */
class PaymentTest extends \CDbTestCase
{
	/**
	 * Перевод с одного счёта на другой
	 *
	 * @dataProvider provideAmount
	 *
	 * @param int       $amount
	 * @param int       $fromAmountReal
	 * @param int       $fromAmountFake
	 * @param int       $fromAmountRealAfter
	 * @param int       $fromAmountFakeAfter
	 * @param int       $toAmountRealAfter
	 * @param int       $toAmountFakeAfter
	 * @param bool|null $isReal
	 */
	public function testCreditAmountTest(
		$amount,
		$fromAmountReal,
		$fromAmountFake,
		$fromAmountRealAfter,
		$fromAmountFakeAfter,
		$toAmountRealAfter,
		$toAmountFakeAfter,
		$isReal = null
	)
	{
		$acFrom = new PaymentsAccount();
		$acFrom->amount_real = $fromAmountReal;
		$acFrom->amount_fake = $fromAmountFake;
		$acFrom->save();

		$acTo = new PaymentsAccount();
		$acTo->save();

		$payment = new Payment($acFrom, $acTo, null, "some reason");
		$payment->addPayment($amount, $isReal);


		$acTo->refresh();
		$acFrom->refresh();

		$this->assertEquals($fromAmountFake, $acFrom->amount_fake, "Виртуальный баланс исходящего счёта");
		$this->assertEquals($fromAmountReal, $acFrom->amount_real, "Реальный баланс исходящего счёта");

		$this->assertEquals(0, $acTo->amount_fake, "Виртуальный баланс счёта назначения");
		$this->assertEquals(0, $acTo->amount_real, "Реальный баланс счёта назначения");

		$this->assertEquals($toAmountFakeAfter, $payment->getAmountFake(), "Виртуальный баланс счёта назначения");
		$this->assertEquals($toAmountRealAfter, $payment->getAmountReal(), "Реальный баланс счёта назначения");
		$this->assertEquals($toAmountRealAfter + $toAmountFakeAfter, $payment->getAmount());
	}

	/**
	 * Проверяем перевод на разных исходных данных
	 *
	 * @return array
	 */
	public function provideAmount()
	{
		return array(
			// Если на исходом счету нету денег, будут сняты фейковые
			array(100.65, 0, 0, 0, -100, 0, 100),
			// Если на исходом счету есть реальные, то они будут сняты реальные
			array(100.65, 0, 200, 0, 100, 0, 100),
			// Если на исходом счету есть реальные деньги, то будут сняты реальные.
			array(100.65, 200, 0, 100, 0, 100, 0),
			// Если на исходом счету недостаточно реальных денег, то будет снято немного фейковых
			array(100.65, 30, 0, 0, -70, 30, 70),
			// Если на исходом счету недостаточно фейковых денег, то будет снято немного реальных
			array(100.65, 300, 30, 230, 0, 70, 30),
			// Если на исходом счету недостаточно ни реальных, ни фейковых. В минус уйдёт именно фейк.
			array(100.65, 25, 40, 0, -35, 25, 75),
			// Если оба счёта на коне - снимаем фейки
			array(100.65, 150, 300, 150, 200, 0, 100),
			// Если основной отрицательный - снимаем фейки.
			array(100.65, -150, 300, -150, 200, 0, 100),
			// Пограничные условия
			// риалы
			array(100, 100, 0, 0, 0, 100, 0),
			// минус
			array(100, -100, -100, -100, -200, 0, 100),
			// фейки
			array(100, 0, 100, 0, 0, 0, 100),
			// поровну
			array(100, 100, 100, 100, 0, 0, 100),
			//
			// Форсируем валюту
			//

			// Если нету денег
			array(100.65, 0, 0, -100, 0, 100, 0, true),
			// Если есть
			array(100.65, 200, 200, 100, 200, 100, 0, true),
			// Если нету денег
			array(100.65, 0, 0, 0, -100, 0, 100, false),
			// Если есть
			array(100.65, 200, 200, 200, 100, 0, 100, false),
		);
	}
} 