<?php
namespace dfs\modules\payment\tests\unit\models;
use CDbTestCase;
use dfs\modules\payments\models\PaymentsAccount;
use dfs\modules\payments\models\PaymentsInvoice;
use dfs\modules\payments\models\PaymentsProcessor;
use YiiBase;

/**
 * Class PaymentsAccountTest
 *
 * @author Aleksey Parshukov <parshukovag@gmail.com>
 * @date 19.09.2013
 *
 * @see https://docdoc.atlassian.net/wiki/pages/viewpage.action?pageId=1310733
 * @package dfs\modules\payments
 *
 */
class PaymentsAccountTest extends CDbTestCase
{
	/**
	 * @var string[]
	 */
	public $fixtures=array(
		'accounts'=>'\dfs\modules\payments\models\PaymentsAccount',
		'payments'=>'\dfs\modules\payments\models\PaymentsProcessor',
	);

	/**
	 * Переопределяем путь для загрузки фикстур
	 */
	protected function setUp()
	{
		YiiBase::$enableIncludePath = false;
		$this->getFixtureManager()->basePath = __DIR__ . '/../../fixtures/';
		parent::setUp();
	}

	/**
	 * Проверяем функцию получения баланса
	 */
	public function testGetAccount()
	{
		$ac = new PaymentsAccount();
		$start = (int)$ac->count();
		$ac->save(false);

		$this->assertGreaterThanOrEqual(
			PaymentsAccount::MIN_USER_ID,
			$ac->id,
			'Новые аккаунты должны создаваться с идентификаторами больше 10000'
		);

		$this->assertEquals(++$start, $ac->count(), "Не удалось создать счёт");
		$this->assertEquals(0, $ac->getAmount(), "Баланс должен быть пустой");

		$ac->amount_fake = 10;
		$ac->save();
		$this->assertEquals(10, $ac->getAmount(), "Баланс должен быть пустой");

		$ac->amount_real = -20;
		$ac->save();
		$this->assertEquals(-10, $ac->getAmount(), "Баланс должен быть пустой");
	}


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
		$isReal=null
	)
	{
		$acFrom=new PaymentsAccount();
		$acFrom->amount_real=$fromAmountReal;
		$acFrom->amount_fake=$fromAmountFake;
		$acFrom->save();

		$acTo=new PaymentsAccount();
		$acTo->save();

		$acFrom->creditAmount($acTo, $amount, $isReal, null, "some reason");

		$acTo->refresh();
		$acFrom->refresh();

		$this->assertEquals($fromAmountFakeAfter, $acFrom->amount_fake, "Виртуальный баланс исходящего счёта");
		$this->assertEquals($fromAmountRealAfter, $acFrom->amount_real, "Реальный баланс исходящего счёта");
		$this->assertEquals($fromAmountRealAfter+$fromAmountFakeAfter, $acFrom->getAmount());

		$this->assertEquals($toAmountFakeAfter, $acTo->amount_fake, "Виртуальный баланс счёта назначения");
		$this->assertEquals($toAmountRealAfter, $acTo->amount_real, "Реальный баланс счёта назначения");
		$this->assertEquals($toAmountRealAfter+$toAmountFakeAfter, $acTo->getAmount());
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