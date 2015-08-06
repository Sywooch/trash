<?php

namespace dfs\tests\docdoc\models;

use dfs\docdoc\models\ClientModel;
use CDbTestCase;
use dfs\docdoc\models\RequestModel;
use Yii;

/**
 * Class ClientModelTest
 *
 * @package dfs\tests\docdoc\models
 */
class ClientModelTest extends CDbTestCase
{
	/**
	 * при старте теста
	 */
	function setUp()
	{
		$fm = $this->getFixtureManager();
		$fm->truncateTable('client');
		$fm->loadFixture('client');
	}

	/**
	 * Тест на создание нового телефона
	 */
	public function testCreateClient()
	{
		//новая заявка, клиента с таким телефоном нет,
		//должен создаться клиент
		$request = new RequestModel();
		$request->attributes = [
			'client_name' => 'Иванов',
			'client_phone' => '74951234567',
		];
		$request->save();
		$clientId = $request->clientId;
		$this->assertNotNull($request->clientId);
		$this->assertEquals($request->client_name, $request->client->name);
		$this->assertEquals($request->client_phone, $request->client->phone);


		//при измении имени клиента в заявке нужно изменить и имя клиента
		//clientId не меняется
		$request->client_name = 'Новое имя клиента';
		$request->save();
		$this->assertEquals($clientId,  $request->clientId);
		$this->assertEquals($request->client_name,  $request->client->name);

		//если в заявке меняется номер телефона и клиента с таким телефоном еще нет,
		//должен измениться номер у клиента
		//clientId не меняется
		$request->client_phone = '74957654321';
		$request->save();
		$this->assertEquals($clientId,  $request->clientId);
		$this->assertEquals($request->client_phone,  $request->client->phone);

		//если в заявке меняется номер телефона и такой телефон уже есть у другого клиента,
		//должен вернуться ID другого клиента
		//у него должно измениться имя
		$phone = '74951111111';
		$name = 'Новое имя клиента';
		$client = ClientModel::model()->byPhone($phone)->find();
		$request->client_phone = $phone;
		$request->client_name = $name;
		$request->save();
		$this->assertEquals($client->clientId,  $request->clientId);
		$this->assertEquals($name, $request->client->name);

		// Проверка на неизменность E-mail
		// При изменении имени и/или телефона клиента, email клиента должен не меняться
		$client = ClientModel::model()->findByPk($clientId);
		$client->email = "test@test.ru";
		$client->save();
		$request->client_name = 'Новое имя клиента';
		$request->client_phone = '74957654321';
		$request->save();
		$this->assertEquals($client->clientId, $request->clientId);
		$this->assertEquals($client->email, $request->client->email);
	}

	/**
	 * тест для ClientModel::saveRegisteredInMixPanel()
	 */
	public function testSaveRegisteredInMixPanel()
	{
		$client = ClientModel::model()->findByPk(1);

		//проверка, что изменится только значение поля registered_in_mixpanel
		$client->name = "aaa";
		$client->first_name = "bbb";
		$client->phone = "79121234567";
		$client->saveRegisteredInMixPanel();

		$client2 = ClientModel::model()->findByPk(1);
		$this->assertEquals(1, $client2->registered_in_mixpanel);
		$this->assertNotEquals('aaa', $client2->name);
		$this->assertNotEquals('bbb', $client2->first_name);
		$this->assertNotEquals('79121234567', $client2->phone);
	}
}