<?php
require_once	dirname(__FILE__)."/../dateTimeLib.php";

class DiagRequest {
    
    public $id = null;
    public $data;

    
    
    
    public function __construct( $id = null ) {
    	$id = intval($id); 
        if ( $id > 0 ) 
            $this -> getModel($id);
    }
    
    
    
	public function setId ($id) {
		$id = intval($id);
		 
        if ( $id > 0 ) 
            $this -> id = $id;
    }
    
    
    
    
    
    /*
     * Получение модели заявки по диагностике
     * @return array
     */
    public function getModel($id) {
    	$data = array();
    	$id = intval($id);
    	
        $sql = "SELECT 
                    t1.diag_req_id AS Id, 
                    t1.phone_from AS PhoneFrom, t1.phone_to AS PhoneTo,
                    t1.clinic_id AS ClinicId,
					DATE_FORMAT(t1.cr_date, '%d.%m.%Y') AS CrDate,
					DATE_FORMAT(t1.cr_date, '%H:%i:%s') AS CrTime,
					DATE_FORMAT(t1.date_admission, '%d.%m.%Y') AS AdmissionDate,
					DATE_FORMAT(t1.date_admission, '%H') AS AdmissionHour,
					DATE_FORMAT(t1.date_admission, '%i') AS AdmissionMin,
					t1.status AS Status, 
					t1.src_type AS Type,
					t1.diagnostica_id AS DiagnosticaId,
					t1.diagnostica_other AS DiagnosticaName,
					t1.patient_fio AS PatientFIO,
					t1.add_patient_phone AS AddPatientPhone,
					t2.name as ClinicName,
					t1.owner_id as OwnerId,
					t1.reject_id as RejectId
                FROM diag_request t1
                LEFT JOIN clinic t2 ON (t1.clinic_id = t2.id)
				LEFT JOIN diagnostica t3 ON (t3.id = t1.diagnostica_id)
                WHERE 
                	t1.diag_req_id = ".$id;
        /*
        CASE 
                        WHEN (t1.diagnostica_id IS NULL OR t1.diagnostica_id = 0) THEN t3.name
                        ELSE t1.diagnostica_other
                    END AS Diagnostica
          */
        $result = query($sql);
        if ( num_rows($result) == 1 ){
            $row = fetch_array($result);
            array_push($data, $row);
            $data[0]['PhoneToFormated'] = formatPhone($data[0]['PhoneTo']);
            $data[0]['PhoneFromFormated'] = formatPhone($data[0]['PhoneFrom']); 
            $data[0]['AddPatientPhoneFormated'] = formatPhone($data[0]['AddPatientPhone']);
             
            
            $this->data = $data[0];
            $this->id = $data[0]['Id'];

            $this->data['AudioList'] = $this->getDiagAudioList();
            $this->data['History'] = $this->getDiagHistory();
        } 
                  
        return $data;
    }
    

    
    
    
    
	public function getDiagAudioList () {
    	$records = array();
    	
    	if ( $this->id > 0 ) {
    		$sql = "SELECT 
	                    t1.record_id AS Id, 
	                    t1.diag_req_id AS RequestId,
	                    t1.file_name AS FileName,
						DATE_FORMAT(t1.record_datetime, '%d.%m.%Y') AS CrDate,
						DATE_FORMAT(t1.record_datetime, '%H:%i') AS CrTime,
						DATE_FORMAT(t1.record_datetime, '%d.%m.%Y %H:%i') AS CrDateTime,
						t1.duration AS Duration,
						t1.src_record AS Type,
						t1.is_appointment AS IsAppointment,
						t1.is_visit AS IsVisit
	                FROM diag_request_record t1
	                WHERE 
	                	t1.diag_req_id = ".$this->id."
	                ORDER BY t1.record_datetime DESC";
    	}
    	$result = query($sql);
        if ( num_rows($result) > 0 ){
        	$i = 0;
         	while( $row = fetch_array($result) ) {
         		array_push($records, $row);
         		$records[$i]['DurationSec'] = $row ['Duration'];
         		$records[$i]['DurationFormated'] = formatTime($row ['Duration']);
         		$i++;
         	}
        }
    		
    	return $records;	
    }
    
    
    
    
	public function getDiagHistory () {
    	$history = array();
    	
    	if ( $this->id > 0 ) {
    		$sql = "SELECT 
	                    t1.history_id AS Id, 
	                    t1.diag_req_id AS RequestId,
	                    t1.text AS Text,
						DATE_FORMAT(t1.cr_date, '%d.%m.%Y') AS CrDate,
						DATE_FORMAT(t1.cr_date, '%H:%i') AS CrTime,
						t1.action AS Type,
						t1.user_id AS UserId,
						t2.user_login as Nick,
						concat(t2.user_fname,' ',t2.user_lname) AS UserName  
	                FROM diag_request_history t1
	                LEFT JOIN `user` t2 ON (t1.user_id = t2.user_id)
	                WHERE 
	                	t1.diag_req_id = ".$this->id."
	                ORDER BY t1.cr_date DESC, t1.history_id DESC";
    	}
    	$result = query($sql);
        if ( num_rows($result) > 0 ){
         	while( $row = fetch_array($result) ) {
         		array_push($history, $row);
         	}
        }
    		
    	return $history;	
    }
    
    
    
    
		
