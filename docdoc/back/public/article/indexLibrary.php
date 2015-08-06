<?php
	require_once	dirname(__FILE__)."/../include/header.php";
	require_once	dirname(__FILE__)."/php/articleLib.php";
	require_once	dirname(__FILE__)."/../lib/php/commonDict.php";

	$user = new user();
	$user -> checkRight4page(array('ADM', 'CNM', 'ACM'));

	pageHeader(dirname(__FILE__)."/xsl/indexLibrary.xsl");

	$id 		= ( isset($_GET["id"]) ) ? checkField ($_GET["id"], "i", 0) : 0;
	$status		= ( isset($_GET["status"]) ) ? checkField ($_GET["status"], "i", '') : '';
	$name		= ( isset($_GET["name"]) ) ? checkField ($_GET["name"], "t", '') : '';
	$sectorId	= ( isset($_GET["shSectorId"]) ) ? checkField ($_GET["shSectorId"], "i", '') : '';
	$section		= ( isset($_GET["shSection"]) ) ? checkField ($_GET["shSection"], "i", '') : '';
	$startPage	= ( isset($_GET["startPage"]) ) ? checkField ($_GET["startPage"], "i") : 0;
	
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
		$xmlString .= '<ShSectionId>'.$section.'</ShSectionId>';
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
		$params['section']		= $section;
		$params['status']		= $status;
		
		// Сортировка
		if ( !empty($sortBy) ) {$params['sortBy'] = $sortBy;}
		if ( !empty($sortBy) ) {$params['sortType'] = $sortType;}
	}

	$xmlString = '<dbInfo>';
	$xmlString .= getArticleListXML($params);
	$xmlString .= specialization4ArticleDictXML();
	$xmlString .= getStatusDictXML();
	$xmlString .= '</dbInfo>';
	setXML($xmlString);

	pageFooter();
?>

