<?php
use dfs\docdoc\models\DistrictModel;
use dfs\docdoc\models\DoctorClinicModel;

	require_once dirname(__FILE__)."/../../lib/php/validate.php";

	function getClinicListXML ($params=array(), $parentId = 0, $cityId = 1) {
		$xml = "";
		$sqlAdd = " t1.city_id = ".$cityId." ";
		$sqlAdd2 = " ";
		$startPage = 1;
		$step = 50;

		if (is_null($parentId)) {
			//не указываю парента в запросе
		} elseif ($parentId == 0) {
			$sqlAdd2 .= " AND t1.parent_clinic_id = 0 ";
		} else {
			$sqlAdd2 .= " AND t1.parent_clinic_id = " . $parentId;
		}
			
		if (count($params) > 0) {

			if	( isset($params['title']) && !empty ($params['title'])  )  {
				$sqlAdd .= " AND ( LOWER(t1.name) LIKE  '%".strtolower($params['title'])."%' OR LOWER(t1.short_name) LIKE  '%".strtolower($params['title'])."%' ) ";
			}
			if	( isset($params['alias']) && !empty ($params['alias'])  )  {
				$sqlAdd .= " AND LOWER(t1.rewrite_name) LIKE  '%".strtolower($params['alias'])."%' ";
			}
			if	( isset($params['status']) && !empty ($params['status'])  )  {
				$sqlAdd .= " AND t1.status = ".$params['status']." ";
			}
			if	( isset($params['type']) && !empty ($params['type'])  )  {
				switch ($params['type']) {
					case 'clinic' : $sqlAdd .= " AND t1.isClinic = 'yes' "; break;
					case 'center' : $sqlAdd .= " AND t1.isDiagnostic = 'yes' "; break;
					case 'privatDoctor' : $sqlAdd .= " AND t1.isPrivatDoctor = 'yes' "; break;
				}
			}
			/*
			if	( !isset($params['branch']) || empty ($params['branch'])  )  {
				$sqlAdd .= " AND t1.parent_clinic_id = 0 ";
			}
			*/
			
			
			if	( isset($params['metroList']) && !empty($params['metroList']) )  {
				$sqlAdd .= " AND t1.id in ( SELECT t6.clinic_id FROM underground_station_4_clinic t6, underground_station t7 WHERE t6.undegraund_station_id = t7.id AND LOWER(t7.name) LIKE '%".strtolower($params['metroList'])."%' GROUP BY t6.clinic_id  ) ";
			}
			
			if	( isset($params['id']) && !empty ($params['id'])  )  {
				$sqlAdd = " t1.id = '".$params['id']."' AND t1.city_id = ".$cityId." ";
			}
		}

		$extra = $parentId == 0 && count($params) > 0 && !isset($params['id']);

		$sqlSelect = '';
		$sqlJoin = '';

		if (isset($params['moderation']) && !empty ($params['moderation'])) {
			$sqlJoin .= 'INNER JOIN diagnostica4clinic as d4c ON (d4c.clinic_id = ' . ($extra ? 'grResult' : 't1') . '.id) ';
			$sqlJoin .= 'INNER JOIN moderation m ON (m.entity_id = d4c.id AND m.entity_class = "DiagnosticClinicModel")	';
			$sqlSelect = ', m.id as moderation_id';
		}

		if ($extra) {
			$sql = "SELECT
						distinct(grResult.id), grResult.parent_clinic_id, grResult.name, grResult.short_name, grResult.rewrite_name, grResult.status, grResult.phone, grResult.phone_appointment, grResult.asterisk_phone, grResult.url, grResult.rating, grResult.email, grResult.contact_name,
						grResult.age, 
						DATE_FORMAT( grResult.created,'%d.%m.%Y') AS crDate,
						grResult.isDiagnostic, grResult.isClinic, grResult.isPrivatDoctor, grResult.rating_total".$sqlSelect."
					FROM
					(SELECT
						t1.id, t1.parent_clinic_id, t1.name, t1.short_name, t1.rewrite_name, t1.status, t1.phone, t1.phone_appointment, t1.asterisk_phone, t1.url, t1.rating, t1.email, t1.contact_name,
						t1.age_selector as age, 
						t1.created,
						t1.isDiagnostic, t1.isClinic, t1.isPrivatDoctor, t1.rating_total
					FROM clinic  t1
					WHERE ".$sqlAdd. "
						 AND t1.parent_clinic_id = 0
					
					UNION 
					
					SELECT
						t2.id, t2.parent_clinic_id, t2.name, t2.short_name, t2.rewrite_name, t2.status, t2.phone, t2.phone_appointment, t2.asterisk_phone, t2.url, t2.rating, t2.email, t2.contact_name,
						t2.age_selector as age, 
						t2.created,
						t2.isDiagnostic, t2.isClinic, t2.isPrivatDoctor, t2.rating_total
					FROM clinic  t2
					WHERE 
						 t2.id IN (
						 	SELECT
								DISTINCT parent_clinic_id 
							FROM clinic  t5
							WHERE ".str_replace('t1.', 't5.', $sqlAdd)." AND t5.parent_clinic_id <> 0 
						 )
					) AS grResult
						".$sqlJoin."
					GROUP BY grResult.id
					ORDER BY grResult.created DESC, grResult.id
					";
		} else {
				$sql = "SELECT
						t1.id, t1.parent_clinic_id, t1.name, t1.short_name, t1.rewrite_name, t1.status, t1.phone, t1.phone_appointment, t1.asterisk_phone, t1.url, t1.rating, t1.email, t1.contact_name,
						t1.age_selector as age, 
						DATE_FORMAT( t1.created,'%d.%m.%Y') AS crDate,
						t1.isDiagnostic, t1.isClinic, t1.isPrivatDoctor, t1.rating_total".$sqlSelect."
					FROM clinic  t1
						".$sqlJoin."
					WHERE ".$sqlAdd.$sqlAdd2."
					ORDER BY t1.created DESC, t1.id";
		}

		//echo $sql."<br/><br/>";
		if ($parentId == 0 ) {
			if ( isset($params['step']) && intval($params['step']) > 0 ) $step = $params['step'];
			if ( isset($params['startPage']) && intval($params['startPage']) > 0 ) $startPage = $params['startPage'];
		

			list($sql, $str) = pager( $sql, $startPage, $step, "loglist"); // функция берется из файла pager.xsl с тремя параметрами. параметр article тут не нужен
			$xml .= $str;
		}
		//echo $str."<br/>";

		$result = query($sql);
		if (num_rows($result) > 0) {
			$xml .= "<ClinicList>";
			while ($row = fetch_object($result)) {
				$xml .= "<Element id=\"".$row -> id."\">";
				$xml .= "<ParentId>".$row -> parent_clinic_id."</ParentId>";
				$xml .= "<CrDate>".$row -> crDate."</CrDate>";
				$xml .= "<Title><![CDATA[".$row -> name."]]></Title>";
				$xml .= "<ShortName><![CDATA[".$row -> short_name."]]></ShortName>";
				$xml .= "<RewriteName><![CDATA[".$row -> rewrite_name."]]></RewriteName>";
				$xml .= "<URL><![CDATA[".$row -> url."]]></URL>";
				$xml .= "<Rating>".$row -> rating."</Rating>";
				$xml .= "<TotalRating>".$row -> rating_total."</TotalRating>";
				$xml .= "<Phone digit=\"".formatPhone4DB($row -> phone)."\">".formatPhone($row -> phone)."</Phone>";
				$xml .= "<AsteriskPhone digit=\"".formatPhone4DB($row -> asterisk_phone)."\">".formatPhone($row -> asterisk_phone)."</AsteriskPhone>";
				$xml .= "<PhoneAppointment digit=\"".formatPhone4DB($row -> phone_appointment)."\">".formatPhone($row -> phone_appointment)."</PhoneAppointment>";
				$xml .= "<ContactName>".$row -> contact_name."</ContactName>";
				$xml .= "<Email>".$row -> email."</Email>";
				$xml .= "<Age>".$row -> age."</Age>";
				$xml .= "<IsDiagnostic>".$row -> isDiagnostic."</IsDiagnostic>";
				$xml .= "<IsClinic>".$row -> isClinic."</IsClinic>";
				$xml .= "<IsPrivatDoctor>".$row -> isPrivatDoctor."</IsPrivatDoctor>";
				$xml .= "<Status>".$row -> status."</Status>";
				$xml .= getClinicBranchXML($row -> id);
				$xml .= getClinicPhonesXML($row -> id);
				$sqlAdd = "SELECT count(*) as cnt FROM doctor_4_clinic WHERE clinic_id =  " . $row->id . ' and type = ' . DoctorClinicModel::TYPE_DOCTOR;
				$resultAdd = query($sqlAdd);
				if (num_rows($resultAdd) == 1) {
					$rowAdd = fetch_object($resultAdd);
					$xml .= "<DoctorCount>".$rowAdd -> cnt."</DoctorCount>";
 				}

				if ($row->parent_clinic_id == 0) {
					$xml .= getClinicListXML($params, $row->id, $cityId);
				}

				if (isset($row->moderation_id)) {
					$xml .= "<ModerationId>" . $row->moderation_id . "</ModerationId>";
				}

				$xml .= "</Element>";
				
			}
			$xml .= "</ClinicList>";
		}
		return $xml;
	}



	function getStatusDictXML () {
		$xml = "";


		$xml .= "<StatusDict  mode='clinicDict'>";
		$xml .= "<Element id=\"1\">Регистрация</Element>";
		$xml .= "<Element id=\"2\">Новая</Element>";
		$xml .= "<Element id=\"3\">Активная</Element>";
		$xml .= "<Element id=\"4\">Заблокирована</Element>";
		$xml .= "<Element id=\"5\">К удалению</Element>";
		$xml .= "</StatusDict>";
		
		return $xml;
	}
	
	
	
	
	function contractDictXML () {
		$xml = "";
		
		$sql="	SELECT 
					contract_id as id, title
				FROM contract_dict
				ORDER BY title";
		//echo $sql;
	  	$result = query($sql);
		if (num_rows($result) > 0) {
			$xml .= "<ContractDict>";
			while ($row = fetch_object($result)) 
				$xml .= "<Element id=\"".$row -> id."\">".$row -> title."</Element>";
			$xml .= "</ContractDict>";
		}
		
		return $xml;
	}
	
	
	/*	Костыль, копирует функцию из /lib/php/commonDict.php */
	function getContractList ( ) {
		$data = array();
	
		$sql = "SELECT
					t1.contract_id as Id,
					t1.title AS Name,
					t1.isClinic AS IsClinic, 
					t1.isDiagnostic AS IsDiagnostic
				FROM contract_dict t1
				ORDER BY t1.title";
		//echo $sql;
		$result = query($sql);
		if (num_rows($result)  > 0) {
			while ($row = fetch_array($result)) {
				array_push($data, $row);
			}
		}
	
		return $data;
	}

