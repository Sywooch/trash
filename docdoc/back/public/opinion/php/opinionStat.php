<?php
use dfs\docdoc\models\DoctorClinicModel;

	function getOpinionStatisticXML ($cityId = 1) {
		$xml = "";
		
		$xml .= "<OpinionStat>";
			// Опубликовано всего
			$sql = "SELECT count( distinct do.id) as cnt 
					FROM doctor_opinion do
					INNER JOIN doctor_4_clinic d4c ON (d4c.doctor_id = do.doctor_id and d4c.type = " . DoctorClinicModel::TYPE_DOCTOR . ")
					INNER JOIN clinic c ON (c.id = d4c.clinic_id)
					WHERE 
						c.city_id = ".$cityId." AND do.allowed = 1";
			//echo $sql."<br/>";
			$result = query($sql);
			if (num_rows($result) == 1 ) {
				$row = fetch_object($result);
				$xml .= "<Element status=\"publish\">".$row -> cnt."</Element>";
			}
			
			// Опубликовано ОРИГИНАЛЬНЫХ
			$sql = "SELECT count( distinct do.id) as cnt 
					FROM doctor_opinion do
					INNER JOIN doctor_4_clinic d4c ON (d4c.doctor_id = do.doctor_id and d4c.type = " . DoctorClinicModel::TYPE_DOCTOR . ")
					INNER JOIN clinic c ON (c.id = d4c.clinic_id)
					WHERE 
						c.city_id = ".$cityId." 
						AND do.allowed = 1
						AND do.origin = 'original'";
			//echo $sql."<br/>";
			$result = query($sql);
			if (num_rows($result) == 1 ) {
				$row = fetch_object($result);
				$xml .= "<Element status=\"original\">".$row -> cnt."</Element>";
			}
			
			
			// Опубликовано РЕДАКТОРСКИХ
			$sql = "SELECT count( distinct do.id) as cnt 
					FROM doctor_opinion do
					INNER JOIN doctor_4_clinic d4c ON (d4c.doctor_id = do.doctor_id and d4c.type = " . DoctorClinicModel::TYPE_DOCTOR . ")
					INNER JOIN clinic c ON (c.id = d4c.clinic_id)
					WHERE 
						c.city_id = ".$cityId." 
						AND do.allowed = 1
						AND do.origin = 'editor'";
			//echo $sql."<br/>";
			$result = query($sql);
			if (num_rows($result) == 1 ) {
				$row = fetch_object($result);
				$xml .= "<Element status=\"editor\">".$row -> cnt."</Element>";
			}
			
			
			
			// Опубликовано C САЙТА
			$sql = "SELECT count( distinct do.id) as cnt 
					FROM doctor_opinion do
					INNER JOIN doctor_4_clinic d4c ON (d4c.doctor_id = do.doctor_id and d4c.type = " . DoctorClinicModel::TYPE_DOCTOR . ")
					INNER JOIN clinic c ON (c.id = d4c.clinic_id)
					WHERE 
						c.city_id = ".$cityId." 
						AND do.allowed = 1
						AND do.author = 'gues'";
			//echo $sql."<br/>";
			$result = query($sql);
			if (num_rows($result) == 1 ) {
				$row = fetch_object($result);
				$xml .= "<Element status=\"guest\">".$row -> cnt."</Element>";
			}
			
			
			
			// Опубликовано КОНТЕНТ
			$sql = "SELECT count( distinct do.id) as cnt 
					FROM doctor_opinion do
					INNER JOIN doctor_4_clinic d4c ON (d4c.doctor_id = do.doctor_id and d4c.type = " . DoctorClinicModel::TYPE_DOCTOR . ")
					INNER JOIN clinic c ON (c.id = d4c.clinic_id)
					WHERE 
						c.city_id = ".$cityId." 
						AND do.allowed = 1
						AND do.author = 'cont'";
			//echo $sql."<br/>";
			$result = query($sql);
			if (num_rows($result) == 1 ) {
				$row = fetch_object($result);
				$xml .= "<Element status=\"content\">".$row -> cnt."</Element>";
			}
			
		
		$xml .= "</OpinionStat>";
		
		return $xml;
	}

?>
