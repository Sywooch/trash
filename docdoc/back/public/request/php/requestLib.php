<?php
use dfs\docdoc\models\PartnerModel;
use dfs\docdoc\models\QueueModel;
use dfs\docdoc\models\BookingModel;
use dfs\docdoc\models\RequestModel;
use dfs\docdoc\models\ClinicModel;
use dfs\docdoc\models\DiagnosticaModel;
use dfs\docdoc\models\DoctorClinicModel;

require_once dirname(__FILE__) . "/../../lib/php/validate.php";
require_once dirname(__FILE__) . "/../../lib/php/dateconvertionLib.php";
require_once dirname(__FILE__) . "/../../doctor/php/doctorLib.php";
require_once dirname(__FILE__) . "/../../lib/php/rating.php";
require_once __DIR__ . "/../../lib/php/models/Record.php";



/**
 * Получение параметров фильтров
 *
 * @return array
 */
function getRequestParams()
{
	$params = array();
	$filterParams = array();

	$dateToday = date("d.m.Y");

	$addLineState   = isset($_GET["addLineState"]) ? checkField($_GET["addLineState"], "t", "expand") : 'expand';
	$id             = isset($_GET["id"]) ? checkField($_GET["id"], "i", 0) : 0;
	$clinicId       = isset($_GET["shClinicId"]) ? checkField($_GET["shClinicId"], "i", '') : '';
	$clinicNotFound = (isset($_GET['clinicNotFound']) && $_GET['clinicNotFound'] == 'on') ? 1 : 0;
	$withBranches   = isset($_GET['withBranches']) ? 1 : 0;
	$crDateFrom     = isset($_GET["crDateShFrom"]) ? checkField($_GET["crDateShFrom"], "t", "") : '';
	$crDateFrom     = !isset($_GET['today']) ? $crDateFrom : $dateToday;
	$crDateTill     = isset($_GET["crDateShTill"]) ? checkField($_GET["crDateShTill"], "t", "") : '';
	$dateReciveFrom = isset($_GET["crDateReciveFrom"]) ? checkField($_GET["crDateReciveFrom"], "t", "") : '';
	$dateReciveTill = isset($_GET["crDateReciveTill"]) ? checkField($_GET["crDateReciveTill"], "t", "") : '';
	$recDateFrom    = isset($_GET["recDateShFrom"]) ? checkField($_GET["recDateShFrom"], "dt", "") : "";
	$recDateTill    = isset($_GET["recDateShTill"]) ? checkField($_GET["recDateShTill"], "dt", "") : "";
	$phone          = isset($_GET["phone"]) ? checkField($_GET["phone"], "t", "") : '';
	$client         = isset($_GET["client"]) ? checkField($_GET["client"], "t", "") : '';
	$startPage      = isset($_GET["startPage"]) ? checkField($_GET["startPage"], "i") : 0;
	$sortBy         = isset($_GET['sortBy']) ? checkField($_GET['sortBy'], "t", "") : ''; // Сортировка
	$sortType       = isset($_GET['sortType']) ? checkField($_GET['sortType'], "t", "") : ''; // Сортировка
	$typeArr        = isset($_GET["shType"]) ? checkArrayToInt($_GET["shType"]) : array();
	$kindArr        = isset($_GET["shKind"]) ? checkArrayToInt($_GET["shKind"]) : array();
	$statusArr      = isset($_GET["shStatus"]) ? checkArrayToInt($_GET["shStatus"]) : array();
	$specArr        = isset($_GET['shSectorId']) ? checkArrayToInt($_GET["shSectorId"]) : array();
	$ownerArr       = isset($_GET['shOwner']) ? checkArrayToInt($_GET["shOwner"]) : array();
	$sourceTypeArr  = isset($_GET["shSourceType"]) ? checkArrayToInt($_GET["shSourceType"]) : array();
	$destPhoneArr   = isset($_GET['destinationPhoneId']) ? checkArrayToInt($_GET["destinationPhoneId"]) : array();
	$diagnosticArr  = isset($_GET['diagnostica']) ? checkArrayToInt($_GET["diagnostica"]) : array();
	$partnerArr     = isset($_GET['partner']) ? checkArrayToInt($_GET["partner"]) : array();
	$type           = isset($_GET['type']) ? $_GET['type'] : 'default';
	$partnerStatusArr = isset($_GET["shPartnerStatus"]) ? checkArrayToInt($_GET["shPartnerStatus"]) : array();
	$billingStatusArr = isset($_GET["shBillingStatus"]) ? checkArrayToInt($_GET["shBillingStatus"]) : array();
	$cityArr        = isset($_GET["shCity"]) ? checkArrayToInt($_GET["shCity"]) : array();
	$hasDeparture   = Yii::app()->request->getQuery("hasDeparture") ? 1 : 0;
	$step           = isset($_GET["step"]) ? $_GET["step"] : 50;

	// Параметры для выборки

	if ($id > 0) {
		$params['id'] = $id;
	} else {
		$params['step'] = $step;
		$params['startPage'] = $startPage;
		$params['crDateFrom'] = $crDateFrom;
		$params['crDateTill'] = $crDateTill;
		$params['dateReciveFrom'] = $dateReciveFrom;
		$params['dateReciveTill'] = $dateReciveTill;
		$params['dateRecFrom'] = $recDateFrom;
		$params['dateRecTill'] = $recDateTill;
		$params['status'] = implode(',', $statusArr);
		$params['type'] = implode(',', $typeArr);
		$params['kind'] = implode(',', $kindArr);
		$params['sourceType'] = implode(',', $sourceTypeArr);
		$params['shOwner'] = implode(',', $ownerArr);
		$params['shSector'] = implode(',', $specArr);
		$params['partner'] = implode(',', $partnerArr);
		$params['diagnostics'] = implode(',', $diagnosticArr);
		$params['phone'] = $phone;
		$params['destinationPhoneId'] = implode(',', $destPhoneArr);
		$params['client'] = $client;
		$params['clinic'] = $clinicId;
		$params['clinicNotFound'] = $clinicNotFound;
		$params['branch'] = $withBranches;
		$params['partnerStatus'] = implode(',', $partnerStatusArr);
		$params['billingStatus'] = implode(',', $billingStatusArr);
		$params['shCity'] = implode(',', $cityArr);
		$params['hasDeparture'] = $hasDeparture;

		// Сортировка
		if (!empty($sortBy)) {
			$params['sortBy'] = $sortBy;
		}
		if (!empty($sortBy)) {
			$params['sortType'] = $sortType;
		}
	}

	$interface = new RequestInterface($type);
	if ($interface->isCallCenter()) {
		if (!count($typeArr)) {
			$typeArr = array(
				RequestModel::TYPE_WRITE_TO_DOCTOR,
				RequestModel::TYPE_PICK_DOCTOR,
				RequestModel::TYPE_CALL,
			);
		}
		$params['type'] = implode(',', $typeArr);
	} elseif ($interface->isListener()) {
		$params['type'] = DocRequest::TYPE_CALL_TO_DOCTOR;
	}

	// Параметры для фильтров
	$filterParams['typeView'] = $type;
	$filterParams['addLineState'] = $addLineState;
	$filterParams['startPage'] = $startPage;
	$filterParams['sortBy'] = $sortBy;
	$filterParams['sortType'] = $sortType;
	$filterParams['shType'] = $typeArr;
	$filterParams['shKind'] = $kindArr;
	$filterParams['shSourceType'] = $sourceTypeArr;
	$filterParams['shStatus'] = $statusArr;
	$filterParams['crDateShFrom'] = $crDateFrom;
	$filterParams['crDateShTill'] = $crDateTill;
	$filterParams['shSectorId'] = $specArr;
	$filterParams['diagnostica'] = $diagnosticArr;
	$filterParams['diagnostics'] = implode(',', $diagnosticArr);
	$filterParams['shOwner'] = $ownerArr;
	$filterParams['crDateReciveFrom'] = $dateReciveFrom;
	$filterParams['crDateReciveTill'] = $dateReciveTill;
	$filterParams['dateRecFrom'] = $recDateFrom;
	$filterParams['dateRecTill'] = $recDateTill;
	$filterParams['phone'] = $phone;
	$filterParams['destinationPhoneId'] = $destPhoneArr;
	$filterParams['client'] = $client;
	$filterParams['id'] = $id;
	$filterParams['clinicId'] = $clinicId;
	$filterParams['clinicNotFound'] = $clinicNotFound;
	$filterParams['withBranches'] = $withBranches;
	$filterParams['partner'] = $partnerArr;
	$filterParams['shPartnerStatus'] = $partnerStatusArr;
	$filterParams['shBillingStatus'] = $billingStatusArr;
	$filterParams['shCity'] = $cityArr;
	$filterParams['hasDeparture'] = $hasDeparture;
	$filterParams['step'] = $step;

	return array(
		'params'   => $params,
		'filters'  => $filterParams,
	);
}

