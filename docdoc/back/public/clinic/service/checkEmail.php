<?php
	require_once dirname(__FILE__)."/../../lib/php/user.class.php";
	require_once dirname(__FILE__)."/../../include/common.php";
	require_once dirname(__FILE__)."/../../lib/php/validate.php";
	

	$user = new user();	 
	$user -> checkRight4page(array('ADM', 'CNM', 'ACM'),'simple');
	
	$q	= (isset($_GET['q'])) ? checkField($_GET['q'], "t", "") : '';
	
	if ( empty ($q) ) {
		echo "-2";
	} else if ( checkEmail($q) ) {
		$sql="SELECT COUNT(clinic_admin_id) AS cnt FROM `clinic_admin` WHERE LOWER(email) = LOWER('".$q."')";
	  	$result = query($sql);
		$row = fetch_object($result); 
		echo $row -> cnt;	
	} else {
		echo "-1";
	}

