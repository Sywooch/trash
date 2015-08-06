<?php
	require_once dirname(__FILE__)."/include/header.php";
	  
	pageHeader(dirname(__FILE__)."/xsl/noRights.xsl"); 
	
	$xmlString  = '<srvInfo>'; 
	$xmlString .= '</srvInfo>';
	setXML($xmlString);
	
	pageFooter();
?>
