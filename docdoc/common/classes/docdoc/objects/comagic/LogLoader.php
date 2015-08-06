<?php
/**
 * Created by PhpStorm.
 * User: a.tyutyunnikov
 * Date: 04.08.14
 * Time: 16:57
 */

namespace dfs\docdoc\objects\comagic;

use Curl;

/**
 * Клас для загрузки логов comagic
 * Class LogLoader
 *
 * @package dfs\docdoc\objects\comagic
 */
class LogLoader
{
	/**
	 * @var string
	 */
	protected $url;

	/**
	 * @var string
	 */
	protected $login;

	/**
	 * @var string
	 */
	protected $password;

	/**
	 * @var string
	 */
	protected $sessionkey;

	/**
	 * @var string
	 */
	protected $customerId;

	/**
	 * Конструктор
	 *
	 * @param string $url
	 * @param string $login
	 * @param string $password
	 * @param string $customerId
	 */
	public function __construct($url, $login, $password, $customerId)
	{
		$this->url = $url;
		$this->login = $login;
		$this->password = $password;
		$this->customerId = $customerId;
	}

	/**
	 * Логин в систему
	 *
	 * @throws \CException
	 */
	protected function login()
	{
		$curl = new Curl();
		$response = $curl->post($this->url . '/api/login/', ['login' => $this->login, 'password' => $this->password]);
		$json = $this->json_decode($response->body, true);

		if (!isset($json['data']['session_key'])) {
			throw new \CException('При аутентификации не получен ожидаемый ответ');
		}

		$this->sessionkey = $json['data']['session_key'];
	}

	/**
	 * Скачивает актуальные логи к нам в базу
	 *
	 * @param string $dateFrom
	 * @param string $dateTill
	 *
	 * @return string[]
	 * @throws \CException
	 */
	public function loadLogs($dateFrom, $dateTill)
	{
		$this->login();

		$curl = new Curl();

		$params =
			[
				'session_key' => $this->sessionkey,
				'date_from'   => $dateFrom,
				'date_till'   => $dateTill,
				'customer_id' => $this->customerId,
			];

		$response = $curl->get($this->url . '/api/v1/call/', $params);
		$json = $this->json_decode($response->body, true);

		if (!isset($json['success']) || !isset($json['data'])) {
			throw new \CException('Не получен ожидаемый ответ ' . $response->body);
		}

		if (!$json['success']) {
			throw new \CException('Не суцес!!! ' . $response->body);
		}

		return $json['data'];
	}

	/**
	 * Вынести бы куданить эту ф-ю глобально
	 *
	 * @param string $text
	 * @param bool   $assoc
	 *
	 * @return mixed
	 * @throws \CException
	 */
	protected function json_decode($text, $assoc = true)
	{
		$json = json_decode($text, $assoc);

		switch (json_last_error()) {
			case JSON_ERROR_NONE:
				$error = null;
				break;
			case JSON_ERROR_DEPTH:
				$error = 'Достигнута максимальная глубина стека';
				break;
			case JSON_ERROR_STATE_MISMATCH:
				$error = 'Некорректные разряды или не совпадение режимов';
				break;
			case JSON_ERROR_CTRL_CHAR:
				$error = 'Некорректный управляющий символ';
				break;
			case JSON_ERROR_SYNTAX:
				$error = 'Синтаксическая ошибка, не корректный JSON';
				break;
			case JSON_ERROR_UTF8:
				$error = 'Некорректные символы UTF-8, возможно неверная кодировка';
				break;
			default:
				$error = 'Неизвестная ошибка';
				break;
		}

		if (!is_null($error)) {
			throw new \CException($error);
		}

		return $json;
	}
} 
