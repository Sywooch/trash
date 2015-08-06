<?php
use dfs\docdoc\models\DoctorClinicModel;

require_once dirname(__FILE__)."/../../lib/php/dateconvertionLib.php";
require_once dirname(__FILE__)."/../../doctor/php/doctorLib.php";
require_once dirname(__FILE__)."/../../lib/php/rating.php";

function getRequestListXML ($params=array(), $cityId = 1) {
	$xml = "";
	$sqlAdd = " t1.id_city = ".$cityId."  ";
	$sqlSort = " ORDER BY st, t1.req_created DESC, t1.req_id";
	$startPage = 1;
	$step = 50;
	$withPager = true;
	

	if	( isset($params['withPager']) )  {
		$withPager = $params['withPager'];
	}
			
	if	( isset($params['status']) && $params['status'] != '' )  {
		$sqlAdd .= " AND t1.req_status = '".$params['status']."' ";
	} else {
		$sqlAdd .= " AND t1.req_status <> '4' ";
	}
		
	if (count($params) > 0) {

			
		if	( isset($params['type']) && $params['type'] != '')  {
			$sqlAdd .= " AND t1.req_type = ".$params['type']." ";
		}
		if	( isset($params['shOwner']) && $params['shOwner'] > 0)  {
			$sqlAdd .= " AND t1.req_user_id = '".$params['shOwner']."' ";
		}
		if	( isset($params['clinicId']) && $params['clinicId'] > 0)  {
			$sqlAdd .= " AND t1.clinic_id = '".$params['clinicId']."' ";
		}
		if	( isset($params['clinic']) )  {
			if ($params['clinic'] >= 0  ) {
				if ( isset($params['branch']) && intval ($params['branch']) == 1  )  {
					$subSQL = "SELECT id FROM clinic WHERE id= ".$params['clinic']." OR parent_clinic_id=".$params['clinic'];
					$sqlAdd .= " AND t1.clinic_id IN (".$subSQL.") ";
				} else {
					$sqlAdd .= " AND t1.clinic_id = ".$params['clinic']." ";
				}
				//$sqlAdd .= " AND t1.clinic_id = ".intval($params['clinic'])." " ;
			} else  
				$sqlAdd .= " AND t1.clinic_id is null " ;
		}
		
		if	( isset($params['branch']) && $params['branch'] == '1' )  {
			
			
		}
		
		/*	Дата создания заявки	*/
		if	( isset($params['crDateFrom']) && !empty ($params['crDateFrom'])  )  {
			$sqlAdd .= " AND t1.req_created >= ".strtotime(convertDate2DBformat($params['crDateFrom']))." " ;
		}
		if	( isset($params['crDateTill']) && !empty ($params['crDateTill'])  )  {
			$sqlAdd .= " AND t1.req_created <= ".(strtotime(convertDate2DBformat($params['crDateTill']))+86400)." " ;
		}
		/*	Дата приёма	*/
		if	( isset($params['dateReciveFrom']) && !empty ($params['dateReciveFrom'])  )  {
			$sqlAdd .= " AND t1.date_admission >= ".strtotime(convertDate2DBformat($params['dateReciveFrom']))." AND t1.date_admission IS NOT NULL " ;
		}
		if	( isset($params['dateReciveTill']) && !empty ($params['dateReciveTill'])  )  {
			$sqlAdd .= " AND t1.date_admission <= ".(strtotime(convertDate2DBformat($params['dateReciveTill']))+86400)." " ;
		}
		if	( isset($params['shDoctorId']) && !empty ($params['shDoctorId'])  )  {
			$sqlAdd .= " AND t1.doctor_id = ".$params['shDoctorId']." ";
		}
		/*	Специализация	*/
		if	( isset($params['shSector']) && !empty ($params['shSector'])  )  {
			$sqlAdd .= " AND t1.req_sector_id = ".$params['shSector']." ";
		}
		/*	Клиент	*/
		if	( isset($params['client']) && !empty ($params['client'])  )  {
			$sqlAdd .= " AND LOWER(t1.client_name) LIKE  '%".strtolower($params['client'])."%' ";
		}
			
		/*	Телефон	*/
		if	( isset($params['phone']) && !empty ($params['phone'])  )  {
			$phone = ereg_replace("[^0-9]",'',$params['phone']);
			$sqlAdd .= " AND t1.client_phone  LIKE  '%" . $phone . "%' ";
		}
			
		if	( isset($params['id']) && !empty ($params['id'])  )  {
			$sqlAdd = " t1.req_id = '".$params['id']."'";
		}
			
		if ( isset($params['sortBy']) )  {
			switch ($params['sortBy']) {
				case 'crDate'	: $sortBy= " t1.req_created";break;
				case 'status'	: $sortBy= " st";break;
				case 'call_later'	: $sortBy= " call_later_time";break;
				case 'admDate'		: $sortBy= " t1.date_admission ";break;
				case 'id'		: $sortBy= " t1.req_id ";break;
				default:break;
			}
			if (isset($params['sortType']) && $params['sortType'] == 'asc')  {
				$sqlSort = " order by ".$sortBy." asc";
			} else {
				$sqlSort = " order by ".$sortBy." desc";
			}
		}
			
	}


	$sql = "SELECT
					t1.req_id as id, 
					t1.clinic_id,
					t1.client_name, t1.client_phone,
					t1.req_created, t1.req_status as status, t1.req_type, 
					t1.clientId, t1.call_later_time,
					t1.req_doctor_id as doctor_id, t2.name as doctor, t1.req_sector_id,
					t1.req_user_id as owner, t3.user_lname, t3.user_fname, t3.user_email,
					t4.name as sector,
					t1.date_admission, t1.appointment_status,
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
					END AS st 
				FROM request  t1
				LEFT JOIN doctor t2 ON (t2.id = t1.req_doctor_id)
				LEFT JOIN `user` t3 ON (t3.user_id = t1.req_user_id)
				LEFT JOIN sector t4 ON (t4.id = t1.req_sector_id)
				WHERE ".$sqlAdd.$sqlSort;


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
			$xml .= "<CrDate>".date("d.m.y",$row -> req_created )."</CrDate>";
			$xml .= "<CrTime>".date("H:i",$row -> req_created )."</CrTime>";
			$xml .= "<Client id=\"".$row -> clientId."\"><![CDATA[".$row -> client_name."]]></Client>";
			$xml .= "<ClientPhone>".$row -> client_phone."</ClientPhone>";
			$xml .= "<ClinicId>".$row -> clinic_id."</ClinicId>";

			$xml .= "<AppointmentStatus>".$row -> appointment_status."</AppointmentStatus>";
			if ( !empty($row -> date_admission) ) {
				$xml .= "<AppointmentDate>".date("d.m.y",$row -> date_admission )."</AppointmentDate>";
				$xml .= "<AppointmentTime>".date("H:i",$row -> date_admission )."</AppointmentTime>";
			}
			if ( !empty($row -> call_later_time) ) {
				$xml .= "<CallLaterDate>".date("d.m.y",$row -> call_later_time )."</CallLaterDate>";
				$xml .= "<CallLaterTime>".date("H:i",$row -> call_later_time )."</CallLaterTime>";
				$xml .= "<RemainTime>".(mktime() - $row -> call_later_time)."</RemainTime>";
			}
			$xml .= "<Owner id=\"".$row -> owner."\">".$row -> user_lname." ".$row -> user_fname."</Owner>";
			$xml .= "<Status>".$row -> status."</Status>";
			$xml .= "<Type>".$row -> req_type."</Type>";
			//$xml .= getLastCommentXML($row -> id);
			$xml .= getCommentListXML($row -> id);
			$xml .= "</Element>";
		}
		$xml .= "</RequestList>";
	}
	return $xml;
}




