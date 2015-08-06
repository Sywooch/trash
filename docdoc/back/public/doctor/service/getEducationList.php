<?php
	require_once dirname(__FILE__)."/../../include/common.php";


	header('Content-Type: text/html; charset=utf-8');
	$q = $_GET["q"];
	
	if ($q) {
	  	$sql="	SELECT
					title, education_id as id, type
				FROM education_dict 
				WHERE 
					LOWER(title) LIKE LOWER('%".$q."%') 
				ORDER BY title";
	  	$result = query($sql);
		if (num_rows($result) > 0) {
			while ($row = fetch_object($result)) {
				print $row->title."|".$row->id."|".$row->type."\n";	
			}
		}
	  }
