<?php
namespace dfs\modules\payment\tests\unit\models;

use dfs\modules\payments\models\PaymentsAccount;
use dfs\modules\payments\models\PaymentsInvoice;
use dfs\modules\payments\models\PaymentsOperations;
use dfs\modules\payments\models\PaymentsProcessor;
use YiiBase;

/**
 * Class PaymentsInvoiceTest
 *
 * Тестируем сохранение лога операций
 *
 * @author  Aleksey Parshukov <parshukovag@gmail.com>
 * @date    16.10.2013
 *
 * @see     https://docdoc.atlassian.net/wiki/pages/viewpage.action?pageId=1310733
 * @package dfs\modules\payments
 *
 */
class PaymentsOperationTest extends \CDbTestCase
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
	 * Тестируем создание лога операций
	 */
	public function testOperationLog()
	{
		PaymentsOperations::model()->deleteAll();

		$message = "Тестируем создание фейковых д<>!&енег";
		$email = "test@test.ru";
		$ac = new PaymentsAccount();
		$ac->save();

		$processor = PaymentsProcessor::findByKey('testProcessor');
		$this->assertNotNull($processor);
		$invoice = $ac->createInvoice(1000.64, $processor, false, $message, $email);

		$this->assertTrue($invoice->close());

		$operations = PaymentsOperations::model()->findAll();
		$this->assertCount(2, $operations);

		foreach ($operations as $operation) {
			if (isset($id)) {
				$this->assertEquals($id, $operation->id, "Идентификаторы должны совпадать");
			}
			$id = $operation->id;

			if (isset($income)) {
				$this->assertNotEquals($income, $operation->income);
			}
			$income = $operation->income;

			$this->assertNotEmpty($id);
			$this->assertInternalType(\PHPUnit_Framework_Constraint_IsType::TYPE_STRING, $id);
			$this->assertEquals(36, strlen($id));

			$this->assertNotVeryOldDate($operation->getCreateDate());
			$this->assertEquals(PaymentsOperations::TYPE_TOP_UP, $operation->type);
			$this->assertEquals("Close invoice #{$invoice->id}", $operation->message);
			$this->assertEquals(0, $operation->amount_real);
			$this->assertEquals($invoice->id, $operation->invoice_id);

			if ($operation->income) {
				$this->assertEquals($ac->id, $operation->account_from);
				$this->assertEquals($processor->account_id, $operation->account_to);
				$this->assertEquals(-1000, $operation->amount_fake);

			} else {
				$this->assertEquals($processor->account_id, $operation->account_from);
				$this->assertEquals($ac->id, $operation->account_to);
				$this->assertEquals(1000, $operation->amount_fake);


			}
		}
	}

	/**
	 * Проверяем, что дата не слишком старая.
	 *
	 * @param \DateTime $dt
	 * @param int       $ttl В секундах
	 */
	public function assertNotVeryOldDate($dt, $ttl = 5)
	{
		$this->assertInstanceOf('\DateTime', $dt);
		$this->assertLessThan($ttl, $dt->diff(new \DateTime())->s);
	}
} 