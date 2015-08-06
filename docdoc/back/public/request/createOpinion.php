<?php
	require_once dirname(__FILE__)."/../include/header.php";
	require_once dirname(__FILE__)."/../lib/php/validate.php";
	//require_once dirname(__FILE__)."/php/requestLib.php";
	require_once dirname(__FILE__)."/../opinion/php/opinionLib.php";
	require_once dirname(__FILE__)."/../lib/php/request.class.php";

	$user = new user();
	$user -> checkRight4page(array('ADM','OPR','SOP'));

	pageHeader(dirname(__FILE__)."/xsl/createOpinion.xsl","noHead");

	$id = ( isset($_GET["id"]) ) ? checkField ($_GET["id"], "i", 0) : 0;
	$reqId = ( isset($_GET["reqId"]) ) ? checkField ($_GET["reqId"], "i", 0) : 0;

	$xmlString = '<srvInfo>';
	$xmlString .= '<Date>'.date("d.m.Y").'</Date>';
	$xmlString .= $user -> getUserXML();
	$xmlString .= '<Id>'.$id.'</Id>';
	$xmlString .= '<RequestId>'.$reqId.'</RequestId>';
	$xmlString .= '<Date>'.date("d.m.Y").'</Date>';
	$xmlString .= '<Hour>'.date("H").'</Hour>';
	$xmlString .= '</srvInfo>';
	setXML($xmlString);


	$xmlString = '<dbInfo>';
	if ( $id > 0 ) {
		$xmlString .= getOpinionByIdXML($id);
	}
	if ( $reqId > 0 ) {
		$request = new request();
		$request -> getRequest($reqId);
		$xmlString .= $request -> getXMLtree();  
	}
	$xmlString .= '</dbInfo>';
	setXML($xmlString);

	pageFooter("noHead");
?>

