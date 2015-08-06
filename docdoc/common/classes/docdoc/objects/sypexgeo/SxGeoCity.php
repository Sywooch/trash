<?php

namespace dfs\docdoc\objects\sypexgeo;

use Curl;
use Yii;

/**
 * Class SxGeoCity
 *
 * Объект для определения города по IP
 *
 * @package dfs\docdoc\objects\sypexgeo
 */
class SxGeoCity
{
	/**
	 * Получение
	 */
	const URL_SYPEXGEO_API = 'http://api.sypexgeo.net';

	const FORMAT_RESPONSE = 'json';

	/**
	 * Город по умолчанию
	 */
	const DEFAULT_CITY = 1;

	/**
	 * Массив соответствия региона и города
	 *
	 * @var array
	 */
	private $_cityPrefixes = [
		"Sankt-Peterburg"           => 2,
		"Leningradskaya Oblast'"    => 2,
		"Sverdlovskaya Oblast'"     => 4,
		"Novosibirskaya Oblast'"    => 5,
		"Perm Krai"                 => 6,
		"Nizhegorodskaya Oblast'"   => 7,
		"Tatarstan"                 => 8,
		"Samarskaya Oblast'"        => 9,
	];

	/**
	 * @var string
	 */
	private $_ip;

	/**
	 * @var string
	 */
	private $_url;

	/**
	 * @var int
	 */
	private $_timeout = 100;

	/**
	 * @param string $ip
	 */
	public function __construct($ip = '')
	{
		$this->_ip = $ip;

		$key = Yii::app()->params['sypexgeo']['key'];
		$url = self::URL_SYPEXGEO_API . "/" . (!empty($key) ? "{$key}/" : '') . self::FORMAT_RESPONSE . "/";
		$this->setUrl($url);
	}

	/**
	 * Получение идентификатора города
	 *
	 * @return integer
	 */
	public function getCity()
	{
		$curl = new Curl();
		$curl->options['CURLOPT_NOSIGNAL'] = 1;
		$curl->options['CURLOPT_TIMEOUT_MS'] = $this->_timeout;
		$curl->options['CURLOPT_RETURNTRANSFER'] = 1;
		try {
			$response = $curl->get($this->getUrl() . $this->_ip);
		} catch (\CurlException $e) {
			return self::DEFAULT_CITY;
		}

		if (empty($response->body)) {
			return self::DEFAULT_CITY;
		}

		$body = json_decode($response->body);
		$region = null;
		if (isset($body->region->name_en)) {
			$region = $body->region->name_en;
		}

		return isset($this->_cityPrefixes[$region]) ? $this->_cityPrefixes[$region] : self::DEFAULT_CITY;
	}

	/**
	 * Получение урла
	 *
	 * @return string
	 */
	public function getUrl()
	{
		return $this->_url;
	}

	/**
	 * Установка урла
	 *
	 * @param string $url
	 */
	public function setUrl($url)
	{
		$this->_url = $url;
	}

	/**
	 * Установка таймаута
	 *
	 * @param $timeout
	 */
	public function setTimeout($timeout)
	{
		$this->_timeout = $timeout;
	}

}
