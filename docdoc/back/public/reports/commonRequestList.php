<?php
	require_once	dirname(__FILE__)."/../include/header.php";
	require_once	dirname(__FILE__)."/../lib/php/validate.php";
	require_once	dirname(__FILE__)."/php/reportLib.php";
	require_once	dirname(__FILE__)."/../request/php/requestLib.php";
	require_once	dirname(__FILE__)."/../lib/php/dateTimeLib.php";

	$user = new user();
	$user -> checkRight4page(array('ADM','SAL','SOP'));

	pageHeader(dirname(__FILE__)."/xsl/commonRequestList.xsl");

	$crDateFrom	= ( isset($_GET["crDateShFrom"]) ) ? checkField ($_GET["crDateShFrom"], "t", "01.".date("m.Y") ) : "01.".date("m.Y"); 
	$crDateTill	= ( isset($_GET["crDateShTill"]) ) ? checkField ($_GET["crDateShTill"], "t", date("d.m.Y")) : date("d.m.Y");
	$startPage	= ( isset($_GET["startPage"]) ) ? checkField ($_GET["startPage"], "i") : 0;

	$xmlString = '<srvInfo>';
	$xmlString .= $user -> getUserXML();
	$xmlString .= '<CrDateShFrom>'.$crDateFrom.'</CrDateShFrom>';
	$xmlString .= '<CrDateShTill>'.$crDateTill.'</CrDateShTill>';
	$xmlString .= '<StartPage>'.$startPage.'</StartPage>';
	$year = date("Y");
	
	$month = date("n");
	if ( $month < 1 ) $month = 12 + $month;
	if ( $month > 12 ) $month = $month - 12;
	
	$xmlString .= '<MonthList>';
	$xmlString .= '<Element start="'.date("01.m.Y", strtotime("-2 month")).'" end="'.date("d.m.Y", strtotime("last day of -2 month")).'">'.getRusMonth(date("n") - 2).'</Element>';
	$xmlString .= '<Element start="'.date("01.m.Y", strtotime("-1 month")).'" end="'.date("d.m.Y", strtotime("last day of -1 month")).'">'.getRusMonth(date("n") - 1).'</Element>';
	$xmlString .= '<Element start="'.date("01.m.Y").'" end="'.date("d.m.Y", strtotime("last day of +0 month")).'">'.getRusMonth(date("n")).'</Element>';
	$xmlString .= '</MonthList>';
	
	
	$xmlString .= getCityXML();
	$xmlString .= '</srvInfo>';
	setXML($xmlString);


	$params = array();
	$params['dateReciveFrom']	= $crDateFrom;
	$params['dateReciveTill']	= $crDateTill;
	$params['step'] 		= "100";
	$params['startPage']	= $startPage;
	
	$xmlString = '<dbInfo>';
	$xmlString .= getClinicListByXML($crDateFrom, $crDateTill, getCityId());
	$xmlString .= getStatus4RequestXML();
	$xmlString .= '</dbInfo>';
	setXML($xmlString);

	pageFooter();

?>

