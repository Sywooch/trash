<?php

use dfs\docdoc\models\ClinicModel;

require_once __DIR__ . "/../include/header.php";
require_once __DIR__ . "/../lib/php/validate.php";
require_once __DIR__ . "/php/requestLib.php";


pageHeader(dirname(__FILE__) . "/xsl/clinicList.xsl", "noHead");

$clinicId    = isset($_GET["shClinicId"]) ? checkField($_GET["shClinicId"], "i", 0) : 0;
$type        = isset($_GET['typeView']) ? $_GET['typeView'] : 'default';
$diagnostics = isset($_GET["subdiagnostica"]) ? $_GET["subdiagnostica"] : array();
$districts   = isset($_GET['shDistrict']) ? $_GET['shDistrict'] : [];

if (count($diagnostics) == 1 && in_array(0, $diagnostics)) {
	$diagnostics = array();
}

$metroList = isset($_GET['shMetro']) ? rtrim( trim($_GET['shMetro'] ), ',') : '';
$metroList = !empty($metroList) ? explode (",", $metroList) : array();
$metroIdList  = array();
if (count($metroList) > 0) {
	$metroIdList = getMetroIdList($metroList);
}

$xmlString = '<srvInfo>';
$xmlString .= "<HostFront>" . SERVER_FRONT . "</HostFront>";
$xmlString .= "<TypeView>{$type}</TypeView>";
$xmlString .= "<ClinicId>{$clinicId}</ClinicId>";

$xmlString .= getCityXML();
$xmlString .= '</srvInfo>';
setXML($xmlString);

$xmlString = '<dbInfo>';

if ($clinicId > 0) {
	$items[0] = ClinicModel::model()->findByPk($clinicId);
} else {
	$items = ClinicModel::model()
		->relevant()
		->onlyDiagnostic()
		->inCity(getCityId())
		->searchByStations($metroIdList)
		->inDistricts($districts)
		->searchByDiagnostics($diagnostics, false)
		->findAll(array('order' => 'sort4commerce'));
}

$xmlString .= "<ClinicList>";
foreach ($items as $item) {
	$xmlString .= "<Element id='{$item->id}'>";
	$xmlString .= "<Id>{$item->id}</Id>";
	$xmlString .= "<Name>{$item->name}</Name>";
	$xmlString .= "<Alias>{$item->rewrite_name}</Alias>";
	$xmlString .= "<Priority>{$item->sort4commerce}</Priority>";
	$xmlString .= "<Address>{$item->street} {$item->house}</Address>";
	$phones = getPhones4ClinicArr($item->id);

	if (!count($phones) && !empty($item->asterisk_phone)) {
		$phones[] = array(
			'Id'    => 0,
			'Phone' => $item->asterisk_phone,
			'PhoneFormat' => formatPhone($item->asterisk_phone),
			'Label' => 'Доп.',
		);
	}

	$xmlString .= "<PhoneList>" . arrayToXML($phones) . "</PhoneList>";
	$xmlString .= getMetroByClinicIdXML($item->id);
	$xmlString .= "</Element>";
}
$xmlString .= '</ClinicList>';

$xmlString .= '</dbInfo>';
setXML($xmlString);

pageFooter("noHead");
