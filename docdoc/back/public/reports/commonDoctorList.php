<?php
	require_once	dirname(__FILE__)."/../include/header.php";
	require_once	dirname(__FILE__)."/../lib/php/validate.php";
	require_once	dirname(__FILE__)."/php/reportLib.php";

	$user = new user();
	$user -> checkRight4page(array('ADM','CNM','SCM'));

	pageHeader(dirname(__FILE__)."/xsl/commonDoctorList.xsl");

	$startPage	= ( isset($_GET["startPage"]) ) ? checkField ($_GET["startPage"], "i") : 0;

	$xmlString = '<srvInfo>';
	$xmlString .= $user -> getUserXML();
	$xmlString .= '<StartPage>'.$startPage.'</StartPage>';
	
	$xmlString .= getCityXML();
	$xmlString .= '</srvInfo>';
	setXML($xmlString);


	$params = array();
	$params['step'] 		= "100";
	$params['startPage']	= $startPage;
	
	$xmlString = '<dbInfo>';
	$xmlString .= getClinicList4DoctorsByXML(getCityId());
	$xmlString .= '</dbInfo>';
	setXML($xmlString);

	pageFooter();
?>

