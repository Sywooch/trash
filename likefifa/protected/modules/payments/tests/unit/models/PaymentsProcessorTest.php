<?php

namespace dfs\modules\payment\tests\unit\models;

use dfs\modules\payments\models\PaymentsAccount;
use dfs\modules\payments\models\PaymentsProcessor;
use YiiBase;

/**
 * Class PaymentsProcessorTest
 *
 * Тест модели процессора платежей
 *
 * @author Aleksey Parshukov <parshukovag@gmail.com>
 * @date 24.09.2013
 *
 * @see https://docdoc.atlassian.net/wiki/pages/viewpage.action?pageId=1310733
 * @package dfs\modules\payments
 *
 */
class PaymentsProcessorTest extends \CDbTestCase
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
	 * Получение неизвестного парнёра
	 */
	public function testGetNull()
	{
		$this->assertNull(PaymentsProcessor::findByKey('notfound'));
	}

	/**
	 * Тестурем функию получения процессора
	 */
	public function testGetRobokassaProcessor()
	{
		$processor=PaymentsProcessor::findByKey('robokassa')->getProcessor();
		$this->assertInstanceOf('\dfs\modules\payments\base\Processor', $processor);
	}

	/**
	 * Тестируем создание урлы для робокассы
	 */
	public function testRobokassaProcessorGetUtl()
	{
		$price=90;
		$eMail='test@test.ru';
		$comment="Тест пополнения счёта";

		$ac=New PaymentsAccount();
		$ac->save();

		$processor=PaymentsProcessor::findByKey('robokassa');
		$invoice=$ac->createInvoice($price, $processor, true, $comment, $eMail);
		$url=$invoice->getProcessorUrl();

		$this->assertStringStartsWith(
			'https://auth.robokassa.ru/Merchant/Index.aspx?',
			$url
		);

		parse_str(parse_url($url, PHP_URL_QUERY), $output);
		$this->assertEquals(32, strlen($output['SignatureValue']));
		unset($output['SignatureValue']);

		$this->assertArrayHasKey('shp_invoice', $output);
		$this->assertEquals(36, strlen($output['shp_invoice']));
		unset($output['shp_invoice']);

		$this->assertEquals(
			array(
				'MrchLogin' => 'likefifa',
				'OutSum' => (string)$price,
				'Desc' => $comment,
				'Email' => $eMail,
			),
			$output
		);
	}
} 