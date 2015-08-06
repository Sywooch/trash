<?php

namespace dfs\tests\docdoc\api\rpc;

use dfs\docdoc\api\components\ApiFactory;
use CDbTestCase;
use Yii;

/**
 * Class API_v100Test
 *
 * @package dfs\tests\docdoc\api\rpc
 */
class API_v100Test extends CDbTestCase
{

	/**
	 * Тестируем получение клиник
	 *
	 * @dataProvider bookDataProvider
	 *
	 * @param array $params
	 * @param int $result
	 */
	public function testBook(array $params, $result)
	{
		$this->loadFixtures();
		$api = ApiFactory::getApi('/api/rpc/1.0.0/json', $params);
		$response = $api->run();
		$this->assertEquals($result, $response);
	}

	/**
	 * Запросы на создание заявки
	 *
	 * @return array
	 */
	public function bookDataProvider()
	{
		return array(

			// Запрос с валидными данными
			array(
				array(
					'dataType' => 'json',
					'partnerId'  => 1,
					'rawData' => '{
						"jsonrpc":"2.0",
						"method":"book",
						"params":[{
							"bookId":"sdrgwg54yvqwe563uhgs2",
							"bookType":"static",
							"organizationId":"1",
							"serviceId":"1",
							"resourceId":"2",
							"fullname":"Test",
							"phone":"+74951234567",
							"comment":"test test!!!!!!"
						}],
						"id":1
					 }',
				),
				'{"jsonrpc":"2.0","result":{"status":"ACCEPTED","url":"http://docdoc.ru/request/thanks/id/sdrgwg54yvqwe563uhgs2"},"id":1}',
			),

			// Запрос с неактивным врачом
			array(
				array(
					'dataType' => 'json',
					'partnerId'  => 1,
					'rawData' => '{
						"jsonrpc":"2.0",
						"method":"book",
						"params":[{
							"bookId":"sdrgwg54yvqwe563uhgs21",
							"bookType":"static",
							"organizationId":"1",
							"serviceId":"1",
							"resourceId":"1",
							"fullname":"Test",
							"phone":"+74951234567",
							"comment":"test test!!!!!!"
						}],
						"id":1
					 }',
				),
				'{"jsonrpc":"2.0","error":{"code":-32602,"message":"Invalid params"},"id":1}',
			),

			// Запрос с пустыми данными
			array(
				array(
					'dataType' => 'json',
					'partnerId'  => 1,
					'rawData' => '',
				),
				'{"jsonrpc":"2.0","error":{"code":-32600,"message":"Invalid Request"},"id":null}',
			),

		);
	}

	/**
	 * подготовка базы для тестов
	 */
	public function loadFixtures()
	{
		$this->getFixtureManager()->checkIntegrity(false);
		$this->getFixtureManager()->truncateTable('request');
		$this->getFixtureManager()->truncateTable('request_4_remote_api');
		$this->getFixtureManager()->truncateTable('clinic');
		$this->getFixtureManager()->truncateTable('doctor_4_clinic');
		$this->getFixtureManager()->truncateTable('doctor');
		$this->getFixtureManager()->loadFixture('clinic');
		$this->getFixtureManager()->loadFixture('doctor');
		$this->getFixtureManager()->loadFixture('doctor_4_clinic');
	}

} 