<?php
use dfs\docdoc\models\ClinicModel;

require_once dirname(__FILE__) . "/../include/header.php";
require_once dirname(__FILE__) . "/../lib/php/validate.php";
require_once dirname(__FILE__) . "/php/reportLib.php";
require_once dirname(__FILE__) . "/../lib/php/models/clinic.class.php";

$user = new user();
$user->checkRight4page(array('ADM', 'CNM', 'SCM'));

pageHeader(dirname(__FILE__) . "/xsl/priceClinicServices.xsl");

$startPage = (isset($_GET["startPage"])) ? checkField($_GET["startPage"], "i") : 0;

$xmlString = '<srvInfo>';
$xmlString .= $user->getUserXML();
$xmlString .= '<StartPage>' . $startPage . '</StartPage>';

$xmlString .= getCityXML();
$xmlString .= '</srvInfo>';
setXML($xmlString);

$items = ClinicModel::model()
	->active()
	->inCity(getCityId())
	->onlyDiagnostic()
	->findAll();

$data = array();
foreach ($items as $item) {
	$data[] = array(
		'Id'    => $item->id,
		'Name'  => $item->name,
	);
}

$xmlString = '<dbInfo>';
$xmlString .= '<ClinicList>' . arrayToXML($data) . '</ClinicList>';
$xmlString .= '</dbInfo>';
setXML($xmlString);

pageFooter();
