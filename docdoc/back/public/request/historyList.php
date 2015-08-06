<?php
	require_once dirname(__FILE__)."/../include/header.php";
	require_once dirname(__FILE__)."/../lib/php/validate.php";
	require_once dirname(__FILE__)."/php/requestLib.php";

	$user = new user();
	$user -> checkRight4page(array('ADM','OPR','SOP','LIS'));

	pageHeader(dirname(__FILE__)."/xsl/historyList.xsl","noHead");

	$id = ( isset($_GET["id"]) ) ? checkField ($_GET["id"], "i", 0) : 0;

	$xmlString = '<srvInfo>';
	$xmlString .= $user -> getUserXML();
	$xmlString .= '<Id>'.$id.'</Id>';
	$xmlString .= '</srvInfo>';
	setXML($xmlString);


	$xmlString = '<dbInfo>';
	if ( $id > 0 ) {
		$xmlString .= getCommentListXML ($id);
	}
	$xmlString .= getAction4RequestHistoryXML();
	$xmlString .= '</dbInfo>';
	setXML($xmlString);

	pageFooter("noHead");
?>

