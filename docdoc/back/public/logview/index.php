<?php
	require_once		dirname(__FILE__)."/../include/header.php";
	include			dirname(__FILE__)."/../lib/php/dictionaryLib.php";
	
	include			dirname(__FILE__)."/php/logLib.php";

	$user = new user();
	$user -> checkRight4page(array('ADM'));

	pageHeader(dirname(__FILE__)."/xsl/index.xsl");
	$id 		= ( isset($_GET["id"]) ) ? checkField ($_GET["id"], "i") : 0;

	$startPage	= ( isset($_GET["startPage"]) ) ? checkField ($_GET["startPage"], "i") : 0;
	$crDateFrom	= ( isset($_GET["crDateShFrom"]) ) ? checkField ($_GET["crDateShFrom"], "t", "") : '';
	$crDateTill	= ( isset($_GET["crDateShTill"]) ) ? checkField ($_GET["crDateShTill"], "t", "") : '';
	$login		= ( isset($_GET["login"]) ) ? checkField ($_GET["login"], "t", "", true) : '';
	$idLogCode	= ( isset($_GET["idLogCode"]) ) ? checkField ($_GET["idLogCode"], "t", "", true) : '';

	$xmlString = '<srvInfo>';
	if ($id > 0 ) {
		$xmlString .= '<Id>'.$id.'</Id>';
	} else {
		$xmlString .= '<StartPage>'.$startPage.'</StartPage>';
		$xmlString .= '<CrDateShFrom>'.$crDateFrom.'</CrDateShFrom>';
		$xmlString .= '<CrDateShTill>'.$crDateTill.'</CrDateShTill>';
		$xmlString .= '<Login>'.$login.'</Login>';
		$xmlString .= '<IdLogCode>'.$idLogCode.'</IdLogCode>';
	}

	$xmlString .= '</srvInfo>';
	setXML($xmlString);


	$params = array();
	if ( $id > 0 ) { $params['id'] 	= $id; }
	else {
		$params['crDateFrom']	= $crDateFrom; // добавил
		$params['crDateTill']	= $crDateTill; // добавил
		$params['step'] 	= "200";
		$params['startPage']	= $startPage;
		$params['login']	= $login;
		$params['idLogCode']	= $idLogCode;
	}

	$xmlString = '<dbInfo>';

	$user = new user();
	$xmlString .= $user -> getUserXML();

	$xmlString .= getLogListXML($params);
	$xmlString .= getLogDictXML();
	$xmlString .= '</dbInfo>';
	setXML($xmlString);

	pageFooter();
?>

