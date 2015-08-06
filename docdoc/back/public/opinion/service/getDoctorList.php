<?php
use dfs\docdoc\models\DoctorClinicModel;

	require_once dirname(__FILE__)."/../../include/common.php";
	require_once dirname(__FILE__)."/../../lib/php/validate.php";


	header('Content-Type: text/html; charset=utf-8');
	$q = $_GET["q"];
	$city = (isset($_GET['cityId'])) ? checkField($_GET['cityId'], "i", 1) : 1;
	
	if ($q) {
		$sql="	SELECT
				t1.name, t1.id
			FROM doctor t1
			LEFT JOIN doctor_4_clinic t2 ON (t1.id = t2.doctor_id and t2.type = " . DoctorClinicModel::TYPE_DOCTOR . ")
			LEFT JOIN clinic t3 ON (t2.clinic_id = t3.id)
			WHERE
				t3.city_id = $city
				AND
				LOWER(t1.name) LIKE LOWER('%".$q."%')
				AND
				t1.status = 3
			ORDER BY t1.name";
	  	$result = query($sql);
		if (num_rows($result) > 0) {
			while ($row = fetch_object($result)) {
				print $row->name."|".$row->id."\n";	
			}
		}
	  }
