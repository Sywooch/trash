<?php
use dfs\docdoc\models\RequestModel;

class request {
	public $id;	
	public $attributes = array();
	
	
	
	
	/*	Определяем начальное состояние самого себя	*/
    function __construct() {
    	$this -> id = 0;
		$this -> attributes['id'] = 0;
		$this -> attributes['RequestId'] = $this -> attributes['Request'] = 0;
		
		
		$this -> attributes['Doctor']['name'] = "";
		$this -> attributes['Doctor']['id'] = 0;
		
		$this -> attributes['Sector']['name'] = "";
		$this -> attributes['Sector']['id'] = 0;
		
		$this -> attributes['Clinic']['name'] = "";
		$this -> attributes['Clinic']['address'] = "";
		$this -> attributes['Clinic']['id'] = 0;
		
		$this -> attributes['CrDate'] = "";
		$this -> attributes['CrTime'] = "";
		
		$this -> attributes['ClientName']= "";
		$this -> attributes['Client']['name'] = "";
		$this -> attributes['Client']['id'] = 0;
		$this -> attributes['Client']['phone'] = "";
		$this -> attributes['Client']['phoneDig'] = "";
		$this -> attributes['ClientPhone'] = "";
		
		$this -> attributes['IsGoHome'] = 0;
		$this -> attributes['AgeSelector'] = "";
		
		$this -> attributes['AppointmentStatus'] = "";
		$this -> attributes['DateAdmission'] = "";
		$this -> attributes['AppointmentDate'] = "";
		$this -> attributes['AppointmentDateTime'] = "";
		$this -> attributes['AppointmentTime']['Hour'] = ""; 
		$this -> attributes['AppointmentTime']['Min'] = "";
		
		$this -> attributes['CallLater'] = "";
		$this -> attributes['CallLaterDate'] = "";
		$this -> attributes['CallLaterTime'] = "";
		$this -> attributes['RemainTime'] = "";

		$this -> attributes['Owner']['Name'] = "";
		$this -> attributes['Owner']['Id'] = 0;
		
		
		$this -> attributes['Status'] = "";
		$this -> attributes['SMSstatus'] = "";
		$this -> attributes['Type'] = "";
		$this -> attributes['CityId'] = "";
		
		$this -> attributes['ClientComment'] = "";
		
		$this -> attributes['Records'] = array();
    }
	
	
	
	/*	Список клиник. в которых принимает врач  */
    function getClinic4Doctor() {
		
    }
    
	/*	Записи разговоров  */
    function getAudioList() {
    	if (!empty($this -> id) ) {
	   		$sql = "SELECT
							t1.request_id as id, t1.record, 
							DATE_FORMAT( t1.crDate,'%d.%m.%Y') AS crDate,
							DATE_FORMAT( t1.crDate,'%d.%m.%Y %H.%i') AS crDateTime,
							t1.duration, t1.comments as note, t1.isOpinion
						FROM request_record t1
						WHERE 
							t1.request_id = ".$this -> id."
						ORDER BY record";
			//echo $sql."<br/>";
			$result = query($sql);
			if (num_rows($result) > 0 ) {
				while ($row = fetch_object($result)) {
					$record = array();
					$record['Path'] = $row -> record;
					$record['IsOpinion'] = $row -> isOpinion;
					$record['CrDate'] = $row -> crDate;
					$record['CrDateTime'] = $row -> crDateTime;
					array_push($this -> attributes['Records'], $record);
				}
			}
			
    	}
    }
    
	/*	Лог действий  */
    function getLogHistory() {
		
    }	
	
	
	