function getRequestByIdXML ( $id = 0 ) {
	$xml = "";

	$id = intval ($id);

	if ( $id > 0 ) {
		$sql = "SELECT
					t1.req_id as id, 
					t1.clinic_id, 
					t1.client_name, t1.client_phone,
					t1.req_created, t1.req_status as status, t1.req_type, t1.req_sector_id, 
					t1.clientId, t1.call_later_time,t1.req_departure as isGoHome,
					t1.req_doctor_id as doctor_id, t2.name as doctor, t1.req_sector_id, 
					t1.req_user_id as owner, t3.user_lname, t3.user_fname, t3.user_email,
					t4.name as sector,
					t1.date_admission, t1.appointment_status, t2.status as doctorStatus, t1.is_transfer, 
					cl.id as clinicId, cl.name as clinic, 
					t1.client_comments, t1.age_selector,  t1.id_city
				FROM request  t1
				LEFT JOIN doctor t2 ON (t2.id = t1.req_doctor_id)
				LEFT JOIN `user` t3 ON (t3.user_id = t1.req_user_id)
				LEFT JOIN `clinic` cl ON (cl.id = t1.clinic_id)
				LEFT JOIN sector t4 ON (t4.id = t1.req_sector_id)
				WHERE 
					req_id = $id";
		//echo $sql;
		$result = query($sql);
		if (num_rows($result) == 1) {
			$row = fetch_object($result);
			$xml .= "<Request id=\"".$row -> id."\">";
			$xml .= "<CityId>".$row -> id_city."</CityId>";
			$xml .= "<Doctor  id=\"".$row -> doctor_id."\" status=\"".$row -> doctorStatus."\">".$row -> doctor."</Doctor>";
			$xml .= "<Sector  id=\"".$row -> req_sector_id."\">".$row -> sector."</Sector>";
			$xml .= "<Clinic  id=\"".$row -> clinic_id."\">".$row -> clinic."</Clinic>";
			$xml .= getClinicXML($row -> doctor_id);
			$xml .= "<CrDate>".date("d.m.Y",$row -> req_created )."</CrDate>";
			$xml .= "<CrTime>".date("H:i",$row -> req_created )."</CrTime>";
			$xml .= "<Client id=\"".$row -> clientId."\"><![CDATA[".$row -> client_name."]]></Client>";
			$xml .= "<ClientPhone phoneNum=\"".formatPhone4DB($row -> client_phone)."\">".formatPhone($row -> client_phone)."</ClientPhone>";
			$xml .= "<IsGoHome>".$row -> isGoHome."</IsGoHome>";
			$xml .= "<AgeSelector>".$row -> age_selector."</AgeSelector>";

			$xml .= "<AppointmentStatus>".$row -> appointment_status."</AppointmentStatus>";
			if ( !empty($row -> date_admission) ) {
				$xml .= "<AppointmentDate>".date("d.m.Y",$row -> date_admission )."</AppointmentDate>";
				$xml .= "<AppointmentTime Hour=\"".date("H",$row -> date_admission)."\" Min=\"".date("i",$row -> date_admission )."\">".date("H:i",$row -> date_admission )."</AppointmentTime>";
			}
			if ( !empty($row -> call_later_time) ) {
				$xml .= "<CallLaterDate>".date("d.m.Y",$row -> call_later_time )."</CallLaterDate>";
				$xml .= "<CallLaterTime Hour=\"".date("H",$row -> call_later_time)."\" Min=\"".date("i",$row -> call_later_time )."\">".date("H:i",$row -> call_later_time )."</CallLaterTime>";
				$xml .= "<RemainTime>".(mktime() - $row -> call_later_time)."</RemainTime>";
			}
			$xml .= "<Owner id=\"".$row -> owner."\">".$row -> user_lname." ".$row -> user_fname."</Owner>";
			$xml .= "<Status>".$row -> status."</Status>";
			$xml .= "<SectorId>".$row -> req_sector_id."</SectorId>";
			$xml .= "<IsTransfer>".$row -> is_transfer."</IsTransfer>";
			$xml .= "<Type>".$row -> req_type."</Type>";
			$xml .= "<ClientComment><![CDATA[".$row -> client_comments."]]></ClientComment>";

			//$xml .= getLastCommentXML($row -> id);
			$xml .= getCommentListXML($row -> id);
			$xml .= getMetroList4requestXML($row -> id);
			$xml .= getAudio4RequestXML($row -> id);
			$xml .= getAnotherClinicXML($row -> clinic_id);
			$xml .= "</Request>";
		}
	}

	return $xml;
}



