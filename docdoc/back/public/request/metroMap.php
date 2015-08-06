<?php
	require_once dirname(__FILE__)."/../include/header.php";

	$user = new user();
	$user -> checkRight4page(array('ADM','OPR','SOP'));

	pageHeader(dirname(__FILE__)."/xsl/metroMap.xsl","noHead");

	

	$xmlString = '<srvInfo>';
	$xmlString .= $user -> getUserXML();
	$xmlString .= '</srvInfo>';
	setXML($xmlString);

	pageFooter("noHead");

?>

