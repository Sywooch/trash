<?php

	require_once	dirname(__FILE__)."/../include/header.php";
	require_once	dirname(__FILE__)."/../lib/php/dictionaryLib.php";
	require_once	dirname(__FILE__)."/php/emailLib.php";

	$user = new user();
	$user -> checkRight4page(array('ADM'));

	pageHeader(dirname(__FILE__)."/xsl/emailList.xsl");

	$startPage	= ( isset($_GET["startPage"]) ) ? checkField ($_GET["startPage"], "i") : 0;
	$crDateFrom	= ( isset($_GET["crDateShFrom"]) ) ? checkField ($_GET["crDateShFrom"], "t", "") : ''; // добавил
	$crDateTill	= ( isset($_GET["crDateShTill"]) ) ? checkField ($_GET["crDateShTill"], "t", "") : ''; // добавил

	$xmlString = '<srvInfo>';
	$xmlString .= '<StartPage>'.$startPage.'</StartPage>';
	$xmlString .= '<CrDateShFrom>'.$crDateFrom.'</CrDateShFrom>';
	$xmlString .= '<CrDateShTill>'.$crDateTill.'</CrDateShTill>';

	$xmlString .= '</srvInfo>';
	setXML($xmlString);


	$params = array();
	$params['crDateFrom']	= $crDateFrom; // добавил
	$params['crDateTill']	= $crDateTill; // добавил
	$params['step'] 	= "200";
	$params['startPage']	= $startPage;


	$xmlString = '<dbInfo>';

	$xmlString .= getEmailListXML($params);
	$xmlString .= '</dbInfo>';
	setXML($xmlString);

	pageFooter();
?>

