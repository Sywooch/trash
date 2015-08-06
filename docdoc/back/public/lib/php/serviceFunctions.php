<?php

function arrayToXML($data) {
    $xml = '';
    
    if(array_key_exists(0, $data)) {

        foreach($data as $item){
            if(isset($item['Id']))
                $xml .= '<Element id="'.$item['Id'].'">';
            else
                $xml .= '<Element>';
	
	        if (isset($item) && count($item) > 0 && is_array($item) )
		        foreach ( $item as $attr => $val ) {
		            $attr = ucfirst($attr);
		            if  (is_array($val)){
		                $xml .= '<'.$attr.'>'.arrayToXML($val).'</'.$attr.'>';
		            } else {
		                if(is_numeric($val))
		                    $xml .= '<'.$attr.'>'.$val.'</'.$attr.'>';
		                else
		                    $xml .= '<'.$attr.'><![CDATA['.$val.']]></'.$attr.'>';
		            }
		        }
	        
	        $xml .= '</Element>';
        }
        
    } else {
    	if (isset($data ) && count($data) > 0 )
	        foreach($data as $attr => $val){
	            $attr = ucfirst($attr);
	            if(is_array($val)){
	                $xml .= '<'.$attr.'>'.arrayToXML($val).'</'.$attr.'>';
	            } else {
	                if(is_numeric($val))
	                    $xml .= '<'.$attr.'>'.$val.'</'.$attr.'>';
	                else
	                    $xml .= '<'.$attr.'><![CDATA['.$val.']]></'.$attr.'>';
	            }
	        }
    }
    
    return $xml;
}

/**
 * @param string $name
 * @param array $data
 *
 * @return string
 */
function dictionaryXML($name, $data) {
	$xml = '<' . $name . '>';
	foreach ($data as $key => $value) {
		$xml .= '<Element id="' . $key . '">' . $value . '</Element>';
	}
	$xml .= '</' . $name . '>';
	return $xml;
}

function parseRequestHeaders() {
    $headers = array();
    foreach($_SERVER as $key => $value) {
        if (substr($key, 0, 5) <> 'HTTP_') {
            continue;
        }
        $header = str_replace(' ', '-', ucwords(str_replace('_', ' ', strtolower(substr($key, 5)))));
        $headers[$header] = $value;
    }
    return $headers;
}

function getData($url) {
	$ch = curl_init();
	curl_setopt_array($ch, array(
			CURLOPT_URL => $url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_HEADER => false,
			CURLOPT_HTTPHEADER => array('Content-type: application/json'),
			CURLOPT_POST => false
		));

	$response = json_decode(curl_exec($ch));
	curl_close($ch);

	$objects = $response->response->GeoObjectCollection->featureMember;

	return $objects;
}

/**
 * Конвертация и загрузка аудиозаписей на фтп
 *
 * @param $file4Convert
 *
 * @return bool
 */
function convertWavToMp3($file4Convert)
{
	if (empty($file4Convert)) {
		Yii::log("convertWavToMp3: Empty params", CLogger::LEVEL_ERROR);
		return false;
	}

	if (!file_exists($file4Convert)) {
		Yii::log("convertWavToMp3: Error file not exists: {$file4Convert}", CLogger::LEVEL_ERROR);
		return false;
	}

	$file = pathinfo($file4Convert);
	$dir = $file['dirname'];

	$srcFile = $file4Convert;
	$targetFileName = $file['filename'] . ".mp3";
	$targetFile = $dir . DIRECTORY_SEPARATOR . $targetFileName;

	$command = "lame --silent " . escapeshellarg($srcFile) . " " . escapeshellarg($targetFile);

	if (!file_exists($targetFile)) {
		Yii::log("Try convert " . $srcFile . " into " . $targetFile);
		Yii::log("command: {$command}");

		$lastLine = system($command, $retval);
		if ($retval != 0) {
			Yii::log("Convert error: {$lastLine}");
		} else {
			chmod($targetFile, FILE_MODE);
		}
	} else {
		Yii::log("File " . $targetFile . " already exist");
	}

	if (file_exists($targetFile) && filesize($targetFile) > 128) { // what is minimum
		return $targetFile;
	}

	return false;
}