/*
 * Формирование xml для клиники
 * @return string
 */
function getClinicByIdXML($id = 0)
{
	$xml = "";

	$id = intval($id);

	if ($id > 0) {
		$sql = "SELECT
						t1.id, t1.parent_clinic_id,  
						t1.name,  t1.short_name, t1.rewrite_name,
						t1.status, t1.phone, t1.phone_appointment, t1.asterisk_phone, t1.url,
						t1.rating, t1.email, t1.contact_name, 
						DATE_FORMAT( t1.created,'%d.%m.%Y') AS crDate,
						t1.url, t1.longitude, t1.latitude, t1.age_selector as age,
						t1.city, t1.district_id, t1.street, t1.house, t1.description, t1.shortDescription, t1.operator_comment,
						t1.isDiagnostic, t1.isClinic, t1.isPrivatDoctor,
						t1.sort4commerce as sortPosition, t1.logoPath,
						t1.weekdays_open, t1.weekend_open, t1.saturday_open, t1.sunday_open,
						t1.show_in_advert,
						t1.notify_emails,
						t1.notify_phones,
						t1.scheduleForDoctors,
						t1.email_reconciliation,
						t1.discount_online_diag
					FROM clinic  t1
					WHERE
						t1.id = $id";

		$result = query($sql);
		if (num_rows($result) == 1) {
			$row = fetch_object($result);
			$xml .= "<Clinic id=\"" . $row->id . "\">";
			$xml .= "<CrDate>" . $row->crDate . "</CrDate>";
			$xml .= "<ParentClinicId>" . $row->parent_clinic_id . "</ParentClinicId>";
			$xml .= "<Title><![CDATA[" . $row->name . "]]></Title>";
			$xml .= "<ShortName><![CDATA[" . $row->short_name . "]]></ShortName>";
			$xml .= "<RewriteName><![CDATA[" . $row->rewrite_name . "]]></RewriteName>";
			$xml .= "<URL><![CDATA[" . $row->url . "]]></URL>";
			$xml .= "<Rating>" . $row->rating . "</Rating>";
			$xml .= "<Phone>" . $row->phone . "</Phone>";
			$xml .= "<AsteriskPhone>" . formatPhone($row->asterisk_phone) . "</AsteriskPhone>";
			$xml .= "<PhoneAppointment>" . $row->phone_appointment . "</PhoneAppointment>";
			$xml .= "<ContactName>" . $row->contact_name . "</ContactName>";
			$xml .= "<Description><![CDATA[" . $row->description . "]]></Description>";
			$xml .= "<ShortDescription><![CDATA[" . $row->shortDescription . "]]></ShortDescription>";
			$xml .= "<OperatorComment><![CDATA[" . $row->operator_comment . "]]></OperatorComment>";
			$xml .= "<Email>" . $row->email . "</Email>";
			$xml .= "<Status>" . $row->status . "</Status>";
			$xml .= "<Longitude>" . $row->longitude . "</Longitude>";
			$xml .= "<Latitude>" . $row->latitude . "</Latitude>";
			$xml .= "<Age>" . $row->age . "</Age>";
			$xml .= "<IsDiagnostic>" . $row->isDiagnostic . "</IsDiagnostic>";
			$xml .= "<IsClinic>" . $row->isClinic . "</IsClinic>";
			$xml .= "<IsPrivatDoctor>" . $row->isPrivatDoctor . "</IsPrivatDoctor>";
			$xml .= "<City>" . $row->city . "</City>";
			$xml .= "<DistrictId>" . $row->district_id . "</DistrictId>";
			$xml .= "<House>" . $row->house . "</House>";
			$xml .= "<Street>" . $row->street . "</Street>";
			$xml .= "<SortPosition>" . $row->sortPosition . "</SortPosition>";
			$xml .= "<DiscountOnlineDiag>{$row->discount_online_diag}</DiscountOnlineDiag>";
			$xml .= "<LogoPath>" . $row->logoPath . "</LogoPath>";
			if (!empty($row->logoPath)) {
				$xml .= "<FullLogoPath>http://" . SERVER_FRONT . "/upload/kliniki/logo/{$row->logoPath}</FullLogoPath>";
			}
			$xml .= "<ShowInAdvertising>" . $row->show_in_advert . "</ShowInAdvertising>";
			$xml .= "<WorkTime>";
			$xml .= "<WeekDays>" . $row->weekdays_open . "</WeekDays>";
			$xml .= "<WeekEnd>" . $row->weekend_open . "</WeekEnd>";
			$xml .= "<Saturday>" . $row->saturday_open . "</Saturday>";
			$xml .= "<Sunday>" . $row->sunday_open . "</Sunday>";
			$xml .= "</WorkTime>";
			//$xml .= "<OldAddress>".$row -> oldAddress."</OldAddress>";
			$xml .= getClinicOldBranchXML($row->id);
			$xml .= getClinicBranchXML($row->id);
			$xml .= getClinicMetroListXML($row->id);
			$xml .= getClinicAdminListXML($row->id);
			$xml .= getClinicPhonesXML($row->id);
			if ($row->isDiagnostic == 'yes') {
				$xml .= getDiagnostic4Clinic($row->id);
			}

			if($row->notify_emails){
				$xml .= "<NotifyEmails>";
				$notifyEmails = explode(',', $row->notify_emails);
				foreach($notifyEmails as $email){
					$xml .= "<Element>";
					$xml .= $email;
					$xml .= "</Element>";
				}
				$xml .= "</NotifyEmails>";
			}

			//notify_phones
			if($row->notify_phones){
				$xml .= "<NotifyPhones>";
				$notifyPhones = explode(',', $row->notify_phones);
				foreach($notifyPhones as $element){
					$xml .= "<Element>";
					$xml .= $element;
					$xml .= "</Element>";
				}
				$xml .= "</NotifyPhones>";
			}

			$xml .= "<ScheduleForDoctors>" . $row->scheduleForDoctors . "</ScheduleForDoctors>";
			$xml .= "<EmailReconciliation>" . $row->email_reconciliation . "</EmailReconciliation>";

			$xml .= "</Clinic>";
		}
	}

	return $xml;
}


