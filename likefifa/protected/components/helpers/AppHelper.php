<?php

namespace likefifa\components\helpers;

/**
 * Хелпер со всякими разными вспомогательными штуками
 *
 * Class AppHelper
 *
 * @package likefifa\components\helpers
 */
class AppHelper
{
	/**
	 * Загружает что-то из удаленного источника
	 *
	 * @param string $url
	 *
	 * @return mixed
	 */
	public static function getByUrl($url)
	{
		$ch = curl_init();
		$timeout = 5;
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		$data = curl_exec($ch);

		$http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);

		if ($http_status != 200) {
			return false;
		}

		return $data;
	}
} 