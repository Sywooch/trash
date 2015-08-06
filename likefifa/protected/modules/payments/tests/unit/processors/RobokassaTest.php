<?php
namespace dfs\modules\payment\tests\unit\models;
use dfs\modules\payments\models\PaymentsInvoice;
use dfs\modules\payments\processors\Robokassa;

/**
 * Class RobokassaTest
 *
 * @author Aleksey Parshukov <parshukovag@gmail.com>
 * @date 26.09.2013
 *
 * @package dfs\modules\payment\tests\unit\models
 */
class RobokassaTest extends \CDbTestCase
{
	/**
	 * @var string[]
	 */
	public $fixtures=array(
		'accounts'=>'\dfs\modules\payments\models\PaymentsAccount',
		'payments'=>'\dfs\modules\payments\models\PaymentsProcessor',
		'invoices'=>'\dfs\modules\payments\models\PaymentsInvoice',
	);

	/**
	 * Переопределяем путь для загрузки фикстур
	 */
	protected function setUp()
	{
		$this->getFixtureManager()->basePath = __DIR__ . '/../../fixtures/';
		parent::setUp();
	}

	/**
	 * Типичный запрос
	 *
	 * @return array
	 */
	public function getRequest()
	{
		return array(
			'OutSum'=>'8',
			'InvId'=>'846259152',
			'SignatureValue'=>'4D481DB4AE23780AF899AA2FC3B033B0',
			'shp_invoice'=>'eb306cc1-26b5-11e3-b927-5404a646adec',
		);
	}

	/**
	 * Тестируем ввалидацию подписи
	 */
	public function testSignature()
	{
		$result=$this->getRequest();
		$processor=new Robokassa();
		$this->assertTrue($processor->validateSignature($result));
		$result['OutSum']='11';
		$this->assertFalse($processor->validateSignature($result));
	}


	/**
	 * Тестируем обработку запроса
	 *
	 * @dataProvider provideResultData
	 */
	public function testResultException($message, array $request)
	{
		$processor=new Robokassa();
		try{
			$processor->result($request);
			$this->fail("Запрос не должен пройти");
		}
		catch(\Exception $e)
		{
			$this->assertEquals($message, $e->getMessage());
		}
	}

	/**
	 * Тестируем обработку Успешного запроса
	 */
	public function testResultSuccess()
	{
		$request=$this->getRequest();
		$processor=new Robokassa();
		$invoiceId=$request['shp_invoice'];
		$invoice=PaymentsInvoice::model()->findByPk($invoiceId);
		$this->assertNotNull($invoice, "Не загрузилась фикстура сцетов");
		$account=$invoice->account;
		$amountStart=$account->getAmount();
		$processorAccount=$invoice->processor->account;
		$processorAmountStart=$processorAccount->getAmount();
		$this->assertEquals('OK846259152', $processor->result($request));

		$account->refresh();
		$processorAccount->refresh();
		$this->assertEquals($amountStart+$request['OutSum'], $account->getAmount());
		$this->assertEquals($processorAmountStart-$request['OutSum'], $processorAccount->getAmount());
	}

	/**
	 * Проверяем повторное закрытие
	 */
	public function testRepeatResult()
	{
		$request=$this->getRequest();
		$processor=new Robokassa();
		$this->assertEquals('OK846259152', $processor->result($request));

		try
		{
			$processor->result($request);
			$this->fail("Не должно пройти");
		}
		catch(\Exception $e)
		{
			$this->assertEquals('Already paid', $e->getMessage());
		}
	}

	/**
	 * Проверяем ошибки
	 *
	 * @return array
	 */
	public function provideResultData()
	{
		return array(
			array(
				'Invalid request',
				array()
			),
			array(
				'Invalid signature',
				array(
					'OutSum'=>'10',
					'InvId'=>'262861636',
					'SignatureValue'=>'10A93636F5662F3764D961B28F79C296',
					'shp_invoice'=>'8755012d-26ad-11e3-b927-5404a646adec',
				),
			),

			array(
				'Invalid processor',
				array(
					'OutSum'=>'10',
					'InvId'=>'262861636',
					'SignatureValue'=>'ef84ecd04ae7aa93f41c955f8ff7f2b8',
					'shp_invoice'=>'eb306cc1-26b5-11e3-b927-5404a646aded',
				),
			),

			array(
				'Invoice not found',
				array(
					'OutSum'=>'10',
					'InvId'=>'262861636',
					'SignatureValue'=>'56c39f12602f52329a582c03e0a20012',
					'shp_invoice'=>'eb306cc1-26b5-11e3-b927-5404a646adee',
				),
			),
		);
	}
} 