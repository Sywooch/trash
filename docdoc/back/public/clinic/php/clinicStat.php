<?php
	require_once dirname(__FILE__)."/../../lib/php/validate.php";
	
	function getClinicStatisticXML ($type='clinic', $cityId = 1) {
		$xml = "";
		
		$type = checkField ($type, "e", "", false, array("clinic","center","privatDoctor") );
		
		$status = getStatusClinicArray();
		
		$xml .= "<ClinicStat type=\"".$type."\">";
		foreach ($status as $key => $value) {
			
			switch ($type) {
				case 'clinic' : $sqlAdd = " AND isClinic = 'yes' "; break;
				case 'center' : $sqlAdd = " AND isDiagnostic = 'yes' "; break;
				case 'privatDoctor' : $sqlAdd = " AND isPrivatDoctor = 'yes' "; break;
				default: $sqlAdd = "";
			}
			
			$sql = "SELECT count(*) as cnt FROM `clinic` 
					WHERE city_id = ".$cityId." AND status = ".$key." ".$sqlAdd;
			//echo $sql."<br/>";
			$result = query($sql);
			if (num_rows($result) == 1 ) {
				$row = fetch_object($result);
				$xml .= "<Element status=\"".$key."\">".$row -> cnt."</Element>";
			}
		} 
		$xml .= "</ClinicStat>";
		
		return $xml;
	}
	
	
	function getStatusClinicArray () {
		$status = array();
	
		$status[1] = "Регистрация";
		$status[2] = "Новая";
		$status[3] = "Активная";
		$status[4] = "Заблокирована";
		$status[5] = "К удалению";
	
		return $status;
	}
	
	
	
	function getStatusDict4ClinicXML () {
		$xml = "";


		$xml .= "<StatusDict  mode='clinicDict'>";
		$statusArray = getStatusClinicArray();
		foreach ($statusArray as $key => $status ){
			$xml .= "<Element id=\"".$key."\">".$status."</Element>";
		}
		$xml .= "</StatusDict>";
		
		return $xml;
	}

?>