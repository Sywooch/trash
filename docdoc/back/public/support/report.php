<?php
	require_once 	dirname(__FILE__)."/../include/header.php"; 
	require_once	dirname(__FILE__)."/../lib/php/dictionaryLib.php"; 
	require_once 	dirname(__FILE__)."/../lib/php/validate.php";
	   
	
	$url = ( isset($_POST['URL']) )? checkField($_POST['URL'], "t", SERVER_BACK) : SERVER_BACK;
	$user = new user();	
	if ( !($user->checkLoginUser()) )
		header ("Location: /noRights.htm?mode=noHead");
		
	pageHeader(dirname(__FILE__)."/xsl/report.xsl","noHead");
	
	if (debugMode == 'yes') {
		$xmlString = '<DebugMode>no</DebugMode>';
		setXML($xmlString);
	}
	
	
	$xmlString = '<srvInfo>'; 
	$xmlString .= '<URL><![CDATA['.str_replace("---ak---", "&", $url).']]></URL>';	
	$xmlString .= $user -> getUserXML();
	$xmlString .= '</srvInfo>';
	setXML($xmlString);

	
	pageFooter('simple');

?>
