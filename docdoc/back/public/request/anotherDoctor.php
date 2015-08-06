<?php
require_once dirname(__FILE__) . "/../include/header.php";
require_once dirname(__FILE__) . "/php/requestLib.php";

$user = new user();
$user->checkRight4page(array('ADM', 'CNM', 'SOP', 'ACM', 'OPR', 'LIS'));

pageHeader(dirname(__FILE__) . "/xsl/anotherDoctor.xsl", "noHead");

$id = (isset($_GET["id"])) ? checkField($_GET["id"], "i", 0) : 0;

$xmlString = '<srvInfo>';
$xmlString .= $user->getUserXML();
$xmlString .= '<RequestId>' . $id . '</RequestId>';
$xmlString .= getCityXML();
$xmlString .= '</srvInfo>';
setXML($xmlString);

$xmlString = '<dbInfo>';
if ($id > 0) {
	$xmlString .= getRequestByIdXML($id);
}
$xmlString .= getSectorListXML();

$xmlString .= '</dbInfo>';
setXML($xmlString);

pageFooter("noHead");
