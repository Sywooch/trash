<?php
class RemoteAPI {

	protected static $instance = null;
	
	/**
	 * @return RemoteAPI
	 */
	public static function me() {
		if (self::$instance === null) {
			self::$instance = new self;
		}
		
		return self::$instance;
	}
	
	public function call($method, array $params) {
		$ch = curl_init();
		curl_setopt_array($ch, array(
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_FRESH_CONNECT => true,
			CURLOPT_CRLF => true,
			CURLOPT_URL => REMOTE_API_HOST.$method,
			CURLOPT_HEADER => true,
			CURLOPT_POST => true,
			CURLOPT_POSTFIELDS => http_build_query($params),
		));
		
		$response = curl_exec($ch);
		curl_close($ch);
		
		return $response;
	}
	
	public function get($url){
		$ch = curl_init();
		curl_setopt_array($ch, array(
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_FRESH_CONNECT => true,
			CURLOPT_CRLF => true,
			CURLOPT_URL => $url,
			CURLOPT_HEADER => true,
			CURLOPT_POST => false,
		));
		
		$response = curl_exec($ch);
		curl_close($ch);
		
		return $response;
	}

}