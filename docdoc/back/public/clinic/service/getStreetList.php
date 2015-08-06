<?php
	require_once dirname(__FILE__)."/../../include/common.php";
	require_once dirname(__FILE__)."/../../lib/php/validate.php";
	
	header('Content-Type: text/html; charset=utf-8');
	$q = $_GET["q"];
	$city = (isset($_GET['cityId'])) ? checkField($_GET['cityId'], "i", 1) : 1;
	
	if ($q) {
	  	$sql="	SELECT
					title, street_id
				FROM street_dict 
				WHERE 
					city_id = ".$city." 
					AND
					LOWER(title) LIKE LOWER('".$q."%') 
				ORDER BY title";
		//echo $sql;
	  	$result = query($sql);
		if (num_rows($result) > 0) {
			while ($row = fetch_object($result)) {
				print $row->title."|".$row->street_id."\n";	
			}
		}
	  }
