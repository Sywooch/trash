<?php
use \dfs\docdoc\models\DoctorClinicModel;

	/**
	 * 
	 * Получает справичник специализаций
	 * 
	 */

	function getSpetializationList ( $isActiv = false, $city = 1 ) {
		$data = array(); 
		
		if ( $isActiv ) {
			$sql = "SELECT 
		    			sec.id as Id, 
		    			sec.name as Name, 
		    			sec.rewrite_name as RewiriteName 
		    		FROM sector sec
		    		INNER JOIN doctor_sector as d4sec ON (sec.id = d4sec.sector_id)
		    		INNER JOIN doctor as doctor ON (doctor.id = d4sec.doctor_id)
		    		INNER JOIN doctor_4_clinic AS d4cl ON (d4cl.doctor_id = doctor.id and d4cl.type = " . DoctorClinicModel::TYPE_DOCTOR . ")
		    		INNER JOIN clinic AS clinic ON (d4cl.clinic_id = clinic.id)
		            WHERE 
		            	doctor.status = 3
			            AND 
			            clinic.status = 3
			            AND 
			            clinic.city_id = ".$city."
		            GROUP BY sec.id
		            ORDER BY sec.name";
		} else {
			$sql = "SELECT
						t1.id as Id, t1.name as Name, t1.rewrite_name as RewiriteName
					FROM sector t1
					ORDER BY t1.name";	
		}
		
		
		
		$result = query($sql);
		if (num_rows($result) > 0) {
			while ($row = fetch_array ($result) ) {
				array_push($data, $row);
			}
		}
		
		return $data;
	}	
?>
