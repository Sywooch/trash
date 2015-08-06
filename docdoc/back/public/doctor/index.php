<?php
	require_once	dirname(__FILE__)."/../include/header.php";
	require_once	dirname(__FILE__)."/../lib/php/validate.php";
	require_once	dirname(__FILE__)."/php/doctorLib.php";
	require_once	dirname(__FILE__)."/php/doctorStat.php";

	$user = new user();
	$user -> checkRight4page(array('ADM','CNM','SOP', 'ACM'));

	pageHeader(dirname(__FILE__)."/xsl/index.xsl");

	$id 		= ( isset($_GET["id"]) ) ? checkField ($_GET["id"], "i", 0) : 0;
	$status		= ( isset($_GET["status"]) ) ? checkField ($_GET["status"], "i", '') : '';
	$name		= ( isset($_GET["name"]) ) ? checkField ($_GET["name"], "t", '') : '';
	$clinicId	= ( isset($_GET["shClinicId"]) ) ? checkField ($_GET["shClinicId"], "i", '') : '';
	$clinic		= ( isset($_GET["shClinic"]) ) ? checkField ($_GET["shClinic"], "t", '') : '';
	$sectorId	= ( isset($_GET["shSectorId"]) ) ? checkField ($_GET["shSectorId"], "i", '') : '';
	$sector		= ( isset($_GET["shSector"]) ) ? checkField ($_GET["shSector"], "t", '') : '';
	$startPage	= ( isset($_GET["startPage"]) ) ? checkField ($_GET["startPage"], "i") : 0;
	$shBranch 	= ( isset($_GET["shBranch"]) ) ? checkField ($_GET["shBranch"], "i", 0) : 0;
	$noClinic	= ( isset($_GET["noClinic"]) ) ? checkField ($_GET["noClinic"], "i", 0) : 0;
	$moderation = ( isset($_GET["shModeration"]) ) ? checkField ($_GET["shModeration"], "i", 0) : 0;
	$kidsReception = (isset($_GET["kidsReception"])) ? checkField($_GET["kidsReception"], "i", 0) : 0;
	
	$sortBy		= (isset($_GET['sortBy'])) ? checkField ($_GET['sortBy'], "t", "") : '';	// Сортировка
	$sortType	= (isset($_GET['sortType'])) ? checkField($_GET['sortType'], "t", "") : '';	// Сортировка

	$xmlString = '<srvInfo>';
	
	// Сортировка
	if ( !empty($sortBy) ) {	$xmlString .= '<SortBy>'.$sortBy.'</SortBy>';	}
	if ( !empty($sortType) ) {	$xmlString .= '<SortType>'.$sortType.'</SortType>';	}
	
	$xmlString .= $user -> getUserXML();
	if ($id > 0 ) {
		$xmlString .= '<Id>'.$id.'</Id>';
	} else {
		$xmlString .= '<StartPage>'.$startPage.'</StartPage>';
		$xmlString .= '<Status>'.$status.'</Status>';
		$xmlString .= '<Name>'.$name.'</Name>';
		$xmlString .= '<ShClinic>'.$clinic.'</ShClinic>';
		$xmlString .= '<ShClinicId>'.$clinicId.'</ShClinicId>';
		$xmlString .= '<Branch>'.$shBranch.'</Branch>';
		$xmlString .= '<ShSector>'.$sector.'</ShSector>';
		$xmlString .= '<ShSectorId>'.$sectorId.'</ShSectorId>';
		$xmlString .= '<noClinic>'.$noClinic.'</noClinic>';
		$xmlString .= '<Moderation>'.$moderation.'</Moderation>';
		$xmlString .= '<kidsReception>' . $kidsReception . '</kidsReception>';
	}
	$xmlString .= getCityXML();
	$xmlString .= '</srvInfo>';
	setXML($xmlString);


	$params = array();
	if ( $id > 0 ) { $params['id'] 	= $id; }
	else {
		$params['step'] 		= "100";
		$params['startPage']	= $startPage;
		$params['name']			= $name;
		$params['clinic']		= $clinicId;
		$params['branch']		= $shBranch;
		$params['sector']		= $sectorId;
		$params['status']		= $status;
		$params['noClinic']		= $noClinic;
		$params['moderation']	= $moderation;
		$params['kidsReception'] = $kidsReception;
		
		// Сортировка
		if ( !empty($sortBy) ) {$params['sortBy'] = $sortBy;}
		if ( !empty($sortBy) ) {$params['sortType'] = $sortType;}
	}

	$xmlString = '<dbInfo>';

	$xmlString .= getDoctorListXML($params, getCityId());
	$xmlString .= getStatusDictXML();
	$xmlString .= getDoctorStatisticXML(getCityId());
	$xmlString .= '</dbInfo>';
	setXML($xmlString);

	pageFooter();
?>