function getClinicOldBranchXML ( $id ) {
		$xml = "";
				
		$id = intval ($id);
		
		$sql = "SELECT
					t1.id, t1.address, t1.isNew
				FROM clinic_address  t1
				WHERE t1.clinic_id=$id";
		$result = query($sql);
		if (num_rows($result) > 0) {
			$xml .= "<ClinicOldBranchList>";
			while ($row = fetch_object($result)) {
				$xml .= "<Element id=\"".$row -> id."\" isNew=\"".$row -> isNew."\">";
				$xml .= "<Title><![CDATA[".$row -> address."]]></Title>";
				$xml .= "</Element>";
			}
			$xml .= "</ClinicOldBranchList>";
		}
		
		return $xml;
	}
	
	
	function getClinicBranchXML ( $id ) {
		$xml = "";
				
		$id = intval ($id);
		
		$sql = "SELECT
					t1.id,  t1.name, t1.short_name, t1.rewrite_name, 
					t1.status, t1.phone, t1.phone_appointment, t1.url, t1.rating, t1.email, t1.contact_name,
					t1.age_selector as age, 
					t1.city, t1.street, t1.house, 
					DATE_FORMAT( t1.created,'%d.%m.%Y') AS crDate
				FROM clinic  t1
				WHERE t1.parent_clinic_id = ".$id."
				ORDER BY  t1.created DESC, t1.id";
		$result = query($sql);
		if (num_rows($result) > 0) {
			$xml .= "<ClinicBranchList>";
			while ($row = fetch_object($result)) {
				$xml .= "<Element id=\"".$row -> id."\">";
				$xml .= "<CrDate>".$row -> crDate."</CrDate>";
				$xml .= "<Title><![CDATA[".$row -> name."]]></Title>";
				$xml .= "<ShortName><![CDATA[".$row -> short_name."]]></ShortName>";
				$xml .= "<RewriteName><![CDATA[".$row -> rewrite_name."]]></RewriteName>";
				$xml .= "<URL><![CDATA[".$row -> url."]]></URL>";
				$xml .= "<Rating>".$row -> rating."</Rating>";
				$xml .= "<Phone>".$row -> phone."</Phone>";
				$xml .= "<PhoneAppointment>".$row -> phone_appointment."</PhoneAppointment>";
				$xml .= "<ContactName>".$row -> contact_name."</ContactName>";
				$xml .= "<Email>".$row -> email."</Email>";
				$xml .= "<City>".$row -> city."</City>";
				$xml .= "<House>".$row -> house."</House>";
				$xml .= "<Street>".$row -> street."</Street>";
				$xml .= "<Age>".$row -> age."</Age>";
				$xml .= "<Status>".$row -> status."</Status>";
				$xml .= "</Element>";
			}
			$xml .= "</ClinicBranchList>";
		}
		
		return $xml;
	}
	
	
	
	function getClinicMetroListXML ( $id ) {
		$xml = "";
				
		$id = intval ($id);
		
		$sql = "SELECT
					t2.id, t2.name
				FROM underground_station_4_clinic  t1, underground_station t2
				WHERE t1.undegraund_station_id = t2.id AND t1.clinic_id=$id";
		$result = query($sql);
		if (num_rows($result) > 0) {
			$xml .= "<MetroList>";
			while ($row = fetch_object($result)) {
				$xml .= "<Element id=\"".$row -> id."\"><![CDATA[".$row -> name."]]></Element>";
			}
			$xml .= "</MetroList>";
		}
		
		return $xml;
	}
	
	
	function getClinicAdminListXML ( $id ) {
		$xml = "";
				
		$id = intval ($id);
		
		$sql = "SELECT
					t2.clinic_admin_id as id, t2.email, t2.lname, t2.fname, t2.mname,
					t2.phone, t2.cell_phone, t2.admin_comment
				FROM admin_4_clinic t1, clinic_admin t2
				WHERE 
					t1.clinic_admin_id = t2.clinic_admin_id 
					AND 
					t1.clinic_id=$id";
		$result = query($sql);
		if (num_rows($result) > 0) {
			$xml .= "<AdminList>";
			while ($row = fetch_object($result)) {
				$xml .= "<Element id=\"".$row -> id."\">";
				$xml .= "<FName>".$row -> fname."</FName>";
				$xml .= "<LName>".$row -> lname."</LName>";
				$xml .= "<MName>".$row -> mname."</MName>";
				$xml .= "<Email>".$row -> email."</Email>";
				$xml .= "<Phone phone=\"".$row -> phone."\">".formatPhone($row -> phone)."</Phone>";
				$xml .= "<CellPhone phone=\"".$row -> cell_phone."\">".formatPhone($row -> cell_phone)."</CellPhone>";
				$xml .= "<Operator_Comm>".$row -> admin_comment."</Operator_Comm>";
				$xml .= "</Element>";
			}
			$xml .= "</AdminList>";
		}
		
		return $xml;
	}
	
	
	
	function getClinicPhonesXML ( $id ) {
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
	
	
	
	function getMetroIdList ( $metroList = array() ) {
		$out = array();
		
		if ( count($metroList) > 0 ) {
			foreach ($metroList as $key => $data) {
				$sql="	SELECT 
							id
						FROM underground_station 
						WHERE 
							LOWER(name) LIKE LOWER('".$data."%') 
						LIMIT 1";
				//echo $sql;
			  	$result = query($sql);
				if (num_rows($result) > 0) {
					$row = fetch_object($result);
					array_push ($out, $row -> id);
				}
			}
		}
		
		return $out;
	}
	

	function getIdListFromArray ( $list =  array() ) {
		$str = "";
		
		if ( count($list) > 0 ) {
			$i = 1;
			foreach ( $list as $key => $data ) {
				if ( $i < count($list) ) {
					$str .= $data.", ";
				} else {
					$str .= $data;
				}
			}
		}
		return $str;
	}
	
	
	
	
	function getDiagnosticList ( $parent = 0 ) {
		$xml = "";
		
		$sql="	SELECT 
					id, name, title, parent_id
				FROM diagnostica 
				WHERE 
					parent_id = $parent 
				ORDER BY name";
		//echo $sql;
	  	$result = query($sql);
		if (num_rows($result) > 0) {
			$xml .= "<DiagnosticList>";
			while ($row = fetch_object($result)) {
				$xml .= "<Element id=\"".$row -> id."\">";
				$xml .= "<Name>".$row -> name."</Name>";
				$xml .= "<Title>".$row -> title."</Title>";
				if ( $row -> parent_id == 0 ) {
					$xml .= getDiagnosticList($row -> id);
				}
				$xml .= "</Element>";
			}
			$xml .= "</DiagnosticList>";
		}
		
		return $xml;
	}
	
	
	function getDiagnostic4Clinic( $clinicid  ) {
		$xml = "";
		
		$clinicid  =  intval($clinicid);
		if ( $clinicid > 0 ) {
			$sql="	SELECT 
						t.id as doctor_clinic_id, t.diagnostica_id as id,
						t.price, t.special_price, t.price_for_online,
						m.id as moderation_id
					FROM diagnostica4clinic as t
						LEFT JOIN moderation m ON (m.entity_id = t.id AND m.entity_class = 'DiagnosticClinicModel')
					WHERE 
						t.clinic_id = $clinicid
					ORDER BY t.id";
			//echo $sql;
		  	$result = query($sql);
			if (num_rows($result) > 0) {
				$xml .= "<Diagnostics>";
				while ($row = fetch_object($result)) {
					$xml .= "<Element id=\"".$row -> id."\">";
					$xml .= "<Price>".$row -> price."</Price>";
					$xml .= "<SpecialPrice>".$row -> special_price."</SpecialPrice>";
					$xml .= "<PriceForOnline>{$row ->price_for_online}</PriceForOnline>";
					$xml .= "<ModerationId>" . $row->moderation_id . "</ModerationId>";
					$xml .= "<DoctorClinicId>" . $row->doctor_clinic_id . "</DoctorClinicId>";
					$xml .= "</Element>";
				}
				$xml .= "</Diagnostics>";
			}
		}
		
		return $xml;
	}
	
	
	
	/**
	 * 
	 * Коэфициенты рейтингов. Масив - константа
	 */	
	function ratingDict() {
		$rating = array();
		
		$rating[0] = array(	"weight"=>0.25, 
							"title"=> "Платежеспособность",
							"type" => array( 
											array("weight"=> 1.00, "title"=>"Клиника оплатила вовремя"), 
											array("weight"=> 0.50, "title"=>"Клиника - должник за прошлый период") 
										) 
							);
		$rating[1] = array(	"weight"=>0.25, 
							"title"=> "Выгодность условий сотрудничества",
							"type" => array( 
											array("weight"=> 1.00, "title"=>"Работаем за записи"), 
											array("weight"=> 0.60, "title"=>"Работаем за дошедших"),
											array("weight"=> 0.20, "title"=>"Работаем за %")
										) 
							);
		$rating[2] = array(	"weight"=>0.3, 
							"title"=> "Лояльность",
							"type" => array( 
											array("weight"=> 1.00, "title"=>"Согласовывает от 95 до 100% пациентов"), 
											array("weight"=> 0.80, "title"=>"Согласовывает от 80 до 95% пациентов"),
											array("weight"=> 0.60, "title"=>"Согласовывает от 70 до 80% пациентов"),
											array("weight"=> 0.20, "title"=>"Согласовывает менее 70% пациентов")
										) 
							);
		$rating[3] = array(	"weight"=>0.2, 
							"title"=> "Отработка со звонками",
							"type" => array( 
											array("weight"=> 1.00, "title"=>"До 50% из звонков в приходы"), 
											array("weight"=> 0.80, "title"=>"До 40% из звонков в приходы"),
											array("weight"=> 0.60, "title"=>"До 30% из звонков в приходы")
										) 
							);
		return $rating;
	}
	
	
	
	
	/**
	 *
	 * Функция получения справочника коэфициентов в формате XML
	 */
	function ratingDictXML () {
		$xml = "";
		
		$rating = ratingDict();
		
		$i = 1;
		$xml .= "<RatingDict>";
		foreach ($rating as $ratingLine) {
			$xml .= "<Element id='".$i."' weight='".$ratingLine['weight']."'>";
			$xml .= "<Title>".$ratingLine['title']."</Title>";
			foreach ($ratingLine['type'] as $element) {
				$xml .= "<Type weight='".$element['weight']."'>".$element['title']."</Type>";
			}
			$xml .= "</Element>";
			$i++;
		}
		$xml .= "</RatingDict>";

		return $xml;
	}

	/**
	 * Получение справочника районов в формате XML
	 * @return string
	 */
	function districtDictXML () {
		$xml = "";

		$data = DistrictModel::model()
			->inCity(getCityId())
			->findAll(['order' => 'name']);

		$xml .= "<DistrictDict>";
		foreach ($data as $district) {
			$xml .= "<Element id=\"". $district->id ."\">". $district->name ."</Element>";
		}
		$xml .= "</DistrictDict>";

		return $xml;
	}
?>
