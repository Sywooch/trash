<?php

namespace dfs\modules\payment\tests\unit\models;
use dfs\modules\payments\models\PaymentsAccount;
use dfs\modules\payments\models\PaymentsInvoice;
use dfs\modules\payments\models\PaymentsProcessor;

/**
 * Class PaymentsInvoiceTest
 *
 * Тестируем работу с инвойсами
 *
 * @author Aleksey Parshukov <parshukovag@gmail.com>
 * @date 25.09.2013
 *
 * @see https://docdoc.atlassian.net/wiki/pages/viewpage.action?pageId=1310733
 * @package dfs\modules\payments
 *
 */
class PaymentsInvoiceTest extends \CDbTestCase
{
	/**
	 * Тестируем верхний алгоритм работы с инвойсамиl
	 */
	public function testCreateNewInvoice()
	{
		$message="Тестируем создание фейковых д<>!&енег";
		$email="test@test.ru";

		$ac=new PaymentsAccount();
		$ac->save();

		$this->assertGreaterThan(0, PaymentsProcessor::model()->count(), "Не загрузилась фикстура с процессорами");

		$processor = PaymentsProcessor::findByKey('testProcessor');
		$this->assertNotNull($processor);
		$invoice = $ac->createInvoice(1000.64, $processor, false, $message, $email);

		$this->assertFalse($invoice->getIsNewRecord());

		$this->assertNotEmpty($invoice->id);
		$this->assertInternalType(\PHPUnit_Framework_Constraint_IsType::TYPE_STRING, $invoice->id);
		$this->assertEquals(36, strlen($invoice->id));

		do {
			/**
			 * Проверяем до и после перезагрузки
			 */
			$this->assertEquals(PaymentsInvoice::STATUS_NEW, $invoice->status);
			$this->assertEquals($message, $invoice->message);
			$this->assertEquals(1000, $invoice->getAmount());
			$this->assertFalse($invoice->isReal());
			$this->assertEquals($processor->id, $invoice->processor_id);
			$this->assertEquals($ac->id, $invoice->account_to);
			$this->assertEquals($email, $invoice->email);

			if ($invoice->getCreateDate()) {
				break;
			}

			$invoice = PaymentsInvoice::model()->findByPk($invoice->getPrimaryKey());
		} while(true);

		$this->assertNotVeryOldDate($invoice->getCreateDate());
		$this->assertNotVeryOldDate($invoice->getStatusDate());
	}

	/**
	 * Тестируем закрытие инвойса
	 */
	public function testInvoiceClose()
	{
		$message="Тестируем создание фейковых д<>!&енег";
		$email="test@test.ru";
		$ac=new PaymentsAccount();
		$ac->save();

		$processor=PaymentsProcessor::findByKey('testProcessor');
		$this->assertNotNull($processor);
		$startBalance=$processor->getBalance();
		$invoice=$ac->createInvoice(1000.64, $processor, false, $message, $email);

		$this->assertTrue($invoice->close());

		$this->assertEquals(PaymentsInvoice::STATUS_CLOSE, $invoice->status, "Не изменился статус");
		$this->assertNotVeryOldDate($invoice->getStatusDate());

		$ac->refresh();
		$processor->refresh();

		$this->assertEquals($invoice->getAmount(), $ac->getAmount());
		$this->assertEquals($startBalance-$invoice->getAmount(), $processor->getBalance());

		try{
			$invoice->close();
			$this->fail("Повторное закрытие не допустимо");
		} catch(\Exception $e) {
			$this->assertEquals("Already closed", $e->getMessage());
		}
	}

	/**
	 * Проверяем, что дата не слишком старая.
	 *
	 * @param \DateTime $dt
	 * @param int $ttl В секундах
	 */
	public function assertNotVeryOldDate($dt, $ttl=5)
	{
		$this->assertInstanceOf('\DateTime', $dt);
		$this->assertLessThan($ttl, $dt->diff(new \DateTime())->s);
	}
}