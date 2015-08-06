<?php
	require_once dirname(__FILE__)."/../../include/common.php";
	require_once dirname(__FILE__)."/../../lib/php/validate.php";


	$user = new user();	 
	$user -> checkRight4page(array('ADM','CNM', 'ACM'),'simple');
	
	$id		= (isset($_GET['id'])) ? checkField($_GET['id'], "i", 0) : '0';
	$q 		= (isset($_GET['q'])) ? checkField($_GET['q'], "t", "") : '';

	if ( !empty($q) ) {
	  	$sqlAdd = "";
		$sql="SELECT id, name FROM `doctor` WHERE LOWER( name ) like ('%".strtolower(trim($q))."%') ";
//		echo $sql;
	  	$result = query($sql);
	  	$i = 0;
	  	$idList = array();
	  	while ($row = fetch_object($result)) {
	  		array_push($idList, array("id" =>$row -> id, "name" => $row -> name) );
	  		$i++;
	  	}

		echo htmlspecialchars(json_encode(array('count'=>$i, 'list' => $idList)), ENT_NOQUOTES);
	} 
