<?php
	require_once dirname(__FILE__)."/../../include/common.php";
	require_once dirname(__FILE__)."/../../lib/php/validate.php";


	$user = new user();	 
	$user -> checkRight4page(array('ADM','CNM', 'ACM'),'simple');
	
	$id		= (isset($_GET['id'])) ? checkField($_GET['id'], "i", 0) : '0';
	$q 		= (isset($_GET['q'])) ? checkField($_GET['q'], "t", "") : '';

	if ( !empty($q) ) {
	  	$sqlAdd = "";
	  	if ( $id > 0) {$sqlAdd = "AND id <> ".$id." ";}
		$sql="SELECT COUNT(rewrite_name) AS cnt FROM `doctor` WHERE LOWER(rewrite_name) = LOWER('".$q."') ".$sqlAdd;
//		echo $sql;
	  	$result = query($sql);
		$row = fetch_object($result); 
		echo $row -> cnt;	
	} else {
		echo "-1";
	}
