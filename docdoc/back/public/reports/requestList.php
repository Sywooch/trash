<?php
require_once dirname(__FILE__) . "/../include/header.php";
require_once dirname(__FILE__) . "/../lib/php/validate.php";
require_once dirname(__FILE__) . "/php/reportLib.php";
require_once dirname(__FILE__) . "/../request/php/requestLib.php";
require_once dirname(__FILE__) . "/../lib/php/models/clinic.class.php";

$user = new user();
$user->checkRight4page(array('ADM', 'SAL', 'SOP'));

pageHeader(dirname(__FILE__) . "/xsl/requestList.xsl");

$crDateFrom = (isset($_GET["crDateShFrom"])) ? checkField($_GET["crDateShFrom"], "dt", "") : "";
$crDateTill = (isset($_GET["crDateShTill"])) ? checkField($_GET["crDateShTill"], "dt", "") : "";
$dateReciveFrom = (isset($_GET["dateFrom"])) ? checkField($_GET["dateFrom"], "dt", '') : '';
$dateReciveTill = (isset($_GET["dateTill"])) ? checkField($_GET["dateTill"], "dt", '') : '';


$clinicId = (isset($_GET["shClinicId"])) ? checkField($_GET["shClinicId"], "i", '') : '';
$clinic = (isset($_GET["shClinic"])) ? checkField($_GET["shClinic"], "t", '') : '';
$shBranch = (isset($_GET["shBranch"])) ? checkField($_GET["shBranch"], "i", 0) : 0;
$status = (isset($_GET["shStatus"])) ? checkField($_GET["shStatus"], "i", 0) : 0;
$state = (isset($_GET["shState"])) ? checkField($_GET["shState"], "i", 0) : 0;

$startPage = (isset($_GET["startPage"])) ? checkField($_GET["startPage"], "i") : 0;
$sortBy = (isset($_GET['sortBy'])) ? checkField($_GET['sortBy'], "t", "") : ''; // Сортировка
$sortType = (isset($_GET['sortType'])) ? checkField($_GET['sortType'], "t", "") : ''; // Сортировка

if (empty($crDateFrom) && empty($crDateTill) &&
	empty($dateReciveFrom) && empty($dateReciveTill)
) {

	$crDateFrom = "01." . date("m.Y");
	$crDateTill = date("d.m.Y");
}


$xmlString = '<srvInfo>';
$xmlString .= $user->getUserXML();
$xmlString .= '<CrDateShFrom>' . $crDateFrom . '</CrDateShFrom>';
$xmlString .= '<CrDateShTill>' . $crDateTill . '</CrDateShTill>';
$xmlString .= '<CrDateReciveFrom>' . $dateReciveFrom . '</CrDateReciveFrom>';
$xmlString .= '<CrDateReciveTill>' . $dateReciveTill . '</CrDateReciveTill>';
$xmlString .= '<StartPage>' . $startPage . '</StartPage>';

$xmlString .= '<ShClinic>' . $clinic . '</ShClinic>';
$xmlString .= '<ShClinicId>' . $clinicId . '</ShClinicId>';
$xmlString .= '<Branch>' . $shBranch . '</Branch>';
$xmlString .= '<ShStatus>' . $status . '</ShStatus>';
$xmlString .= '<ShState>' . $state . '</ShState>';

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
$params['crDateFrom'] = $crDateFrom;
$params['crDateTill'] = $crDateTill;
$params['step'] = "100";
$params['startPage'] = $startPage;
if ($clinicId == '') {
	$clinicId = 0;
}
$params['clinic'] = $clinicId;
$params['branch'] = $shBranch;
$params['withPrice'] = true;
$params['kind'] = DocRequest::KIND_DOCTOR;


if ($status > 0) {
	$params['status'] = $status;
}

if (!empty($crDateFrom) && !empty($crDateTill)) {
	$params['crDateFrom'] = $crDateFrom;
	$params['crDateTill'] = $crDateTill;
}
if (!empty($dateReciveFrom) && !empty($dateReciveTill)) {
	$params['dateReciveFrom'] = $dateReciveFrom;
	$params['dateReciveTill'] = $dateReciveTill;
}


switch ($state) {
	case 1 :
	{
		$params['isTransfer'] = "1";
	}
		break;
	case 2 :
	{
		$params['isDateAdmission'] = "1";
	}
		break;
	case 3 :
	{
		$params['status'] = "3";
	}
		break;
	case 4 :
		$params['status'] = "5";
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
$xmlString .= getRequestListXML($params, getCityId());
if ($clinicId > 0) {
	$xmlString .= getClinicListByIdWithBranchesXML($clinicId);
}
$xmlString .= getStatus4RequestXML();
$xmlString .= getRequestStatus4ReportXML();
$xmlString .= '</dbInfo>';
setXML($xmlString);

pageFooter();