/**
 * Получение параметров фильтра в виде строки XML
 *
 * @param array $params
 *
 * @return string
 */
function getRequestFilterParamsXML($params = array())
{
	$xmlString = "";

	$requestStr = "?";
	if (is_array($params) && count($params) > 0) {
		$requestStr .= http_build_query($params);
	}

	$xmlString .= '<RequestFilterString><![CDATA[' . rtrim($requestStr, "?") . ']]></RequestFilterString>';

	// Сортировка
	if (!empty($params['sortBy'])) {
		$xmlString .= '<SortBy>' . $params['sortBy'] . '</SortBy>';
	}
	if (!empty($params['sortType'])) {
		$xmlString .= '<SortType>' . $params['sortType'] . '</SortType>';
	}

	$xmlString .= "<TypeView>" . $params['typeView'] . "</TypeView>";

	if ($params['id'] > 0) {
		$xmlString .= '<Id>' . $params['id'] . '</Id>';
	}
	$xmlString .= '<StartPage>' . $params['startPage'] . '</StartPage>';
	$xmlString .= '<CrDateShFrom>' . $params['crDateShFrom'] . '</CrDateShFrom>';
	$xmlString .= '<CrDateShTill>' . $params['crDateShTill'] . '</CrDateShTill>';
	$xmlString .= '<DateReciveFrom>' . $params['crDateReciveFrom'] . '</DateReciveFrom>';
	$xmlString .= '<DateReciveTill>' . $params['crDateReciveTill'] . '</DateReciveTill>';
	$xmlString .= '<RecDateShFrom>' . $params['dateRecFrom'] . '</RecDateShFrom>';
	$xmlString .= '<RecDateShTill>' . $params['dateRecTill'] . '</RecDateShTill>';
	$xmlString .= '<Phone>' . $params['phone'] . '</Phone>';
	$xmlString .= '<Client>' . $params['client'] . '</Client>';
	$xmlString .= "<ClinicId>" . $params['clinicId'] . "</ClinicId>";
	$xmlString .= "<DiagnosticaList>" . $params['diagnostics'] . "</DiagnosticaList>";
	$xmlString .= "<ClinicNotFound>" . $params['clinicNotFound'] . "</ClinicNotFound>";
	$xmlString .= "<HasDeparture>" . $params['hasDeparture'] . "</HasDeparture>";
	$xmlString .= "<Step>" . $params['step'] . "</Step>";
	$xmlString .= "<WithBranches>" . $params['withBranches'] . "</WithBranches>";

	$clinic = $params['clinicId'] ? ClinicModel::model()->findByPk($params['clinicId']) : null;
	$clinicName = $clinic ? $clinic->name : '';
	$xmlString .= "<ClinicName>{$clinicName}</ClinicName>";
	$clinicShortName = ($clinic instanceof ClinicModel) ? $clinic->short_name : '';
	$xmlString .= "<ClinicShortName>" . $clinicShortName . "</ClinicShortName>";

	// Выбранные
	$filterParams = array();
	$filterParams['Kind'] = $params['shKind'];
	$filterParams['Type'] = $params['shType'];
	$filterParams['Status'] = $params['shStatus'];
	$filterParams['Spec'] = $params['shSectorId'];
	$filterParams['Owner'] = $params['shOwner'];
	$filterParams['SourceType'] = $params['shSourceType'];
	$filterParams['Diagnostica'] = $params['diagnostica'];
	$filterParams['DestinationPhone'] = $params['destinationPhoneId'];
	$filterParams['Partner'] = $params['partner'];
	$filterParams['PartnerStatus'] = $params['shPartnerStatus'];
	$filterParams['BillingStatus'] = $params['shBillingStatus'];
	$filterParams['City'] = $params['shCity'];
	$xmlString .= RequestInterface::getFilterXml($filterParams);
	$xmlString .= "<StepList><Step>50</Step><Step>100</Step><Step>500</Step></StepList>";

	return $xmlString;
}

/**
 * Получение списка заявок
 *
 * @param array $params
 * @param int $cityId
 * @return string
 */
