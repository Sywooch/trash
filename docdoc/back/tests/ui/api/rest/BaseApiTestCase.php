<?php
/**
 * Created by PhpStorm.
 * User: a.tyutyunnikov
 * Date: 10.11.14
 * Time: 18:26
 */

namespace dfs\back\tests\ui\api\rest;

use Curl;
use CurlResponse;

/**
 * Базовый класс для тестирования рест апи
 *
 * Class BaseApiTestCase
 * @package dfs\back\tests\ui\api\rest
 */
abstract class BaseApiTestCase extends \CTestCase
{
	/**
	 * Версия апи
	 *
	 * @return mixed
	 */
	abstract protected function getVersion();

	/**
	 * Полный путь к корню апи
	 *
	 * @return string
	 */
	protected function getUrl()
	{
		$host = \Yii::app()->getParams()['hosts']['back'];

		return "https://{$host}/api/rest/" . $this->getVersion();
	}

	/**
	 * Отправка запроса
	 *
	 * @param string $path путь запроса
	 * @param array|string $data данные, передаваемые в запросе
	 * @param array $auth данные аутентификации
	 *
	 * @return CurlResponse
	 */
	public function open($path, $data = [], array $auth = ['test-partner', 'docdoc_test654'])
	{
		$curl = new Curl;

		if ($auth) {
			list($login, $password) = $auth;
			$curl->setAuth($login, $password);
		}

		$curl->options = [
			'CURLOPT_SSL_VERIFYHOST' => false,
			'CURLOPT_SSL_VERIFYPEER' => false,
		];

		if(is_array($data)){
			$resp = $curl->get($this->getUrl() . $path, $data);
		} else {
			$curl->headers['Content-Type'] = 'application/json';
			$resp = $curl->post($this->getUrl() . $path, $data);
		}

		return $resp;
	}

	/**
	 * Отправляет запрос и проверяет его на 200 и на формат json
	 *
	 * @param $path
	 * @param array|string $data
	 * @param array $auth
	 * @return CurlResponse
	 * @throws \CException
	 */
	public function openWithCheck($path, $data = [], array $auth = ['test-partner', 'docdoc_test654'])
	{
		$resp = $this->open($path, $data, $auth);

		$this->assertStatusCode(200, $resp);
		$this->assertResponseFormat('json', $resp);

		json_decode($resp->body, true);
		$this->assertEquals(JSON_ERROR_NONE, json_last_error());

		return $resp;
	}

	/**
	 * Из массива в чпу
	 *
	 * @param array $params
	 * @return string
	 */
	protected function build4pu(array $params)
	{
		$_4pu = '';

		foreach($params as $k => $v){
			if(is_numeric($k)){
				$_4pu .= "/$k";
			} else {
				$_4pu .= "/$k/$v";
			}
		}

		return $_4pu;
	}

	/**
	 * Проверка на http код респонса
	 *
	 * @param int $statusCode
	 * @param CurlResponse $resp
	 */
	public function assertStatusCode($statusCode, CurlResponse $resp)
	{
		$this->assertEquals($statusCode, $resp->headers['Status-Code']);
	}

	/**
	 * Проверка на формат http респонса
	 *
	 * @param string $format
	 * @param CurlResponse $resp
	 *
	 * @throws \CException
	 */
	public function assertResponseFormat($format = 'json', CurlResponse $resp)
	{
		if($format == 'json'){
			$this->assertEquals('text/json; charset=utf-8', $resp->headers['Content-Type']);
		} else {
			throw new \CException('Bad response format for assertion');
		}
	}

	/**
	 * Тест одиного объекта
	 *
	 * @param array $expected
	 * @param array $actual
	 * @param null|string $key
	 * @param bool $empty
	 */
	public function structTest($expected, $actual, $key = null, $empty = false)
	{
		if ($key) {
			$this->assertEquals([$key], array_keys($actual));
			$this->assertEquals(!(int)$empty, count($actual[$key]));

			$actual = array_shift($actual[$key]);
		}

		if(!$empty){
			$this->assertEquals(array_keys($expected), array_keys($actual));

			foreach ($expected as $k => $v) {
				if (is_array($v) && !is_array($actual[$k])) {
					$this->fail("Ожидается массив для $k но увы...");
				} elseif (!is_array($v) && is_array($actual[$k])) {
					$this->fail("Ожидается не массив для $k но увы...");
				}
			}
		}
	}

	/**
	 * Тест массива объектов
	 *
	 * @param array$expected
	 * @param array $struct
	 * @param null|string $key
	 * @param bool $empty
	 */
	public function structListTest(array $expected, array $struct, $key = null, $empty = false)
	{
		if ($key) {
			$this->assertEquals([$key], array_keys($struct));
			$this->assertEquals(!$empty, (bool)count($struct[$key]));
			$struct = $struct[$key];
		}


		foreach($struct as $actual){
			$this->structTest($expected, $actual, null, $empty);
		}
	}
} 
