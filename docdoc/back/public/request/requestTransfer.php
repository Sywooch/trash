<?php
require_once dirname(__FILE__)."/../include/header.php";
require_once dirname(__FILE__)."/../lib/php/validate.php";
require_once dirname(__FILE__)."/../doctor/php/doctorLib.php";

$user = new user();
$user -> checkRight4page(array('ADM','OPR','SOP'));

pageHeader(dirname(__FILE__)."/xsl/requestTransfer.xsl","noHead");


$id 		= ( isset($_GET["docId"]) ) ? checkField ($_GET["docId"], "i", 0) : 0;
$sector		= ( isset($_GET["sector"]) ) ? checkField ($_GET["sector"], "i", 0) : 0;
$clinic		= ( isset($_GET["clinicId"]) ) ? checkField ($_GET["clinicId"], "t", '') : '';

$xmlString = '<srvInfo>';
$xmlString .= $user -> getUserXML();
$xmlString .= '<DoctorId>'.$id.'</DoctorId>';
$xmlString .= '<ClinicId>'.$clinic.'</ClinicId>';
$xmlString .= '<Sector>'.$sector.'</Sector>';
$xmlString .= '</srvInfo>';
setXML($xmlString);

$xmlString = '<dbInfo>';
$xmlString .= getDoctorByIdXML($id);
$xmlString .= '</dbInfo>';
setXML($xmlString);

pageFooter("noHead");

?>