function getRequestListXML($params = array(), $cityId = 1)
{
	$xml = "";

	$sqlSort = " ORDER BY t1.is_hot DESC, st, t1.req_created DESC, t1.req_id";
	$startPage = 1;
	$step = 50;
	$withPager = true;

	if (isset($params['withPager'])) {
		$withPager = $params['withPager'];
	}

	if (isset($params['status']) && $params['status'] !== '') {
		$sqlAdd = " t1.req_status IN ({$params['status']}) ";
	} else {
		$sqlAdd = " t1.req_status <> 4 ";
	}

	if (isset($params['shCity'])) {
		if ($params['shCity']) {
			$sqlAdd .= " AND t1.id_city IN ({$params['shCity']}) ";
		}
	}
	elseif ($cityId !== null) {
		$sqlAdd .= " AND t1.id_city = {$cityId} ";
	}

	if (isset($params['partnerStatus']) && $params['partnerStatus'] !== '') {
		$sqlAdd .= " AND t1.partner_status IN ({$params['partnerStatus']}) ";
	}

	if (isset($params['billingStatus']) && $params['billingStatus'] !== '') {
		$sqlAdd .= " AND t1.billing_status IN ({$params['billingStatus']}) ";
	}

	if (count($params) > 0) {
		if (isset($params['type']) && $params['type'] !== '') {
			$sqlAdd .= " AND t1.req_type IN ({$params['type']}) ";
		}
		if (isset($params['kind']) && $params['kind'] !== '') {
			$sqlAdd .= " AND t1.kind IN ({$params['kind']}) ";
		}
		if (isset($params['for_listener']) && is_int($params['for_listener'])) {
			$sqlAdd .= " AND t1.for_listener = {$params['for_listener']} ";
		}
		if (isset($params['sourceType']) && $params['sourceType'] !== '') {
			$sqlAdd .= " AND t1.source_type IN ({$params['sourceType']}) ";
		}

		if (isset($params['shOwner']) && strlen($params['shOwner'])) {
			if ($params['shOwner'] !== '0') {
				$sqlAdd .= " AND t1.req_user_id IN ({$params['shOwner']}) ";
			} else {
				$sqlAdd .= " AND (t1.req_user_id=0 OR t1.req_user_id IS NULL) ";
			}
		}
		if (isset($params['clinic']) && $params['clinic'] > 0) {
			if (isset($params['branch']) && intval($params['branch']) == 1) {
				$subSQL = "SELECT id FROM clinic WHERE id={$params['clinic']} OR parent_clinic_id={$params['clinic']}";
				$sqlAdd .= " AND t1.clinic_id IN ({$subSQL}) ";
			} else {
				$sqlAdd .= " AND t1.clinic_id = {$params['clinic']} ";
			}
		}
		if (isset($params['clinicNotFound']) && $params['clinicNotFound']) {
			$sqlAdd .= " AND (t1.clinic_id = 0 OR t1.clinic_id IS NULL) ";
		}

		/*	Дата создания заявки	*/
		if (isset($params['crDateFrom']) && !empty(strtotime($params['crDateFrom']))) {
			$sqlAdd .= " AND t1.req_created >= " . strtotime($params['crDateFrom']) . " ";
		}
		if (isset($params['crDateTill']) && !empty(strtotime($params['crDateTill']))) {
			$sqlAdd .= " AND t1.req_created <= " . (strtotime($params['crDateTill']) + 86400) . " ";
		}

		/*	Дата приёма	*/
		if (isset($params['dateReciveFrom']) && !empty(strtotime($params['dateReciveFrom']))) {
			$sqlAdd .= " AND t1.date_admission >= " . strtotime($params['dateReciveFrom']) . " AND t1.date_admission IS NOT NULL ";
		}
		if (isset($params['dateReciveTill']) && !empty(strtotime($params['dateReciveTill']))) {
			$sqlAdd .= " AND t1.date_admission <= " . (strtotime($params['dateReciveTill']) + 86400) . " ";
		}
		/*	Дата записи	*/
		if (isset($params['dateRecFrom']) && !empty($params['dateRecFrom'])) {
			$dateRecFrom = new Datetime($params['dateRecFrom']);
			$sqlAdd .= " AND t1.date_record >= '" . $dateRecFrom->format('Y-m-d H:i:s') . "' ";
		}
		if (isset($params['dateRecTill']) && !empty($params['dateRecTill'])) {
			$dateRecTill = new Datetime($params['dateRecTill']);
			$dateRecTill->setTime(23, 59, 59);
			$sqlAdd .= " AND t1.date_record <= '" . $dateRecTill->format('Y-m-d H:i:s') . "' ";
		}

		if (isset($params['shDoctorId']) && !empty ($params['shDoctorId'])) {
			$sqlAdd .= " AND t1.doctor_id = " . $params['shDoctorId'] . " ";
		}
		/*	Специализация	*/
		if (isset($params['shSector']) && strlen($params['shSector'])) {
			if ($params['shSector'] !== '0') {
				$sqlAdd .= " AND t1.req_sector_id IN ({$params['shSector']}) ";
			} else {
				$sqlAdd .= " AND (t1.req_sector_id=0 OR t1.req_sector_id IS NULL) ";
			}
		}
		/*	Диагностика	*/
		if	(isset($params['diagnostics']) && !empty($params['diagnostics'])) {
			$sqlAdd .= " AND t1.diagnostics_id IN ({$params['diagnostics']}) ";
		}
		/*	Клиент	*/
		if (isset($params['client']) && !empty ($params['client'])) {
			$sqlAdd .= " AND LOWER(t1.client_name) LIKE  '%" . strtolower($params['client']) . "%' ";
		}

		/*	Переведён	*/
		if (isset($params['isTransfer']) && intval($params['isTransfer']) == 1) {
			$sqlAdd .= " AND t1.is_transfer = '1' ";
		}

		/*	Установлена дата приёма	*/
		if (isset($params['isDateAdmission']) && intval($params['isDateAdmission']) == 1) {
			$sqlAdd .= " AND t1.date_admission IS NOT NULL AND t1.date_admission > 0 ";
		}

		/*	Телефон	*/
		if (isset($params['phone']) && !empty ($params['phone'])) {
			$phone = preg_replace("/[\D]/", '', $params['phone']);

			$sqlAdd .=
				" AND (t1.client_phone LIKE  '%" .
				$phone .
				"%' OR t1.add_client_phone LIKE  '%" .
				$phone .
				"%') ";
		}

		if (isset($params['destinationPhoneId']) && !empty($params['destinationPhoneId'])) {
			$sqlAdd .= " AND t1.destination_phone_id IN ({$params['destinationPhoneId']}) ";
		}

		if (isset($params['id']) && !empty ($params['id'])) {
			$sqlAdd = " t1.req_id = '" . $params['id'] . "'";
		}

		if (isset($params['partner']) && strlen($params['partner'])) {
			if ($params['partner'] !== '0') {
				$sqlAdd .= " AND t1.partner_id IN ({$params['partner']})";
			} else {
				$sqlAdd .= " AND (t1.partner_id=0 OR t1.partner_id IS NULL) ";
			}
		}

		if (isset($params['sortBy'])) {
			switch ($params['sortBy']) {
				case 'crDate'    :
					$sortBy = " t1.req_created";
					break;
				case 'status'    :
					$sortBy = " st";
					break;
				case 'call_later'    :
					$sortBy = " call_later_time";
					break;
				case 'admDate'        :
					$sortBy = " t1.date_admission ";
					break;
				case 'id'        :
					$sortBy = " t1.req_id ";
					break;
				default:
					$sortBy = " t1.req_created ";
					break;
			}
			if (isset($params['sortType']) && $params['sortType'] == 'asc') {
				$sqlSort = " order by " . $sortBy . " asc";
			} else {
				$sqlSort = " order by " . $sortBy . " desc";
			}
		}

		if (!empty($params['hasDeparture'])) {
			$sqlAdd .= " AND t1.req_departure = 1 ";
		}
	}

	// 112014
	$sqlAdd .= " AND (t1.partner_id IS NULL OR t1.partner_id <> " . PartnerModel::SMART_MEDIA_2 . ")";

	$sql = "SELECT
					t1.req_id as id, 
					t1.clinic_id,
					t1.client_name, t1.client_phone,
					t1.req_created, t1.req_status as status, t1.req_type, t1.kind,
					t1.clientId, t1.call_later_time,
					t1.req_doctor_id as doctor_id, t2.name as doctor, t1.req_sector_id,
					t1.diagnostics_id, t1.diagnostics_other,
					t1.req_user_id as owner, t3.user_lname, t3.user_fname, t3.user_email,
					t4.name as sector,
					t1.date_admission, t1.appointment_status,
					t1.source_type,
					t1.is_hot,
					t1.partner_id,
					t5.login as partner_name,
					t1.partner_status,
					t1.billing_status,
					t1.partner_cost,
					t1.id_city,
					CASE 
						WHEN t1.req_status  = 0 THEN 0
						WHEN t1.req_status  = 6 THEN 1
						WHEN t1.req_status  = 1 THEN 2
						WHEN t1.req_status  = 2 THEN 3
						WHEN t1.req_status  = 3 THEN 4
						WHEN t1.req_status  = 7 THEN 5
						WHEN t1.req_status  = 5 THEN 6
						WHEN t1.req_status  = 4 THEN 7
						ELSE 0
					END AS st ,
					(SELECT SUM(t6.Duration) FROM request_record t6 WHERE t6.request_id = t1.req_id) AS Duration,
					t6.name AS clinic_name
				FROM request  t1
				LEFT JOIN doctor t2 ON t2.id = t1.req_doctor_id
				LEFT JOIN `user` t3 ON t3.user_id = t1.req_user_id
				LEFT JOIN sector t4 ON t4.id = t1.req_sector_id
				LEFT JOIN partner t5 ON t1.partner_id = t5.id
				LEFT JOIN clinic t6 ON t6.id = t1.clinic_id
				WHERE
					" . $sqlAdd . $sqlSort;

	$sqlForCountEstimate = "SELECT
					count(*)
				FROM request  t1
				LEFT JOIN doctor t2 ON t2.id = t1.req_doctor_id
				LEFT JOIN `user` t3 ON t3.user_id = t1.req_user_id
				LEFT JOIN sector t4 ON t4.id = t1.req_sector_id
				WHERE
					" . $sqlAdd;

	if (isset($params['step']) && intval($params['step']) > 0) $step = $params['step'];
	if (isset($params['startPage']) && intval($params['startPage']) > 0) $startPage = $params['startPage'];

	if ($withPager) {
		list($sql, $str) = pager($sql, $startPage, $step, "request-list", $sqlForCountEstimate); // функция берется из файла pager.xsl с тремя параметрами. параметр article тут не нужен
		$xml .= $str;
	}

	$partnerStatuses = RequestModel::getPartnerStatuses();
	$billingStatuses = RequestModel::getBillingStatusList();

	$result = query($sql);
	if (num_rows($result) > 0) {
		$xml .= "<RequestList>";
		while ($row = fetch_object($result)) {
			$specName = '';
			if ($row->kind == RequestModel::KIND_DOCTOR) {
				$specName = $row->sector;
			} elseif ($row->kind == RequestModel::KIND_DIAGNOSTICS) {
				$diagnostic = DiagnosticaModel::model()->findByPk($row->diagnostics_id);
				if (!is_null($diagnostic)) {
					$specName = $diagnostic->getFullName();
				} elseif (!is_null($row->diagnostics_other)) {
					$specName = $row->diagnostics_other;
				}
			}
			$xml .= "<Element id=\"" . $row->id . "\">";
			$xml .= "<Id>" . $row->id . "</Id>";
			$xml .= "<CityId>" . $row->id_city . "</CityId>";
			$xml .= "<Doctor  id=\"" . $row->doctor_id . "\">" . $row->doctor . "</Doctor>";
			$xml .= "<Sector  id=\"" . $row->req_sector_id . "\">" . $row->sector . "</Sector>";
			$xml .= "<DiagnosticsId>{$row->diagnostics_id}</DiagnosticsId>";
			$xml .= "<DiagnosticsOther>{$row->diagnostics_other}</DiagnosticsOther>";
			$xml .= "<SpecName>{$specName}</SpecName>";
			$xml .= "<CrDate>" . date("d.m.y", $row->req_created) . "</CrDate>";
			$xml .= "<CrTime>" . date("H:i", $row->req_created) . "</CrTime>";
			$xml .= "<Client id=\"" . $row->clientId . "\"><![CDATA[" . mb_convert_case($row->client_name, MB_CASE_TITLE, 'UTF8') . "]]></Client>";
			$xml .= "<ClientPhone>" . formatPhone($row->client_phone) . "</ClientPhone>";
			$xml .= "<ClinicId>" . $row->clinic_id . "</ClinicId>";
			$clinicName = !empty($row->clinic_id) ? $row->clinic_name : '';
			$xml .= "<ClinicName>{$clinicName}</ClinicName>";

			$xml .= "<AppointmentStatus>" . $row->appointment_status . "</AppointmentStatus>";
			if (!empty($row->date_admission)) {
				$xml .= "<AppointmentDate>" . date("d.m.y", $row->date_admission) . "</AppointmentDate>";
				$xml .= "<AppointmentTime>" . date("H:i", $row->date_admission) . "</AppointmentTime>";
			}
			if (!empty($row->call_later_time)) {
				$xml .= "<CallLaterDate>" . date("d.m.y", $row->call_later_time) . "</CallLaterDate>";
				$xml .= "<CallLaterTime>" . date("H:i", $row->call_later_time) . "</CallLaterTime>";
				$xml .= "<RemainTime>" . (time() - $row->call_later_time) . "</RemainTime>";
			}
			$xml .= "<Owner id=\"" . $row->owner . "\">" . $row->user_lname . " " . $row->user_fname . "</Owner>";
			$xml .= "<Duration>" . formatTime($row->Duration) . "</Duration>";
			$xml .= "<Status>{$row->status}</Status>";
			$xml .= "<PartnerName id=\"{$row->partner_id}\">{$row->partner_name}</PartnerName>";
			$xml .= "<IsHot>{$row->is_hot}</IsHot>";
			$xml .= "<Type>" . $row->req_type . "</Type>";
			$xml .= "<Kind>{$row->kind}</Kind>";
			$xml .= "<SourceType>" . $row->source_type . "</SourceType>";
			$xml .= getCommentListXML($row->id);
			$xml .= '<PartnerCost>' . $row->partner_cost . '</PartnerCost>';

			$xml .= '<PartnerStatus id="' . $row->partner_status . '">'
				. (isset($partnerStatuses[$row->partner_status]) ? $partnerStatuses[$row->partner_status] : '')
				. '</PartnerStatus>';

			$xml .= '<BillingStatus id="' . $row->billing_status . '">'
				. (isset($billingStatuses[$row->billing_status]) ? $billingStatuses[$row->billing_status] : '')
				. '</BillingStatus>';

			if (isset($params['withPrice']) && $params['withPrice'] == true) {
				$priceParam = array();
				$priceParam ['doctor'] = $row->doctor_id;
				$priceParam ['clinic'] = $row->clinic_id;
				$priceParam ['sector'] = $row->req_sector_id;
				$priceParam ['partnerName'] = $row->partner_name;
				$priceParam ['reqCreated'] = $row->req_created;
				$xml .= "<Price>" . getPrice4Request($priceParam) . "</Price>";
			}
			$xml .= "</Element>";
		}
		$xml .= "</RequestList>";
	}


	return $xml;
}

