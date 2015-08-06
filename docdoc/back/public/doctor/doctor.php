<?php
	require_once 	dirname(__FILE__)."/../include/header.php"; 
	require_once 	dirname(__FILE__)."/../lib/php/validate.php";
	require_once	dirname(__FILE__)."/php/doctorLib.php"; 
	   
	$user = new user();	
	$user -> checkRight4page(array('ADM','CNM','SOP', 'ACM'),'simple');	
	
	$id		= ( isset($_GET["id"]) ) ? checkField ($_GET["id"], "i", 0) : 0;
		
	pageHeader(dirname(__FILE__)."/xsl/doctor.xsl","noHead");
	
	$xmlString = '<srvInfo>'; 
	$xmlString .= '<Id>'.$id.'</Id>';
	$xmlString .= '<Random>'.rand(0, 1000).'</Random>';
	$xmlString .= $user -> getUserXML();
	$xmlString .= getCityXML();   
	$xmlString .= '</srvInfo>';
	setXML($xmlString);
	
	$xmlString = '<dbInfo>';
	$xmlString .= getDoctorByIdXML($id);
	
	$xmlString .= getEducationTypeDictXML();
	$xmlString .= getStatusDictXML();
	$xmlString .= getDegreeDictXML();
	$xmlString .= getCategoryDictXML();
	$xmlString .= getRankDictXML();
	$xmlString .= '<NextPhoneNumber>'.getNextPhoneNumber().'</NextPhoneNumber>';
	$xmlString .= '</dbInfo>';
	setXML($xmlString);

	pageFooter('simple');

?>
