<?php
	require_once 	dirname(__FILE__)."/../include/header.php"; 
	include			dirname(__FILE__)."/php/userLib.php"; 
	
	$user = new user();	
	if ( !(intval($user -> idUser) > 0) ) {
		header ("Location: /noRights.htm?mode=simple");
		exit;
	}
	
	pageHeader(dirname(__FILE__)."/xsl/personal.xsl","noHead");

	$xmlString = '<dbInfo>';
	$xmlString .= $user -> getUserXML();
	$xmlString .= getRightList();	
	$xmlString .= '</dbInfo>';

	setXML($xmlString);

	pageFooter('simple');
?>
