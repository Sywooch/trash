<?php
	require_once	dirname(__FILE__)."/../include/header.php";
	require_once	dirname(__FILE__)."/../lib/php/validate.php";
	require_once	dirname(__FILE__)."/php/reportLib.php";
	require_once	dirname(__FILE__)."/../lib/php/dateTimeLib.php";
	require_once	dirname(__FILE__)."/../request/php/requestLib.php";
	require_once	dirname(__FILE__)."/../lib/php/models/clinic.class.php";
	require_once	dirname(__FILE__)."/../lib/php/serviceFunctions.php";

	$user = new user();
	$user -> checkRight4page(array('ADM','SAL','SOP'));

	pageHeader(dirname(__FILE__)."/xsl/monthRequestReportByClinic.xsl");

	$crDateFrom	= ( isset($_GET["crDateShFrom"]) ) ? checkField ($_GET["crDateShFrom"], "t", "01.".date("m.Y") ) : "01.".date("m.Y"); 
	$crDateTill	= ( isset($_GET["crDateShTill"]) ) ? checkField ($_GET["crDateShTill"], "t", date("d.m.Y")) : date("d.m.Y");
	$clinicList	= ( isset($_GET["clinicList"]) ) ? $_GET["clinicList"]: array();

	//$clinicList = array(86, 13);
	
	
	
	$xmlString = '<srvInfo>';
	$xmlString .= $user -> getUserXML();
	$xmlString .= '<CrDateShFrom>'.$crDateFrom.'</CrDateShFrom>';
	$xmlString .= '<CrDateShTill>'.$crDateTill.'</CrDateShTill>';
	
	if ( count($clinicList) > 0  ) {
		$xmlString .= '<ClinicList>';
		foreach ( $clinicList as $clinic ) {
			$clinic = intval($clinic);
			$xmlString .= '<Clinic>'.$clinic.'</Clinic>';
		}	
		$xmlString .= '</ClinicList>';
	}
	
	$xmlString .= getCityXML();
	$city 	= getCityId();
	$xmlString .= '</srvInfo>';
	
	$city 	= getCityId();
	setXML($xmlString);


	$params = array();
	$params['dateReciveFrom']	= $crDateFrom;
	$params['dateReciveTill']	= $crDateTill;
	
	$xmlString = '<dbInfo>';

	$xmlString .= getClinicLisFromArrayXML($clinicList);
	
	$monthArray = monthBetweenTwoDate($crDateFrom, $crDateTill);
	$dateStartArr = explode(".", $crDateFrom);
	$dateEndArr = explode(".", $crDateTill);
	$lastDay = date("t", strtotime($crDateTill));
	
	$xmlString .= '<ClinicReports>';
	foreach ( $clinicList as $clinic ) {
		$clinic = intval($clinic);
		
		$xmlString .= '<Clinic id="'.$clinic.'">';
		foreach ($monthArray as $line => $data) {
			if ( $data[0] == '01'.".".$dateStartArr[1].".".$dateStartArr[2] ) {
			 	$startDate = $dateStartArr[0].".".$dateStartArr[1].".".$dateStartArr[2];
			} else {
			 	$startDate = $data[0];
			}
			
			
			if ( $data[1] == $lastDay.".".$dateEndArr[1].".".$dateEndArr[2] ) {
			 	$endDate = $dateEndArr[0].".".$dateEndArr[1].".".$dateEndArr[2];
			} else {
			 	$endDate = $data[1];
			}
			
			$xmlString .= "<Report>";
				$xmlString .= "<StartDate>".$startDate."</StartDate>";
				$xmlString .= "<EndDate>".$endDate."</EndDate>";
				$month = date("m",strtotime($data[0]));
				$xmlString .= "<Month id=\"".$month."\">".getRusMonth($month)."</Month>";
	
				$xmlString .= "<Total>".getRequestCount($clinic, 'total', $startDate, $endDate, true, $city)."</Total>";
				$xmlString .= "<Transfer>".getRequestCount($clinic,'transfer', $startDate, $endDate, true, $city)."</Transfer>";
				$xmlString .= "<Apointment>".getRequestCount($clinic,'apointment', $startDate, $endDate, true, $city)."</Apointment>";
				$xmlString .= "<Complete>".getRequestCount($clinic,'complete', $startDate, $endDate, true, $city)."</Complete>";
			$xmlString .= "</Report>"; 
		} 
		
		$xmlString .= '</Clinic>';
	}	

	$xmlString .= '</ClinicReports>';
	$xmlString .= '</dbInfo>';
	setXML($xmlString);

	pageFooter();
?>