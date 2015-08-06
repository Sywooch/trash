<?php
use dfs\docdoc\models\DoctorClinicModel;

	function getDoctorStatisticXML ($cityId = 1) {
		$xml = "";
		
		$status = getStatusDoctorArray();
		
		$xml .= "<DoctorStat>";
		foreach ($status as $key => $value) {
			
			$sql = "SELECT count( distinct d.id) as cnt 
					FROM `doctor` as d
					INNER JOIN doctor_4_clinic d4c ON (d.id = d4c.doctor_id and d4c.type = " . DoctorClinicModel::TYPE_DOCTOR . ")
					INNER JOIN clinic c ON (c.id = d4c.clinic_id)
					WHERE 
						c.city_id = ".$cityId." AND d.status = ".$key;
			//echo $sql."<br/>";
			$result = query($sql);
			if (num_rows($result) == 1 ) {
				$row = fetch_object($result);
				$xml .= "<Element status=\"".$key."\">".$row -> cnt."</Element>";
			}
		} 
		$xml .= "</DoctorStat>";
		
		return $xml;
	}
	
	
	function getStatusDoctorArray () {
		$status = array();
	
		$status[1] = "Регистрация";
		$status[2] = "Новый";
		$status[3] = "Активная";
		$status[4] = "Заблокирована";
		$status[5] = "К удалению";
		$status[6] = "На модерации";
		$status[7] = "Другой врач";
	
		return $status;
	}

?>
