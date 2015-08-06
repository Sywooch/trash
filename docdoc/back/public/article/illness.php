<?php
	require_once 	dirname(__FILE__)."/../include/header.php";
	require_once	dirname(__FILE__)."/../lib/php/serviceFunctions.php";
	require_once	dirname(__FILE__)."/../lib/php/models/illness.class.php";
	require_once	dirname(__FILE__)."/../lib/php/commonDict.php"; 
	//require_once	dirname(__FILE__)."/php/articleLib.php"; 
	   
	$user = new user();	
	$user -> checkRight4page(array('ADM', 'CNM', 'ACM'),'simple');	
	
	$id		= ( isset($_GET["id"]) ) ? checkField ($_GET["id"], "i", 0) : 0;
		
	pageHeader(dirname(__FILE__)."/xsl/illness.xsl","noHead");
	
	$xmlString = '<srvInfo>'; 
	$xmlString .= '<Id>'.$id.'</Id>';
	$xmlString .= $user -> getUserXML();
	$xmlString .= '</srvInfo>';
	setXML($xmlString);
	
	$xmlString = '<dbInfo>';

	$article = new Illness($id);
	$xmlString .= "<Illness>".arrayToXML($article)."</Illness>";
	$xmlString .= specializationDictXML();
	$xmlString .= '</dbInfo>';
	setXML($xmlString);

	pageFooter('simple');

?>
