<?php
require_once dirname(__FILE__) . "/../include/header.php";
require_once dirname(__FILE__) . "/../lib/php/validate.php";
require_once dirname(__FILE__) . "/php/clinicLib.php";
require_once dirname(__FILE__) . "/php/clinicStat.php";

$user = new user();
$user->checkRight4page(array('ADM', 'CNM', 'SOP', 'ACM', 'SAL'));

pageHeader(dirname(__FILE__) . "/xsl/index.xsl");

$id = (isset($_GET["id"])) ? checkField($_GET["id"], "i", 0) : 0;
$shBranch = (isset($_GET["shBranch"])) ? checkField($_GET["shBranch"], "i", 0) : 0;
$status = (isset($_GET["status"])) ? checkField($_GET["status"], "i", '') : '';
$type = (isset($_GET["type"])) ? checkField($_GET["type"], "t", '') : '';
$title = (isset($_GET["title"])) ? checkField($_GET["title"], "t", '') : '';
$alias = (isset($_GET["alias"])) ? checkField($_GET["alias"], "t", '') : '';
$phone = (isset($_GET["phone"])) ? checkField($_GET["phone"], "t", '') : '';
$startPage = (isset($_GET["startPage"])) ? checkField($_GET["startPage"], "i") : 0;
$metroList = (isset ($_GET['shMetro'])) ? checkField($_GET["shMetro"], "t", "") : "";
$moderation = ( isset($_GET["shModeration"]) ) ? checkField ($_GET["shModeration"], "i", 0) : 0;

$xmlString = '<srvInfo>';
$xmlString .= $user->getUserXML();
$xmlString .= "<CreateClinic>" . $user->checkRight4userByCode(['ADM', 'CNM', 'SOP', 'ACM']) . "</CreateClinic>";
if ($id > 0) {
	$xmlString .= '<Id>' . $id . '</Id>';
} else {
	$xmlString .= '<StartPage>' . $startPage . '</StartPage>';
	$xmlString .= '<Status>' . $status . '</Status>';
	$xmlString .= '<Type>' . $type . '</Type>';
	$xmlString .= '<Branch>' . $shBranch . '</Branch>';
	$xmlString .= '<Title>' . $title . '</Title>';
	$xmlString .= '<Alias>' . $alias . '</Alias>';
	$xmlString .= '<Phone>' . $phone . '</Phone>';
	$xmlString .= '<shMetro>' . $metroList . '</shMetro>';
	$xmlString .= '<Moderation>'.$moderation.'</Moderation>';
}
$xmlString .= getCityXML();
$xmlString .= '</srvInfo>';
setXML($xmlString);


$params = array();

if ($id > 0) {
	$params['id'] = $id;
} else {
	$params['step'] = "50";
	$params['startPage'] = $startPage;
	$params['title'] = $title;
	$params['alias'] = $alias;
	$params['phone'] = $phone;
	$params['status'] = $status;
	$params['type'] = $type;
	$params['branch'] = $shBranch;
	$params['metroList'] = $metroList;
	$params['moderation']	= $moderation;
}

$xmlString = '<dbInfo>';

$xmlString .= getClinicListXML($params, $id > 0 ? null : 0, getCityId());
$xmlString .= getClinicStatisticXML($type, getCityId());
$xmlString .= getStatusDictXML();
$xmlString .= '</dbInfo>';
setXML($xmlString);

pageFooter();


