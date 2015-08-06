<?php
	require_once 	dirname(__FILE__)."/../include/header.php"; 
	require_once 	dirname(__FILE__)."/../lib/php/validate.php";
	require_once	dirname(__FILE__)."/php/opinionLib.php"; 
	   
	$user = new user();	
	$user -> checkRight4page(array('ADM','CNM','SOP', 'ACM'),'simple');	
	
	$id		= ( isset($_GET["id"]) ) ? checkField ($_GET["id"], "i", 0) : 0;
		
	pageHeader(dirname(__FILE__)."/xsl/opinion.xsl","noHead");
	
	$xmlString = '<srvInfo>'; 
	$xmlString .= '<Id>'.$id.'</Id>';
	$xmlString .= $user -> getUserXML();
	$xmlString .= getCityXML();     
	$xmlString .= '</srvInfo>';
	setXML($xmlString);
	
	$xmlString = '<dbInfo>';
	if ( $id > 0 ) {
		$xmlString .= getOpinionByIdXML($id);
	}
	$xmlString .= '</dbInfo>';
	setXML($xmlString);

	pageFooter('simple');

?>