function getLastCommentXML ( $id = 0 ) {
	$xml = "";

	$id = intval ($id);

	if ( $id > 0 ) {
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
			$xml .= "<LastComment id=\"".$row -> id."\">";
			$xml .= "<Text><![CDATA[".$row -> text."]]></Text>";
			$xml .= "<CrDate>".$row ->crDate."</CrDate>";
			$xml .= "<Owner id=\"".$row -> user_id."\">".$row -> user_lname."</Owner>";
			$xml .= "</LastComment>";
		}
	}

	return $xml;
}



function getCommentListXML ( $id = 0 ) {
	$xml = "";

	$id = intval ($id);

	if ( $id > 0 ) {
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
		if (num_rows($result)  > 0) {
			$xml .= "<CommentList>";
			while ($row = fetch_object($result)) {
				$xml .= "<Element id=\"".$row -> id."\">";
				$xml .= "<Text><![CDATA[".$row -> text."]]></Text>";
				$xml .= "<CrDate>".$row ->crDate."</CrDate>";
				$xml .= "<CrTime>".$row ->crTime."</CrTime>";
				$xml .= "<Type>".$row -> action."</Type>";
				$xml .= "<Owner id=\"".$row -> user_id."\">".$row -> user_lname." ".$row -> user_fname."</Owner>";
				$xml .= "</Element>";
			}
			$xml .= "</CommentList>";
		}
	}

	return $xml;
}











