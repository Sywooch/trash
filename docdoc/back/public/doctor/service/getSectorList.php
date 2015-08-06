<?php
	require_once dirname(__FILE__)."/../../include/common.php";


	header('Content-Type: text/html; charset=utf-8');
	$q = ( isset($_GET["q"]) ) ? checkField ($_GET["q"], "t", '') : '';
	
	if ( !empty($q) && strlen($q) <= 50 ) {
		$sql = "
			SELECT
					name as title,
					id
				FROM sector 
				WHERE 
					LOWER(name) LIKE LOWER(:q)
				ORDER BY name
		";
		$result = query($sql, [
			':q' => "%{$q}%",
		]);
		if (num_rows($result) > 0) {
			while ($row = fetch_object($result)) {
				print $row->title."|".$row->id."\n";	
			}
		}
	}
