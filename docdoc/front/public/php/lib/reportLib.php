<?php
	require_once dirname(__FILE__)."/../../lib/php/dateconvertionLib.php";
	
	define ("validTimeLimit","20");
	define ("validTimeLimitII","20");
	
	
	function diagnosticaCallRepoetXML ($params=array()) {
		$xml = "";
		$sqlAdd = " 1=1 ";

		if (count($params) > 0) {
			if	( isset($params['crDateFrom']) && !empty ($params['crDateFrom'])  )  {
				$sqlAdd .= " AND t1.crDate >= date(".convertDate2DBformat($params['crDateFrom']).") " ;
			}
			if	( isset($params['crDateTill']) && !empty ($params['crDateTill'])  )  {
				$sqlAdd .= " AND t1.crDate <= date(".convertDate2DBformat($params['crDateTill']).") " ;
			}
		}

	
		$dayArray = getInterval($params['crDateFrom'], $params['crDateTill'] );
		$xml .= "<DayList>";
		foreach ( $dayArray as $day)
			$xml .= "<Day>".$day."</Day>";
		$xml .= "</DayList>";
		
		
		//Список клиник
		$sql = "SELECT 
					t1.id_clinic as id, t1.numberTo, t1.price,
					t2.name as clinic, t2.short_name
				FROM call4diagnostica t1
				LEFT JOIN clinic t2 ON (t1.id_clinic = t2.id)
				WHERE 
					 DATE(crDate) between DATE('".convertDate2DBformat($params['crDateFrom'])."') AND DATE('".convertDate2DBformat($params['crDateTill'])."')
				GROUP BY t1.id_clinic";
		//echo $sql;
		$result = query($sql);
		if (num_rows($result) > 0) {
			$xml .= "<ClinicList>";
			$clinicList = array();;
			while ($row = fetch_object($result)) {
				array_push($clinicList,  $row -> id);
				$xml .= "<Element id=\"".$row -> id."\">";
				$xml .= "<Name>".$row -> clinic."</Name>";
				$xml .= "<SoftName>".$row -> clinic."</SoftName>";
				$xml .= "<ShortName>".$row -> short_name."</ShortName>";
				$xml .= "<Phone number=\"".$row -> numberTo."\">".formatPhone($row -> numberTo)."</Phone>";
				$xml .= "<Price>".$row -> price."</Price>";
				$xml .= "</Element>";
			}
			$xml .= "</ClinicList>";
		}
		

		
		if ( isset($dayArray) && isset($clinicList) ) {
			$xml .= "<TotalData>";
			foreach ( $dayArray as $day) {
				$xml .= "<Day day=\"".$day."\">";
				
				foreach ( $clinicList as $id) {
					$xml .= "<Clinic id=\"".$id."\"> ";
					
					// Общая статистика
					$sql = "SELECT 
								count(*) as total,
								count( distinct numberFrom ) as uniqCaller
							FROM call4diagnostica
							WHERE 
								 DATE(crDate) = DATE('".convertDate2DBformat($day)."')
								 AND
								 id_clinic = ".$id;
					//echo $sql;
					$result = query($sql);
					if (num_rows($result) == 1) {
						$row = fetch_object($result);
						$xml .= "<Data total=\"".$row -> total."\"  uniq=\"".$row -> uniqCaller."\"/>";
					}
					
					// Валидные звонки
					$sql = "SELECT 
								count(*) as total,
								count( distinct numberFrom ) as uniqCaller
							FROM call4diagnostica
							WHERE 
								DATE(crDate) = DATE('".convertDate2DBformat($day)."')
								AND
								id_clinic = ".$id."
								AND
								duration >= ".validTimeLimit;
					//echo $sql;
					$result = query($sql);
					if (num_rows($result) == 1) {
						$row = fetch_object($result);
						$xml .= "<ValidData total=\"".$row -> total."\"  uniq=\"".$row -> uniqCaller."\"/>";
					}
					
					// Валидные звонки > порога 2
					$sql = "SELECT 
								count(*) as total,
								count( distinct numberFrom ) as uniqCaller
							FROM call4diagnostica
							WHERE 
								DATE(crDate) = DATE('".convertDate2DBformat($day)."')
								AND
								id_clinic = ".$id."
								AND
								duration >= ".validTimeLimitII;
					//echo $sql;
					$result = query($sql);
					if (num_rows($result) == 1) {
						$row = fetch_object($result);
						$xml .= "<ValidDataII total=\"".$row -> total."\"  uniq=\"".$row -> uniqCaller."\"/>";
					}
					
					$xml .= "</Clinic>";
				}
				$xml .= "</Day>";
			}
			$xml .= "</TotalData>";
		}
		
		return $xml;
	}
		
		
		
	function getClinicListByIdWithBranchesXML ($id) {
		$xml = "";
		
		$id = intval($id);

		if ( $id > 0) {
			//Список клиник
			$sql = "SELECT id, name, short_name FROM clinic WHERE id= ".$id." OR parent_clinic_id=".$id;
			//echo $sql;
			$result = query($sql);
			if (num_rows($result) > 0) {
				$xml .= "<ClinicList>";
				$clinicList = array();;
				while ($row = fetch_object($result)) {
					array_push($clinicList,  $row -> id);
					$xml .= "<Element id=\"".$row -> id."\">";
					$xml .= "<Name>".$row -> name."</Name>";
					$xml .= "<ShortName>".$row -> short_name."</ShortName>";
					$xml .= "</Element>";
				}
				$xml .= "</ClinicList>";
			}
		}
		
		return $xml;
	}
	
	
	
	
	function getClinicListByXML ($dateFrom, $dateTill, $cityId = 1) {
		$xml = "";
		
		$dateFrom =  convertDate2DBformat($dateFrom);
		$dateTill = convertDate2DBformat($dateTill);
		$cityId = intval($cityId);
		

			//Список клиник (не филилов)
			$sql = "SELECT id, name, short_name, parent_clinic_id FROM clinic WHERE isClinic = 'yes' AND parent_clinic_id = 0 AND city_id = ".$cityId." AND status = 3 ORDER BY name";
			//echo $sql;
			$result = query($sql);
			if (num_rows($result) > 0) {
				$xml .= "<ClinicList>";
				
				while ($row = fetch_object($result)) {
					$xml .= "<Element id=\"".$row -> id."\">";
					$xml .= "<ParentId>".$row -> parent_clinic_id."</ParentId>";
					$xml .= "<Name>".$row -> name."</Name>";
					$xml .= "<ShortName>".$row -> short_name."</ShortName>";
					$xml .= "<Transfer>".getRequestCount($row -> id, 'transfer', $dateFrom, $dateTill)."</Transfer>";
					$xml .= "<Apointment>".getRequestCount($row -> id, 'apointment', $dateFrom, $dateTill)."</Apointment>";
					$xml .= "<Complete>".getRequestCount($row -> id, 'complete', $dateFrom, $dateTill)."</Complete>";
					//$xml .= "<Total>".getRequestCount($row -> id, 'total', $dateFrom, $dateTill)."</Total>";
					$xml .= "</Element>";
				
					if ( $row -> id > 0 ) {
						// Филиалы
						$sqlAdd = "SELECT id, name, short_name, parent_clinic_id FROM clinic WHERE isClinic = 'yes' AND parent_clinic_id = ".$row -> id." AND status = 3 ORDER BY name";
						$resultAdd = query($sqlAdd);
						if (num_rows($resultAdd) > 0) {
							while ($rowAdd = fetch_object($resultAdd)) {
								$xml .= "<Element id=\"".$rowAdd -> id."\">";
								$xml .= "<ParentId>".$rowAdd -> parent_clinic_id."</ParentId>";
								$xml .= "<Name>".$rowAdd -> name."</Name>";
								$xml .= "<ShortName>".$rowAdd -> short_name."</ShortName>";
								$xml .= "<Transfer>".getRequestCount($rowAdd -> id, 'transfer', $dateFrom, $dateTill)."</Transfer>";
								$xml .= "<Apointment>".getRequestCount($rowAdd -> id, 'apointment', $dateFrom, $dateTill)."</Apointment>";
								$xml .= "<Complete>".getRequestCount($rowAdd -> id, 'complete', $dateFrom, $dateTill)."</Complete>";
								//$xml .= "<Total>".getRequestCount($rowAdd -> id, 'total', $dateFrom, $dateTill)."</Total>";
								$xml .= "</Element>";
							}
						}
					}
				}
			
				$xml .= "</ClinicList>";
			}
		
		
		return $xml;
	}

	
	
	
	
	function getRequestCount ($clinicId, $type = 'complete', $dateFrom, $dateTill ) {
		$count = -1;
		
		$sql = "";
		switch ($type) {
			case 'complete' : $sql = "SELECT count(req_id) as cnt FROM request WHERE clinic_id = $clinicId AND req_status = 3 AND FROM_UNIXTIME(date_admission) between date('".$dateFrom."') and date('".$dateTill."') "; break;
			case 'apointment' : $sql = "SELECT count(req_id) as cnt FROM request WHERE clinic_id = $clinicId AND date_admission is not null AND  date_admission > 0  AND FROM_UNIXTIME(date_admission) between date('".$dateFrom."') and date('".$dateTill."') "; break;
			case 'transfer' : $sql = "SELECT count(req_id) as cnt FROM request WHERE clinic_id = $clinicId AND is_transfer = 1 AND FROM_UNIXTIME(req_created) between date('".$dateFrom."') and date('".$dateTill."') "; break;
			//case 'total' : $sql = "SELECT count(req_id) as cnt FROM request WHERE clinic_id = $clinicId  AND FROM_UNIXTIME(req_created) between date('".$dateFrom."') and date('".$dateTill."') "; break;
		}
		
		$result = query($sql);
		if (num_rows($result) == 1) {
			$row = fetch_object($result);
			$count = $row -> cnt;	
		}
		
		return $count;
	}
	


	
	function getInterval($start, $end, $format='d.m.Y')
	{
	   return array_map(create_function('$item', 'return date("'.$format.'", $item);'),range(strtotime($start), strtotime($end), 60*60*24));
	}
