<?php
	require_once dirname(__FILE__) . "/../include/header.php";
	require_once dirname(__FILE__) . "/../lib/php/validate.php";
	require_once dirname(__FILE__) . "/php/clinicLib.php";

	$user = new user();
	$user->checkRight4page(array('ADM', 'SOP', 'CNM', 'ACM'), 'simple');
	$id = (isset($_GET['id'])) ? intval($_GET['id']) : 0;

	pageHeader(dirname(__FILE__) . "/xsl/chImage.xsl", "simple");
	
	$xmlString = '<srvInfo>'; 
	$xmlString .= '<Id>' . $id . '</Id>';
	$xmlString .= '<Random>' . rand(0, 1000) . '</Random>';

	$xmlString .= '</srvInfo>';
	setXML($xmlString);

	$xmlString = '<dbInfo>';
	if ($id > 0) {
		$xmlString .= getClinicByIdXML($id);
	}
	$xmlString .= '</dbInfo>';
	setXML($xmlString);

	pageFooter('simple');

?>
