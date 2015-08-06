<?php
	require_once	dirname(__FILE__)."/../include/header.php";
	require_once	dirname(__FILE__)."/php/reportLib.php";
	require_once	dirname(__FILE__)."/../lib/php/models/diag_request.class.php";
	require_once	dirname(__FILE__)."/../lib/php/commonDict.php";
	require_once	dirname(__FILE__)."/../lib/php/models/clinic.class.php";
	require_once	dirname(__FILE__)."/../lib/php/serviceFunctions.php";

	$user = new user();
	$user -> checkRight4page(array('ADM','SAL','SOP'));

	pageHeader(dirname(__FILE__)."/xsl/diagRequestReportByClinic.xsl");

	$crDateFrom	= ( isset($_GET["crDateShFrom"]) ) ? checkField ($_GET["crDateShFrom"], "t", "" ) : ""; 
	$crDateTill	= ( isset($_GET["crDateShTill"]) ) ? checkField ($_GET["crDateShTill"], "t", "") : "";
	$clinicList	= ( isset($_GET["clinicList"]) ) ? $_GET["clinicList"]: array();
	
	if ( empty($crDateFrom) && empty($crDateFrom2) ) { $crDateFrom = "01.".date("m.Y"); }
	if ( empty($crDateTill) && empty($crDateTill2) ) { $crDateTill = date("d.m.Y"); }
	
	
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
	$params['dateReciveFrom']		= $crDateFrom;
	$params['dateReciveTill']		= $crDateTill;
//	$params['dateAdmissionFrom']	= $crDateFrom2;
//	$params['dateAdmissionTill']	= $crDateTill2;
	
	$xmlString = '<dbInfo>';

	$xmlString .= getClinicLisFromArrayXML($clinicList);
	
	$dateMethod = 'create'; 
	if (!empty($crDateFrom) && !empty($crDateTill) ) {
		$monthArray = monthBetweenTwoDate($crDateFrom, $crDateTill);
		$dateStartArr = explode(".", $crDateFrom);
		$dateEndArr = explode(".", $crDateTill);
		$lastDay = date("t", strtotime($crDateTill));
		
		$startTotalPeriod = $crDateFrom;
		$endTotalPeriod = $crDateTill;
		$dateMethod = 'create';
	} else if (!empty($crDateFrom2) && !empty($crDateTill2)) {
		$monthArray = monthBetweenTwoDate($crDateFrom2, $crDateTill2);
		$dateStartArr = explode(".", $crDateFrom2);
		$dateEndArr = explode(".", $crDateTill2);
		$lastDay = date("t", strtotime($crDateTill2));
		
		$startTotalPeriod = $crDateFrom2;
		$endTotalPeriod = $crDateTill2;
		$dateMethod = 'admission';
	}
	
	
	
	$xmlString .= '<ClinicReports>';
	foreach ( $clinicList as $clinic ) {
		$clinic = intval($clinic);
		
		$xmlString .= '<Clinic id="'.$clinic.'">';
		$currentClinic = new Clinic();
		$currentClinic -> getClinic($clinic);
		$contractId = ( !empty($currentClinic -> diagSettings['contractId']) ) ? $currentClinic -> diagSettings['contractId'] : 0;
		
		$xmlString .= "<Title><![CDATA[".$currentClinic -> title."]]></Title>";
		$xmlString .= "<Settings>".arrayToXML($currentClinic -> getDiagnosticaSettings())."</Settings>";
		
		$startDate = $crDateFrom; 
		$endDate = $crDateTill;
		switch ($contractId) {
			case 3 : { $dateMethod = "create"; } break; // за звонок
			case 4 : { $dateMethod = "create"; } break; // за запись
			case 5 : { $dateMethod = "admission"; } break; // за дошёл
			default : {
				$dateMethod = "create";
			}
		} 
		
		$params['contractId']	= $contractId;
	
		$xmlString .= "<Report>";
			$xmlString .= "<StartDate>".$crDateFrom."</StartDate>";
			$xmlString .= "<EndDate>".$crDateTill."</EndDate>";

			$xmlString .= "<Total>".getDiagRequestCount4Billing($clinic, 'total', $startDate, $endDate, $dateMethod)."</Total>";
			$xmlString .= "<Total30>".getDiagRequestCount4Billing($clinic, 'total30', $startDate, $endDate, $dateMethod)."</Total30>";
			$xmlString .= "<Reject>".getDiagRequestCount4Billing($clinic,'reject', $startDate, $endDate, $dateMethod)."</Reject>";
			$xmlString .= "<Admission>".getDiagRequestCount4Billing($clinic,'admission', $startDate, $endDate, $dateMethod)."</Admission>";
			$xmlString .= "<Complete>".getDiagRequestCount4Billing($clinic,'complete', $startDate, $endDate, $dateMethod)."</Complete>";
			$xmlString .= "<Rings>".getDiagRecordCount4Billing($clinic, $startDate, $endDate, 0, $dateMethod)."</Rings>";
			$xmlString .= "<Rings30>".getDiagRecordCount4Billing($clinic, $startDate, $endDate, 30, $dateMethod)."</Rings30>";
			//$xmlString .= "<RejectReason>".arrayToXML(getRejectReason($clinic, $startDate, $endDate, $dateMethod))."</RejectReason>";
		$xmlString .= "</Report>"; 
		
		$xmlString .= '</Clinic>';
	}	

	$xmlString .= '</ClinicReports>';
	$xmlString .= "<ContractListDict>".arrayToXML(getContractList())."</ContractListDict>";
	$xmlString .= '<RejectDict>'.arrayToXML(getRjectDict(true)).'</RejectDict>';
	$xmlString .= '</dbInfo>';
	setXML($xmlString);

	pageFooter();
?>