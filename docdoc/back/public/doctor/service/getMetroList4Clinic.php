<?php
	require_once dirname(__FILE__)."/../../include/common.php";
	require_once dirname(__FILE__)."/../../lib/php/validate.php";


	$clinicId = ( isset($_GET["id"]) ) ? checkField ($_GET["id"], "i", 0) : 0;
	
	$str ="";
	
	if ( $clinicId > 0 ) {
		$sql = "SELECT
						t2.id, t2.name
					FROM underground_station_4_clinic  t1, underground_station t2
					WHERE t1.undegraund_station_id = t2.id AND t1.clinic_id=$clinicId";
			$result = query($sql);
			if (num_rows($result) > 0) {
				while ($row = fetch_object($result)) {
					$str .= $row -> name.", ";
				}
				$str = rtrim($str, ", "); 
			}
	}
	
	echo $str;
