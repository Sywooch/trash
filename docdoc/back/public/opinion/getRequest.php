<?php
	require_once 	dirname(__FILE__)."/../include/header.php"; 
	require_once 	dirname(__FILE__)."/../lib/php/validate.php";

	require_once 	dirname(__FILE__)."/../request/php/requestLib.php";
	   
	$user = new user();	
	$user -> checkRight4page(array('ADM','CNM','SOP', 'ACM'),'simple');	
	
	$id		= ( isset($_GET["request"]) ) ? checkField ($_GET["request"], "i", 0) : 0;
		
	pageHeader(dirname(__FILE__)."/xsl/getRequest.xsl","noHead");
	
	$xmlString = '<srvInfo>'; 
	$xmlString .= '<Id>'.$id.'</Id>';
	$xmlString .= $user -> getUserXML();   
	$xmlString .= '</srvInfo>';
	setXML($xmlString);
	
	$xmlString = '<dbInfo>';
	if ( $id > 0 ) {
		$xmlString .= getRequestByIdXML($id);	 
	}
	$xmlString .= '</dbInfo>';
	setXML($xmlString);

	pageFooter('simple');

?>
