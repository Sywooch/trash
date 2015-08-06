<?php
	require_once 	dirname(__FILE__)."/../include/header.php"; 
	require_once 	dirname(__FILE__)."/../lib/php/validate.php"; 
	require_once 	dirname(__FILE__)."/php/doctorLib.php";  
	   
	$user = new user();	
	$user -> checkRight4page(array('ADM','SOP', 'CNM', 'ACM'),'simple');	
	
	$id	= (isset($_GET['id'])) ? intval($_GET['id']) : 0;
	$headers = Yii::app()->request->getQuery('headers') !== '0';

	pageHeader(dirname(__FILE__)."/xsl/chImage.xsl","simple", $headers);
	
	$xmlString = '<srvInfo>'; 
	$xmlString .= '<Id>'.$id.'</Id>'; 
	$xmlString .= '<Random>'.rand(0, 1000).'</Random>';
	
	if (file_exists(Path4Upload."doctor/".$id.".jpg")) {
		$xmlString .= '<IsFile>yes</IsFile>';
	}   
	$xmlString .= '</srvInfo>';
	setXML($xmlString);
	
	$xmlString = '<dbInfo>';
	if ($id > 0) {
		$xmlString .= getDoctorByIdXML($id);
	}
//	$xmlString .= getImageTypes();
	$xmlString .= '</dbInfo>';
	setXML($xmlString);

	pageFooter('simple', $headers);

?>
