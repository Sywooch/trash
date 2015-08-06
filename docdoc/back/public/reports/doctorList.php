<?php
	require_once	dirname(__FILE__)."/../include/header.php";
	require_once	dirname(__FILE__)."/../lib/php/validate.php";
	require_once	dirname(__FILE__)."/php/reportLib.php";
	require_once	dirname(__FILE__)."/../lib/php/dateTimeLib.php";

	$user = new user();
	$user -> checkRight4page(array('ADM','CNM','SAL', 'SOP'));

	pageHeader(dirname(__FILE__)."/xsl/doctorList.xsl");

	$clinicId	= ( isset($_GET["shClinicId"]) ) ? checkField ($_GET["shClinicId"], "i", '') : '';
	$clinic		= ( isset($_GET["shClinic"]) ) ? checkField ($_GET["shClinic"], "t", '') : '';
	$shBranch 	= ( isset($_GET["shBranch"]) ) ? checkField ($_GET["shBranch"], "i", 0) : 0;
	//$status 	= ( isset($_GET["shStatus"]) ) ? checkField ($_GET["shStatus"], "i", 0) : 0;
	$statusList	= ( isset($_GET["status"]) ) ? $_GET["status"] : array();
	
	$shImg		= ( isset($_GET["shImg"]) ) ? checkField ($_GET["shImg"], "i", 0) : 0;
	$shExp		= ( isset($_GET["shExp"]) ) ? checkField ($_GET["shExp"], "i", 0) : 0;
	$shRank		= ( isset($_GET["shRank"]) ) ? checkField ($_GET["shRank"], "i", 0) : 0;
	$shDepart	= ( isset($_GET["shDepart"]) ) ? checkField ($_GET["shDepart"], "i", 0) : 0;
	
	$startPage	= ( isset($_GET["startPage"]) ) ? checkField ($_GET["startPage"], "i") : 0;
	$sortBy		= (isset($_GET['sortBy'])) ? checkField ($_GET['sortBy'], "t", "") : '';	// Сортировка
	$sortType	= (isset($_GET['sortType'])) ? checkField($_GET['sortType'], "t", "") : '';	// Сортировка

	$xmlString = '<srvInfo>';
	$xmlString .= $user -> getUserXML();
	$xmlString .= '<StartPage>'.$startPage.'</StartPage>';
	
	$xmlString .= '<ShClinic>'.$clinic.'</ShClinic>';
	$xmlString .= '<ShClinicId>'.$clinicId.'</ShClinicId>';
	$xmlString .= '<Branch>'.$shBranch.'</Branch>';
	
	$xmlString .= '<ShImg>'.$shImg.'</ShImg>';
	$xmlString .= '<ShExp>'.$shExp.'</ShExp>';
	$xmlString .= '<ShRank>'.$shRank.'</ShRank>';
	$xmlString .= '<ShDepart>'.$shDepart.'</ShDepart>';
	
	//$xmlString .= '<ShStatus>'.$status.'</ShStatus>';
	if ( !empty ($statusList) ) {
		$xmlString .= '<StatusList>';
		foreach ( $statusList as $key => $data) {
			$xmlString .= '<Status>'.$data.'</Status>';
		}
		$xmlString .= '</StatusList>';
	}
	
	
	$xmlString .= getCityXML();
	// Сортировка
	if ( !empty($sortBy) ) {	$xmlString .= '<SortBy>'.$sortBy.'</SortBy>';	}
	if ( !empty($sortType) ) {	$xmlString .= '<SortType>'.$sortType.'</SortType>';	}
	
	$xmlString .= '</srvInfo>';
	setXML($xmlString);


	$params = array();
		if ( $clinicId == '' ) { $clinicId = 0; }
	$params['clinic']		= $clinicId;
	$params['branch']		= $shBranch;
	$params['shImg']		= $shImg;
	$params['shExp']		= $shExp;
	$params['shRank']		= $shRank;
	$params['departure']	= $shDepart;
	//$params['status']		= $status;
	unset($statusList['all']);
	$statusList = array_diff($statusList, array("all"));
	$params['statusList']	= $statusList;  
	
	// Сортировка
	if ( !empty($sortBy) ) {$params['sortBy'] = $sortBy;}
	if ( !empty($sortBy) ) {$params['sortType'] = $sortType;}
		
	$xmlString = '<dbInfo>';
	$xmlString .= getDoctorListReportXML($params, getCityId());
	$xmlString .= getStatusDictXML();
	$xmlString .= '</dbInfo>';
	setXML($xmlString);

	pageFooter();
?>

