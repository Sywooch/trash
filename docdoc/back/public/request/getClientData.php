<?php
	require_once dirname(__FILE__)."/../include/header.php";
	require_once dirname(__FILE__)."/../lib/php/validate.php";
	require_once dirname(__FILE__)."/php/requestLib.php";

	$user = new user();
	$user -> checkRight4page(array('ADM','OPR','SOP'));

	pageHeader(dirname(__FILE__)."/xsl/getClientData.xsl","noHead");

	$id 		= ( isset($_GET["id"]) ) ? checkField ($_GET["id"], "i", 0) : 0;

	$xmlString = '<srvInfo>';
	$xmlString .= '<Date>'.date("d.m.Y").'</Date>';
	$xmlString .= $user -> getUserXML();
	$xmlString .= '<Id>'.$id.'</Id>';
	$xmlString .= '<Date>'.date("d.m.Y").'</Date>';
	$xmlString .= '<Hour>'.date("H").'</Hour>';
	$xmlString .= '</srvInfo>';
	setXML($xmlString);


	$xmlString = '<dbInfo>';
	if ( $id > 0 ) {
		$xmlString .= getRequestByIdXML($id);
	}

	$xmlString .= getStatus4RequestXML();
	$xmlString .= getType4RequestXML();
	$xmlString .= getOperatorListXML();
	$xmlString .= getSectorListXML();
	$xmlString .= '</dbInfo>';
	setXML($xmlString);

	pageFooter("noHead");
?>