/**
 * Получение XML всех видов заявок
 * @return array
 */
function getKindsXML()
{
	$data = array();
	$kinds = DocRequest::getKindNames();
	foreach ($kinds as $key => $item) {
		$data[$key]['id'] = $key;
		$data[$key]['Name'] = $item;
	}
	return "<KindList>" . arrayToXML($data) . "</KindList>";
}

/**
 * Список XML всех видов заявок
 *
 * @return string XML
 */
function getKinds4RequestXML()
{
	$xml = "";
	$xml .= "<KindDict>";

	$kinds = DocRequest::getKindNames();
	foreach ($kinds as $key => $item) {
		$xml .= "<Element id=\"{$key}\">{$item}</Element>";
	}

	$xml .= "</KindDict>";

	return $xml;
}

function getRequestByIdXML($id = 0)
{
	$xml = "";

	$id = intval($id);

	if ($id > 0) {
		$sql = "SELECT
					t1.req_id as id, 
					t1.clinic_id, 
					t1.client_name, t1.client_phone, t1.add_client_phone,
					t1.req_created, t1.date_record,
					t1.req_status as status, 
					t1.lk_status as LKStatus,
					t1.diagnostics_id, t1.diagnostics_other,
					t1.req_type, t1.req_sector_id, t1.source_type, t1.kind,
					t1.clientId, t1.call_later_time,t1.req_departure as isGoHome,
					t1.req_doctor_id as doctor_id, t2.name as doctor, t1.req_sector_id, 
					t1.req_user_id as owner, t3.user_lname, t3.user_fname, t3.user_email,
					t4.name as sector,
					t1.date_admission, t1.appointment_status, t2.status as doctorStatus, t1.is_transfer, 
					cl.id as clinicId, cl.name as clinic,
					t1.client_comments, t1.age_selector,  t1.id_city,
					t1.clientId as clientId, 
					t1.reject_reason,
					b.id as booking_id,
					dc.id as doctor_4_clinic_id,
					t1.billing_status,
					t1.partner_status,
					t1.partner_id,
					p.name as partner_name,
					b.id as booking_id
				FROM request  t1
				LEFT JOIN doctor t2 ON (t2.id = t1.req_doctor_id)
				LEFT JOIN `user` t3 ON (t3.user_id = t1.req_user_id)
				LEFT JOIN `clinic` cl ON (cl.id = t1.clinic_id)
				LEFT JOIN sector t4 ON (t4.id = t1.req_sector_id)
				LEFT JOIN partner p ON (p.id = t1.partner_id)
				LEFT JOIN booking b on (b.request_id = t1.req_id and b.status in (". implode(',', BookingModel::model()->getSuccessStatuses())."))
				left join doctor_4_clinic dc on dc.clinic_id = t1.clinic_id and dc.doctor_id = t1.req_doctor_id and dc.type = " . DoctorClinicModel::TYPE_DOCTOR . "
				WHERE t1.req_id = $id
				GROUP BY t1.req_id";
		//echo $sql;
		$result = query($sql);
		if (num_rows($result) == 1) {
			$row = fetch_object($result);
			$xml .= "<Request id=\"" . $row->id . "\">";
			$xml .= "<CityId>" . $row->id_city . "</CityId>";
			$xml .= "<Doctor  id=\"" . $row->doctor_id . "\" status=\"" . $row->doctorStatus . "\">" . $row->doctor . "</Doctor>";
			$xml .= "<Sector  id=\"" . $row->req_sector_id . "\">" . $row->sector . "</Sector>";
			$xml .= "<DiagnosticsId>{$row->diagnostics_id}</DiagnosticsId>";
			$xml .= "<DiagnosticsOther>{$row->diagnostics_other}</DiagnosticsOther>";
			$xml .= "<Clinic  id=\"" . $row->clinic_id . "\">" . $row->clinic . "</Clinic>";
			$xml .= "<Client  id=\"" . $row->clientId . "\">";
			$xml .= "<Name><![CDATA[" . $row->client_name . "]]></Name>";
			$xml .= "</Client>";
			$xml .= getClinicXML($row->doctor_id);
			$xml .= "<CrDate>" . date("d.m.Y", $row->req_created) . "</CrDate>";
			$xml .= "<CrTime>" . date("H:i", $row->req_created) . "</CrTime>";
			$xml .= "<ClientPhone phoneNum=\"" . formatPhone4DB($row->client_phone) . "\">" . formatPhone($row->client_phone) . "</ClientPhone>";
			$xml .= "<AddClientPhone phoneNum='" . formatPhone4DB($row->add_client_phone) . "'>" . formatPhone($row->add_client_phone) . "</AddClientPhone>";
			$xml .= "<IsGoHome>" . $row->isGoHome . "</IsGoHome>";
			$xml .= "<AgeSelector>" . $row->age_selector . "</AgeSelector>";

			$xml .= "<AppointmentStatus>" . $row->appointment_status . "</AppointmentStatus>";
			if (!empty($row->date_admission)) {
				$xml .= "<AppointmentDate>" . date("d.m.Y", $row->date_admission) . "</AppointmentDate>";
				$xml .= "<AppointmentTime Hour=\"" . date("H", $row->date_admission) . "\" Min=\"" . date("i", $row->date_admission) . "\">" . date("H:i", $row->date_admission) . "</AppointmentTime>";
			}
			if (!empty($row->call_later_time)) {
				$xml .= "<CallLaterDate>" . date("d.m.Y", $row->call_later_time) . "</CallLaterDate>";
				$xml .= "<CallLaterTime Hour=\"" . date("H", $row->call_later_time) . "\" Min=\"" . date("i", $row->call_later_time) . "\">" . date("H:i", $row->call_later_time) . "</CallLaterTime>";
				$xml .= "<RemainTime>" . (time() - $row->call_later_time) . "</RemainTime>";
			}
			if (!empty($row->date_record)) {
				$dateRecord = strtotime($row->date_record);
				if ($dateRecord > 0) {
					$xml .= "<DateRecord>" . date('d.m.Y', $dateRecord) . "</DateRecord>";
					$xml .= "<TimeRecord Hour='" . date("H", $dateRecord) . "' Min='" . date("i", $dateRecord) . "'>" . date('H:i', $dateRecord) . "</TimeRecord>";
				}
			}
			$xml .= "<Owner id=\"" . $row->owner . "\">" . $row->user_lname . " " . $row->user_fname . "</Owner>";
			$xml .= "<Status>" . $row->status . "</Status>";
			$xml .= "<LKStatus>" . $row->LKStatus . "</LKStatus>";
			$xml .= "<BillingStatus>" . $row->billing_status . "</BillingStatus>";
			$xml .= "<PartnerStatus>" . $row->partner_status . "</PartnerStatus>";
			$xml .= "<SectorId>" . $row->req_sector_id . "</SectorId>";
			$xml .= "<IsTransfer>" . $row->is_transfer . "</IsTransfer>";
			$xml .= "<Type>" . $row->req_type . "</Type>";
			$xml .= "<Kind>" . $row->kind . "</Kind>";
			$xml .= "<SourceType>" . $row->source_type . "</SourceType>";
			$xml .= "<ClientComment><![CDATA[" . $row->client_comments . "]]></ClientComment>";
			$xml .= "<RejectReasonId>" . $row->reject_reason . "</RejectReasonId>";
			$xml .= "<Partner  id=\"" . $row->partner_id . "\">" . $row->partner_name . "</Partner>";

			//$xml .= getLastCommentXML($row -> id);
			$xml .= getCommentListXML($row->id);
			$xml .= getMetroList4requestXML($row->id);
			$xml .= getAudio4RequestXML($row->id);
			$xml .= getAnotherClinicXML($row->clinic_id);

			//booking
			$booking = BookingModel::model()->findByPk($row->booking_id);
			if($row->booking_id && $booking && $booking->request->clinic->online_booking) {
				$xml .= '<Booking>';
					$xml .= '<Id>' . $booking->id . '</Id>';
					$xml .= '<Status>' . $booking->status . '</Status>';
					$xml .= '<IsReserved>' . intval($booking->status == BookingModel::STATUS_RESERVED) . '</IsReserved>';
					$xml .= '<CanCancel>' . $booking->canChangeStatus(BookingModel::STATUS_CANCELED_BY_ORGANIZATION) .  '</CanCancel>';

					$xml .= '<Slot>';
						$xml .= '<Id>' . $booking->slot_id . '</Id>';
						$xml .= '<Doctor_4_clinic_id>' . $row->doctor_4_clinic_id . '</Doctor_4_clinic_id>';
						$xml .= '<StartTime>' . date('H:i d-m-Y', strtotime($booking->start_time)) . '</StartTime>';
						$xml .= '<FinishTime>' . date('H:i d-m-Y', strtotime($booking->finish_time)) . '</FinishTime>';
					$xml .= '</Slot>';
				$xml .= '</Booking>';
			}

			$xml .= "</Request>";
		}
	}

	return $xml;
}