function getType4RequestXML () {
	$xml = "";

	$xml .= "<TypeDict>";
	$xml .= "<Element id=\"0\">Запись к врачу</Element>";
	$xml .= "<Element id=\"1\">Подбор врача</Element>";
	$xml .= "<Element id=\"2\">Телефонное обращение</Element>";
	$xml .= "<Element id=\"3\" display=\"no\">Партнерка</Element>";
	$xml .= "<Element id=\"4\" display=\"no\">Mail</Element>";
	$xml .= "</TypeDict>";

	return $xml;
}



function getStatus4RequestXML () {
	$xml = "";

	$xml .= "<StatusDict>";
	$xml .= "<Element id=\"0\">Новая</Element>";
	$xml .= "<Element id=\"6\">Принята</Element>";
	$xml .= "<Element id=\"1\">В обработке</Element>";
	$xml .= "<Element id=\"2\">Обработана</Element>";
	$xml .= "<Element id=\"3\">Завершена</Element>";
	$xml .= "<Element id=\"5\">Отказ</Element>";
	$xml .= "<Element id=\"7\">Перезвонить</Element>";
	$xml .= "<Element id=\"4\" display=\"no\">Удалена</Element>";
	$xml .= "</StatusDict>";

	return $xml;
}

function getStatusArray () {
	$status = array();

	$status[0] = "Новая";
	$status[1] = "В обработке";
	$status[2] = "Обработана";
	$status[3] = "Завершена";
	$status[5] = "Отказ";
	$status[6] = "Принята";
	$status[7] = "Перезвонить";
	$status[4] = "Удалена";

	return $status;
}




function getAction4RequestHistoryXML () {
	$xml = "";

	$xml .= "<ActionDict>";
	$xml .= "<Element id=\"1\">Изменение статуса</Element>";
	$xml .= "<Element id=\"2\">Добавление комментария</Element>";
	$xml .= "<Element id=\"3\">Изменения в заявке</Element>";
	$xml .= "<Element id=\"4\">Звонок совершен</Element>";
	$xml .= "</ActionDict>";

	return $xml;
}


