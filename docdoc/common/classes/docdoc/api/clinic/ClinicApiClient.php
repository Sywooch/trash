<?php

namespace dfs\docdoc\api\clinic;

use StdClass;
use dfs\docdoc\api\JsonRpcClient;
use dfs\docdoc\exceptions\NotFoundException;

/**
 * Class ClinicApiClient
 */
class ClinicApiClient extends JsonRpcClient
{

	/**
	 * код ошибки, когда не найдена клиника, врач в клинике
	 */
	const NOT_FOUND_ERROR = -32404;

	/**
	 * создание клиента
	 *
	 * @return ClinicApiClient
	 */
	static public function createClient()
	{
		$apiParams = \Yii::app()->params['booking'];

		$jsonRpcClient = new self($apiParams['apiUrl']);
		$jsonRpcClient->setAuth($apiParams['login'], $apiParams['password']);

		return $jsonRpcClient;
	}

	/**
	 * Установка параметров авторизации
	 *
	 * @param string $login
	 * @param string $password
	 */
	public function setAuth($login, $password)
	{
		$this->addHeader('Authorization: Basic ' . base64_encode("{$login}:{$password}"));
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
		//отлавливаем NOT_FOUND_ERROR
		if ($response->error->code === self::NOT_FOUND_ERROR) {
			$e = new NotFoundException($response->error->message, $response->error->code);
			if (isset($response->error->data)) {
				$e->setDebugData($response->error->data);
			}
			throw $e;
		}

		parent::handleError($response);
	}

	/**
	 * Получение списка клиник
	 *
	 * @param array $params
	 * @return StdClass[]
	 */
	public function getClinics(array $params = [])
	{
		return $this->__call('getClinics', [$params]);
	}

	/**
	 * Получение списка врачей
	 *
	 * @param array $params
	 * @param string $type
	 *
	 * @return StdClass[]
	 */
	public function getResources(array $params, $type = '')
	{
		return $this->__call('getResources', [$params, $type]);
	}

	/**
	 * Получение списка слотов
	 *
	 * @param string[] $ids
	 * @param array $params
	 * @return StdClass[]
	 */
	public function getSlots(array $ids, array $params = [])
	{
		return $this->__call('getSlots', [$ids, $params]);
	}

	/**
	 * Бронь
	 *
	 * @param array $params
	 * @return StdClass
	 */
	public function book(array $params)
	{
		return $this->__call('book', [$params]);
	}

	/**
	 * Отмена брони
	 *
	 * @param string $externalBookId
	 * @return StdClass
	 */
	public function cancelBook($externalBookId)
	{
		return $this->__call('cancelBook', [$externalBookId]);
	}

	/**
	 * Статус брони
	 *
	 * @param string $externalBookId
	 * @return StdClass
	 */
	public function getBookStatus($externalBookId)
	{
		return $this->__call('getBookStatus', [$externalBookId]);
	}
}
