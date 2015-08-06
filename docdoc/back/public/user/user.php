<?php
	use dfs\docdoc\models\RequestModel;


	require_once 	dirname(__FILE__)."/../include/header.php"; 
	require_once	dirname(__FILE__)."/../lib/php/dictionaryLib.php"; 
	require_once 	dirname(__FILE__)."/../lib/php/validate.php";
	require_once	dirname(__FILE__)."/php/userLib.php"; 

	$user = new user();	
	$user -> checkRight4page(array('ADM','SOP'),'simple');

	$id	= checkField($_GET['id'], "i", "0");


	$template = $user->checkRight4userByCode(['ADM']) ? '/xsl/user.xsl' : '/xsl/userOperatorStream.xsl';
	pageHeader(dirname(__FILE__) . $template, "noHead");
	
	if (debugMode == 'yes') {
		$xmlString = '<DebugMode>yes</DebugMode>';
		setXML($xmlString);
	}
	
	
	$xmlString = '<srvInfo>'; 
	$xmlString .= '<Id>'.$id.'</Id>';   
	$xmlString .= '</srvInfo>';
	setXML($xmlString);
	
	$xmlString = '<dbInfo>';
	if ( $id > 0 ) {
		$currUser = new user();
		$currUser ->  getUserById($id);
		$xmlString .= $currUser -> getUserXML();
	}
	$streams = [
		['Value' => RequestModel::OPERATOR_STREAM_NEW, 'Title' => 'Новые заявки'],
		['Value' => RequestModel::OPERATOR_STREAM_CALL_LATER, 'Title' => 'Заявки на перезвон'],
	];
	$xmlString .= '<OperatorStreams>' . arrayToXML($streams) . '</OperatorStreams>';
	$xmlString .= getRightList();	
	$xmlString .= '</dbInfo>';
	setXML($xmlString);

	pageFooter('simple');
