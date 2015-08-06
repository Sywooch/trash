<?php
	require_once	dirname(__FILE__)."/../../include/common.php";
	require_once 	dirname(__FILE__)."/../../lib/php/commonDict.php";
	require_once 	dirname(__FILE__)."/../../lib/php/pager.php";
	require_once	dirname(__FILE__)."/../../lib/php/models/clinic.class.php";
	require_once	dirname(__FILE__)."/../php/reportLib.php";
	require_once	dirname(__FILE__)."/../php/report4clinicLib.php";
	require_once	dirname(__FILE__)."/../../request/php/requestLib.php";
	require_once	dirname(__FILE__)."/../../lib/php/style4Excel.php";	// стили для ячеек

	
	$crDateFrom	= ( isset($_GET["dateFrom"]) ) ? checkField ($_GET["dateFrom"], "t", date("d.m.Y")) : date("d.m.Y"); 
	$crDateTill	= ( isset($_GET["dateTill"]) ) ? checkField ($_GET["dateTill"], "t", date("d.m.Y")) : date("d.m.Y");
	
	$clinicList 	= ( isset($_GET["line"]) ) ? $_GET["line"] : array();
	
	$clinicList = array(450);
	
	$params = array();
	$params['dateReciveFrom']	= $crDateFrom;
	$params['dateReciveTill']	= $crDateTill;
	
	if ( count($clinicList) > 0 ) {
		foreach ($clinicList as $clinic) {
			$clinic = checkField ($clinic, "i", 0);
			if ( $clinic > 0 ) {
				$params['clinic']	= $clinic;
				$file = reportGenerator ($params);
			}
		}
	}
	
	header('Content-type: text/xml; charset=UTF-8');
	print $file;

	
	
	function reportGenerator ($params = array()) {
		
		$xmlString  = '<?xml version="1.0" encoding="UTF-8"?>';
		$xmlString .= "<dbInfo>";

		$clinic = new Clinic();
		$clinic -> getClinic($params['clinic']);
		
		$xmlString .= $clinic -> getClinicXML(); 
		$xmlString .= specializationDictXML();
		$params["clinic"] = $clinic;
		$xmlString .= getPatientListXML($params, $clinic -> id);
		$xmlString .= getStatus4RequestXML();
		$xmlString .= '</dbInfo>';
	
		return $xml;
		/*
		$doc = new DOMDocument('1.0','UTF-8');
		if ($doc -> loadXML($xmlString) ) {
			$xml = new SimpleXMLElement($xmlString);
		} else {
			echo "не XML";		
		}
		*/
		
	}
	
?>