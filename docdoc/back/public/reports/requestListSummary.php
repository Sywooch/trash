<?php
require_once dirname(__FILE__) . "/../include/header.php";
require_once dirname(__FILE__) . "/../lib/php/validate.php";
require_once dirname(__FILE__) . "/php/reportLib.php";
require_once dirname(__FILE__) . "/../lib/php/dateTimeLib.php";
require_once dirname(__FILE__) . "/../request/php/requestLib.php";
require_once dirname(__FILE__) . "/../lib/php/models/clinic.class.php";

$user = new user();
$user->checkRight4page(array('ADM', 'SAL', 'SOP'));

pageHeader(dirname(__FILE__) . "/xsl/requestListSummary.xsl");

$crDateFrom = (isset($_GET["crDateShFrom"])) ? checkField($_GET["crDateShFrom"], "t", "01." . date("m.Y")) : "01." . date("m.Y");
$crDateTill = (isset($_GET["crDateShTill"])) ? checkField($_GET["crDateShTill"], "t", date("d.m.Y")) : date("d.m.Y");
$clinicId = (isset($_GET["shClinicId"])) ? checkField($_GET["shClinicId"], "i", '') : '';
$clinic = (isset($_GET["shClinic"])) ? checkField($_GET["shClinic"], "t", '') : '';
$shBranch = (isset($_GET["shBranch"])) ? checkField($_GET["shBranch"], "i", 0) : 0;
$withBranch = ($shBranch == 1) ? true : false;
$status = (isset($_GET["shStatus"])) ? checkField($_GET["shStatus"], "i", 0) : 0;

$startPage = (isset($_GET["startPage"])) ? checkField($_GET["startPage"], "i") : 0;
$sortBy = (isset($_GET['sortBy'])) ? checkField($_GET['sortBy'], "t", "") : ''; // Сортировка
$sortType = (isset($_GET['sortType'])) ? checkField($_GET['sortType'], "t", "") : ''; // Сортировка


$xmlString = '<srvInfo>';
$xmlString .= $user->getUserXML();
$xmlString .= '<CrDateShFrom>' . $crDateFrom . '</CrDateShFrom>';
$xmlString .= '<CrDateShTill>' . $crDateTill . '</CrDateShTill>';
$xmlString .= '<StartPage>' . $startPage . '</StartPage>';

$xmlString .= '<ShClinic>' . $clinic . '</ShClinic>';
$xmlString .= '<ShClinicId>' . $clinicId . '</ShClinicId>';
$xmlString .= '<Branch>' . $shBranch . '</Branch>';
$xmlString .= '<ShStatus>' . $status . '</ShStatus>';

$xmlString .= getCityXML();
// Сортировка
if (!empty($sortBy)) {
	$xmlString .= '<SortBy>' . $sortBy . '</SortBy>';
}
if (!empty($sortType)) {
	$xmlString .= '<SortType>' . $sortType . '</SortType>';
}

$xmlString .= '</srvInfo>';
setXML($xmlString);


$params = array();
$params['dateReciveFrom'] = $crDateFrom;
$params['dateReciveTill'] = $crDateTill;
$params['step'] = "100";
$params['startPage'] = $startPage;
if ($clinicId == '') {
	$clinicId = 0;
}
$params['clinic'] = $clinicId;
$params['branch'] = $shBranch;
$params['withPrice'] = true;


switch ($status) {
	case 1 :
	{
		$params['isTransfer'] = "1";
		$params['crDateFrom'] = $crDateFrom;
		$params['crDateTill'] = $crDateTill;
		$params['dateReciveFrom'] = "";
		$params['dateReciveTill'] = "";
	}
		break;
	case 3 :
		$params['status'] = "3";
		break;
	case 4 :
		$params['status'] = "8";
		break;
	case 5 :
		$params['status'] = "9";
		break;
}


// Сортировка
if (!empty($sortBy)) {
	$params['sortBy'] = $sortBy;
}
if (!empty($sortBy)) {
	$params['sortType'] = $sortType;
}

$xmlString = '<dbInfo>';

$xmlString .= "<Reports>";
$monthArray = monthBetweenTwoDate($crDateFrom, $crDateTill);
foreach ($monthArray as $line => $data) {
	$xmlString .= "<Report>";
	$xmlString .= "<StartDate>" . $data[0] . "</StartDate>";
	$xmlString .= "<EndDate>" . $data[1] . "</EndDate>";
	$month = date("m", strtotime($data[0]));
	$xmlString .= "<Month id=\"" . $month . "\">" . getRusMonth($month) . "</Month>";
	$xmlString .= "<Transfer>" . getRequestCount($clinicId, 'transfer', $data[0], $data[1], $withBranch) . "</Transfer>";
	$xmlString .= "<Apointment>" . getRequestCount($clinicId, 'apointment', $data[0], $data[1], $withBranch) . "</Apointment>";
	$xmlString .= "<Complete>" . getRequestCount($clinicId, 'complete', $data[0], $data[1], $withBranch) . "</Complete>";
	$xmlString .= "<Reject>" . getRequestCount($clinicId, 'reject', $data[0], $data[1], $withBranch) . "</Reject>";
	$xmlString .= "<Total>" . getRequestCount($clinicId, 'total', $data[0], $data[1], $withBranch) . "</Total>";
	$xmlString .= "</Report>";
}
$xmlString .= "</Reports>";
$xmlString .= '</dbInfo>';
setXML($xmlString);

pageFooter();
