<?php
	require_once dirname(__FILE__)."/../../include/common.php";
	require_once dirname(__FILE__)."/../../lib/php/validate.php";


	$user = new user();	 
	$user -> checkRight4page(array('ADM','CNM', 'ACM'),'simple');
	
	$q 		= (isset($_GET['q'])) ? checkField($_GET['q'], "i", 0) : 0;
	$id		= (isset($_GET['id'])) ? checkField($_GET['id'], "i", 0) : '0';

	if ( $q > 0 ) {
	  	$sqlAdd = "";
	  	if ( $id > 0) {$sqlAdd = " AND id <> ".$id." ";}
		$sql="SELECT COUNT(addNumber) AS cnt FROM `doctor` WHERE addNumber = ".$q.$sqlAdd;
//		echo $sql;
	  	$result = query($sql);
		$row = fetch_object($result); 
		echo $row -> cnt;	
	} else {
		echo "-1";
	}