	/**
     * 
     * Метод добавления записи в историю изменений по заявке
     * @param $text - текст сообщения
     * @param $action = bot | human
     * @param $userId = Id пользователя 
     */
    public function modifyData($params = array() ) {

    	if ( $this->id > 0 && count($params) > 0 ) {
    		$sqlAdd = "";
    		
    		if ( !empty($params['clientPhone'])  ) { 
    			$sqlAdd .= " add_patient_phone = '".$params['clientPhone']."', ";
    		} else {
				$sqlAdd .= " add_patient_phone = NULL, ";
			}
			
			if ( empty($params['clientName'])  ) {
				$sqlAdd .= " patient_fio = NULL, ";
			} else {
				$sqlAdd .= " patient_fio = '".$params['clientName']."', ";
			}

			
			if ( !empty($params['owner'])  ) {
				$sqlAdd .= " owner_id = ".$params['owner'].", ";
			}
			
    		if ( !empty($params['reject_id'])  ) {
				$sqlAdd .= " reject_id = ".$params['reject_id'].", ";
			} else {
				$sqlAdd .= " reject_id = NULL, ";
			}
			
			
			if ( $params['diagnosticaId'] > 0  ) {
				$sqlAdd .= " diagnostica_other = NULL, diagnostica_id = ".$params['diagnosticaId'].", ";
			} else if ( !empty($params['diagnosticaName']) ) {
				$sqlAdd .= " diagnostica_other = '".$params['diagnosticaName']."', diagnostica_id = 0, ";
			} else {
				$sqlAdd .= " diagnostica_other = NULL, diagnostica_id = 0, ";
			}
			
			if ( !empty($params['admissionDate'])  ) { 
				$apointmentHour = ( isset($params['apointmentHour']) ) ? $params['apointmentHour'] : '00';
				$apointmentMin = ( isset($params['apointmentMin']) ) ? $params['apointmentMin'] : '00';
				$sqlAdd .= " date_admission = '".convertDate2DBformat($params['admissionDate'])." ".$apointmentHour.":".$apointmentMin."' ";
			} else {
				$sqlAdd .= " date_admission = NULL ";
			}
	
			
			$sql = "UPDATE `diag_request` SET
						".$sqlAdd."
					WHERE diag_req_id = ".$this->id;
			//echo $sql;
			$result = query($sql);
			if (!$result) return false;
			
    		return true;
    	}
    	 
    	return false;
    }
    
    
    
/**
     * 
     * Метод добавления записи в историю изменений по заявке
     * @param $text - текст сообщения
     * @param $action = bot | human
     * @param $userId = Id пользователя 
     */
    public function setStatus($status) {
    	$status = intval($status);
    	
    	if ( $this->id > 0 && $status >= 0 ) {
			$sql = "UPDATE `diag_request` SET
						status = ".$status."
					WHERE diag_req_id = ".$this->id;
			//echo $sql;
			$result = query($sql);
			if (!$result) return false;
			
    		return true;
    	}
    	 
    	return false;
    }

    
    
    
    
    /**
     * 
     * Метод добавления записи в историю изменений по заявке
     * @param $text - текст сообщения
     * @param $action = bot | human
     * @param $userId = Id пользователя 
     */
    public function addHistory($text, $action = 'bot', $userId = 0) {

    	if ( $this->id > 0 ) {
    		$sql = "INSERT INTO diag_request_history SET
    					diag_req_id = ".$this->id.",
    					text = '".$text."',
    					cr_date = NOW(),
    					action = '".$action."',
    					user_id = ".$userId;
    		$result = query($sql);
    		$id = legacy_insert_id();
    		return $id;
    	}
    	 
    	return false;
    }
    
    
    
