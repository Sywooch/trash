<?php
	require_once	dirname(__FILE__)."/../include/header.php";
	require_once	dirname(__FILE__)."/../lib/php/validate.php";
	require_once	dirname(__FILE__)."/php/reportLib.php";

	$user = new user();
	$user -> checkRight4page(array('ADM','SOP'));

	pageHeader(dirname(__FILE__)."/xsl/index.xsl");

	$crDateFrom	= ( isset($_GET["crDateShFrom"]) ) ? checkField ($_GET["crDateShFrom"], "t", date("d.m.Y")) : date("d.m.Y"); 
	$crDateTill	= ( isset($_GET["crDateShTill"]) ) ? checkField ($_GET["crDateShTill"], "t", date("d.m.Y")) : date("d.m.Y");

	$xmlString = '<srvInfo>';
	$xmlString .= $user -> getUserXML();
	$xmlString .= '<CrDateShFrom>'.$crDateFrom.'</CrDateShFrom>';
	$xmlString .= '<CrDateShTill>'.$crDateTill.'</CrDateShTill>';
	$xmlString .= '</srvInfo>';
	setXML($xmlString);


	$params = array();
	$params['crDateFrom']	= $crDateFrom;
	$params['crDateTill']	= $crDateTill;
		
	$xmlString = '<dbInfo>';
	$xmlString .= diagnosticaCallRepoetXML($params);
	$xmlString .= '</dbInfo>';
	setXML($xmlString);

	pageFooter();
?>

