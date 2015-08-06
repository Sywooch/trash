<?php
	require_once dirname(__FILE__)."/../../include/common.php";
	require_once dirname(__FILE__)."/../../lib/php/validate.php";
	

	header('Content-Type: text/html; charset=utf-8');
	$city = (isset($_GET['cityId'])) ? checkField($_GET['cityId'], "i", 1) : 1;
	$clinic = (isset($_GET['q'])) ? checkField($_GET['q'], "i", 0) : 0;
	
	if ( $clinic > 0 ) {
		$sql="	SELECT
			t1.name, t1.id, t1.status
		FROM clinic t1
		WHERE
			t1.id <> $clinic
			AND
			(
				t1.parent_clinic_id = $clinic
				OR
				t1.parent_clinic_id = ( SELECT CASE WHEN parent_clinic_id = 0 THEN NULL ELSE parent_clinic_id END AS parent_id FROM clinic WHERE id = $clinic )
			)
			AND
			t1.status_new = 3
		ORDER BY t1.id ";
	  	$result = query($sql);
		if (num_rows($result) > 0) {
			while ($row = fetch_object($result)) {
				print $row->name."|".$row->id."|".$row->status."\n";	
			}
		}
	  }