function getAudio4RequestXML ( $id ) {
	$xml = "";

	$id = intval ($id);

	if ( $id > 0 ) {
		$sql = "SELECT
						t1.request_id as id, t1.record, 
						DATE_FORMAT( t1.crDate,'%d.%m.%Y') AS crDate,
						DATE_FORMAT( t1.crDate,'%d.%m.%Y %H.%i') AS crDateTime,
						t1.duration, t1.comments as note,
						t1.isOpinion
					FROM request_record t1
					WHERE 
						t1.request_id = ".$id."
					ORDER BY record";
		//echo $sql."<br/>";
		$result = query($sql);
		if (num_rows($result) > 0 ) {
			$xml .= "<RecordList>";
			while ($row = fetch_object($result)) {
				$xml .= "<Record id =\"".$row -> id."\">";
				$xml .= "<Path>".$row -> record."</Path>";
				if ( $row -> isOpinion =='yes' )
					$xml .= "<IsOpinion>yes</IsOpinion>";
				$xml .= "<Duration>".$row -> duration."</Duration>";
				$xml .= "<Note>".$row -> note."</Note>";
				$xml .= "<CrDate>".$row -> crDate."</CrDate>";
				$xml .= "<CrDateTime>".$row -> crDateTime."</CrDateTime>";
				$xml .= "</Record>";
			}
			$xml .= "</RecordList>";
		}
	}

	return $xml;

}



function getMetroList4requestXML ( $id ) {
	$xml = "";

	$id = intval ($id);

	if ( $id > 0 ) {
		$sql = "SELECT
						t1.station_id as id,
						t2.name, t2.underground_line_id
					FROM request_station t1
					LEFT JOIN underground_station t2 ON (t2.id = t1.station_id)
					WHERE 
						t1.request_id = ".$id."
					ORDER BY underground_line_id, id";
		//echo $sql."<br/>";
		$result = query($sql);
		if (num_rows($result) > 0 ) {
			$xml .= "<MetroList>";
			while ($row = fetch_object($result)) {
				$xml .= "<Metro id=\"".$row -> id."\" line=\"".$row -> underground_line_id."\">".$row -> name."</Metro>";
			}
			$xml .= "</MetroList>";
		}
	}

	return $xml;

}



function getOperatorListXML (  ) {
	$xml = "";

	$sql = "SELECT
					t1.user_id as id, 
					t1.user_fname, t1.user_lname, t1.user_email, t1.user_status
				FROM `user` t1, right_4_user t2
				WHERE 
					t1.user_id = t2.user_id
					AND
					(t2.right_id = 2 OR t2.right_id = 3)
				GROUP BY t1.user_id 
				ORDER BY t1.user_lname";
	//echo $sql."<br/>";
	$result = query($sql);
	if (num_rows($result) > 0 ) {
		$xml .= "<OperatorList>";
		while ($row = fetch_object($result)) {
			$xml .= "<Element id=\"".$row ->id."\">";
			$xml .= "<LName>".$row -> user_lname."</LName>";
			$xml .= "<FName>".$row -> user_fname."</FName>";
			$xml .= "<Status>".$row -> user_status."</Status>";
			$xml .= "</Element>";
		}
		$xml .= "</OperatorList>";
	}


	return $xml;

}




function getSectorListXML (  ) {
	$xml = "";

	$sql = "SELECT
					t1.id as id, 
					t1.name as title
				FROM `sector` t1
				ORDER BY t1.name";
	//echo $sql."<br/>";
	$result = query($sql);
	if (num_rows($result) > 0 ) {
		$xml .= "<SectorList>";
		while ($row = fetch_object($result)) {
			$xml .= "<Element id=\"".$row ->id."\">".$row -> title."</Element>";
		}
		$xml .= "</SectorList>";
	}


	return $xml;

}






