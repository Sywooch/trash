<?php

namespace dfs\docdoc\api;

use dfs\common\config\Environment;

/**
 *  Абстрактный Json-RPC клиент
 */
abstract class JsonRpcClient
{
	/**
	 * Адрес сервера
	 *
	 * @var string
	 */
	protected $_url;

	/**
	 * id сообщения
	 *
	 * @var string
	 */
	protected $_id;

	/**
	 * массив с заголовками запроса
	 *
	 * @var array
	 */
	protected $_headers = [];

	/**
	 * Конструктор
	 *
	 * @param string|null $url
	 */
	public function __construct($url = null)
	{
		$this->_url = $url;
	}

	/**
	 * Добавление параметра в зааголовок запроса
	 *
	 * @param string $header
	 */
	public function addHeader($header)
	{
		$this->_headers[] = $header;
	}

	/**
	 * Установка значения ID сообщения
	 *
	 * @param string $id
	 */
	public function setId($id)
	{
		$this->_id = $id;
	}

	/**
	 * @param string $name      имя вызываемого метода
	 * @param array  $arguments параметры
	 *
	 * @return mixed
	 * @throws \Exception
	 * @throws JsonRpcException
	 */
	public function __call($name, $arguments)
	{
		$id = (empty($this->_id)) ? md5(microtime()) : $this->_id;

		//сбрасываем $this->_id,  чтобы никто по ошибке не отправлял запросы с одинаковыми ID
		$this->_id = null;

		$request = [
			'jsonrpc' => '2.0',
			'method'  => $name,
			'params'  => $arguments,
			'id'      => $id
		];

		$jsonRequest = json_encode($request);

		$headers = "Content-Type: application/json\r\n" . implode("\r\n", $this->_headers);

		$ctx = stream_context_create(
			[
				'http' => [
					'method'  => 'POST',
					'header'  => $headers,
					'content' => $jsonRequest
				],
				'ssl'  => [
					'verify_peer'      => false,
					'verify_peer_name' => false
				]
			]
		);

		$jsonResponse = '';

		try {
			if ($fp = fopen($this->_url, 'r', false, $ctx)) {
				while ($line = fgets($fp)) {
					$jsonResponse .= trim($line) . "\n";
				}

				fclose($fp);
			}
		} catch (\Exception $e) {
			if (isset($fp) && $fp !== false) {
				fclose($fp);
				throw $e;
			}
		}

		if ($jsonResponse === '') {
			throw new JsonRpcException("{$this->_url} fopen failed", JsonRpcException::INTERNAL_ERROR);
		}

		$response = json_decode($jsonResponse);

		if ($response === null) {
			throw new JsonRpcException('JSON cannot be decoded', JsonRpcException::INTERNAL_ERROR);
		}

		if (!Environment::isTest() && $response->id != $id) {
			throw new JsonRpcException('Mismatched JSON-RPC IDs', JsonRpcException::INTERNAL_ERROR);
		}

		if (property_exists($response, 'error')) {
			$this->handleError($response);
		} else {
			if (property_exists($response, 'result')) {
				return $response->result;
			}
		}

		throw new JsonRpcException('Invalid JSON-RPC response', JsonRpcException::INTERNAL_ERROR);
	}

	/**
	 * Обработка ошибок в ответе сервера
	 *
	 * @param object $response
	 *
	 * @throws
	 */
	protected function handleError($response)
	{
		throw new JsonRpcException($response->error->message, $response->error->code, $response->error->data);
	}
}
