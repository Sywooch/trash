<?php
	require_once dirname(__FILE__)."/../include/header.php";
	require_once LIB_PATH . "../lib/php/validate.php";
	require_once dirname(__FILE__)."/../lib/php/diagnostica.php";
	
	pageHeader(dirname(__FILE__)."/xsl/getPoint.xsl", "noHead");
	
	$id	= ( isset($_GET["id"]) ) ? checkField ($_GET["id"], "i") : 0;
	$diagnostica= ( isset($_GET["diagnostica"]) ) ? checkField ($_GET["diagnostica"], "i", 0) : 0;
	$subDiagnostica= ( isset($_GET["subDiagnostica"]) ) ? checkField ($_GET["subDiagnostica"], "i", 0) : 0;
	
	
	$xmlString  = '<srvInfo>'; 
	$xmlString .= '<Id>'.$id.'</Id>';
	$xmlString .= '<Diagnostica>'.$diagnostica.'</Diagnostica>';
	$xmlString .= '<subDiagnostica>'.$subDiagnostica.'</subDiagnostica>';
	$xmlString .= '</srvInfo>';
	setXML($xmlString);

	$xmlString  = '<dbInfo>';

	$params = array();
	$params['diagnostica']	= $diagnostica;
	$params['subDiagnostica']	= $subDiagnostica;
	
	$xmlString .= getDiagnosticCenter($id, $params);
	$xmlString .= getDiagnosticDict();
	$xmlString .= '</dbInfo>';
	setXML($xmlString);
	
	pageFooter('noHead');