<?php
/**
 * Created by PhpStorm.
 * User: a.tyutyunnikov
 * Date: 14.07.14
 * Time: 10:43
 */

namespace dfs\docdoc\objects;

use dfs\docdoc\models\CallLogModel;

/**
 * Class Loader
 *
 * Класс загрузчик логов
 */
class Loader
{
	/**
	 * @var resource
	 */
	protected $_curl;

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
	protected $url;

	/**
	 * @param string $login
	 * @param string $password
	 * @param string $url
	 */
	public function __construct($login, $password, $url)
	{
		$this->login = $login;
		$this->password = $password;
		$this->url = $url;
	}

	/**
	 * @throws \Exception
	 */
	public function fetchAll()
	{
		$startTime = CallLogModel::model()->getMaxStartTime();

		if(is_null($startTime)){
			$startTime = date('Y-m-d', time());
		} else {
			$startTime = date('Y-m-d H:i:s', strtotime('-2 hour', strtotime($startTime)));
		}

		$this->_login();

		$limit = 100;
		$offset = 0;

		try {
			do {
				$res = $this->_fetch($startTime, null, $limit, $offset);

				$total = (int)$res['totalCount'];
				array_shift($res['rows']); //remove row with total info

				foreach ($res['rows'] as $row) {

					if ($row['start_time'] > $startTime) {
						// у них id у нас ext_id
						$row['ext_id'] = $row['id'];
						unset($row['id']);

						$callLog = new CallLogModel('insert');
						$callLog->attributes = $row;
						$callLog->save();
					}
				}

				$offset += $limit;

			} while ($offset < $total);
		} catch (\Exception $e) {
			throw $e;
		}
	}

	/**
	 * Возвращает лог звонков от Centrex
	 * За предыдущий день
	 *
	 * @return array
	 *
	 * @throws \CException
	 * @throws \Exception
	 */
	public function getCenrexAll()
	{
		$this->_login();

		$dateFrom = date('Y-m-d', strtotime('-1 day', time()));
		$dateTo = date('Y-m-d');
		$limit = 100;
		$offset = 0;
		$records = [];

		do {
			$res = $this->_fetch($dateFrom, $dateTo, $limit, $offset, true);
			$total = (int)$res['totalCount'];
			array_shift($res['rows']); //remove row with total info
			$records = array_merge($records, $res['rows']);
			$offset += $limit;
		} while ($offset < $total);

		return $records;
	}

	/**
	 * @return $this
	 */
	protected function _initCurl()
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_HEADER, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_COOKIESESSION, 1);
		$this->_curl = $ch;

		return $this;
	}

	/**
	 * @return $this
	 * @throws \CException
	 */
	protected function _login()
	{
		// open a site with cookies
		$this->_initCurl();

		curl_setopt($this->_curl, CURLOPT_URL, "{$this->url}/auth/login/");
		curl_setopt($this->_curl, CURLOPT_POSTFIELDS, ['login' => $this->login, 'password' => $this->password]);

		$response = curl_exec($this->_curl);

		// get cookies
		$cookies = array();

		preg_match('/^Set-Cookie:\s*([^;]*)/mi', $response, $m);

		parse_str($m[1], $cookies);

		if (!isset($cookies['sessionid'])) {
			throw new \CException('sessionid is not set in response');
		}

		$sessionid = $cookies['sessionid'];

		curl_setopt($this->_curl, CURLOPT_COOKIE, "sessionid=$sessionid;");

		return $this;
	}

	/**
	 * @param string $fromDate
	 * @param string $toDate
	 * @param int    $limit
	 * @param int    $start
	 * @param bool   $centrex
	 *
	 * @return array
	 * @throws \Exception
	 */
	protected function _fetch($fromDate, $toDate = null, $limit = 100, $start = 0, $centrex = false)
	{
		$query_params = [
			'start' => $start,
			'limit' => $limit,
			'date_from' => $fromDate,
			'sort' => 'start_time',
		];

		if ($toDate) {
			$query_params['date_to'] = $toDate;
		}

		$query_string = http_build_query($query_params);

		curl_setopt(
			$this->_curl,
			CURLOPT_URL,
			"{$this->url}/" . ($centrex ? 'centrex' : 'profile') . "/statistics/cdr_in/get_cdr_in/?$query_string"
		);

		$response = curl_exec($this->_curl);

		$headers_pattern = "/^.*\r\n\r\n/s";
		preg_match($headers_pattern, $response, $headers);
		$response = preg_replace($headers_pattern, '', $response);

		$response = rtrim($response, ')');
		$response = ltrim($response, '(');

		/**
		 * Удаляет ненужные символы ASCII
		 *
		 * @see http://www.asciitable.com/
		 */
		for ($i = 0; $i <= 31; ++$i) {
			$response = str_replace(chr($i), "", $response);
		}
		$response = str_replace(chr(127), "", $response);

		$json = json_decode($response, true);

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
			throw new \Exception($error);
		}

		if (!isset($json['totalCount']) || !isset($json['rows'])) {
			throw new \CException('Ответ uiscom отличается от ожидаемого');
		}

		return $json;
	}
} 
