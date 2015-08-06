<?php
	require_once dirname(__FILE__)."/../../include/common.php";
	require_once dirname(__FILE__)."/../../lib/php/validate.php";


	header('Content-Type: text/html; charset=utf-8');
	$q = $_GET["q"];
	$city = (isset($_GET['cityId'])) ? checkField($_GET['cityId'], "i", 1) : 1;
	
	if ($q) {
		$sql="	SELECT
				t1.name, t1.id, t1.status
			FROM doctor t1
			LEFT JOIN clinic t2 ON (t1.clinic_id = t2.id)
			WHERE
				t2.city_id = $city
				AND
				LOWER(t1.name) LIKE LOWER('%".$q."%')
			ORDER BY t1.name";
	  	$result = query($sql);
		if (num_rows($result) > 0) {
			while ($row = fetch_object($result)) {
				print $row->name."|".$row->id."|".$row->status."\n";	
			}
		}
	  }