function getDoctorList4requestXML ($params=array(), $cityId = 1) {
	$xml = "";
	$sqlAdd = " t2.city_id = ".$cityId." ";
	$addJoin = "";
	$startPage = 1;
	$step = 100;

	/*	Только активные и добавленныке */
	$sqlAdd = " (t1.status = 3 OR t1.status = 7) AND t2.city_id = ".$cityId." ";

	if (count($params) > 0) {

		if	( isset($params['name']) && !empty ($params['name'])  )  {
			$sqlAdd .= " AND LOWER(t1.name) LIKE  '%".strtolower($params['name'])."%' ";
		}
		/*if	( isset($params['status']) && !empty ($params['status'])  )  {
		 $sqlAdd .= " AND t1.status = ".$params['status']." ";
			}*/
			
		if	( isset($params['departure']) && intval ($params['departure']) == 1  )  {
			$sqlAdd .= " AND t1.departure = 1 ";
		}
		if	( isset($params['sector']) && intval ($params['sector']) > 0  )  {
			$sqlAdd .= " AND t3.sector_id = ".$params['sector']." ";
			$addJoin .= " INNER JOIN doctor_sector t3 ON (t3.doctor_id = t1.id) ";
		}
			
		$idDocList = "";
		$sqlAddFix = "";
		if	( isset($params['doctorList']) && count($params['doctorList']) > 0  )  {
			$idList = ""; $i =0;
			foreach ( $params['doctorList'] as $key => $data ) {
				$idList .= $data;
				if ( $i != (count($params['doctorList']) - 1) ) { $idList .= ", ";}
				$i++;
			}
			$idDocList = $idList;
			if ( !empty($idList) ) {
				$sqlAddFix .= " AND t1.id NOT IN (".$idList.")";
			}

		}
			
		if	( isset($params['metroList']) && count($params['metroList']) > 0  )  {
			$idList = "";
			for ($i=0; $i < count($params['metroList']); $i++) {
				$idList .= $params['metroList'][$i];
				if ( $i != (count($params['metroList']) - 1) ) { $idList .= ", ";}
			}

			$addJoin .= " 	INNER JOIN doctor_4_clinic d4c ON (d4c.doctor_id = t1.id and d4c.type = " . DoctorClinicModel::TYPE_DOCTOR . ")
								INNER JOIN underground_station_4_clinic us4c ON (us4c.clinic_id = d4c.clinic_id)
								INNER JOIN underground_station us ON (us.id = us4c.undegraund_station_id)
							";
			$sqlAdd .= " AND us.id in (".$idList.")";
		}
			
		if	( isset($params['phoneExt']) && intval ($params['phoneExt']) > 0  )  {
			$sqlAdd = " t1.addNumber = ".$params['phoneExt']." ";
		}
		if	( isset($params['id']) && !empty ($params['id'])  )  {
			$sqlAdd = " t1.id = '".$params['id']."'";
		}
	}

	$sqlUnion4DocFix  = "";
	if ( !empty($idDocList) ) {
		$sqlUnion4DocFix = "(SELECT
						t1.id,  t1.name as FullName, t1.status, t1.phone, t1.image,  t1.rewrite_name as alias,
						t1.total_rating, t1.rating, t1.rating_opinion, 
						t1.email, t1.sex, t1.price,
						DATE_FORMAT( t1.created,'%d.%m.%Y') AS crDate,
						t1.note, t1.openNote, t1.addNumber, t1.experience_year,
						t2.name as Clinic, t2.id as clinicId,
						concat(t2.street, ', ', t2.house) as clinicAddress,
						CASE WHEN t1.rating = 0 THEN t1.total_rating ELSE t1.rating END AS sortRating
					FROM doctor_4_clinic d4cl, clinic t2, doctor  t1
					WHERE
						t1.id = d4cl.doctor_id 
						AND
						d4cl.type = " . DoctorClinicModel::TYPE_DOCTOR . "
						AND
						t2.id = d4cl.clinic_id 
						AND 
						t1.Id IN (".$idDocList."))
					UNION ";
	}

	$sql = "(SELECT
					t1.id,  t1.name as FullName, t1.status, t1.phone, t1.image, t1.rewrite_name as alias,
					t1.total_rating, t1.rating, t1.rating_opinion, 
					t1.email, t1.sex, t1.price,
					DATE_FORMAT( t1.created,'%d.%m.%Y') AS crDate,
					t1.note, t1.openNote, t1.addNumber, t1.experience_year,
					t2.name as Clinic, t2.id as clinicId,
					concat(t2.street, ', ', t2.house) as clinicAddress,
					CASE WHEN t1.rating = 0 THEN t1.total_rating ELSE t1.rating END AS sortRating
				FROM doctor_4_clinic d4cl, clinic t2, doctor  t1
				".$addJoin."
				WHERE
					t1.id = d4cl.doctor_id
					AND
					d4cl.type = " . DoctorClinicModel::TYPE_DOCTOR . "
					AND  
					t2.id = d4cl.clinic_id 
					AND ".$sqlAdd.$sqlAddFix. "
				ORDER BY sortRating DESC, t1.id
				LIMIT 100)";

	//echo $sqlUnion4DocFix.$sql;
	$result = query($sqlUnion4DocFix.$sql);
	if (num_rows($result) > 0) {
		$xml .= "<DoctorList>";
		while ($row = fetch_object($result)) {
			$xml .= "<Element id=\"".$row -> id."\">";
			$xml .= "<CrDate>".$row -> crDate."</CrDate>";
			$xml .= "<Name>".$row -> FullName."</Name>";
			$xml .= "<Alias>".$row -> alias."</Alias>";
			$xml .= "<Rating rating=\"".$row -> rating."\" total=\"".$row -> total_rating."\">".round($row -> sortRating,2)."</Rating>";
			$xml .= "<Price>".$row -> price."</Price>";
			$xml .= "<Phone>".$row -> phone."</Phone>";
			$xml .= "<AddNumber>".$row -> addNumber."</AddNumber>";
			$xml .= "<Email>".$row -> email."</Email>";
			$xml .= "<Sex>".$row -> sex."</Sex>";
			$xml .= "<Experience startPractice=\"".$row -> experience_year."\">".(date("Y") - $row -> experience_year)."</Experience>";
			$xml .= "<Status>".$row -> status."</Status>";
			$xml .= "<IMG>".$row -> image."</IMG>";
			$xml .= "<Clinic id=\"".$row -> clinicId."\">".$row -> Clinic."</Clinic>";
			$xml .= "<ClinicAddress>".$row -> clinicAddress."</ClinicAddress>";
			$xml .= getPhones4ClinicXML($row -> clinicId);
			$xml .= getSectorByDoctorIdXML ($row -> id);
			//$xml .= getMetroByDoctorIdXML ($row -> id);
			$xml .= getMetroByClinicIdXML ($row -> clinicId);
			$xml .= "<Opinion>".getOpinionCountByDoctorId ($row -> id)."</Opinion>";
			$xml .= "<OperatorComment><![CDATA[".$row -> note."]]></OperatorComment>";
			$xml .= "<OperatorOpenComment><![CDATA[".$row -> openNote."]]></OperatorOpenComment>";
			$xml .= "</Element>";
		}
		$xml .= "</DoctorList>";
	}
	return $xml;
}





