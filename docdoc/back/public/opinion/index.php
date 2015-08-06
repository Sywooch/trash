<?php
	require_once	dirname(__FILE__)."/../include/header.php";
	require_once	dirname(__FILE__)."/../lib/php/validate.php";
	require_once	dirname(__FILE__)."/php/opinionLib.php";
	require_once	dirname(__FILE__)."/php/opinionStat.php";

	$user = new user();
	$user -> checkRight4page(array('ADM','CNM','SOP', 'ACM'));

	pageHeader(dirname(__FILE__)."/xsl/index.xsl");

	$id 		= ( isset($_GET["id"]) ) ? checkField ($_GET["id"], "i", 0) : 0;
	$shAuthor 	= ( isset($_GET["shAuthor"]) ) ? checkField ($_GET["shAuthor"], "t", '') : '';
	$shAllow	= ( isset($_GET["shAllow"]) ) ? trim($_GET["shAllow"]) : '';
	$shOrigin	= ( isset($_GET["shOrigin"]) ) ? trim($_GET["shOrigin"]) : '';
	$sectorId	= ( isset($_GET["shSectorId"]) ) ? checkField ($_GET["shSectorId"], "i", '') : '';
	$sector		= ( isset($_GET["shSector"]) ) ? checkField ($_GET["shSector"], "t", '') : '';
	$rating_color = ( isset($_GET["ratingColor"]) ) ? checkField ($_GET["ratingColor"], "t", '') : '';
	$crDateFrom	= ( isset($_GET["crDateShFrom"]) ) ? checkField ($_GET["crDateShFrom"], "t", "") : ''; 
	$crDateTill	= ( isset($_GET["crDateShTill"]) ) ? checkField ($_GET["crDateShTill"], "t", "") : ''; 
	$startPage	= ( isset($_GET["startPage"]) ) ? checkField ($_GET["startPage"], "i") : 0;
	
	$shDoctor	= (isset ($_GET['shDoctor']))? checkField ($_GET["shDoctor"], "t", "") : "";
	$shDoctorId	= (isset ($_GET['shDoctorId']))? checkField ($_GET["shDoctorId"], "i", "") : "";
	
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
		//$xmlString .= '<Status>'.$status.'</Status>';
		$xmlString .= '<CrDateShFrom>'.$crDateFrom.'</CrDateShFrom>';
		$xmlString .= '<CrDateShTill>'.$crDateTill.'</CrDateShTill>';
		$xmlString .= '<ShAllow>'.$shAllow.'</ShAllow>';
		$xmlString .= '<ShOrigin>'.$shOrigin.'</ShOrigin>';
		$xmlString .= '<ShAuthor>'.$shAuthor.'</ShAuthor>';
		$xmlString .= '<ShDoctor>'.$shDoctor.'</ShDoctor>';
		$xmlString .= '<ShDoctorId>'.$shDoctorId.'</ShDoctorId>';
		$xmlString .= '<ShSector>'.$sector.'</ShSector>';
		$xmlString .= '<RatingColor>'.$rating_color.'</RatingColor>';
		
		$xmlString .= '<ShSectorId>'.$sectorId.'</ShSectorId>';
	}
	$xmlString .= getCityXML();
	$xmlString .= '</srvInfo>';
	setXML($xmlString);


	$params = array();
	if ( $id > 0 ) { $params['id'] 	= $id; }
	else {
		$params['step'] 		= "50";
		$params['startPage']	= $startPage;
		$params['crDateFrom']	= $crDateFrom;
		$params['crDateTill']	= $crDateTill;
		$params['sector']		= $sectorId;
		if ( $rating_color != '' ) 
			$params['rating_color']		= intval($rating_color);
		//$params['status']		= $status;
		$params['author']		= $shAuthor;
		$params['allow']		= $shAllow;
		$params['origin']		= $shOrigin;
		$params['shDoctorId']	= $shDoctorId;
		
		// Сортировка
		if ( !empty($sortBy) ) {$params['sortBy'] = $sortBy;}
		if ( !empty($sortBy) ) {$params['sortType'] = $sortType;}
		
	}

	$xmlString = '<dbInfo>';

	$xmlString .= getOpinionListXML($params, getCityId());
	$xmlString .= getRatingColorDictXML();
	$xmlString .= getOpinionStatisticXML(getCityId());
	$xmlString .= '</dbInfo>';
	setXML($xmlString);

	pageFooter();
?>

