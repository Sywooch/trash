<?php
require_once dirname(__FILE__) . "/../include/header.php";
require_once dirname(__FILE__) . "/../lib/php/validate.php";
require_once dirname(__FILE__) . "/php/reportLib.php";
require_once dirname(__FILE__) . "/../lib/php/dateTimeLib.php";
require_once dirname(__FILE__) . "/../request/php/requestLib.php";
require_once dirname(__FILE__) . "/../lib/php/models/clinic.class.php";
require_once dirname(__FILE__) . "/../lib/php/serviceFunctions.php";

$user = new user();
$user->checkRight4page(array('ADM', 'SAL', 'SOP'));

pageHeader(dirname(__FILE__) . "/xsl/monthRequestReport.xsl");

$crDateFrom = isset($_GET["crDateShFrom"])
	? checkField($_GET["crDateShFrom"], "t", "01." . date("m.Y"))
	: "01." . date("m.Y");

$crDateTill = isset($_GET["crDateShTill"])
	? checkField($_GET["crDateShTill"], "t", date("d.m.Y"))
	: date("d.m.Y");

$xmlString = '<srvInfo>';
$xmlString .= $user->getUserXML();
$xmlString .= '<CrDateShFrom>' . $crDateFrom . '</CrDateShFrom>';
$xmlString .= '<CrDateShTill>' . $crDateTill . '</CrDateShTill>';
$xmlString .= getCityXML();
$xmlString .= '</srvInfo>';

$city = getCityId();
setXML($xmlString);

$params = array();
$params['dateReciveFrom'] = $crDateFrom;
$params['dateReciveTill'] = $crDateTill;

$xmlString = '<dbInfo>';

$xmlString .= "<Reports>";
$monthArray = monthBetweenTwoDate($crDateFrom, $crDateTill);
$dateStartArr = explode(".", $crDateFrom);
$dateEndArr = explode(".", $crDateTill);
$lastDay = date("t", strtotime($crDateTill));

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

	$xmlString .= "<Total>" . getRequestCount(0, 'total', $startDate, $endDate, false, $city) . "</Total>";
	$xmlString .= "<Transfer>" . getRequestCount(0, 'transfer', $startDate, $endDate, false, $city) . "</Transfer>";
	$xmlString .=
		"<Apointment>" . getRequestCount(0, 'apointment', $startDate, $endDate, false, $city) . "</Apointment>";
	$xmlString .= "<Complete>" . getRequestCount(0, 'complete', $startDate, $endDate, false, $city) . "</Complete>";
	$xmlString .=
		"<ThisPeriodComplete>" .
		getRequestCount(0, 'this_period_complete', $startDate, $endDate, false, $city) .
		"</ThisPeriodComplete>";

	$contracts = getContractTypeDict();
	if (count($contracts) > 0) {
		$xmlString .= "<Contracts>";
		foreach ($contracts as $key => $val) {
			$xmlString .= "<Contract id=\"" . $val["id"] . "\">";
			$xmlString .= arrayToXML($val);
			$xmlString .=
				"<Total>" . getSummaryRequestCount('total', $startDate, $endDate, $city, $val['id']) . "</Total>";
			$xmlString .=
				"<Transfer>" .
				getSummaryRequestCount('transfer', $startDate, $endDate, $city, $val['id']) .
				"</Transfer>";
			$xmlString .=
				"<Apointment>" .
				getSummaryRequestCount('apointment', $startDate, $endDate, $city, $val['id']) .
				"</Apointment>";
			$xmlString .=
				"<Complete>" .
				getSummaryRequestCount('complete', $startDate, $endDate, $city, $val['id']) .
				"</Complete>";
			$xmlString .=
				"<ThisPeriodComplete>" .
				getSummaryRequestCount('this_period_complete', $startDate, $endDate, $city, $val['id']) .
				"</ThisPeriodComplete>";
			$xmlString .= "</Contract>";
		}
		$xmlString .= "</Contracts>";
	}

	$xmlString .= "</Report>";
}
$xmlString .= "</Reports>";
$xmlString .= '</dbInfo>';
setXML($xmlString);

pageFooter();