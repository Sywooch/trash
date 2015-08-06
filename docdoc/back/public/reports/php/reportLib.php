<?php
use dfs\docdoc\models\DoctorClinicModel;

require_once __DIR__ . "/../../lib/php/dateconvertionLib.php";
require_once __DIR__ . "/../../lib/php/validate.php";
require_once __DIR__ . "/../../doctor/php/doctorLib.php";

define ("validTimeLimit", "20");
define ("validTimeLimitII", "20");


function diagnosticaCallRepoetXML($params = array())
{
	$xml = "";
	$sqlAdd = " 1=1 ";

	if (count($params) > 0) {
		if (isset($params['crDateFrom']) && !empty ($params['crDateFrom'])) {
			$sqlAdd .= " AND t1.crDate >= date(" . convertDate2DBformat($params['crDateFrom']) . ") ";
		}
		if (isset($params['crDateTill']) && !empty ($params['crDateTill'])) {
			$sqlAdd .= " AND t1.crDate <= date(" . convertDate2DBformat($params['crDateTill']) . ") ";
		}
	}

	$dayArray = getInterval($params['crDateFrom'], $params['crDateTill']);
	$xml .= "<DayList>";
	foreach ($dayArray as $day) {
		$xml .= "<Day>" . $day . "</Day>";
	}
	$xml .= "</DayList>";

	//Список клиник
	$sql =
		"SELECT
							t1.id_clinic as id, t1.numberTo, t1.price,
							t2.name as clinic, t2.short_name
						FROM call4diagnostica t1
						LEFT JOIN clinic t2 ON (t1.id_clinic = t2.id)
						WHERE
							 DATE(crDate) between DATE('" .
		convertDate2DBformat($params['crDateFrom']) .
		"') AND DATE('" .
		convertDate2DBformat($params['crDateTill']) .
		"')
						GROUP BY t1.id_clinic";
	//echo $sql;
	$result = query($sql);
	if (num_rows($result) > 0) {
		$xml .= "<ClinicList>";
		$clinicList = array();;
		while ($row = fetch_object($result)) {
			array_push($clinicList, $row->id);
			$xml .= "<Element id=\"" . $row->id . "\">";
			$xml .= "<Name>" . $row->clinic . "</Name>";
			$xml .= "<SoftName>" . $row->clinic . "</SoftName>";
			$xml .= "<ShortName>" . $row->short_name . "</ShortName>";
			$xml .= "<Phone number=\"" . $row->numberTo . "\">" . formatPhone($row->numberTo) . "</Phone>";
			$xml .= "<Price>" . $row->price . "</Price>";
			$xml .= "</Element>";
		}
		$xml .= "</ClinicList>";
	}

	if (isset($dayArray) && isset($clinicList)) {
		$xml .= "<TotalData>";
		foreach ($dayArray as $day) {
			$xml .= "<Day day=\"" . $day . "\">";

			foreach ($clinicList as $id) {
				$xml .= "<Clinic id=\"" . $id . "\"> ";

				// Общая статистика
				$sql = "SELECT
								count(*) as total,
								count( distinct numberFrom ) as uniqCaller
							FROM call4diagnostica
							WHERE 
								 DATE(crDate) = DATE('" . convertDate2DBformat($day) . "')
								 AND
								 id_clinic = " . $id;
				//echo $sql;
				$result = query($sql);
				if (num_rows($result) == 1) {
					$row = fetch_object($result);
					$xml .= "<Data total=\"" . $row->total . "\"  uniq=\"" . $row->uniqCaller . "\"/>";
				}

				// Валидные звонки
				$sql = "SELECT
								count(*) as total,
								count( distinct numberFrom ) as uniqCaller
							FROM call4diagnostica
							WHERE 
								DATE(crDate) = DATE('" . convertDate2DBformat($day) . "')
								AND
								id_clinic = " . $id . "
								AND
								duration >= " . validTimeLimit;
				//echo $sql;
				$result = query($sql);
				if (num_rows($result) == 1) {
					$row = fetch_object($result);
					$xml .= "<ValidData total=\"" . $row->total . "\"  uniq=\"" . $row->uniqCaller . "\"/>";
				}

				// Валидные звонки > порога 2
				$sql = "SELECT
								count(*) as total,
								count( distinct numberFrom ) as uniqCaller
							FROM call4diagnostica
							WHERE 
								DATE(crDate) = DATE('" . convertDate2DBformat($day) . "')
								AND
								id_clinic = " . $id . "
								AND
								duration >= " . validTimeLimitII;
				//echo $sql;
				$result = query($sql);
				if (num_rows($result) == 1) {
					$row = fetch_object($result);
					$xml .= "<ValidDataII total=\"" . $row->total . "\"  uniq=\"" . $row->uniqCaller . "\"/>";
				}

				$xml .= "</Clinic>";
			}
			$xml .= "</Day>";
		}
		$xml .= "</TotalData>";
	}

	return $xml;
}


function getClinicListByIdWithBranchesXML($id)
{
	$xml = "";

	$id = intval($id);

	if ($id > 0) {
		//Список клиник
		$sql = "SELECT id, name, short_name FROM clinic WHERE id= " . $id . " OR parent_clinic_id=" . $id;
		//echo $sql;
		$result = query($sql);
		if (num_rows($result) > 0) {
			$xml .= "<ClinicList>";
			$clinicList = array();;
			while ($row = fetch_object($result)) {
				array_push($clinicList, $row->id);
				$xml .= "<Element id=\"" . $row->id . "\">";
				$xml .= "<Name>" . $row->name . "</Name>";
				$xml .= "<ShortName>" . $row->short_name . "</ShortName>";
				$xml .= "</Element>";
			}
			$xml .= "</ClinicList>";
		}
	}

	return $xml;
}


function getClinicListByXML($params = array(), $cityId = 1)
{
	$xml = "";

	$dateFrom = convertDate2DBformat($params['dateFrom']);
	$dateTill = convertDate2DBformat($params['dateTill']);
	$cityId = intval($cityId);

	$sqlAddSh = "";
	if (isset($params['type']) && !empty($params['type'])) {
		switch ($params['type']) {
			case 'clinic' :
				$sqlAddSh .= " AND t1.isClinic = 'yes'";
				break;
			case 'center' :
				$sqlAddSh .= " AND t1.isDiagnostic = 'yes'";
				break;
			case 'privatDoctor' :
				$sqlAddSh .= " AND t1.isPrivatDoctor = 'yes'";
				break;
		}

	}

	//Список клиник (не филилов)
	$sql = "SELECT
						t1.id, t1.name, t1.short_name, t1.parent_clinic_id,
						t2.contract_id 
					FROM clinic t1 
					LEFT JOIN clinic_settings t2 ON (t1.settings_id = t2.settings_id) 
					WHERE 
						t1.parent_clinic_id = 0 
						AND 
						t1.city_id = " . $cityId . "
						AND 
						t1.status = 3 
						" . $sqlAddSh . "
					ORDER BY t1.name";
	$result = query($sql);
	if (num_rows($result) > 0) {
		$xml .= "<ClinicList>";

		while ($row = fetch_object($result)) {
			$xml .= "<Element id=\"" . $row->id . "\">";
			$xml .= "<ParentId>" . $row->parent_clinic_id . "</ParentId>";
			$xml .= "<Name><![CDATA[" . $row->name . "]]></Name>";
			$xml .= "<ShortName>" . $row->short_name . "</ShortName>";
			$xml .= "<ContractId>" . $row->contract_id . "</ContractId>";
			$xml .= "<Transfer>" . getRequestCount($row->id, 'transfer', $dateFrom, $dateTill) . "</Transfer>";
			$xml .= "<Apointment>" . getRequestCount($row->id, 'apointment', $dateFrom, $dateTill) . "</Apointment>";
			$xml .= "<Complete>" . getRequestCount($row->id, 'complete', $dateFrom, $dateTill) . "</Complete>";
			$xml .= "<Reject>" . getRequestCount($row->id, 'reject', $dateFrom, $dateTill) . "</Reject>";
			$xml .= "</Element>";

			if ($row->id > 0) {
				// Филиалы
				$sqlAdd = "SELECT
										t1.id, t1.name, t1.short_name, t1.parent_clinic_id, t2.contract_id
									FROM clinic t1
									LEFT JOIN clinic_settings t2 ON (t1.settings_id = t2.settings_id)
									WHERE 
										t1.parent_clinic_id = " . $row->id . "
										AND 
										t1.status = 3
										" . $sqlAddSh . "
									ORDER BY t1.name";
				$resultAdd = query($sqlAdd);
				if (num_rows($resultAdd) > 0) {
					while ($rowAdd = fetch_object($resultAdd)) {
						$xml .= "<Element id=\"" . $rowAdd->id . "\">";
						$xml .= "<ParentId>" . $rowAdd->parent_clinic_id . "</ParentId>";
						$xml .= "<Name><![CDATA[" . $rowAdd->name . "]]></Name>";
						$xml .= "<ShortName>" . $rowAdd->short_name . "</ShortName>";
						$xml .= "<ContractId>" . $rowAdd->contract_id . "</ContractId>";
						$xml .=
							"<Transfer>" .
							getRequestCount($rowAdd->id, 'transfer', $dateFrom, $dateTill) .
							"</Transfer>";
						$xml .=
							"<Apointment>" .
							getRequestCount($rowAdd->id, 'apointment', $dateFrom, $dateTill) .
							"</Apointment>";
						$xml .=
							"<Complete>" .
							getRequestCount($rowAdd->id, 'complete', $dateFrom, $dateTill) .
							"</Complete>";
						$xml .= "<Reject>" . getRequestCount($rowAdd->id, 'reject', $dateFrom, $dateTill) . "</Reject>";
						$xml .= "</Element>";
					}
				}
			}
		}

		$xml .= "</ClinicList>";
	}

	return $xml;
}

/**
 * Получение кол-ва заявок по статусу
 *
 * @param $clinicId
 * @param string $type
 * @param $dateFrom
 * @param $dateTill
 * @param bool $withBranch
 * @param int $city
 *
 * @return int
 */
function getRequestCount($clinicId, $type = 'complete', $dateFrom, $dateTill, $withBranch = false, $city = 1)
{
	$count = -1;
	$city = intval($city);
	$clinicId = intval($clinicId);

	$sql = "";
	$sqlAdd = "";
	$sqlClinic = " 1=1 ";

	if ($clinicId > 0) {
		$sqlClinic = " clinic_id = $clinicId ";
		if ($withBranch) {
			$sqlClinic =
				" clinic_id IN (SELECT DISTINCT grList.id FROM (SELECT id FROM clinic WHERE parent_clinic_id = " .
				$clinicId .
				" UNION SELECT $clinicId AS id) as grList) ";
		}
	}

	$sqlDateRestict = "	AND req_created >= " . strtotime(convertDate2DBformat($dateFrom)) . "
							AND req_created <= " . (strtotime(convertDate2DBformat($dateTill)) + 86400) . " ";

	switch ($type) {
		case 'complete' :
			$sqlAdd =
				" AND req_status = 3 AND appointment_status = 1 " .
				"AND date_admission >= " .
				strtotime(convertDate2DBformat($dateFrom)) .
				" and date_admission <= " .
				(strtotime(convertDate2DBformat($dateTill)) + 86400) .
				" ";
			break;
		case 'this_period_complete' :
			$sqlAdd =
				" AND  req_status = 3 AND appointment_status = 1 AND date_admission >= " .
				strtotime(convertDate2DBformat($dateFrom)) .
				" and  date_admission <= " .
				(strtotime(convertDate2DBformat($dateTill)) + 86400) .
				$sqlDateRestict;
			break;
		case 'apointment' :
			$sqlAdd = " AND date_admission is not null AND  date_admission > 0 " . $sqlDateRestict;
			break;
		//			case 'apointment' : $sqlAdd = " AND (req_status = 2 or req_status = 3) AND date_admission is not null AND  date_admission > 0 ".$sqlDateRestict; break;
		case 'transfer' :
			$sqlAdd = " AND is_transfer = 1 " . $sqlDateRestict;
			break;
		case 'reject' :
			$sqlAdd = " AND req_status = 5 " . $sqlDateRestict;
			break;
		case 'total' :
			$sqlAdd = $sqlDateRestict;
			break;
	}

	$sql = "	SELECT
						count(req_id) as cnt 
					FROM request 
					WHERE 
						" . $sqlClinic . "
						" . $sqlAdd . "
						AND 
						id_city = " . $city . "
						AND
						req_status <> " . DocRequest::STATUS_REMOVED . "
						AND kind = " . DocRequest::KIND_DOCTOR;

	$result = query($sql);
	if (num_rows($result) == 1) {
		$row = fetch_object($result);
		$count = $row->cnt;
	}

	return $count;
}

/**
 * Сумма запросов за указанный период по указанному параметру
 *
 * @param string     $type        Пит выгружаемой статистики
 * @param string     $dateFrom    Дата от
 * @param string     $dateTill    Дата до
 * @param int        $city        Город
 * @param int        $contract_id Контракт
 *
 * @return int
 */
function getSummaryRequestCount($type = 'total', $dateFrom, $dateTill, $city, $contract_id = 0)
{
	$count = -1;
	$city = (intval($city) > 1) ? intval($city) : 1;

	$sqlDateRestict = " AND req_created >= " . strtotime(convertDate2DBformat($dateFrom))
		. " AND req_created <= " . (strtotime(convertDate2DBformat($dateTill)) + 86400) . " ";

	switch ($type) {
		case 'total' :
			$sqlAdd = $sqlDateRestict;
			break;
		case 'complete' :
			$sqlAdd =
				" AND r.req_status = 3 AND r.appointment_status = 1 AND  r.date_admission >= " .
				strtotime(convertDate2DBformat($dateFrom)) .
				" and  r.date_admission <= " .
				(strtotime(convertDate2DBformat($dateTill)) + 86400) .
				" ";
			break;
		case 'this_period_complete' :
			$sqlAdd =
				" AND  r.req_status = 3 AND r.appointment_status = 1 AND r.date_admission >= " .
				strtotime(convertDate2DBformat($dateFrom)) .
				" and  r.date_admission <= " .
				(strtotime(convertDate2DBformat($dateTill)) + 86400) .
				$sqlDateRestict;
			break;
		case 'apointment' :
			$sqlAdd = " AND r.date_admission is not null AND  r.date_admission > 0 " . $sqlDateRestict;
			break;
		case 'transfer' :
			$sqlAdd = " AND r.is_transfer = 1 " . $sqlDateRestict;
			break;
		case 'reject' :
			$sqlAdd = " AND r.req_status = 5 " . $sqlDateRestict;
			break;

		default :
			$sqlAdd = $sqlDateRestict;
			break;
	}

	if (!isset($sqlAdd)) {
		$sqlAdd = '';
	}

	if (!empty($contract_id)) {
		$contract_id = intval($contract_id);
		switch ($contract_id) {
			case 1:
				$sqlAdd .= " AND (st.contract_id = 1 OR st.contract_id IS NULL) ";
				break;
			case 2:
				$sqlAdd .= " AND st.contract_id = 2 ";
				break;
			default :
				$sqlAdd .= " AND st.contract_id = {$contract_id} ";
				break;
		}
	}

	$sql = "
			SELECT count(req_id) as cnt 
			FROM request r, clinic cl
			LEFT JOIN clinic_settings st ON (cl.settings_id = st.settings_id)
			WHERE
				r.clinic_id = cl.id
				AND
				r.id_city = {$city}
				{$sqlAdd}
				AND
				r.req_status <> " . DocRequest::STATUS_REMOVED . "
				AND kind = " . DocRequest::KIND_DOCTOR;
	$result = query($sql);
	if (num_rows($result) == 1) {
		$row = fetch_object($result);
		$count = $row->cnt;
	}

	return $count;
}

/**
 * Кол-во заявок по диагностике
 *
 * @param $clinicId
 * @param string $type
 * @param $dateFrom
 * @param $dateTill
 * @param string $dateMethod
 *
 * @return int
 */
function getDiagRequestCount($clinicId, $type = 'complete', $dateFrom, $dateTill, $dateMethod = 'create')
{
	$count = -1;
	$clinicId = intval($clinicId);

	$sqlJoin = "";

	$params = array(
		'clinicId' => $clinicId,
		'dateFrom' => $dateFrom,
		'dateTill' => $dateTill,
		'dateMethod' => $dateMethod,
		'withBranch' => true,
	);
	$sqlAdd = getAddConditions($params);

	/*
	 * Все состояния отслеживаются исключительно по статуцсам заявки не зависимо от данных, которые проставил оператор.
	 * Со слов Антонова Романа со ссылкой на Дмитрия Петрухина
	 * От 18 сентября 2013
	 * */

	switch ($type) {
		case 'complete' :
			$sqlAdd .= " AND req.req_status = " . DocRequest::STATUS_CAME;
			break; // дошёл
		case 'admission' :
			$sqlAdd .= " AND ( req.date_admission IS NOT NULL ) ";
			break; // записан
		case 'reject' :
			$sqlAdd .= " AND req.req_status = " . DocRequest::STATUS_REJECT;
			break; // отказ
		case 'total' :
			break;
		case 'total30' :
		{
			$sqlAdd .= " AND t2.duration > 30 ";
			$sqlJoin .= " LEFT JOIN request_record t2 ON ( req.req_id = t2.request_id ) ";
		}
			break;
	}

	$sql = "	SELECT
						count(DISTINCT(req.req_id)) as cnt
					FROM request req
					{$sqlJoin}
					WHERE
						req.req_status <> " . DocRequest::STATUS_REMOVED . "
						{$sqlAdd}
						AND req.kind = " . DocRequest::KIND_DIAGNOSTICS;

	$result = query($sql);
	if (num_rows($result) == 1) {
		$row = fetch_object($result);
		$count = $row->cnt;
	}

	return $count;
}


/**
 * Кол-во заявок по диагностике сгруппированные по отказам
 *
 * @param $clinicId
 * @param $dateFrom
 * @param $dateTill
 * @param int $kind
 * @param string $dateMethod
 *
 * @return array
 */
function getRejectReason($clinicId, $dateFrom, $dateTill, $kind, $dateMethod = 'create')
{
	$data = array();

	$params = array(
		'clinicId' => $clinicId,
		'dateFrom' => $dateFrom,
		'dateTill' => $dateTill,
		'dateMethod' => $dateMethod,
		'kind' => $kind,
		'withBranch' => true,
	);
	$sqlAdd = getAddConditions($params);

	$sql = "	SELECT
						COUNT(req_id) AS Cnt,
						reject_reason as RejectId
					FROM request req
					WHERE
						req_status = " . DocRequest::STATUS_REJECT . "
						AND reject_reason IS NOT NULL
						{$sqlAdd}
					GROUP BY reject_reason
					ORDER BY Cnt DESC";

	$result = query($sql);
	if (num_rows($result) > 0) {
		while ($row = fetch_array($result)) {
			array_push($data, $row);
		}
	}

	return $data;
}

/**
 * Кол-во звонков по клинике
 *
 * @param $clinicId
 * @param $dateFrom
 * @param $dateTill
 * @param int $duration
 * @param string $dateMethod
 *
 * @return int
 */
function getDiagRecordCount($clinicId, $dateFrom, $dateTill, $duration = 0, $dateMethod = 'create')
{
	$count = 0;

	$params = array(
		'clinicId' => $clinicId,
		'dateFrom' => $dateFrom,
		'dateTill' => $dateTill,
		'duration' => $duration,
		'dateMethod' => $dateMethod,
		'withBranch' => true,
	);
	$sqlAdd = getAddConditions($params);

	$sql = "	SELECT
						count(t1.record_id) as cnt
					FROM request_record t1, request req
					WHERE 
						t1.request_id = req.req_id
						{$sqlAdd}
						AND
						req.req_status <> 4
						AND req.kind = " . DocRequest::KIND_DIAGNOSTICS;

	$result = query($sql);
	if (num_rows($result) == 1) {
		$row = fetch_object($result);
		$count = $row->cnt;
	}

	return $count;
}

/**
 * Получение доп условий для поиска заявок
 *
 * @param $params
 *
 * @return string
 */
function getAddConditions($params)
{
	$sqlAdd = "";

	$clinicId = isset($params['clinicId']) ? $params['clinicId'] : 0;
	$dateMethod = isset($params['dateMethod']) ? $params['dateMethod'] : 'create';
	$duration = isset($params['duration']) ? $params['duration'] : 0;
	$dateFrom = isset($params['dateFrom']) ? strtotime($params['dateFrom']) : null;
	$dateTill = isset($params['dateTill']) ? strtotime($params['dateTill']) + 86400 : null;
	$withBranch = isset($params['withBranch']) ? $params['withBranch'] : false;
	$kind = isset($params['kind']) ? $params['kind'] : null;

	if ($clinicId > 0) {
		if ($withBranch) {
			$sqlAdd .=
				"  AND  req.clinic_id IN (SELECT DISTINCT grList.id FROM (SELECT id FROM clinic WHERE parent_clinic_id = " .
				$clinicId .
				" UNION SELECT $clinicId AS id) as grList) ";
		} else {
			$sqlAdd .= " AND req.clinic_id = $clinicId ";
		}
	}

	if (!empty($duration) && $duration > 0) {
		$sqlAdd .= " AND t1.duration >= {$duration} ";
	}

	if (!is_null($dateFrom) && !is_null($dateTill)) {
		if ($dateMethod == 'admission') {
			$sqlAdd .= " AND req.date_admission >= {$dateFrom} AND req.date_admission < {$dateTill} ";
		} else {
			$sqlAdd .= " AND req.req_created >= {$dateFrom} AND req.req_created < {$dateTill} ";
		}
	}

	if (!is_null($kind)) {
		$sqlAdd .= " AND req.kind = {$kind}";
	}

	return $sqlAdd;
}


function getInterval($start, $end, $format = 'd.m.Y')
{
	return array_map(
		create_function('$item', 'return date("' . $format . '", $item);'),
		range(strtotime($start), strtotime($end), 60 * 60 * 24)
	);
}


function getRequestStatus4ReportXML()
{
	$xml = "";

	$xml .= "<StatusRequest4Report>";
	$xml .= "<Element id=\"1\">Перевод</Element>";
	$xml .= "<Element id=\"2\">Записано</Element>";
	$xml .= "<Element id=\"3\">Приём состоялся</Element>";
	$xml .= "<Element id=\"4\">Отказ</Element>";
	$xml .= "<Element id=\"5\">Оплачено</Element>";
	$xml .= "</StatusRequest4Report>";

	return $xml;
}


function getClinicList4DoctorsByXML($cityId = 1)
{
	$xml = "";

	$cityId = intval($cityId);

	//Список клиник (не филилов)
	$sql =
		"SELECT id, name, short_name, parent_clinic_id FROM clinic WHERE isClinic = 'yes' AND parent_clinic_id = 0 AND city_id = " .
		$cityId .
		" AND status = 3 ORDER BY name";
	//echo $sql;
	$result = query($sql);
	if (num_rows($result) > 0) {
		$xml .= "<ClinicList>";

		while ($row = fetch_object($result)) {
			$xml .= "<Element id=\"" . $row->id . "\">";
			$xml .= "<ParentId>" . $row->parent_clinic_id . "</ParentId>";
			$xml .= "<Name>" . $row->name . "</Name>";
			$xml .= "<ShortName>" . $row->short_name . "</ShortName>";
			$xml .= "</Element>";

			if ($row->id > 0) {
				// Филиалы
				$sqlAdd =
					"SELECT id, name, short_name, parent_clinic_id FROM clinic WHERE isClinic = 'yes' AND parent_clinic_id = " .
					$row->id .
					" AND status = 3 ORDER BY name";
				$resultAdd = query($sqlAdd);
				if (num_rows($resultAdd) > 0) {
					while ($rowAdd = fetch_object($resultAdd)) {
						$xml .= "<Element id=\"" . $rowAdd->id . "\">";
						$xml .= "<ParentId>" . $rowAdd->parent_clinic_id . "</ParentId>";
						$xml .= "<Name>" . $rowAdd->name . "</Name>";
						$xml .= "<ShortName>" . $rowAdd->short_name . "</ShortName>";
						$xml .= "</Element>";
					}
				}
			}
		}

		$xml .= "</ClinicList>";
	}

	return $xml;
}


function getDoctorListReportXML($params = array(), $cityId = 1)
{
	$xml = "";
	$sqlAdd = " t2.city_id = " . $cityId . " ";
	$addJoin = "";
	$startPage = 1;
	$step = 100;
	$withPager = true;

	if (count($params) > 0) {

		if (isset($params['withPager'])) {
			$withPager = $params['withPager'];
		}

		if (isset($params['name']) && !empty ($params['name'])) {
			$sqlAdd .= " AND LOWER(t1.name) LIKE  '%" . strtolower($params['name']) . "%' ";
		}
		if (isset($params['status']) && !empty ($params['status'])) {
			$sqlAdd .= " AND t1.status = " . $params['status'] . " ";
		}
		if (isset($params['statusList']) && count($params['statusList']) > 0) {
			$sqlAdd .= " AND ( ";
			foreach ($params['statusList'] as $status) {
				$sqlAdd .= " t1.status = '" . $status . "' OR ";
			}
			$sqlAdd = rtrim($sqlAdd, "OR ");
			$sqlAdd .= ") ";
			//$sqlAdd .= " AND sms.status = '".$params['status']."' ";
		}
		if (isset($params['clinic']) && intval($params['clinic']) > 0) {
			if (isset($params['branch']) && intval($params['branch']) == 1) {
				$sqlAdd .=
					" 	AND
														(
															t2.id = " . $params['clinic'] . "
										OR 
										t2.parent_clinic_id = " . $params['clinic'] . "
										OR 
										( 	t2.parent_clinic_id IN ( SELECT parent_clinic_id FROM clinic WHERE id = " .
					$params['clinic'] .
					"  )
																AND
																t2.parent_clinic_id IS NOT NULL
																AND
																t2.parent_clinic_id <> 0
															)
														) ";
			} else {
				$sqlAdd .= " AND d4c.clinic_id = " . $params['clinic'] . " ";
			}
		} else {
			$sqlAdd .= " AND t1.clinic_id is null ";
		}
		if (isset($params['departure']) && intval($params['departure']) == 1) {
			$sqlAdd .= " AND t1.departure = 1 ";
		}
		if (isset($params['sector']) && intval($params['sector']) > 0) {
			$sqlAdd .= " AND t3.sector_id = " . $params['sector'] . " ";
		}

		if (isset($params['shImg']) && !empty ($params['shImg'])) {
			$sqlAdd .= " AND t1.image IS NOT NULL ";
		}
		if (isset($params['shExp']) && !empty ($params['shExp'])) {
			$sqlAdd .= " AND t1.experience_year IS NOT NULL AND  t1.experience_year > 0 ";
		}
		if (isset($params['shRank']) && !empty ($params['shRank'])) {
			$sqlAdd .= " AND ( t1.degree_id > 0 OR t1.category_id > 0 OR t1.rank_id > 0 OR t1.text_degree IS NOT NULL )  ";
		}

		if (isset($params['sortBy'])) {
			switch ($params['sortBy']) {
				case 'crDate'        :
					$sortBy = " t1.created ";
					break;
				case 'name'        :
					$sortBy = " FullName ";
					break;
				case 'rating'        :
					$sortBy = " complexRating ";
					break;
				case 'status'        :
					$sortBy = " t1.status ";
					break;
				case 'sector'        :
					$sortBy = " sec.name ";
					break;
				case 'id'        :
					$sortBy = " t1.id ";
					break;
				default:
					break;
			}
			if (isset($params['sortType']) && $params['sortType'] == 'asc') {
				$sqlSort = " ORDER BY " . $sortBy . " ASC";
			} else {
				$sqlSort = " ORDER BY " . $sortBy . " DESC";
			}
		} else {
			$sqlSort = " ORDER BY t1.created DESC, t1.id";
		}
	}

	$sql = "SELECT
					t1.id,  t1.name as FullName, t1.status,
					t1.total_rating, t1.rating, t1.rating_opinion, t1.kids_reception,
					CASE 
						WHEN t1.rating <> 0 THEN t1.rating 
						WHEN t1.total_rating <> 0 THEN t1.total_rating 
						ELSE 0
						END AS complexRating,
						
					CASE 
						WHEN EXISTS(SELECT * FROM education_4_doctor edu WHERE edu.doctor_id = t1.id ) THEN 'yes'
						WHEN t1.text_education IS NOT NULL AND t1.text_education <> '' THEN 'yes' 
						ELSE 'no'
						END AS isEducation,
					t1.price, t1.special_price,
					DATE_FORMAT( t1.created,'%d.%m.%Y') AS crDate,
					t2.name as clinicFull, t2.short_name as clinicShort, t2.id as clinicId,
					t1.departure as isDeparture, 
					t1.experience_year,
					t1.image,
					t1.degree_id, t1.category_id, t1.rank_id,
					t1.rewrite_name
				FROM doctor  t1
				INNER JOIN  doctor_4_clinic d4c ON (t1.id = d4c.doctor_id and d4c.type = " . DoctorClinicModel::TYPE_DOCTOR . ")
				INNER JOIN clinic t2 ON (d4c.clinic_id = t2.id)
				LEFT JOIN doctor_sector t3 ON (t3.doctor_id = t1.id)
				LEFT JOIN sector sec ON (t3.sector_id = sec.id)
				" . $addJoin . "
				WHERE " . $sqlAdd . "
			    GROUP BY t1.id " . $sqlSort;
	//echo $sql;

	$result = query($sql);
	if (num_rows($result) > 0) {
		$xml .= "<DoctorList>";
		while ($row = fetch_object($result)) {
			$xml .= "<Element id=\"" . $row->id . "\">";
			$xml .= "<CrDate>" . $row->crDate . "</CrDate>";
			$xml .= "<Alias>" . $row->rewrite_name . "</Alias>";
			$xml .=
				"<Url>http://" .
				($cityId == 2 ? 'spb.' : '') .
				"docdoc.ru/doctor/" .
				(!empty($row->rewrite_name) ? $row->rewrite_name : $row->id) .
				"</Url>";
			$xml .= "<Name>" . $row->FullName . "</Name>";
			$xml .= "<TotalRating>" . $row->total_rating . "</TotalRating>";
			$xml .= "<Rating>" . $row->rating . "</Rating>";
			$xml .= "<complexRating>" . $row->complexRating . "</complexRating>";
			$xml .= "<Price>" . $row->price . "</Price>";
			$xml .= "<SpecialPrice>" . $row->special_price . "</SpecialPrice>";
			$xml .= "<Status>" . $row->status . "</Status>";
			$xml .=
				"<Clinic id=\"" .
				$row->clinicId .
				"\">" .
				((!empty($row->clinicShort)) ? $row->clinicShort : $row->clinicFull) .
				"</Clinic>";
			$xml .= getSectorByDoctorIdXML($row->id);
			$xml .= getClinicByDoctorIdXML($row->id);
			//				$xml .= getRequestByDoctorIdXML ($row -> id, $row -> clinicId, $dateFrom, $dateTill);
			$xml .= "<IsDeparture>" . $row->isDeparture . "</IsDeparture>";
			$xml .= "<IsKidsReception>" . $row->kids_reception . "</IsKidsReception>";
			$xml .= "<ExperienceYear>" . $row->experience_year . "</ExperienceYear>";
			$xml .= "<Degree>" . $row->degree_id . "</Degree>";
			$xml .= "<Category>" . $row->category_id . "</Category>";
			$xml .= "<Rank>" . $row->rank_id . "</Rank>";
			$xml .= "<Image>" . $row->image . "</Image>";
			$xml .= "<IsEducation>" . $row->isEducation . "</IsEducation>";
			$xml .= "</Element>";
		}
		$xml .= "</DoctorList>";
	}
	return $xml;
}


function getRequestByDoctorIdXML($doctorId, $clinicId, $dateFrom, $dateTill)
{
	$xml = "";

	if ($doctorId > 0 && $clinicId > 0) {
		$sql = "SELECT count(req_id) as cnt
					FROM request
					WHERE
						req_doctor_id = " . $doctorId . "
						AND
						clinic_id = " . $clinicId . "
						AND
						req_status = 3
						AND
						FROM_UNIXTIME( date_admission  ) BETWEEN DATE('" . $dateFrom . "') AND DATE('" . $dateTill . "')
						AND
						kind = " . DocRequest::KIND_DOCTOR;
		$result = query($sql);
		$row = fetch_object($result);
		$xml .= "<RequestCount>" . $row->cnt . "</RequestCount>";
	}

	return $xml;
}


function getClinicByDoctorIdXML($doctorId)
{
	$xml = "";

	if ($doctorId > 0) {
		$sql = "SELECT cl.id, cl.name, cl.short_name
					FROM clinic cl
					INNER JOIN  doctor_4_clinic d4c ON (cl.id = d4c.clinic_id and d4c.type = " . DoctorClinicModel::TYPE_DOCTOR . ")
					WHERE
						d4c.doctor_id = " . $doctorId;
		//echo $sql."<br>";
		$result = query($sql);
		if (num_rows($result) > 0) {
			$xml .= "<ClinicList>";
			while ($row = fetch_object($result)) {
				$xml .= "<Clinic>";
				$xml .= "<Id>" . $row->id . "</Id>";
				$xml .= "<Name>" . $row->name . "</Name>";
				$xml .= "<ShortName>" . $row->short_name . "</ShortName>";
				$xml .= "</Clinic>";
			}
			$xml .= "</ClinicList>";
		}
	}

	return $xml;
}