	public function addRecord ($fileName, $params = array()) {
		
		$srcRecord	= ( isset($params['srcRecord']) )? $params['srcRecord'] : 'bot';
		$duration	= (isset($params['duration']) && intval($params['duration']) > 0)? intval($params['duration']) : 'null';
		$recordDatetime = (isset($params['crDateTime']) && $params['crDateTime'] )? "'".$params['crDateTime']."'" : 'null';

    	if ( $this->id > 0 && !empty($fileName) ) {
    		$sql = "	INSERT INTO diag_request_record SET
							diag_req_id	= ".$this->id.",
							file_name	= '".$fileName."',
							record_datetime = ".$recordDatetime.",
							cr_date		= NOW(),
							duration	= ".$duration.",
							src_record	= '".$srcRecord."'";
    		
			$result = query($sql);
			$id = legacy_insert_id();
			
    		return $id;
    	}
    	 
    	return false;
    }
    
    
    
    
    public function modifyAudiodata ($isAppointment = array(), $isVisit = array()) {
    	if ( $this->id > 0 ) {
    		if (count($this->data['AudioList']) >  0) {
    			foreach ($this->data['AudioList'] as $row) {
    				$id = $row['Id'];
    				if ( isset($isAppointment[$id]) && $isAppointment[$id] == 'yes' ) { 
    					$sql = "UPDATE diag_request_record SET is_appointment = 'yes' WHERE record_id = ".$id;
    				} else {
    					$sql = "UPDATE diag_request_record SET is_appointment = 'no' WHERE record_id = ".$id;
    				}
    				
    				$result = query($sql);
    				if ( isset($isVisit[$id]) && $isVisit[$id] == 'yes' ) { 
    					$sql = "UPDATE diag_request_record SET is_visit = 'yes' WHERE record_id = ".$id;
    				} else {
    					$sql = "UPDATE diag_request_record SET is_visit = 'no' WHERE record_id = ".$id;
    				}
    				$result = query($sql);
    			}
    			
    		}
		
    		return true;
    	}
    }
    
    
     
    
    public function createDiagRequest ( $params = array() ) {
    	
    	$phoneTo 	= $params['phoneTo'] ;
		$phoneFrom	= (isset($params['phoneFrom']) && !empty($params['phoneFrom']))? $params['phoneFrom'] : 'null';
		$crDateTime	= (isset($params['crDateTime']) && !empty($params['crDateTime']))? "'".$params['crDateTime']."'" : 'NOW()';
		
		$srcType 	= (isset($params['srcType']) && !empty($params['srcType']))? $params['srcType'] : 'phone';
		$clinicId	= (isset($params['clinicId']) && intval($params['clinicId']) > 0 )? intval($params['clinicId']) : 'null';
		$cityId	= (isset($params['cityId']) && $params['cityId'] > 0 ) ? $params['cityId'] : 1;
		
    	if (count($params) > 0 && $phoneTo ) {
			$sql = "	INSERT INTO diag_request SET
							phone_from 	= '".$phoneFrom."',
							phone_to	= '".$phoneTo."',
							status		= 0,
							cr_date		= ".$crDateTime.",
							src_type	= '".$srcType."',
							clinic_id	= ".$clinicId.",
							city_id		= ".$cityId;
			//echo $sql."<br>";
			$result = query($sql);
			$id = legacy_insert_id();
			$this -> setId ($id);
			return  $id;
		}
		
    	return false;
    }
    

}


function getStatusDict () {
	$status = array();

	array_push($status, array("Id"=> 0, "Title" => "Новая",					"Sort"=>1,	"Visibility" => "show"));
	array_push($status, array("Id"=> 1, "Title" => "В обработке",			"Sort"=>2,	"Visibility" => "show"));
	array_push($status, array("Id"=> 7, "Title" => "Пациент дошёл",			"Sort"=>4,	"Visibility" => "show"));
	array_push($status, array("Id"=> 3, "Title" => "Отказ",					"Sort"=>5,	"Visibility" => "show"));
	array_push($status, array("Id"=> 4, "Title" => "Отклонена партнёром", 	"Sort"=>6,	"Visibility" => "hide"));
	array_push($status, array("Id"=> 5, "Title" => "Удалена", 				"Sort"=>7,	"Visibility" => "hide"));
	array_push($status, array("Id"=> 6, "Title" => "Оплачена", 				"Sort"=>8,	"Visibility" => "hide"));
	array_push($status, array("Id"=> 2, "Title" => "Пациент записан", 		"Sort"=>3,	"Visibility" => "show"));
	array_push($status, array("Id"=> 12, "Title" => "Не пришел на прием", 	"Sort"=>9,	"Visibility" => "show"));
	array_push($status, array("Id"=> 13, "Title" => "Условно завершена", 	"Sort"=>10,	"Visibility" => "show"));

	return $status;
}




function getRjectDict ($sort = true) {
	$data = array();

	
	array_push($data, array("Id"=> 1, "Title" => "Оператор не ответил"));
	array_push($data, array("Id"=> 2, "Title" => "Звонок сорвался"));
	array_push($data, array("Id"=> 3, "Title" => "Уточнение данных"));
	array_push($data, array("Id"=> 4, "Title" => "Клиника не записала / не работает"));
	array_push($data, array("Id"=> 5, "Title" => "Временно нет  услуги"));
	array_push($data, array("Id"=> 6, "Title" => "Услуга не предоставляется"));
	array_push($data, array("Id"=> 7, "Title" => "Не устраивает оборудование"));
	array_push($data, array("Id"=> 8, "Title" => "Не устраивает время записи"));
	array_push($data, array("Id"=> 9, "Title" => "Не устраивает адрес клиники"));
	array_push($data, array("Id"=> 10, "Title" => "Не подошла стоимость"));
	array_push($data, array("Id"=> 11, "Title" => "Не проходит по возрасту"));
	array_push($data, array("Id"=> 12, "Title" => "Живая очередь"));
  	array_push($data, array("Id"=> 0, "Title" => "Другое"));
  
	if ($sort )
		ksort($data);
	return $data;
}



?>