function getMetroByDoctorIdXML ( $id ) {
	$xml = "";

	$id = intval ($id);

	if ( $id > 0 ) {
		$sql = "SELECT
						t1.id as id, t1.name as station
					FROM 	underground_station t1, 
							underground_station_4_clinic t2,
							doctor_4_clinic t3
					WHERE 
						t1.id = t2.undegraund_station_id
						AND
						t3.type = " . DoctorClinicModel::TYPE_DOCTOR . "
						AND
						t2.clinic_id =  t3.clinic_id
						AND
						t3.doctor_id = ".$id."
					GROUP BY t1.id
					ORDER BY station";
		//echo $sql."<br/>";
		$result = query($sql);
		if (num_rows($result) > 0 ) {
			$xml .= "<StationList>";
			while ($row = fetch_object($result)) {
				$xml .= "<Element id=\"".$row -> id."\">".$row -> station."</Element>";
			}
			$xml .= "</StationList>";
		}
	}

	return $xml;

}





function getMetroByClinicIdXML ( $id ) {
	$xml = "";

	$id = intval ($id);

	if ( $id > 0 ) {
		$sql = "SELECT
						t1.id as id, t1.name as station
					FROM 	underground_station t1, 
							underground_station_4_clinic t2
					WHERE 
						t1.id = t2.undegraund_station_id
						AND
						t2.clinic_id =  ".$id."
					GROUP BY t1.id
					ORDER BY station";
		//echo $sql."<br/>";
		$result = query($sql);
		if (num_rows($result) > 0 ) {
			$xml .= "<StationList>";
			while ($row = fetch_object($result)) {
				$xml .= "<Element id=\"".$row -> id."\">".$row -> station."</Element>";
			}
			$xml .= "</StationList>";
		}
	}

	return $xml;

}



