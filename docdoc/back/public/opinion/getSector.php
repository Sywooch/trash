<?php
	require_once 	dirname(__FILE__)."/../include/header.php"; 
	require_once 	dirname(__FILE__)."/../lib/php/validate.php";
	require_once	dirname(__FILE__)."/php/opinionLib.php"; 
	require_once	dirname(__FILE__)."/../doctor/php/doctorLib.php";
	   
	$user = new user();	
	$user -> checkRight4page(array('ADM','CNM','SOP', 'ACM'),'simple');	
	
	$id		= ( isset($_GET["doctor"]) ) ? checkField ($_GET["doctor"], "i", 0) : 0;
		
	pageHeader(dirname(__FILE__)."/xsl/getSector.xsl","noHead");
	
	$xmlString = '<srvInfo>'; 
	$xmlString .= '<Id>'.$id.'</Id>';
	$xmlString .= $user -> getUserXML();   
	$xmlString .= '</srvInfo>';
	setXML($xmlString);
	
	$xmlString = '<dbInfo>';
	if ( $id > 0 ) {
		$xmlString .= getSectorByDoctorIdXML($id);
	}
	$xmlString .= '</dbInfo>';
	setXML($xmlString);

	pageFooter('simple');

?>
