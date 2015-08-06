<?php
	require_once dirname(__FILE__)."/../../include/common.php";
	require_once dirname(__FILE__)."/../../lib/php/validate.php";
	
	$myAddress	= (isset($_GET['address'])) ? checkField($_GET['address'], "t", "") : '';
	


	if ( !empty($myAddress) ) {
		$getAddress= new address($myAddress);
		$getAddress -> getAddress();
		
		$latitude = $getAddress -> map_latitude;
		$longitude = $getAddress -> map_longitude;
		
		echo htmlspecialchars(json_encode(array('status'=>'success','latitude'=>$latitude,'longitude'=>$longitude)), ENT_NOQUOTES);
	} else {
		echo htmlspecialchars(json_encode(array('status'=>'error','error'=>'Не передан адрес')), ENT_NOQUOTES);
	}

	

class address {
	public $address_map;	
	public $map_latitude;	
	public $map_longitude;	
	
	function __construct($address) {
		$this -> address_map = $address; 
	}
	
	function getAddress() {
		$params = array(
	      'geocode' => $this -> address_map, // адрес
	      'format'  => 'json',                          // формат ответа
	      'results' => 1,                               // количество выводимых результатов
	      'key'     => YandexAPIkey
	  	);
	  
	  	$response = json_decode(file_get_contents('http://geocode-maps.yandex.ru/1.x/?' . http_build_query($params)));

	  	if ($response->response->GeoObjectCollection->metaDataProperty->GeocoderResponseMetaData->found > 0){
			$codes = explode(' ', $response->response->GeoObjectCollection->featureMember[0]->GeoObject->Point->pos);

			if(!empty($codes)){
	    		$this->map_latitude = $codes[1];
				$this->map_longitude = $codes[0];
			}
		}
	}
	
}

?>