// список клиник для врача
function getClinicXML( $id ) {
	$xml = "";

	$id = intval ($id);

	if ( $id > 0 ) {
		$sql = "SELECT
						cl.id as id, cl.name
					FROM 	clinic cl, doctor_4_clinic d4c
					WHERE 
						cl.id = d4c.clinic_id
						AND
						d4c.type = " . DoctorClinicModel::TYPE_DOCTOR . "
						AND
						d4c.doctor_id = ".$id."
					ORDER BY cl.name";
		//echo $sql."<br/>";
		$result = query($sql);
		if (num_rows($result) > 0 ) {
			$xml .= "<ClinicList>";
			while ($row = fetch_object($result)) {
				$xml .= "<Element id=\"".$row -> id."\">".$row -> name."</Element>";
			}
			$xml .= "</ClinicList>";
		}
	}

	return $xml;
}



function getAnotherClinicXML( $id ) {
	$xml = "";

	$id = intval ($id);

	if ( $id > 0 ) {
		$sql="	SELECT 
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
		if (num_rows($result) > 0 ) {
			$xml .= "<AnotherClinicList>";
			while ($row = fetch_object($result)) {
				$xml .= "<Element id=\"".$row -> id."\">";
				$xml .= "<Clinic>".$row -> name."</Clinic>";
				$xml .= "<Address>".$row -> address."</Address>";
				$xml .= "</Element>";
			}
			$xml .= "</AnotherClinicList>";
		}
	}

	return $xml;

}



//	телефоны для клиник
function getPhones4ClinicXML ( $id ) {
	$xml = "";

	$id = intval ($id);

	$sql = "SELECT
					t1.phone_id as id, t1.number_p, t1.label
				FROM clinic_phone t1
				WHERE 
					t1.clinic_id=$id";
	$result = query($sql);
	if (num_rows($result) > 0) {
		$xml .= "<PhoneList>";
		while ($row = fetch_object($result)) {
			$xml .= "<Element id=\"".$row -> id."\">";
			$xml .= "<Phone>".$row -> number_p."</Phone>";
			$xml .= "<PhoneFormat>".formatPhone($row -> number_p)."</PhoneFormat>";
			$xml .= "<Label>".$row -> label."</Label>";
			$xml .= "</Element>";
		}
		$xml .= "</PhoneList>";
	}

	return $xml;
}



// Список отзывов 
function getOpinionListByRequestIdXML ( $id = 0 ) {
	$xml = "";
			
	$id = intval ($id);
	
	if ( $id > 0 ) {
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
				$xml .= "<Opinion id=\"".$row -> id."\"/>";
			$xml .= "</OpinionList>";
		}
	}
	
	return $xml;
}
?>
