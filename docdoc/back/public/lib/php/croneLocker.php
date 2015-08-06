<?php
use dfs\docdoc\models\SmsQueryModel;

require_once 	dirname(__FILE__)."/../../include/common.php";
require_once 	dirname(__FILE__)."/../../lib/php/mail.php";

	function clearCroneLock ($croneName, $cronNameFile = LOCK_FILE_CRONE) {
		$confStr = readFileIntoArray($cronNameFile);
		
		for ($i=0; $i < count($confStr); $i++ ) 
			if ( isset($confStr[$i]) && count($confStr[$i]) > 2 && $confStr[$i][0] == $croneName ) {
				$confStr[$i][1] = 'true';
				$confStr[$i][2] = 0;
			}

		writeCroneLockFile($confStr, $cronNameFile, $croneName);
	}
	

	
	
	function croneLock ($croneName, $cronNameFile  = LOCK_FILE_CRONE) {
		$confStr = readFileIntoArray($cronNameFile);

		for ($i=0; $i < count($confStr); $i++ ) {
			if ( isset($confStr[$i]) && count($confStr[$i]) > 2 && $confStr[$i][0] == $croneName ) {
				$confStr[$i][1] = 'false';
				$confStr[$i][2] = intval($confStr[$i][2])+1;
			}
		}

		writeCroneLockFile($confStr, $cronNameFile, $croneName);
	}
        
        
        
        
	function readFileIntoArray ($file = LOCK_FILE_CRONE) {
		$strArray = array();
		
		if ( file_exists($file) ) {
			$data = file_get_contents($file); 
			$file_array = explode("\n", $data);
			for($i=0; $i < count($file_array); $i++) {
				$str = explode(":", $file_array[$i]);
				if ( count($str ) > 2 ) {
					array_push($strArray, array(trim($str[0]), trim($str[1]), trim($str[2])) );
				}
			}
			return $strArray;
		} else {
			return false;
		} 
	}

	
	
	
	function checkCroneLock ($croneName, $cronNameFile = LOCK_FILE_CRONE) {
		global $ADMIN_SMS_PHONE;

		$confStr = readFileIntoArray($cronNameFile);
		for ($i=0; $i < count($confStr); $i++ ) {
			$line = $confStr[$i];
			if ( isset($line) && count($line) > 2 && $line[0] == $croneName && $line[1] == 'true' ) {
				return true;
			} else if ( isset($line) && count($line) > 2 && $line[0] == $croneName && $line[1] == 'false' && intval($line[2]) < 3 ) {
				$confStr[$i][2] = intval($confStr[$i][2])+1; 
				writeCroneLockFile($confStr, $cronNameFile, $croneName);
		
				return false;
			} else if ( isset($line) && count($line) > 2 && $line[0] == $croneName && $line[1] == 'false' && intval($line[2]) == 3 ) {
				$params = array(
					"message" => date("d.m.Y H:i")."\n\r"."Ошибки при работе CRONE:$croneName. Server: ".SERVER_BACK,
					"subj" => "Crone $croneName error "
				);

				sendMessage($params["subj"], $params["message"], Yii::app()->params['email']['support']);
				
				$mailBody = "Процесс ".$croneName." остановлен. ".date("d.m.Y H:i");

				foreach ($ADMIN_SMS_PHONE as $phones ) {
					SmsQueryModel::sendSmsToNumber($phones, $mailBody, SmsQueryModel::TYPE_SYSTEM_MSG, true);
				}
				
				/*	фИКС выключения для крона */
				$confStr[$i][1] = true; 
				$confStr[$i][2] = 0;
				/* ************************************** */
				
				writeCroneLockFile($confStr, $cronNameFile, $croneName);
				return false;
			}
		}
		 
		return false;
	}
	
	
	
	
	function writeCroneLockFile ($confStr, $cronNameFile = LOCK_FILE_CRONE, $croneName  ) {
		$handle = fopen($cronNameFile, "w");
		@flock ($handle, LOCK_EX);

		for ($i=0; $i < count($confStr); $i++ ) {
                    
                    
			$line = $confStr[$i];
			fwrite ($handle, $line[0] .":".$line[1].":".$line[2]);
		}
		@flock ($handle, LOCK_UN);
		fclose($handle);
	}
        
	
	
	
// Сохраняем переменную или массив переменных в файл.
function saveCronStatusParam($paramsNew, $cronFileName, $croneName){
	$backlog = new backLog();
	$backlog -> setSource(__FILE__);
	$backlog -> log('Process saveCronStatusParam started', 'MESSAGE');
	
	$params = getCroneStatusParam('', $cronFileName);
	$params = array_merge($params, $paramsNew);
	
	//$log = new commonLog($croneName.".log", "Write config file. ".$cronFileName);
	
	$fileContent = '';
	foreach ($params as $var=>$value){
		$fileContent .= $var.':'.trim($value)."\r\n";
	}
	
	$backlog -> log('Try writing into '.$cronFileName, 'MESSAGE');
	if ( $handle = fopen($cronFileName, 'w') ) {
		@flock ($handle, LOCK_EX);
		fwrite ($handle, trim($fileContent));
		@flock ($handle, LOCK_UN);
		fclose($handle);
		$backlog -> log('Write data: '.str_replace("\r\n", " ", $fileContent), 'MESSAGE');
		$backlog -> log('Writing into '.$cronFileName. ' finished', 'MESSAGE');
	} else {
		$backlog -> log('File '.$cronFileName.' can`t opened', 'ERROR');
	}
	
	//$log = new commonLog($croneName.".log", "End of writing. ".$cronFileName);
}



// Получаем значения из конфига. Если имя параметра указано, то возвращает его значение. Если имя не задано, возвращает массив со всеми значениями всех параметров
function getCroneStatusParam($paramTargetName, $cronConfFileName, $default = ''){
	$backlog = new backLog();
	$backlog -> setSource(__FILE__);
	$backlog -> log('Process getCroneStatusParam started', 'MESSAGE');
	
	if ( $paramTargetName === '' && $default === '') $default = array();
	$result = $default;
	
	if ( file_exists($cronConfFileName) ){
		$paramsAll = file($cronConfFileName);

		foreach($paramsAll as $param){
			$param = explode(':', $param);
			$param[1] = trim($param[1]);
			
			if ($paramTargetName === '') { // Если имя переменной не указано, то возвращаем весь массив
				$result[$param[0]] = $param[1];
			} else {
				if($paramTargetName === $param[0]){
					return $param[1];
				}
			}
		}
	} else {
		$backlog -> log('File '.$cronConfFileName.' not exits' , 'ERROR');
	}
	return $result;
}
