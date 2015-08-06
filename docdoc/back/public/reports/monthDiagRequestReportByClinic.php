<?php
use dfs\docdoc\objects\Rejection;

require_once dirname(__FILE__) . "/../include/header.php";
require_once dirname(__FILE__) . "/php/reportLib.php";
require_once dirname(__FILE__) . "/../lib/php/models/diag_request.class.php";
require_once dirname(__FILE__) . "/../lib/php/commonDict.php";
require_once dirname(__FILE__) . "/../lib/php/models/clinic.class.php";
require_once dirname(__FILE__) . "/../lib/php/serviceFunctions.php";

$user = new user();
$user->checkRight4page(array('ADM', 'SAL', 'SOP'));

pageHeader(dirname(__FILE__) . "/xsl/monthDiagRequestReportByClinic.xsl");

$crDateFrom = (isset($_GET["crDateShFrom"])) ? checkField($_GET["crDateShFrom"], "t", "") : "";
$crDateTill = (isset($_GET["crDateShTill"])) ? checkField($_GET["crDateShTill"], "t", "") : "";
$crDateFrom2 = (isset($_GET["crDateShFrom2"])) ? checkField($_GET["crDateShFrom2"], "t", "") : "";
$crDateTill2 = (isset($_GET["crDateShTill2"])) ? checkField($_GET["crDateShTill2"], "t", "") : "";
$clinicList = (isset($_GET["clinicList"])) ? $_GET["clinicList"] : array();

if (empty($crDateFrom) && empty($crDateFrom2)) {
	$crDateFrom = "01." . date("m.Y");
}
if (empty($crDateTill) && empty($crDateTill2)) {
	$crDateTill = date("d.m.Y");
}


$xmlString = '<srvInfo>';
$xmlString .= $user->getUserXML();
$xmlString .= '<CrDateShFrom>' . $crDateFrom . '</CrDateShFrom>';
$xmlString .= '<CrDateShTill>' . $crDateTill . '</CrDateShTill>';
$xmlString .= '<CrDateShFrom2>' . $crDateFrom2 . '</CrDateShFrom2>';
$xmlString .= '<CrDateShTill2>' . $crDateTill2 . '</CrDateShTill2>';

if (count($clinicList) > 0) {
	$xmlString .= '<ClinicList>';
	foreach ($clinicList as $clinic) {
		$clinic = intval($clinic);
		$xmlString .= '<Clinic>' . $clinic . '</Clinic>';
	}
	$xmlString .= '</ClinicList>';
}

$xmlString .= getCityXML();
$city = getCityId();
$xmlString .= '</srvInfo>';

$city = getCityId();
setXML($xmlString);


$params = array();
$params['dateReciveFrom'] = $crDateFrom;
$params['dateReciveTill'] = $crDateTill;
$params['dateAdmissionFrom'] = $crDateFrom2;
$params['dateAdmissionTill'] = $crDateTill2;

$xmlString = '<dbInfo>';

$xmlString .= getClinicLisFromArrayXML($clinicList);

$dateMethod = 'create';
if (!empty($crDateFrom) && !empty($crDateTill)) {
	$monthArray = monthBetweenTwoDate($crDateFrom, $crDateTill);
	$dateStartArr = explode(".", $crDateFrom);
	$dateEndArr = explode(".", $crDateTill);
	$lastDay = date("t", strtotime($crDateTill));

	$startTotalPeriod = $crDateFrom;
	$endTotalPeriod = $crDateTill;
	$dateMethod = 'create';
} else if (!empty($crDateFrom2) && !empty($crDateTill2)) {
	$monthArray = monthBetweenTwoDate($crDateFrom2, $crDateTill2);
	$dateStartArr = explode(".", $crDateFrom2);
	$dateEndArr = explode(".", $crDateTill2);
	$lastDay = date("t", strtotime($crDateTill2));

	$startTotalPeriod = $crDateFrom2;
	$endTotalPeriod = $crDateTill2;
	$dateMethod = 'admission';
}


$xmlString .= '<ClinicReports>';
foreach ($clinicList as $clinic) {
	$clinic = intval($clinic);

	$xmlString .= '<Clinic id="' . $clinic . '">';
	$currentClinic = new Clinic();
	$currentClinic->getClinic($clinic);
	$xmlString .= "<Settings>" . arrayToXML($currentClinic->getDiagnosticaSettings()) . "</Settings>";
	foreach ($monthArray as $line => $data) {
		if ($data[0] == '01' . "." . $dateStartArr[1] . "." . $dateStartArr[2]) {
			$startDate = $dateStartArr[0] . "." . $dateStartArr[1] . "." . $dateStartArr[2];
		} else {
			$startDate = $data[0];
		}


		if ($data[1] == $lastDay . "." . $dateEndArr[1] . "." . $dateEndArr[2]) {
			$endDate = $dateEndArr[0] . "." . $dateEndArr[1] . "." . $dateEndArr[2];
		} else {
			$endDate = $data[1];
		}


		$xmlString .= "<Report>";
		$xmlString .= "<StartDate>" . $startDate . "</StartDate>";
		$xmlString .= "<EndDate>" . $endDate . "</EndDate>";
		$month = date("m", strtotime($data[0]));
		$xmlString .= "<Month id=\"" . $month . "\">" . getRusMonth($month) . "</Month>";

		$xmlString .= "<Total>" . getDiagRequestCount($clinic, 'total', $startDate, $endDate, $dateMethod) . "</Total>";
		$xmlString .= "<Total30>" . getDiagRequestCount($clinic, 'total30', $startDate, $endDate, $dateMethod) . "</Total30>";
		$xmlString .= "<Reject>" . getDiagRequestCount($clinic, 'reject', $startDate, $endDate, $dateMethod) . "</Reject>";
		$xmlString .= "<Admission>" . getDiagRequestCount($clinic, 'admission', $startDate, $endDate, $dateMethod) . "</Admission>";
		$xmlString .= "<Complete>" . getDiagRequestCount($clinic, 'complete', $startDate, $endDate, $dateMethod) . "</Complete>";
		$xmlString .= "<Rings>" . getDiagRecordCount($clinic, $startDate, $endDate, 0, $dateMethod) . "</Rings>";
		$xmlString .= "<Rings30>" . getDiagRecordCount($clinic, $startDate, $endDate, 30, $dateMethod) . "</Rings30>";
		$xmlString .= "<RejectReason>" . arrayToXML(getRejectReason($clinic, $startDate, $endDate, DocRequest::KIND_DIAGNOSTICS, $dateMethod)) . "</RejectReason>";
		$xmlString .= "</Report>";
	}
	$xmlString .= "<Total>";
	$xmlString .= "<Reject>" . getDiagRequestCount($clinic, 'reject', $startTotalPeriod, $endTotalPeriod, $dateMethod) . "</Reject>";
	$xmlString .= "<RejectReason>" . arrayToXML(getRejectReason($clinic, $startTotalPeriod, $endTotalPeriod, DocRequest::KIND_DIAGNOSTICS, $dateMethod)) . "</RejectReason>";
	$xmlString .= "</Total>";
	$xmlString .= '</Clinic>';
}

$xmlString .= '</ClinicReports>';
$xmlString .= "<ContractListDict>" . arrayToXML(getContractList()) . "</ContractListDict>";

$rejects = Rejection::getReasons();
$rejects[] = array('Id' => 0, 'Name' => 'Неизвестно');
$xmlString .= '<RejectDict>' . arrayToXML($rejects) . '</RejectDict>';
$xmlString .= '</dbInfo>';
setXML($xmlString);

pageFooter();
