<?php
	
	require_once 	dirname(__FILE__)."/../include/header.php"; 
	require_once	dirname(__FILE__)."/../lib/php/dictionaryLib.php"; 
	require_once 	dirname(__FILE__)."/../lib/php/validate.php";
	require_once	dirname(__FILE__)."/php/userLib.php"; 
	
	$user = new user();	
	$user -> checkRight4page(array('ADM','SOP'));
	
	pageHeader(dirname(__FILE__)."/xsl/index.xsl");

	$userName	= ( isset($_GET["userName"]) ) ? checkField ($_GET["userName"], "t", "") : '';
	$status		= ( isset($_GET["status"]) ) ? checkField ($_GET["status"], "t", "") : '';

	$xmlString  = '<srvInfo>';
	$xmlString .= '<UserName>'.$userName.'</UserName>';	   
	$xmlString .= '<Status>'.$status.'</Status>';	
	$xmlString .= '</srvInfo>';
	setXML($xmlString);

	$params = array(); 
	$params['userName'] = $userName;	 
	$params['status'] 	= $status;
	
	$xmlString = '<dbInfo>';
	$xmlString .= getUserListXML($params);
	$xmlString .= getRightList();
	$xmlString .= '</dbInfo>';
	setXML($xmlString);

	pageFooter();
