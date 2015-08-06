<?php
require_once dirname(__FILE__) . "/../include/header.php";
require_once dirname(__FILE__) . "/../lib/php/validate.php";
require_once dirname(__FILE__) . "/php/requestLib.php";
require_once __DIR__ . "/../lib/php/RequestInterface.php";

$user = new user();
$user->checkRight4page(array('ADM', 'OPR', 'SOP', 'LIS'));

pageHeader(dirname(__FILE__) . "/xsl/getAudioList.xsl", "noHead");

$id = (isset($_GET["id"])) ? checkField($_GET["id"], "i", 0) : 0;
$type = isset($_GET['type']) ? $_GET['type'] : 'default';
$interface = new RequestInterface($type);

$addActions = $interface->isCallCenter() || $interface->isDefault();
$xmlString = '<srvInfo>';
$xmlString .= "<AddActions>{$addActions}</AddActions>";
$xmlString .= '<Date>' . date("d.m.Y") . '</Date>';
$xmlString .= $user->getUserXML();
$xmlString .= '<Id>' . $id . '</Id>';
$xmlString .= '<Date>' . date("d.m.Y") . '</Date>';
$xmlString .= '<Hour>' . date("H") . '</Hour>';
$xmlString .= '</srvInfo>';
setXML($xmlString);


$xmlString = '<dbInfo>';
if ($id > 0) {
	$xmlString .= getRequestByIdXML($id);
}

$xmlString .= '</dbInfo>';
setXML($xmlString);

pageFooter("noHead");
