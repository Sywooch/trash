<?php
	require_once dirname(__FILE__)."/../../lib/php/user.class.php";
	require_once dirname(__FILE__)."/../../include/common.php";


	$user = new user();	 
	$user -> checkRight4page(array('ADM'),'simple');
	
	//$q = iconv("UTF-8","WINDOWS-1251",$_GET["q"]);
	$q = $_GET["q"];
	
	if ( isset($q) && !empty($q) ) {
		$sql = "SELECT COUNT(user_id) AS cnt FROM `user` WHERE LOWER(user_email) = LOWER('".$q."')";
	  	$result = query($sql);
		$row = fetch_object($result); 
		echo $row -> cnt;	
	} else {
		echo "-1";
	}
