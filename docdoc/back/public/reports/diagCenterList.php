<?php
	require_once 	dirname(__FILE__)."/../include/header.php";
	require_once	dirname(__FILE__)."/../lib/php/models/clinic.class.php";
	require_once	dirname(__FILE__)."/../lib/php/serviceFunctions.php";

	$user = new user();
	$user -> checkRight4page(array('ADM','OPR','SOP'));

	pageHeader(dirname(__FILE__)."/xsl/clinicList.xsl","noHead");

	$clinicName	= ( isset($_GET["clinicShName"]) ) ? checkField ($_GET["clinicShName"], "t", "") : "";
	$clinicList	= ( isset($_GET["clinicList"]) ) ? $_GET["clinicList"]: array();
	

	$xmlString = '<srvInfo>';
	$xmlString .= "<HostFront>".SERVER_FRONT."</HostFront>";
	$xmlString .= $user -> getUserXML();
	
	$xmlString .= '<ClinicName>'.$clinicName.'</ClinicName>';
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
	setXML($xmlString);


	$params = array();

		
	$params['title']		= $clinicName;
	$params['city']		= $city;
	$params['status']	= '3';
	$params['type']		= 'clinic';
	$params['branch']		= false;
	$params['clinicNotInList']	= $clinicList;
	

	$xmlString = '<dbInfo>';

	$xmlString 	.= getClinicLisFromArrayXML($clinicList);
	$clinicListArray 	= getClinicLisByParams($params);
	if ( is_array($clinicListArray) && count($clinicListArray) > 0)
		$xmlString 			.= "<SearchClinicList>".arrayToXML($clinicListArray)."</SearchClinicList>";
	$xmlString .= '</dbInfo>';
	setXML($xmlString);

	pageFooter("noHead");
?>

