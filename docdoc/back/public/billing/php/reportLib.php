<?php
	require_once dirname(__FILE__)."/../../lib/php/dateconvertionLib.php";
	
	
	
	function getDiagRequestCount4Billing ($clinicId, $type = 'complete', $dateFrom, $dateTill, $dateMethod = 'create') {
		$count = -1;
		$clinicId = intval($clinicId);
		$withBranch = true;
		
		$sql = "";
		$sqlAdd = "";
		$sqlClinic = " 1=1 ";
		$sqlJoin = "";
		
		if ($clinicId > 0) {
			$sqlClinic = " t1.clinic_id = $clinicId ";
			if ( $withBranch ) {
				$sqlClinic = " t1.clinic_id IN (SELECT DISTINCT grList.id FROM (SELECT id FROM clinic WHERE parent_clinic_id = ".$clinicId. " UNION SELECT $clinicId AS id) as grList) ";
			}
		}
		
		if ( $dateMethod == 'admission' ) {
			$sqlDateRestict = "	AND DATE(t1.date_admission) >= '".convertDate2DBformat($dateFrom)."' 
								AND DATE(t1.date_admission) <= '".convertDate2DBformat($dateTill)."' ";
		} else {
			$sqlDateRestict = "	AND DATE(t1.cr_date) >= '".convertDate2DBformat($dateFrom)."' 
								AND DATE(t1.cr_date) <= '".convertDate2DBformat($dateTill)."' ";
			
		}
		
		/*
		 * Все состояния отслеживаются исключительно по статуцсам заявки не зависимо от данных, которые проставил оператор. 
		 * Со слов Антонова Романа со ссылкой на Дмитрия Петрухина 
		 * От 18 сентября 2013 
		 * */
		
		switch ($type) {
			case 'complete' : 	$sqlAdd = " AND t1.status = 7 ".$sqlDateRestict; break; // status = 7 - дошёл
			case 'admission' : 	$sqlAdd = " AND ( t1.date_admission IS NOT NULL ) ".$sqlDateRestict; break; // status = 2 - записан
			case 'reject' : 	$sqlAdd = " AND t1.status = 3 ".$sqlDateRestict; break; // status = 3 - отказ
			case 'total' : 		$sqlAdd = $sqlDateRestict; break;
			case 'total30' : 	{$sqlAdd = " AND t2.duration > 30 ".$sqlDateRestict; $sqlJoin = " LEFT JOIN diag_request_record t2 ON ( t1.diag_req_id = t2.diag_req_id ) ";}  break;
		}

		$sql = "	SELECT 
						count(DISTINCT(t1.diag_req_id)) as cnt
					FROM diag_request t1
					".$sqlJoin."
					WHERE 
						".$sqlClinic." 
						".$sqlAdd."
						AND
						t1.status <> 5";
		
		//if ($type == 'total') echo $sql."<br>";
		$result = query($sql);
		if (num_rows($result) == 1) {
			$row = fetch_object($result);
			$count = $row -> cnt;	
		}
		
		return $count;
	}
	
	
	
	
	
	
	
	function getDiagRecordCount4Billing ($clinicId, $dateFrom, $dateTill, $duration = 0, $dateMethod = 'create') {
		$count = 0;
		$sqlAdd = "";
		$sqlClinic = " 1=1 ";
		$duration = intval($duration);
		$withBranch = true;
				
		if ($clinicId > 0) {
			$sqlClinic = " AND clinic_id = $clinicId ";
			if ( $withBranch ) {
				$sqlClinic = " AND clinic_id IN (SELECT DISTINCT grList.id FROM (SELECT id FROM clinic WHERE parent_clinic_id = ".$clinicId. " UNION SELECT $clinicId AS id) as grList) ";
			}
		}
		
		if ( !empty($duration) && $duration > 0 ) {
			$sqlAdd = " AND t1.duration >= ".$duration." "; 
		}
		
		if ( $dateMethod == 'admission' ) {
			$sqlDateRestict = "	AND DATE(t2.date_admission) >= '".convertDate2DBformat($dateFrom)."' 
								AND DATE(t2.date_admission) <= '".convertDate2DBformat($dateTill)."' ";
		} else {
			$sqlDateRestict = "	AND DATE(t2.cr_date) >= '".convertDate2DBformat($dateFrom)."' 
								AND DATE(t2.cr_date) <= '".convertDate2DBformat($dateTill)."' ";
		}
		
		$sqlAdd .= $sqlDateRestict;
		
		
		 
		$sql = "	SELECT 
						count(t1.record_id) as cnt
					FROM diag_request_record t1, diag_request t2 
					WHERE 
						t1.diag_req_id = t2.diag_req_id
						".$sqlClinic." 
						".$sqlAdd."
						AND
						t2.status <> 4";
		
		//echo $sql."<br>";
		$result = query($sql);
		if (num_rows($result) == 1) {
			$row = fetch_object($result);
			$count = $row -> cnt;	
		}
		
		return $count;
	}


	
	
	
	
	

	
	
	
	
	function getDiagRequestListByContract4BillingXML ($params) {
		$xml = "";
		$sqlAdd = "t1.status <> 5  "; // не удалена
		$sqlSort = " ";
		$sqlHaving = "";
		$withPager = true;
	
		if	( isset($params['withPager']) )  {
			$withPager = $params['withPager'];
		}
				 
		if (count($params) > 0) {
	
			if	( isset($params['contractId']) && $params['contractId'] > 0 )  {
				switch ($params['contractId']) {
					case 4 : { $sqlAdd .= " AND t1.date_admission IS NOT NULL "; } break;
					case 3 : { $sqlHaving .= " HAVING MaxDuration > 30"; } break;
					case 5 : { $sqlAdd .= " AND t1.status = 7 "; } break;
				}
			}
			
			if	( isset($params['status']) && $params['status'] != '' )  {
				$sqlAdd .= " AND t1.status = '".$params['status']."' ";
			}
			
			if	( isset($params['owner']) && $params['owner'] == -1 )  {
				$sqlAdd .= " AND (t1.owner_id IS NULL OR t1.owner_id = 0 ) ";
			} else if (isset($params['owner']) && $params['owner'] > 0 ) {
				$sqlAdd .= " AND t1.owner_id = ".$params['owner']." ";
			}
			
			if	( isset($params['clinicId']) && $params['clinicId'] > 0)  {
				$sqlAdd .= " AND t1.clinic_id = '".$params['clinicId']."' ";
			}
			
			if	( isset($params['client']) && !empty($params['client']) )  {
				$sqlAdd .= " AND LOWER(t1.patient_fio) LIKE '%".strtolower($params['client'])."%' ";
			}
			
			
			/*	Дата создания заявки	*/
			if	( isset($params['crDateFrom']) && !empty ($params['crDateFrom'])  )  {
				$sqlAdd .= " AND date(t1.cr_date) >= date('".convertDate2DBformat($params['crDateFrom'])."') " ;
			}
			if	( isset($params['crDateTill']) && !empty ($params['crDateTill'])  )  {
				$sqlAdd .= " AND date(t1.cr_date) <= date('".convertDate2DBformat($params['crDateTill'])."') " ;
			}
	                
			/*	Дата приёма	*/
			if	( isset($params['dateReciveFrom']) && !empty ($params['dateReciveFrom'])  )  {
				$sqlAdd .= " AND date(t1.date_admission) >= date('".convertDate2DBformat($params['dateReciveFrom'])."') " ;
			}
			if	( isset($params['dateReciveTill']) && !empty ($params['dateReciveTill'])  )  {
				$sqlAdd .= " AND date(t1.date_admission) <= date('".convertDate2DBformat($params['dateReciveTill'])."') " ;
			}
			
			
			/*	Диагностика	*/
			if	( isset($params['diagIdList']) && !empty ($params['diagIdList'])  )  {
				$sqlAdd .= " AND t1.diagnostica_id IN (".$params['diagIdList'].") ";
			}
			
			/*	Телефон исходящий	*/
			if	( isset($params['phoneFrom']) && !empty ($params['phoneFrom'])  )  {
				$sqlAdd .= " AND (t1.phone_from LIKE  '%".$params['phoneFrom']."%' OR t1.add_patient_phone LIKE  '%".$params['phoneFrom']."%')";
			}
			
			
			/*	Длительность	*/
			if	( isset($params['duration']) && !empty ($params['duration'])  )  {
				$sqlHaving .= " HAVING MaxDuration > ".$params['duration']." ";
			}
			
				
			if	( isset($params['id']) && !empty ($params['id'])  )  {
				$sqlAdd = " t1.diag_req_id = '".$params['id']."'";
			}
				
			if ( isset($params['sortBy']) )  {
				switch ($params['sortBy']) {
					case 'crDate'	: $sortBy= " t1.cr_date";break;
					case 'admDate'	: $sortBy= " t1.date_admission";break;
					case 'status'	: $sortBy= " st";break;
					case 'id'		: $sortBy= " t1.diag_req_id ";break;
					default:break;
				}
				if (isset($params['sortType']) && $params['sortType'] == 'asc')  {
					$sqlSort = " order by ".$sortBy." asc";
				} else {
					$sqlSort = " order by ".$sortBy." desc";
				}
			} 
				
		}
	
		$sqlLimit = " 1 = 1 ";
		$sql = "SELECT
						t1.diag_req_id as id, 
						t1.clinic_id,
						t1.cr_date,
						DATE_FORMAT(t1.cr_date, '%d.%m.%Y') as CrDate,
						DATE_FORMAT(t1.cr_date, '%H:%i') AS CrTime,
						t1.date_admission,
						DATE_FORMAT(t1.date_admission, '%d.%m.%Y') as AdmissionDate,
						DATE_FORMAT(t1.date_admission, '%d.%m.%Y') as AdmDate,
						DATE_FORMAT(t1.date_admission, '%H:%i') AS AdmTime,
						t1.status, 
						t1.src_type,
						t1.diagnostica_id,
						t1.diagnostica_other,
						t1.owner_id,
						t1.phone_from, t1.phone_to,
						t1.patient_fio, 
						t1.add_patient_phone,
						t2.name as clinic_name, 
						CASE 
							WHEN t1.status  = 0 THEN 6
							WHEN t1.status  = 1 THEN 5
							WHEN t1.status  = 2 THEN 4
							WHEN t1.status  = 3 THEN 3
							WHEN t1.status  = 5 THEN 2
							WHEN t1.status  = 4 THEN 1
							WHEN t1.status  = 6 THEN 0
							ELSE 0
						END AS st ,
						(SELECT COUNT(t5.record_id) FROM diag_request_record t5 WHERE t5.diag_req_id = t1.diag_req_id) AS RecordsCount,
						(SELECT MAX(t5.Duration) FROM diag_request_record t5 WHERE t5.diag_req_id = t1.diag_req_id) AS MaxDuration,
						(SELECT SUM(t5.Duration) FROM diag_request_record t5 WHERE t5.diag_req_id = t1.diag_req_id) AS Duration,
						CASE 
							WHEN dg2.name IS NOT NULL AND dg1.name IS NOT NULL THEN  CONCAT(dg2.name, ' ', dg1.name)
							WHEN dg1.name IS NOT NULL THEN dg1.name
						END AS  diagnostica
					FROM diag_request  t1
					LEFT JOIN clinic t2 ON (t1.clinic_id = t2.id)
					LEFT JOIN diagnostica dg1 ON (t1.diagnostica_id = dg1.id)
					LEFT JOIN diagnostica dg2 ON (dg2.id = dg1.parent_id)
					
					WHERE
						".$sqlAdd
						 .$sqlHaving
						 .$sqlSort;
	
		 //echo $sql;
		
			if ( isset($params['step']) && intval($params['step']) > 0 ) $step = $params['step'];
			if ( isset($params['startPage']) && intval($params['startPage']) > 0 ) $startPage = $params['startPage'];
		
		if ( $withPager ) {
			list($sql, $str) = pager( $sql, $startPage, $step, "");
			$xml .= $str;
			//echo $str."<br/>";
		}
	
		$result = query($sql);
		if (num_rows($result) > 0) {
			$xml .= "<DiagRequestList>";
			while ($row = fetch_object($result)) {
				$xml .= "<Element id=\"".$row -> id."\">";
				$xml .= "<Id>".$row -> id."</Id>";
				$xml .= "<CrDate>".$row -> CrDate."</CrDate>";
				$xml .= "<CrTime>".$row -> CrTime."</CrTime>";
				$xml .= "<AdmDate>".$row -> AdmDate."</AdmDate>";
				$xml .= "<AdmTime>".$row -> AdmTime."</AdmTime>";
				$xml .= "<Client><![CDATA[".mb_convert_case($row -> patient_fio, MB_CASE_TITLE, 'UTF8')."]]></Client>";
				$xml .= "<Clinic id=\"".$row -> clinic_id."\">".$row -> clinic_name."</Clinic>";
				$xml .= "<DiagnosticaId>".$row -> diagnostica_id."</DiagnosticaId>";
				$xml .= "<DiagnosticaName>".$row -> diagnostica_other."</DiagnosticaName>";
				$xml .= "<DiagnosticaFullName>".$row -> diagnostica."</DiagnosticaFullName>";
				$xml .= "<OwnerId>".$row -> owner_id."</OwnerId>";
				$xml .= "<Status>".$row -> status."</Status>";
				$xml .= "<Type>".$row -> src_type."</Type>";
				$xml .= "<PhoneFrom digit=\"".$row -> phone_from."\">".formatPhone($row -> phone_from)."</PhoneFrom>";
				$xml .= "<PhoneFromAdd digit=\"".$row -> add_patient_phone."\">".formatPhone($row -> add_patient_phone)."</PhoneFromAdd>";
				$xml .= "<PhoneTo digit=\"".$row -> phone_to."\">".formatPhone($row -> phone_to)."</PhoneTo>";
				//$xml .= "<ContractId>".$row -> contract_id."</ContractId>";
				$xml .= "<RecordsCount>".$row -> RecordsCount."</RecordsCount>";
				$xml .= "<Duration sec=\"".$row -> Duration."\">".formatTime($row -> Duration)."</Duration>";
				$xml .= "</Element>";
			}
			$xml .= "</DiagRequestList>";
		}
		return $xml;
	}
?>
