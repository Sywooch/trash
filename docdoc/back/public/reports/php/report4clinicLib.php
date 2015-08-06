<?php
	function getPatientListXML ($params=array(), $clinicId = 0 ) {
	
	$delta = 3600; // задержка для клиники на отклик по заявке, в секундах
	$deltaStatus = 3600*24*3; // задержка для обжалования статуса заявки
	
    $xml = "";
    $sqlAdd = "";
    $sqlSort = " ORDER BY st, t1.date_admission DESC, t1.req_created DESC  ";
    $startPage = 1;
    $step = 30;
    $withPager = true;

	$contractId = $params["clinic"]->contractId;
	
    if (count($params) > 0) {

        if	( isset($params['withPager']) )  {
            $withPager = $params['withPager'];
        }

        /*
        if	( isset($params['clinic']) )  {
            if ($params['clinic'] >= 0  ) {
                $subSQL = "SELECT id FROM clinic WHERE id= ".$params['clinic']." OR parent_clinic_id=".$params['clinic'];
                $sqlAdd .= " AND t1.clinic_id IN (".$subSQL.") ";
            } else
                $sqlAdd .= " AND t1.clinic_id is null " ;
        }
        */

        /*	Дата создания заявки	*/
        /*if	( isset($params['crDateFrom']) && !empty ($params['crDateFrom'])  )  {
            $sqlAdd .= " AND t1.req_created >= ".strtotime($params['crDateFrom'])." " ;
        }*/
        /*if	( isset($params['crDateTill']) && !empty ($params['crDateTill'])  )  {
            $sqlAdd .= " AND t1.req_created <= ".(strtotime($params['crDateTill'])+86400)." " ;
        }*/

        /*	Дата приёма	*/
        if	( isset($params['crDateFrom']) && !empty ($params['crDateFrom'])  )  {
            $sqlAdd .= " AND t1.date_admission >= ".strtotime(convertDate2DBformat($params['crDateFrom']))." AND t1.date_admission IS NOT NULL " ;
        }
        if	( isset($params['crDateTill']) && !empty ($params['crDateTill'])  )  {
            $sqlAdd .= " AND t1.date_admission <= ".(strtotime(convertDate2DBformat($params['crDateTill']))+86400)." " ;
        }

        /*	Специализация	*/
        if	( isset($params['shSector']) && !empty ($params['shSector'])  )  {
            $sqlAdd .= " AND UPPER(t3.name) LIKE '%".strtoupper($params['shSector'])."%' ";
        }

        /*  Врач  */
        if	( isset($params['shDoctor']) && !empty ($params['shDoctor'])  )  {
            $sqlAdd .= " AND UPPER(t2.name) LIKE '%".strtoupper($params['shDoctor'])."%' ";
        }
        
    	if	( isset($params['requestId']) && $params['requestId'] > 0 )  {
            $sqlAdd = " AND t1.req_id = ".$params['requestId']." ";
        }

        if ( isset($params['sortBy']) && !empty ($params['sortBy']))  {
            switch ($params['sortBy']) {
                case 'crDate'	: $sortBy= " t1.req_created";break;
                case 'admDate'  : $sortBy= " t1.date_admission ";break;
                case 'reqId'	: $sortBy= " t1.req_id ";break;
                case 'patient'  : $sortBy= " t1.client_name ";break;
                case 'docName'  : $sortBy= " t2.name ";break;
                case 'docSpec'  : $sortBy= " t3.name ";break;
                //case 'Status'	: $sortBy= " statusName ";break;
                default:break;
            }
            if (isset($params['sortType']) && $params['sortType'] == 'asc')  {
                $sqlSort = " order by ".$sortBy." asc";
            } else {
                $sqlSort = " order by ".$sortBy." desc";
            }
        }

    }
            
	switch ($contractId) {
		case 2 :  {
				// Фикс 600/1000
				$sql = "SELECT
                        t1.req_id as id,
                        t1.clinic_id,
                        t1.client_name, t1.client_phone,
                        t1.req_created, t1.req_status as status, t1.req_type,
                        t1.clientId, t1.call_later_time,
                        t1.req_doctor_id as doctor_id, t2.name as doctor, t1.req_sector_id,
                        t3.name as sector,
                        t1.date_admission, t1.appointment_status, 
                        1  AS st,
                        'visited' AS statusName,
                        0 AS changeable
                    FROM request  t1
                    LEFT JOIN doctor t2 ON (t2.id = t1.req_doctor_id)
                    LEFT JOIN sector t3 ON (t3.id = t1.req_sector_id)
                    WHERE t1.clinic_id = ".$clinicId."
                    	AND 
                    	(t1.req_status  = 2 OR  t1.req_status = 3 )
                    	AND 
                    	t1.date_admission IS NOT NULL  
                    ".$sqlAdd.$sqlSort;
		} break;
		
		default : {
			// Фикс 800/1200/1500
			
			if	( isset($params['status']) && $params['status'] != '' )  {
	            switch ($params['status']) {
	                case 'transferred'	: $sqlAdd .= " AND t1.req_status = 8 "; break;
	                case 'registered'   : $sqlAdd .= " AND t1.req_status = 2 AND t1.is_transfer = 1 AND t1.date_admission > (UNIX_TIMESTAMP(NOW()) - $delta) "; break;
	                case 'visited'      : $sqlAdd .= " AND (t1.req_status = 3 OR (t1.req_status = 2 AND t1.date_admission <= (UNIX_TIMESTAMP(NOW()) + $delta)))"; break;
	                case 'expired'      : $sqlAdd .= " AND t1.req_status=2 AND (t1.date_admission <= UNIX_TIMESTAMP(NOW())) "; break;
	                case 'declined'     : $sqlAdd .= " AND t1.req_status=9 "; break;
	                default:break;
	            }
	        } else {
	            $sqlAdd .= " AND (t1.req_status IN (2,3,8,9) AND t1.is_transfer=1) ";
	        }
        
			$sql = "SELECT
                        t1.req_id as id,
                        t1.clinic_id,
                        t1.client_name, t1.client_phone,
                        t1.req_created, t1.req_status as status, t1.req_type,
                        t1.clientId, t1.call_later_time,
                        t1.req_doctor_id as doctor_id, t2.name as doctor, t1.req_sector_id,
                        t3.name as sector,
                        t1.date_admission, t1.appointment_status, 
                        CASE
                            WHEN t1.req_status  = 2 AND t1.date_admission >  (UNIX_TIMESTAMP( NOW() ) - $delta) THEN 0
                            WHEN t1.req_status  = 2 AND t1.date_admission <=  (UNIX_TIMESTAMP (NOW() ) - $delta) THEN 1
                            WHEN t1.req_status  = 3 THEN 1
                            WHEN t1.req_status  = 8 THEN 2
                            ELSE 3
                        END AS st,
                        CASE
                            WHEN t1.req_status  = 2 AND t1.date_admission >  (UNIX_TIMESTAMP( NOW() ) - $delta) THEN 'registered'
                            WHEN t1.req_status  = 2 AND t1.date_admission <=  (UNIX_TIMESTAMP (NOW() ) - $delta) THEN 'visited'
                            WHEN t1.req_status  = 3 THEN 'visited'
                            WHEN t1.req_status  = 8 THEN 'declined'
                            WHEN t1.req_status  = 9 THEN 'payed'
                            ELSE 'registered'
                        END AS statusName,
                        CASE
        	                WHEN (SELECT count(*) FROM request_record rec WHERE rec.request_id = t1.req_id AND isOpinion ='yes') = 1 THEN 0    
            	            WHEN t1.req_status = 2 AND t1.date_admission < (UNIX_TIMESTAMP(NOW()) - $deltaStatus) THEN 0
            	            WHEN t1.req_status = 3 THEN 0
                            ELSE 1
                        END AS changeable
                    FROM request  t1
                    LEFT JOIN doctor t2 ON (t2.id = t1.req_doctor_id)
                    LEFT JOIN sector t3 ON (t3.id = t1.req_sector_id)
                    WHERE t1.clinic_id = ".$clinicId." ".$sqlAdd.$sqlSort;
		}
				
	}
      //echo $sql;

    if ( isset($params['step']) && intval($params['step']) > 0 ) $step = $params['step'];
    if ( isset($params['startPage']) && intval($params['startPage']) > 0 ) $startPage = $params['startPage'];

    if ( $withPager ) {
        list($sql, $str) = pager( $sql, $startPage, $step, "loglist"); // функция берется из файла pager.xsl с тремя параметрами. параметр article тут не нужен
        $xml .= $str;
        //echo $str."<br/>";
    }

    $result = query($sql);
    if (num_rows($result) > 0) {
        $xml .= "<RequestList>";
        while ($row = fetch_object($result)) {
            $xml .= "<Element id=\"".$row -> id."\">";
            $xml .= "<Doctor  id=\"".$row -> doctor_id."\">".$row -> doctor."</Doctor>";
            $xml .= "<Sector  id=\"".$row -> req_sector_id."\">".$row -> sector."</Sector>";

            $xml .= "<Client id=\"".$row -> clientId."\"><![CDATA[".$row -> client_name."]]></Client>";
            $xml .= "<ClientPhone>".formatPhone($row -> client_phone)."</ClientPhone>";

            $xml .= "<CrDate>".date("d.m.y",$row -> req_created )."</CrDate>";
            $xml .= "<CrTime>".date("H:i",$row -> req_created )."</CrTime>";
            if ( !empty($row -> date_admission) ) {
                $xml .= "<AppointmentDate>".date("d.m.y",$row -> date_admission )."</AppointmentDate>";
                $xml .= "<AppointmentTime>".date("H:i",$row -> date_admission )."</AppointmentTime>";
            }

            $xml .= "<Status>".$row -> statusName."</Status>";
            $xml .= "<Changeable>".$row -> changeable."</Changeable>";
            $xml .= "<Type>".$row -> req_type."</Type>";
            
            $clinic = $params["clinic"];
            if ( !empty($clinic -> settingsId) ) {
	            $xml .= "<ShowBilling>".$clinic -> showBilling."</ShowBilling>";
	            $xml .= "<ContractId>".$clinic -> contractId."</ContractId>";
	            $requestParams = array();
	            $requestParams['sectorId'] = $row -> req_sector_id;
	            $requestParams['contractId'] = $clinic -> contractId;
	            $requestParams['requestId'] = $row -> id;
	            $xml .= "<Cost>".$clinic -> getCost4request($requestParams)."</Cost>";
	            
	            // Для контракта 2-ого типа (Фикс 600/1000) список аудиозаписей
	            if ( $clinic -> contractId == 2) {
		        	$sql2 = "SELECT
									t1.request_id as id, t1.record, 
									DATE_FORMAT( t1.crDate,'%d.%m.%Y') AS crDate,
									DATE_FORMAT( t1.crDate,'%d.%m.%Y %H.%i') AS crDateTime,
									t1.duration, t1.comments as note, t1.isAppointment
								FROM request_record t1
								WHERE 
									t1.request_id = ".$row -> id."
									AND
									t1.isAppointment = 'yes'
								ORDER BY crDate";
					//echo $sql."<br/>";
					$result2 = query($sql2);
					if (num_rows($result2) > 0 ) {
						$xml .= "<RecordList>";
						while ($row2 = fetch_object($result2)) {
							$xml .= "<Element>";
							$xml .= "<Path>".$row2 -> record."</Path>";
							$xml .= "<IsAppointment>".$row2 -> isAppointment."</IsAppointment>";
							$xml .= "<CrDate>".$row2 -> crDate."</CrDate>";
							$xml .= "<CrDateTime>".$row2 -> crDateTime."</CrDateTime>";
							$xml .= "</Element>";
						}
						$xml .= "</RecordList>";
					}
		            	
		            
	            } 
            }
            
            
        	/* проверка, был ли отказ клиники (пациент не пришёл). Если был комментарий клиники и статус заявки завершена и есть аудиозапись с отзывом, то признак "yes" иначе "no" */
            $forced = 'no';
            $sql2 = "SELECT
						distinct(hist.request_id) 
					FROM request_history hist, request_record rec, request req
					WHERE 
						hist.request_id = ".$row -> id."
						AND
						req.req_id = hist.request_id
						AND
						req.req_status = 3
						AND
						req.appointment_status = 1
						AND 
						rec.request_id = hist.request_id
						AND
						rec.isOpinion = 'yes'
						AND
						hist.action = 6";
            $result2 = query($sql2);
            if (num_rows($result2) > 0 ) {
            	$forced = "yes";
            } 
            $xml .= "<Forced>".$forced."</Forced>";
            
            
            /* список аудиозаписей. Если признак forsed = yes */
            if ($forced == "yes") {
	        	$sql2 = "SELECT
								t1.request_id as id, t1.record, 
								DATE_FORMAT( t1.crDate,'%d.%m.%Y') AS crDate,
								DATE_FORMAT( t1.crDate,'%d.%m.%Y %H.%i') AS crDateTime,
								t1.duration, t1.comments as note, t1.isOpinion
							FROM request_record t1
							WHERE 
								t1.request_id = ".$row -> id."
								AND
								t1.isOpinion = 'yes'
							ORDER BY record";
				//echo $sql."<br/>";
				$result2 = query($sql2);
				if (num_rows($result2) > 0 ) {
					$xml .= "<RecordList>";
					while ($row2 = fetch_object($result2)) {
						$xml .= "<Element>";
						$xml .= "<Path>".$row2 -> record."</Path>";
						$xml .= "<IsOpinion>".$row2 -> isOpinion."</IsOpinion>";
						$xml .= "<CrDate>".$row2 -> crDate."</CrDate>";
						$xml .= "<CrDateTime>".$row2 -> crDateTime."</CrDateTime>";
						$xml .= "</Element>";
					}
					$xml .= "</RecordList>";
				}
            	
            }
            
			$xml .= "</Element>";
            
            
            
        }
        
        $xml .= "</RequestList>";
    }
    return $xml;
}




