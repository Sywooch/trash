<?php

namespace dfs\docdoc\api\components;

use CException;
use dfs\docdoc\api\BaseAPI;

/**
 * Class ApiFactory
 * @package dfs\docdoc\api
 */
class ApiFactory {

	/**
	 * Фабрика, создающая объект API
	 *
	 * @param string $url
	 * @param array $params
	 *
	 * @return BaseAPI
	 *
	 * @throws CException
	 */
	static public function getApi($url, $params = [])
	{
		$pattern = '~^/api(/(?P<type>(rest|rpc|soap)))?(/(?P<ver>([0-9]+.[0-9]+.[0-9]+)))?(/(?P<format>(json|xml)))?(?P<query>(.*))?~';
		preg_match($pattern, $url, $matches);

		$params = array_merge($params, [
			'type'      => $matches['type'] ?: 'rest',
			'version'   => $matches['ver'] ?: '1.0.0',
			'dataType'  => $matches['format'] ?: 'json',
			'query'     => $matches['query'],
		]);

		$params['rawData'] = isset($params['rawData']) ? $params['rawData'] : '';

		$apiClass = 'dfs\docdoc\api\\' . $params['type'] . '\API_v' . str_replace('.', '', $params['version']);
		if (class_exists($apiClass)) {
			$api = new $apiClass($params);
			return $api;
		}

		throw new CException("API version {$params['version']} not found!");
	}

}
