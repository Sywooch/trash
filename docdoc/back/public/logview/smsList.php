<?php
use dfs\docdoc\models\SmsQueryModel;

require_once dirname(__FILE__) . "/../include/header.php";
require_once dirname(__FILE__) . "/../lib/php/dictionaryLib.php";

$user = new user();
$user->checkRight4page(array('ADM'));

pageHeader(dirname(__FILE__) . "/xsl/smsList.xsl");

$startPage = (isset($_GET["startPage"])) ? checkField($_GET["startPage"], "i") : 0;
$crDateFrom = (isset($_GET["crDateShFrom"])) ? checkField($_GET["crDateShFrom"], "t", "") : '';
$crDateTill = (isset($_GET["crDateShTill"])) ? checkField($_GET["crDateShTill"], "t", "") : '';
$phoneTo = (isset($_GET["shPhone"])) ? checkField($_GET["shPhone"], "t", "") : "";
//$status	= ( isset($_GET["status"]) ) ? checkField ($_GET["status"], "t", "") : "";
$statusList = (isset($_GET["status"])) ? $_GET["status"] : array();

$type = (isset($_GET["type"])) ? checkField($_GET["type"], "t", "") : "";

$xmlString = '<srvInfo>';
$xmlString .= '<StartPage>' . $startPage . '</StartPage>';
$xmlString .= '<CrDateShFrom>' . $crDateFrom . '</CrDateShFrom>';
$xmlString .= '<CrDateShTill>' . $crDateTill . '</CrDateShTill>';
$xmlString .= '<ShPhone>' . $phoneTo . '</ShPhone>';
//$xmlString .= '<Status>'.$status.'</Status>';
if (!empty ($statusList)) {
	$xmlString .= '<StatusList>';
	foreach ($statusList as $key => $data) {
		$xmlString .= '<Status>' . $data . '</Status>';
	}
	$xmlString .= '</StatusList>';
}

$xmlString .= '<Type>' . $type . '</Type>';

$xmlString .= '</srvInfo>';
setXML($xmlString);


$params = array();
$params['crDateFrom'] = $crDateFrom;
$params['crDateTill'] = $crDateTill;
$params['phoneTo'] = $phoneTo;
//$params['status']		= $status;
unset($statusList['all']);
$statusList = array_diff($statusList, array("all"));
$params['statusList'] = $statusList;
$params['type'] = $type;

$params['step'] = "200";
$params['startPage'] = $startPage;


$xmlString = '<dbInfo>';

$xmlString .= getSMSListQuery($params);
$xmlString .= getSMSTypeList();
$xmlString .= getSMSStatusList();
$xmlString .= '</dbInfo>';
setXML($xmlString);

pageFooter();

function getSMSListQuery($params = [])
{
	$xml       = "";
	$startPage = 1;
	$step      = 50;
	$withPager = true;
	$sqlAdd    = " 1=1 ";

	if (isset($params['withPager'])) {
		$withPager = $params['withPager'];
	}

	if (isset($params) && count($params) > 0) {
		/*	Дата создания заявки	*/
		if (isset($params['crDateFrom']) && !empty ($params['crDateFrom'])) {
			$sqlAdd .= " AND date(sms.crDate) >= date('" . convertDate2DBformat($params['crDateFrom']) . "') ";
		}
		if (isset($params['crDateTill']) && !empty ($params['crDateTill'])) {
			$sqlAdd .= " AND date(sms.crDate) <= date('" . convertDate2DBformat($params['crDateTill']) . "') ";
		}
		if (isset($params['phoneTo']) && !empty ($params['phoneTo'])) {
			$phoneTo = preg_replace("/[\D]/", '', $params['phoneTo']);
			$sqlAdd .= " AND sms.phoneTo LIKE '%" . $phoneTo . "%' ";
		}
		if (isset($params['status']) && !empty ($params['status'])) {
			$sqlAdd .= " AND sms.status = '" . $params['status'] . "' ";
		}
		if (isset($params['statusList']) && count($params['statusList']) > 0) {
			$sqlAdd .= " AND ( ";
			foreach ($params['statusList'] as $status) {
				$sqlAdd .= " sms.status = '" . $status . "' OR ";
			}
			$sqlAdd = rtrim($sqlAdd, "OR ");
			$sqlAdd .= ") ";
			//$sqlAdd .= " AND sms.status = '".$params['status']."' ";
		}
		if (isset($params['type']) && !empty ($params['type'])) {
			$sqlAdd .= " AND sms.typeSMS = " . $params['type'] . " ";
		}

	}

	$sql = "SELECT
						sms.idMessage AS id,
						DATE_FORMAT( sms.crDate,'%d.%m.%Y %H:%i') AS CrDate,
						DATE_FORMAT( sms.sendDate,'%d.%m.%Y %H:%i') AS SendDate,
						sms.phoneTo,
						sms.message,
						sms.priority,
						sms.typeSMS,
						sms.status,
						smsType.title AS type,
						sms.gateId,
						sms.systemId,
						sms.ttl
					FROM `SMSQuery` sms
					LEFT JOIN `SMStype` smsType ON ( smsType.id_type = sms.typeSMS)
					WHERE " . $sqlAdd . "
					ORDER BY sms.crDate DESC";

	if (isset($params['step']) && intval($params['step']) > 0) {
		$step = $params['step'];
	}
	if (isset($params['startPage']) && intval($params['startPage']) > 0) {
		$startPage = $params['startPage'];
	}

	if ($withPager) {
		list($sql, $str) = pager($sql, $startPage, $step); // функция берется из файла pager.xsl с тремя параметрами.
		$xml .= $str;
	}

	$result = query($sql);
	if (num_rows($result) > 0) {
		$xml .= "<SMSList>";
		while ($row = fetch_object($result)) {
			$xml .= "<Element id=\"" . $row->id . "\">";
			$xml .= "<Phone digit = \"" . $row->phoneTo . "\">" . formatPhone($row->phoneTo) . "</Phone>";
			$xml .= "<CrDate>" . $row->CrDate . "</CrDate>";
			$xml .= "<SendDate>" . $row->SendDate . "</SendDate>";
			$xml .= "<Message><![CDATA[" . $row->message . "]]></Message>";
			$xml .= "<Type id=\"" . $row->typeSMS . "\">" . $row->type . "</Type>";
			$xml .= "<Priority>" . $row->priority . "</Priority>";
			$xml .= "<Status>" . $row->status . "</Status>";
			$xml .= "<GateId>" . $row->gateId . "</GateId>";
			$xml .= "<SystemId>" . $row->systemId . "</SystemId>";
			$xml .= "<Ttl>" . $row->ttl . "</Ttl>";
			$xml .= "</Element>";
		}
		$xml .= "</SMSList>";
	}

	return $xml;
}

function getSMSTypeList()
{
	$xml = "";

	$sql = "SELECT
					id_type AS id,
					title
				FROM `SMStype`
				ORDER BY title ";

	$result = query($sql);
	if (num_rows($result) > 0) {
		$xml .= "<TypeDict>";
		while ($row = fetch_object($result)) {
			$xml .= "<Element id=\"" . $row->id . "\">" . $row->title . "</Element>";
		}
		$xml .= "</TypeDict>";
	}

	return $xml;
}

function getSMSStatusList()
{
	$xml = "";

	$statusArray = SmsQueryModel::getStatuses();

	$xml .= "<SMSStatusDict>";

	foreach ($statusArray as $data) {
		$xml .= "<Element id=\"" . $data . "\">" . $data . "</Element>";
	}

	$xml .= "</SMSStatusDict>";

	return $xml;
}