function getLastCommentXML($id = 0)
{
	$xml = "";

	$id = intval($id);

	if ($id > 0) {
		$sql = "SELECT
					t1.id, t1.text, t1.user_id, 
					DATE_FORMAT( t1.created,'%d.%m.%Y') AS crDate,
					t2.user_lname, t2.user_fname, t2.user_email
				FROM request_history  t1
				LEFT JOIN `user` t2 ON (t2.user_id = t1.user_id)
				WHERE 
					t1.action = 2
					AND
					t1.request_id = $id
				ORDER BY t1.created DESC
				LIMIT 1";
		//echo $sql;
		$result = query($sql);
		if (num_rows($result) == 1) {
			$row = fetch_object($result);
			$xml .= "<LastComment id=\"" . $row->id . "\">";
			$xml .= "<Text><![CDATA[" . $row->text . "]]></Text>";
			$xml .= "<CrDate>" . $row->crDate . "</CrDate>";
			$xml .= "<Owner id=\"" . $row->user_id . "\">" . $row->user_lname . "</Owner>";
			$xml .= "</LastComment>";
		}
	}

	return $xml;
}


function getCommentListXML($id = 0)
{
	$xml = "";

	$id = intval($id);

	if ($id > 0) {
		$sql = "SELECT
					t1.id, t1.text, t1.user_id, t1.action,
					DATE_FORMAT( t1.created,'%d.%m.%Y') AS crDate,
					DATE_FORMAT( t1.created,'%H:%i') AS crTime,
					t2.user_lname, t2.user_fname, t2.user_email
				FROM request_history  t1
				LEFT JOIN `user` t2 ON (t2.user_id = t1.user_id)
				WHERE 
					t1.request_id = $id
				ORDER BY t1.created DESC, t1.id DESC";
		//echo $sql;
		$result = query($sql);
		if (num_rows($result) > 0) {
			$xml .= "<CommentList>";
			while ($row = fetch_object($result)) {
				$xml .= "<Element id=\"" . $row->id . "\">";
				$xml .= "<Text><![CDATA[" . $row->text . "]]></Text>";
				$xml .= "<CrDate>" . $row->crDate . "</CrDate>";
				$xml .= "<CrTime>" . $row->crTime . "</CrTime>";
				$xml .= "<Type>" . $row->action . "</Type>";
				$xml .= "<Owner id=\"" . $row->user_id . "\">" . $row->user_lname . " " . $row->user_fname . "</Owner>";
				$xml .= "</Element>";
			}
			$xml .= "</CommentList>";
		}
	}

	return $xml;
}

/**
 * Цена заявки
 *
 * Если клиники не существует цену будет в нуле
 *
 * @param array $params ['doctor'] = doctor_id;
 * $params ['clinic'] = clinic_id;
 * $params ['sector'] = req_sector_id;
 *
 * @return int
 */
function getPrice4Request($params = array())
{
	$price = 0;

	//для Яндекса стоимость не учитываем с 01.03.2014
	if (isset($params['partnerName'])
		&& isset($params['reqCreated'])
		&& PartnerModel::isFreePartner($params['partnerName'], $params['reqCreated'])
	){
		return 0;
	}

	if (count($params) > 0 && !empty($params['clinic'])) {
		$clinic = new Clinic();
		$clinic->getClinic($params['clinic']);
		$price = $clinic->getPrice4Specizlization($params['sector']);
	}

	return $price;
}

/**
 * Список возможнох источников заявок
 *
 * @return string XML
 */
function getType4RequestXML()
{
	$xml = "";

	$types = RequestModel::getTypeNames();
	$xml .= "<TypeDict>";
	foreach ($types as $id => $name) {
		$xml .= "<Element id='{$id}'>{$name}</Element>";
	}
	$xml .= "</TypeDict>";

	return $xml;
}


function getSourceType4RequestXML()
{
	$xml = "";

	$sql = "SELECT source_id as id, title FROM source_dict ORDER BY title";
	//echo $sql."<br/>";
	$result = query($sql);
	if (num_rows($result) > 0) {
		$xml .= "<SourceTypeDict>";
		while ($row = fetch_object($result)) {
			$xml .= "<Element id=\"" . $row->id . "\">" . $row->title . "</Element>";
		}
		$xml .= "</SourceTypeDict>";
	}

	return $xml;
}