function getRequestStatisticXML ($clinicId, $dateFrom = null, $dateTill = null) {
    $xml ='';
    $xml .= '<RequestStatistic>';
    $xml .= '<All>'.getRequestCountByClinic($clinicId).'</All>';
    $xml .= '<ForLastMonth>'.getRequestCountByClinic($clinicId, time()-30*24*3600, time()).'</ForLastMonth>';
    $xml .= '<ForCurrentMonth>'.getRequestCountByClinic($clinicId, strtotime(date("Y-m")), time()).'</ForCurrentMonth>';
    if(!empty($dateFrom) && !empty($dateTill))
        $xml .= '<ForPeriod>'.getRequestCountByClinic($clinicId, strtotime($dateFrom), strtotime($dateTill)).'</ForPeriod>';
    $xml .= '</RequestStatistic>';
    return $xml;
}




function getRequestCountByClinic ($clinicId, $dateFrom = null, $dateTill = null) {
    $sqlAdd = '';

    if(!empty($dateFrom) && !empty($dateTill)){
        $sqlAdd .= ' AND t1.req_created BETWEEN '.$dateFrom.' AND ('.$dateTill.'+86400)';
    }

    $sql = "SELECT COUNT(t1.req_id) AS cnt
            FROM request t1
            LEFT JOIN clinic t2 ON t2.id=t1.clinic_id
            WHERE t2.id IN (SELECT id FROM clinic WHERE id= ".$clinicId." OR parent_clinic_id=".$clinicId.")
                AND (t1.req_status IN (2,3,8,9) AND t1.is_transfer=1)
                ".$sqlAdd;
    $result = query($sql);
    $row = fetch_object($result);

    return $row->cnt;
}


	/**
	 * 
	 * расчёт стоимости заявки
	 */
	function getCost4request($params = array()) {
		$cost = "0";
		
		switch($params["contractId"]) {
			case 1 :  {
				// Фикс 800/1200/1500
				switch ($params["sectorId"]) {
					case 86 : $cost = 1500.00; break;
					case 90 : $cost = 1200.00; break;
					default:$cost = 800.00;
				} 
			} break;
			case 2 :  {
				// Фикс 600/1000
				switch ($params["sectorId"]) {
					case 86 : $cost = 1000.00; break;
					case 90 : $cost = 1000.00; break;
					default:$cost = 800.00;
				}
				
			} break;
			default : 
				// Фикс 800/1200/1500
				switch ($params["sectorId"]) {
					case 86 : $cost = 1500.00; break;
					case 90 : $cost = 1200.00; break;
					default:$cost = 800.00;
				} 
		}
	}
?>
