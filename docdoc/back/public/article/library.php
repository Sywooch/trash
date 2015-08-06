<?php
	require_once 	dirname(__FILE__)."/../include/header.php";
	require_once	dirname(__FILE__)."/../lib/php/serviceFunctions.php";
	require_once	dirname(__FILE__)."/../lib/php/models/article.class.php";
	require_once	dirname(__FILE__)."/../lib/php/commonDict.php"; 
	//require_once	dirname(__FILE__)."/php/articleLib.php"; 
	   
	$user = new user();	
	$user -> checkRight4page(array('ADM', 'CNM', 'ACM'),'simple');	
	
	$id		= ( isset($_GET["id"]) ) ? checkField ($_GET["id"], "i", 0) : 0;
		
	pageHeader(dirname(__FILE__)."/xsl/library.xsl","noHead");
	
	$xmlString = '<srvInfo>'; 
	$xmlString .= '<Id>'.$id.'</Id>';
	$xmlString .= $user -> getUserXML();
	$xmlString .= '</srvInfo>';
	setXML($xmlString);
	
	$xmlString = '<dbInfo>';
	$article = new Article($id);
	$xmlString .= "<Article>".arrayToXML($article)."</Article>";
	$xmlString .= specialization4ArticleDictXML();
	$xmlString .= '</dbInfo>';
	setXML($xmlString);

	pageFooter('simple');

?>