function getDestinationPhonesXML()
{
	$xml = "";

	$sql = "SELECT id, number FROM phone";
	$result = query($sql);
	if (num_rows($result) > 0) {
		$xml .= "<DestinationPhone>";
		while ($row = fetch_object($result)) {
			$xml .= "<Element id=\"" . $row->id . "\">" . $row->number . "</Element>";
		}
		$xml .= "</DestinationPhone>";
	}

	return $xml;
}


function getStatus4RequestXML()
{
	$xml = "";

	$xml .= "<StatusDict mode='requestDict'>";
	$xml .= "<Element id=\"0\">Новая</Element>";
	$xml .= "<Element id=\"6\">Принята</Element>";
	$xml .= "<Element id=\"1\">В обработке</Element>";
	$xml .= "<Element id=\"2\">Обработана</Element>";
	$xml .= "<Element id=\"3\">Завершена</Element>";
	$xml .= "<Element id=\"5\">Отказ</Element>";
	$xml .= "<Element id=\"7\">Перезвонить</Element>";
	$xml .= "<Element id=\"10\">Повторный звонок</Element>";
	$xml .= "<Element id=\"4\">Удалена</Element>";
	$xml .= "<Element id=\"11\">Ожидают валидации</Element>";
	$xml .= "<Element id=\"12\">Не пришел</Element>";
	$xml .= "<Element id=\"13\">Условно завершена</Element>";
	$xml .= "</StatusDict>";

	return $xml;
}

function getLKStatus4RequestXML()
{
	$xml = "";

	$xml .= "<StatusDict mode='LKrequestDict'>";
	$xml .= "<Element id=\"1\">Новая</Element>";
	$xml .= "<Element id=\"2\">Пациент дошёл</Element>";
	$xml .= "<Element id=\"3\">Отклонена партнёром</Element>";
	$xml .= "<Element id=\"4\">Принята клиникой</Element>";
	$xml .= "<Element id=\"5\">Завершена</Element>";
	$xml .= "<Element id=\"6\">Отказ</Element>";
	$xml .= "</StatusDict>";

	return $xml;
}

function getStatusArray()
{
	$status = array();

	$status[0] = "Новая";
	$status[1] = "В обработке";
	$status[2] = "Обработана";
	$status[3] = "Завершена";
	$status[5] = "Отказ";
	$status[6] = "Принята";
	$status[7] = "Перезвонить";
	$status[8] = "Отклонена партнёром";
	$status[9] = "Оплачена";
	$status[4] = "Удалена";
	$status[10] = "Повторный звонок";

	return $status;
}


function getAction4RequestHistoryXML()
{
	$xml = "";

	$xml .= "<ActionDict>";
	$xml .= "<Element id=\"1\">Изменение статуса</Element>";
	$xml .= "<Element id=\"2\">Добавление комментария</Element>";
	$xml .= "<Element id=\"3\">Изменения в заявке</Element>";
	$xml .= "<Element id=\"4\">Звонок совершен</Element>";
	$xml .= "<Element id=\"5\">Комментарий клиники</Element>";
	$xml .= "</ActionDict>";

	return $xml;
}


function getRequestByPhoneXML($phone)
{
	$xml = "";


	$phone = modifyPhone($phone);

	if (!empty($phone)) {
		$sql = "SELECT
						t2.req_id as id
					FROM client t1, request t2
					WHERE t2.clientId = t1.clientId
						AND t1.cell_phone like '%" . $phone . "%'
					ORDER BY t2.req_created DESC";
		//echo $sql."<br/>";
		$result = query($sql);
		if (num_rows($result) > 0) {
			$xml .= "<RequestList>";
			while ($row = fetch_object($result)) {
				$xml .= "<Request id=\"" . $row->id . "\">" . $row->id . "</Request>";
			}
			$xml .= "</RequestList>";
		}
	}

	return $xml;
}

/**
 * Получение XML списка аудиозаписей
 *
 * @param $id
 * @return string
 */
function getAudio4RequestXML($id)
{
	$xml = "";
	$xml .= "<RecordList>";
	$xml .= arrayToXML(Record::getItems(array('requestId' => $id)));
	$xml .= "</RecordList>";

	return $xml;
}


function getMetroList4requestXML($id)
{
	$xml = "";

	$id = intval($id);

	if ($id > 0) {
		$sql = "SELECT
						t1.station_id as id,
						t2.name, t2.underground_line_id
					FROM request_station t1
					LEFT JOIN underground_station t2 ON (t2.id = t1.station_id)
					WHERE 
						t1.request_id = " . $id . "
					ORDER BY underground_line_id, id";
		//echo $sql."<br/>";
		$result = query($sql);
		if (num_rows($result) > 0) {
			$xml .= "<MetroList>";
			while ($row = fetch_object($result)) {
				$xml .= "<Metro id=\"" . $row->id . "\" line=\"" . $row->underground_line_id . "\">" . $row->name . "</Metro>";
			}
			$xml .= "</MetroList>";
		}
	}

	return $xml;

}

/**
 * Список операторов и слухачей
 * @return string
 */
function getOperatorListXML()
{
	$xml = "";

	$sql = "SELECT
					t1.user_id as id, 
					t1.user_fname, t1.user_lname, t1.user_email, t1.status
				FROM `user` t1, right_4_user t2
				WHERE 
					t1.user_id = t2.user_id
					AND
					t2.right_id IN (2, 3, 7) -- Оператор, старший оператор, слухач
					AND 
					status = 'enable'
				GROUP BY t1.user_id 
				ORDER BY t1.user_lname";
	//echo $sql."<br/>";
	$result = query($sql);
	if (num_rows($result) > 0) {
		$xml .= "<OperatorList>";
		while ($row = fetch_object($result)) {
			$xml .= "<Element id=\"" . $row->id . "\">";
			$xml .= "<Id>{$row->id}</Id>";
			$xml .= "<LName>" . $row->user_lname . "</LName>";
			$xml .= "<FName>" . $row->user_fname . "</FName>";
			$xml .= "<Status>" . $row->status . "</Status>";
			$xml .= "<Name>{$row->user_lname} {$row->user_fname}</Name>";
			$xml .= "</Element>";
		}
		$xml .= "</OperatorList>";
	}


	return $xml;

}


function getSectorListXML()
{
	$xml = "";

	$sql = "SELECT
					t1.id as id, 
					t1.name as title
				FROM `sector` t1
				ORDER BY t1.name";
	//echo $sql."<br/>";
	$result = query($sql);
	if (num_rows($result) > 0) {
		$xml .= "<SectorList>";
		while ($row = fetch_object($result)) {
			$xml .= "<Element id=\"" . $row->id . "\">" . $row->title . "</Element>";
		}
		$xml .= "</SectorList>";
	}


	return $xml;

}

function getSectorListAPIXML()
{
	$xml = "";

	$sql = "SELECT
					t1.id as id, 
					t1.name as title
				FROM `sector` t1
				ORDER BY t1.name";
	//echo $sql."<br/>";
	$result = query($sql);
	if (num_rows($result) > 0) {
		$xml .= "<SectorList>";
		while ($row = fetch_object($result)) {
			$xml .= "<Element>";
			$xml .= "<Id>" . $row->id . "</Id>";
			$xml .= "<Name>" . $row->title . "</Name>";
			$xml .= "</Element>";
		}
		$xml .= "</SectorList>";
	}


	return $xml;

}

/**
 * Получает XML список докторов для заявки
 *
 * @param array   $params    параметры
 * @param integer $cityId    идентификатор города
 * @param integer $requestId идентификатор заявки
 *
 * @return string
 */