	function getRequest ($id) {
		$id = intval ($id);
		
		if ( $id > 0 ) {
				$sql = "SELECT
						t1.req_id as id, 
						t1.clinic_id, 
						t1.client_name, t1.client_phone,
						t1.req_created, t1.req_status as status, t1.req_type, t1.req_sector_id, 
						t1.clientId, t1.call_later_time,t1.req_departure as isGoHome,
						t1.req_doctor_id as doctor_id, t2.name as doctor, t1.req_sector_id, 
						t1.req_user_id as owner, t3.user_lname, t3.user_fname, t3.user_email,
						t4.name as sector,
						t1.date_admission, t1.appointment_status, t2.status as doctorStatus, 
						cl.id as clinicId, cl.name as clinic, 
						concat (cl.street, ', ', cl.house) as clinicAddress,
						t1.client_comments, t1.age_selector, t1.status_sms, t1.id_city
					FROM request  t1
					LEFT JOIN doctor t2 ON (t2.id = t1.req_doctor_id)
					LEFT JOIN `user` t3 ON (t3.user_id = t1.req_user_id)
					LEFT JOIN `clinic` cl ON (cl.id = t1.clinic_id)
					LEFT JOIN sector t4 ON (t4.id = t1.req_sector_id)
					WHERE 
						req_id = ".$id;
				//echo $sql;
				$result = query($sql);
				if (num_rows($result) == 1) {
					$row = fetch_object($result);
					
					$this -> id = $row -> id;
					
					$this -> attributes['id'] 				= $row -> id;
					$this -> attributes['Request'] 			= $row -> id;
					$this -> attributes['RequestId'] 		= $row -> id;
					
					$this -> attributes['Doctor']['name'] 	= $row -> doctor;
					$this -> attributes['Doctor']['id'] 	= $row -> doctor_id;
					$this -> attributes['Doctor']['status'] = $row -> doctorStatus;
					
					$this -> attributes['Sector']['name'] 	= $row -> sector;
					$this -> attributes['Sector']['id'] 	= $row -> req_sector_id;
					
					$this -> attributes['Clinic']['name'] 	= $row -> clinic;
					$this -> attributes['Clinic']['id'] 	= $row -> clinic_id;
					$this -> attributes['Clinic']['address'] 	= $row -> clinicAddress;
					
					$this -> attributes['CrDate'] 			= date("d.m.Y",$row -> req_created );
					$this -> attributes['CrTime'] 			= date("H:i",$row -> req_created );
					
					$this -> attributes['ClientName'] 		= $row -> client_name;
					$this -> attributes['Client']['name'] 	= $row -> client_name;
					$this -> attributes['Client']['id'] 	= $row -> clientId;
					$this -> attributes['Client']['phone'] 	= $row -> client_phone;
					$this -> attributes['Client']['phoneDig'] 	= formatPhone4DB($row -> client_phone);
					$this -> attributes['ClientPhone'] 		= formatPhone($row -> client_phone);
					
					$this -> attributes['IsGoHome'] 		= $row -> isGoHome;
					$this -> attributes['AgeSelector'] 		= $row -> age_selector;
					
					$this -> attributes['AppointmentStatus']= $row -> appointment_status;
					$this -> attributes['DateAdmission'] 	= $row -> date_admission;
					if ( !empty($row -> date_admission) ) {
						$this -> attributes['DateAdmission'] 			= $row -> date_admission;
						$this -> attributes['AppointmentDate'] 			= date("d.m.Y",$row -> date_admission );
						$this -> attributes['AppointmentDateTime']		= date("d.m.Y H:i",$row -> date_admission );
						$this -> attributes['AppointmentTime']['Data']	= date("H:i",$row -> date_admission );
						$this -> attributes['AppointmentTime']['Hour']	= date("H",$row -> date_admission);
						$this -> attributes['AppointmentTime']['Min'] 	= date("i",$row -> date_admission);
					}
					
					if ( !empty($row -> call_later_time) ) {
						$this -> attributes['CallLater'] 				= $row -> call_later_time;
						$this -> attributes['CallLaterDate'] 			= date("d.m.Y",$row -> call_later_time );
						$this -> attributes['CallLaterTime']['Data'] 	= date("H:i",$row -> call_later_time);
						$this -> attributes['CallLaterTime']['Hour'] 	= date("H",$row -> call_later_time);
						$this -> attributes['CallLaterTime']['Min'] 	= date("i",$row -> call_later_time);
						$this -> attributes['RemainTime'] 				= (mktime() - $row -> call_later_time);
					}
			
					$this -> attributes['Owner']['Name'] = $row -> user_lname." ".$row -> user_fname;
					$this -> attributes['Owner']['Id'] = $row -> owner;
					$this -> attributes['Author']= 'oper';
					
					$this -> attributes['Status'] 		= $row -> status;
					$this -> attributes['SMSstatus'] 	= $row -> status_sms;
					$this -> attributes['Type'] 		= $row -> req_type;
					$this -> attributes['CityId']		= $row -> id_city;
					
					$this -> attributes['ClientComment'] = $row -> client_comments;
				}
			}
		
		
	}
	
	
/*	Пстроение XML дерева	*/
	public function getXMLtree () {
		$xml = "";
		
		if ( count($this -> attributes) > 0 ) {
			$xml .= "<Request  id='".$this -> attributes['id']."'>";
			foreach ( $this -> attributes as $tagName => $data) {
				if ( is_array ( $data ) )  {
					if ( isset( $data['name']) && isset( $data['id']) ) {
						$xml .= "<".$tagName." id=\"".$data['id']."\">".$data['name']."</".$tagName.">";
					} else {
						$xml .= "<".$tagName ;
						foreach ( $data as $subNode => $subData ) {
							if (!is_array($subData)) {
								$xml .= " ".$subNode."=\"".$subData."\" ";
							} 
						}
						$xml .= "/>";
					}
				} else {
					$xml .= "<".$tagName.">";
					$xml .= $data;
					$xml .= "</".$tagName.">";
				}
			}
			$xml .= "</Request>";
		}
		
		return $xml;
	}

	/**
	 * изменение статуса заявки
	 *
	 * @param int $status
	 */
	public function setStatus($status)
	{
		if (!empty($this->id)) {
			$request = RequestModel::model()->findByPk($this->id);
			$request->saveStatus($status);
		}
	}
}