function getDoctorList4requestXML($params = array(), $cityId = 1, $requestId = null)
{
	$xml = "";
	$sqlAdd = " t2.city_id = " . $cityId . " ";
	$addJoin = "";
	$startPage = 1;
	$step = 100;

	/*	Только активные и добавленныке */
	$sqlAdd = " (t1.status = 3 OR t1.status = 7) AND t2.city_id = " . $cityId . " ";

	if (count($params) > 0) {

		if (isset($params['name']) && !empty ($params['name'])) {
			$sqlAdd .= " AND LOWER(t1.name) LIKE  '%" . strtolower($params['name']) . "%' ";
		}
		/*if	( isset($params['status']) && !empty ($params['status'])  )  {
		 $sqlAdd .= " AND t1.status = ".$params['status']." ";
			}*/

		if (isset($params['departure']) && intval($params['departure']) == 1) {
			$sqlAdd .= " AND t1.departure = 1 ";
		}
		if (isset($params['sector']) && intval($params['sector']) > 0) {
			$sqlAdd .= " AND t3.sector_id = " . $params['sector'] . " ";
			$addJoin .= " INNER JOIN doctor_sector t3 ON (t3.doctor_id = t1.id) ";
		}
		if (isset($params['clinicAvailable']) && ($params['clinicAvailable']) == 'yes') {
			$sqlAdd .= " AND t2.status = 3 ";
		}

		if (!empty($params['kidsReception'])) {
			$sqlAdd .= ' AND t1.kids_reception = 1 ';
			if (!empty($params['kidsAgeFrom'])) {
				$sqlAdd .= ' AND t1.kids_age_from <=  ' . intval($params['kidsAgeFrom']);
				if (empty($params['kidsAgeTo'])) {
					$sqlAdd .= ' AND t1.kids_age_to >=  ' . intval($params['kidsAgeFrom']);
				}
			}
			if (!empty($params['kidsAgeTo'])) {
				$sqlAdd .= ' AND t1.kids_age_to >= ' . intval($params['kidsAgeTo']);
				if (empty($params['kidsAgeFrom'])) {
					$sqlAdd .= ' AND t1.kids_age_from <=  ' . intval($params['kidsAgeTo']);
				}
			}
		}

		$idDocList = "";
		$sqlAddFix = "";
		if (isset($params['doctorList']) && count($params['doctorList']) > 0) {
			$idList = "";
			$i = 0;
			foreach ($params['doctorList'] as $key => $data) {
				$idList .= $data;
				if ($i != (count($params['doctorList']) - 1)) {
					$idList .= ", ";
				}
				$i++;
			}
			$idDocList = $idList;
			if (!empty($idList)) {
				$sqlAddFix .= " AND t1.id NOT IN (" . $idList . ")";
			}

		}

		if (!empty($params['metroList']) || !empty($params['districts'])) {
			$addJoin .= "INNER JOIN doctor_4_clinic d4c ON (d4c.doctor_id = t1.id and d4c.type = " . DoctorClinicModel::TYPE_DOCTOR . ")";
		}

		if (!empty($params['metroList'])) {
			$idList = "";
			for ($i = 0; $i < count($params['metroList']); $i++) {
				$idList .= $params['metroList'][$i];
				if ($i != (count($params['metroList']) - 1)) {
					$idList .= ", ";
				}
			}

			$addJoin .= " INNER JOIN underground_station_4_clinic us4c ON (us4c.clinic_id = d4c.clinic_id)
							INNER JOIN underground_station us ON (us.id = us4c.undegraund_station_id)
							";
			$sqlAdd .= " AND us.id in (" . $idList . ")";
		}

		if (!empty($params['districts'])) {
			$districts = implode(', ', $params['districts']);
			$addJoin .= "INNER JOIN clinic cl ON (cl.id = d4c.clinic_id)";
			$sqlAdd .= " AND cl.district_id IN ({$districts})";
		}

		if (!empty($params['workFrom']) && !empty($params['workTo'])) {

			$addJoin .= " INNER JOIN slot s ON (d4cl.id = s.doctor_4_clinic_id) ";

			$sqlAdd .= " AND d4cl.has_slots=1 AND s.start_time >='{$params['workFrom']}' AND s.finish_time <= '{$params['workTo']}' ";
		}

		if (isset($params['phoneExt']) && intval($params['phoneExt']) > 0) {
			$sqlAdd = " t1.addNumber = " . $params['phoneExt'] . " ";
		}
		if (isset($params['id']) && !empty ($params['id'])) {
			$sqlAdd = " t1.id = '" . $params['id'] . "'";
		}
	}

	$sqlUnion4DocFix = "";
	if (!empty($idDocList)) {
		$sqlUnion4DocFix = "(SELECT
						t1.id,  t1.name as FullName, t1.status, t1.phone, t1.image,  t1.rewrite_name as alias,
						t1.total_rating, t1.rating, t1.rating_opinion, 
						t1.email, t1.sex, t1.price, t1.special_price,
						DATE_FORMAT( t1.created,'%d.%m.%Y') AS crDate,
						t1.note, t1.openNote, t1.addNumber, t1.experience_year,
						t2.name as Clinic, t2.id as clinicId, t2.status as clStatus,
						concat(t2.street, ', ', t2.house) as clinicAddress,
						t2.url as clinicUrl,
						CASE WHEN t1.rating = 0 THEN t1.total_rating ELSE t1.rating END AS sortRating,
						d4cl.has_slots,
						t2.online_booking and ad.enabled and d4cl.has_slots as online_booking, t2.rewrite_name
					FROM doctor_4_clinic d4cl
					     INNER JOIN clinic t2 ON (t2.id = d4cl.clinic_id )
	 				     INNER JOIN doctor t1 ON (t1.id = d4cl.doctor_id )
	 				     LEFT JOIN api_doctor ad on ad.id = d4cl.doc_external_id
					WHERE
						t1.Id IN (" . $idDocList . ") and d4cl.type = " . DoctorClinicModel::TYPE_DOCTOR . " )
					UNION ";
	}

	$sql = "(SELECT
					t1.id,  t1.name as FullName, t1.status, t1.phone, t1.image, t1.rewrite_name as alias,
					t1.total_rating, t1.rating, t1.rating_opinion, 
					t1.email, t1.sex, t1.price, t1.special_price,
					DATE_FORMAT( t1.created,'%d.%m.%Y') AS crDate,
					t1.note, t1.openNote, t1.addNumber, t1.experience_year,
					t2.name as Clinic, t2.id as clinicId, t2.status as clStatus,
					concat(t2.street, ', ', t2.house) as clinicAddress,
					t2.url as clinicUrl,
					CASE WHEN t1.rating = 0 THEN t1.total_rating ELSE t1.rating END AS sortRating,
					d4cl.has_slots,
					t2.online_booking and ad.enabled and d4cl.has_slots as online_booking, t2.rewrite_name
				FROM doctor_4_clinic d4cl
				 	INNER JOIN clinic t2 ON (t2.id = d4cl.clinic_id )
	 				INNER JOIN doctor t1 ON (t1.id = d4cl.doctor_id )
	 				LEFT JOIN api_doctor ad on ad.id = d4cl.doc_external_id
				" . $addJoin . "
				WHERE
					" . $sqlAdd . $sqlAddFix . " and d4cl.type = " . DoctorClinicModel::TYPE_DOCTOR . "
				GROUP BY d4cl.id
				ORDER BY t2.rating DESC, sortRating DESC, t1.id
				LIMIT 100)";

	$fullSql = $sqlUnion4DocFix.$sql;

	$useSpecialPrice = true;
	if ($requestId) {
		$requestModel = RequestModel::model()->findByPk($requestId);
		if ($requestModel && $requestModel->partner && !$requestModel->partner->use_special_price) {
			$useSpecialPrice = false;
		}
	}

	$result = query($fullSql);
	if (num_rows($result) > 0) {
		$xml .= "<DoctorList>";
		while ($row = fetch_object($result)) {
			$xml .= "<Element id=\"{$row->id}\">";
			$xml .= "<CrDate>{$row->crDate}</CrDate>";
			$xml .= "<Name>{$row->FullName}</Name>";
			$xml .= "<Alias>{$row->alias}</Alias>";
			$xml .= "<Rating rating=\"{$row->rating}\" total=\"{$row->total_rating}\">" . round($row->sortRating, 2) ."</Rating>";
			$xml .= "<Price>{$row->price}</Price>";
			$xml .=
				"<SpecialPrice>" .
				($useSpecialPrice && $row->special_price ? $row->special_price : $row->price) .
				"</SpecialPrice>";
			$xml .= "<Phone>{$row->phone}</Phone>";
			$xml .= "<AddNumber>{$row->addNumber}</AddNumber>";
			$xml .= "<Email>{$row->email}</Email>";
			$xml .= "<Sex>{$row->sex}</Sex>";
			$xml .= "<HasSlots>{$row->has_slots}</HasSlots>";
			$xml .= "<CanBooking>{$row->online_booking}</CanBooking>";
			$xml .= "<Experience startPractice=\"{$row->experience_year}\">" . (date('Y') - $row->experience_year) . "</Experience>";
			$xml .= "<Status>{$row->status}</Status>";
			$xml .= "<IMG>{$row->image}</IMG>";
			$xml .= "<Clinic id=\"{$row->clinicId}\" status =\"{$row->clStatus}\" rewrite_name=\"{$row->rewrite_name}\">{$row->Clinic}</Clinic>";
			$xml .= "<ClinicAddress>{$row->clinicAddress}</ClinicAddress>";
			$xml .= "<PhoneList>" . arrayToXML(getPhones4ClinicArr($row->clinicId)) . "</PhoneList>";
			$xml .= "<ClinicUrl>" .str_replace('http://', '', $row->clinicUrl) . "</ClinicUrl>";
			$xml .= getSectorByDoctorIdXML($row->id);
			//$xml .= getMetroByDoctorIdXML ($row -> id);
			$xml .= getMetroByClinicIdXML($row->clinicId);
			$xml .= "<Opinion>" . getOpinionCountByDoctorId($row->id) . "</Opinion>";
			$xml .= "<OperatorComment><![CDATA[{$row->note}]]></OperatorComment>";
			$xml .= "<OperatorOpenComment><![CDATA[{$row->openNote}]]></OperatorOpenComment>";
			$xml .= "</Element>";
		}
		$xml .= "</DoctorList>";
	}
	return $xml;
}


function getMetroByDoctorIdXML($id)
{
	$xml = "";

	$id = intval($id);

	if ($id > 0) {
		$sql = "SELECT
						t1.id as id, t1.name as station
					FROM 	underground_station t1, 
							underground_station_4_clinic t2,
							doctor_4_clinic t3
					WHERE 
						t1.id = t2.undegraund_station_id
						AND
						t2.clinic_id =  t3.clinic_id
						AND
						t3.doctor_id = " . $id . "
						AND
						t3.type = " . DoctorClinicModel::TYPE_DOCTOR . "
					GROUP BY t1.id
					ORDER BY station";
		//echo $sql."<br/>";
		$result = query($sql);
		if (num_rows($result) > 0) {
			$xml .= "<StationList>";
			while ($row = fetch_object($result)) {
				$xml .= "<Element id=\"" . $row->id . "\">" . $row->station . "</Element>";
			}
			$xml .= "</StationList>";
		}
	}

	return $xml;

}


function getMetroByClinicIdXML($id)
{
	$xml = "";

	$id = intval($id);

	if ($id > 0) {
		$sql = "SELECT
						t1.id as id, t1.name as station
					FROM 	underground_station t1, 
							underground_station_4_clinic t2
					WHERE 
						t1.id = t2.undegraund_station_id
						AND
						t2.clinic_id =  " . $id . "
					GROUP BY t1.id
					ORDER BY station";
		//echo $sql."<br/>";
		$result = query($sql);
		if (num_rows($result) > 0) {
			$xml .= "<StationList>";
			while ($row = fetch_object($result)) {
				$xml .= "<Element id=\"" . $row->id . "\">" . $row->station . "</Element>";
			}
			$xml .= "</StationList>";
		}
	}

	return $xml;

}


// список клиник для врача
function getClinicXML($id)
{
	$xml = "";

	$id = intval($id);

	if ($id > 0) {
		$sql = "SELECT
						cl.id as id, cl.name
					FROM 	clinic cl, doctor_4_clinic d4c
					WHERE 
						cl.id = d4c.clinic_id
						AND
						d4c.doctor_id = " . $id . "
						AND
						d4c.type = " . DoctorClinicModel::TYPE_DOCTOR . "
					ORDER BY cl.name";
		//echo $sql."<br/>";
		$result = query($sql);
		if (num_rows($result) > 0) {
			$xml .= "<ClinicList>";
			while ($row = fetch_object($result)) {
				$xml .= "<Element id=\"" . $row->id . "\">" . $row->name . "</Element>";
			}
			$xml .= "</ClinicList>";
		}
	}

	return $xml;
}


function getAnotherClinicXML($id)
{
	$xml = "";

	$id = intval($id);

	if ($id > 0) {
		$sql = "	SELECT
					t1.name, t1.id, t1.status, concat(t1.street, ', ' , t1.house) as address
				FROM clinic t1
				WHERE 
					t1.id =  $id
					OR
					t1.parent_clinic_id = $id 
					OR 
					t1.parent_clinic_id = ( SELECT CASE WHEN parent_clinic_id = 0 THEN NULL ELSE parent_clinic_id END AS parent_id FROM clinic WHERE id = $id  )
					OR 
					t1.id = ( SELECT CASE WHEN parent_clinic_id = 0 THEN NULL ELSE parent_clinic_id END AS parent_id FROM clinic WHERE id = $id  )
					AND 
					t1.status = 3
				ORDER BY t1.id ";
		//echo $sql."<br/>";
		$result = query($sql);
		if (num_rows($result) > 0) {
			$xml .= "<AnotherClinicList>";
			while ($row = fetch_object($result)) {
				$xml .= "<Element id=\"" . $row->id . "\">";
				$xml .= "<Clinic>" . $row->name . "</Clinic>";
				$xml .= "<Address>" . $row->address . "</Address>";
				$xml .= "</Element>";
			}
			$xml .= "</AnotherClinicList>";
		}
	}

	return $xml;

}

/**
 * Получение списка телефонов клиники
 * @param $id
 * @return array
 */
function getPhones4ClinicArr($id)
{
	$data = array();
	$id = intval($id);

	$sql = "SELECT
					t1.phone_id AS Id, t1.number_p AS Phone, t1.label AS Label
				FROM clinic_phone t1
				WHERE
					t1.clinic_id=$id";
	$result = query($sql);
	if (num_rows($result) > 0) {
		while ($row = fetch_array($result)) {
			$row['PhoneFormat'] = formatPhone($row['Phone']);
			array_push($data, $row);
		}
	}

	return $data;
}


// Список отзывов
function getOpinionListByRequestIdXML($id = 0)
{
	$xml = "";

	$id = intval($id);

	if ($id > 0) {
		$sql = "SELECT
					t1.id, t1.request_id
				FROM doctor_opinion  t1
				WHERE 
					t1.request_id = $id";
		//echo $sql;
		$result = query($sql);
		if (num_rows($result) > 0) {
			$xml .= "<OpinionList>";
			while ($row = fetch_object($result))
				$xml .= "<Opinion id=\"" . $row->id . "\"/>";
			$xml .= "</OpinionList>";
		}
	}

	return $xml;
}

/** Получение дистанции в метрах между 2 точками с заданными координатами */
function getDistance($lat1, $lng1, $lat2, $lng2)
{
	$lat1 = deg2rad($lat1);
	$lng1 = deg2rad($lng1);
	$lat2 = deg2rad($lat2);
	$lng2 = deg2rad($lng2);

	return round(6378137 * acos(cos($lat1) * cos($lat2) * cos($lng2 - $lng1) + sin($lat1) * sin($lat2)));
}

/** Получение данных о линии метро */
function getMetroLine($name)
{
	$sql = "SELECT * FROM underground_line WHERE LOWER(name) LIKE '" . mb_strtolower($name) . "'";
	$result = query($sql);
	$line = fetch_array($result);

	return $line;
}

/**
 * Получение id станции по их названиям
 *
 * @param array $metroList
 * @return array
 */
function getMetroIdList($metroList = array())
{
	$out = array();

	if (count($metroList) > 0) {

		foreach ($metroList as $key => $data) {
			$sql = "SELECT
						id
					FROM underground_station
					WHERE
						LOWER(name) LIKE LOWER('%" . trim($data) . "%')
					LIMIT 1";
			$result = query($sql);

			if (num_rows($result) > 0) {
				$row = fetch_object($result);
				array_push($out, $row->id);
			}
		}
	}
	return $out;
}

/**
 * Получаем XML со списком SIP каналов
 *
 * @return string
 */
function getQueueDict()
{
	$xmlString = "<QueueDict>";
	foreach (QueueModel::getSIPChannels() as $key) {
		$xmlString .= "<Element>$key</Element>";
	}
	$xmlString .= "</QueueDict>";

	return $xmlString;
}
