<?php
require_once dirname(__FILE__)."/../../lib/php/dateconvertionLib.php";

function getPatientListXML ($params=array(), $cityId = 1) {
    $xml = "";
    $sqlAdd = " t1.id_city = ".$cityId."  ";
    $sqlSort = " ORDER BY st, t1.req_created DESC, t1.req_id";
    $startPage = 1;
    $step = 30;
    $withPager = true;

    if (count($params) > 0) {

        if	( isset($params['withPager']) )  {
            $withPager = $params['withPager'];
        }

        if	( isset($params['status']) && $params['status'] != '' )  {
            switch ($params['status']) {
                case 'transferred'	: $sqlAdd .= " AND (t1.req_status IN (2,3,8,9) AND t1.is_transfer=1) "; break;
                case 'registered'   : $sqlAdd .= " AND t1.req_status=2 "; break;
                case 'visited'      : $sqlAdd .= " AND t1.req_status IN (3,8) "; break;
                case 'expired'      : $sqlAdd .= " AND t1.req_status=2 AND (t1.date_admission <= UNIX_TIMESTAMP(NOW())) "; break;
                case 'declined'     : $sqlAdd .= " AND t1.req_status=9 "; break;
                default:break;
            }
        } else {
            $sqlAdd .= " AND (t1.req_status IN (2,3,8,9) AND t1.is_transfer=1) ";
        }

        if	( isset($params['clinic']) )  {
            if ($params['clinic'] >= 0  ) {
                $subSQL = "SELECT id FROM clinic WHERE id= ".$params['clinic']." OR parent_clinic_id=".$params['clinic'];
                $sqlAdd .= " AND t1.clinic_id IN (".$subSQL.") ";
            } else
                $sqlAdd .= " AND t1.clinic_id is null " ;
        }

        /*	Дата создания заявки	*/
        if	( isset($params['crDateFrom']) && !empty ($params['crDateFrom'])  )  {
            $sqlAdd .= " AND t1.req_created >= ".strtotime($params['crDateFrom'])." " ;
        }
        if	( isset($params['crDateTill']) && !empty ($params['crDateTill'])  )  {
            $sqlAdd .= " AND t1.req_created <= ".(strtotime($params['crDateTill'])+86400)." " ;
        }

        /*	Дата приёма	*/
        if	( isset($params['appDateFrom']) && !empty ($params['appDateFrom'])  )  {
            $sqlAdd .= " AND t1.date_admission >= ".strtotime(convertDate2DBformat($params['appDateFrom']))." AND t1.date_admission IS NOT NULL " ;
        }
        if	( isset($params['appDateTill']) && !empty ($params['appDateTill'])  )  {
            $sqlAdd .= " AND t1.date_admission <= ".(strtotime(convertDate2DBformat($params['appDateTill']))+86400)." " ;
        }

        /*	Специализация	*/
        if	( isset($params['shSector']) && !empty ($params['shSector'])  )  {
            $sqlAdd .= " AND UPPER(t3.name) LIKE '%".mb_strtoupper($params['shSector'])."%' ";
        }

        /*  Врач  */
        if	( isset($params['shDoctor']) && !empty ($params['shDoctor'])  )  {
            $sqlAdd .= " AND UPPER(t2.name) LIKE '%".mb_strtoupper($params['shDoctor'])."%' ";
        }

        if ( isset($params['sortBy']) && !empty ($params['sortBy']))  {
            switch ($params['sortBy']) {
                case 'crDate'	: $sortBy= " t1.req_created";break;
                case 'admDate'  : $sortBy= " t1.date_admission ";break;
                case 'patient'  : $sortBy= " t1.client_name ";break;
                case 'docName'  : $sortBy= " t2.name ";break;
                case 'docSpec'  : $sortBy= " t3.name ";break;
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
                        t3.name as sector,
                        t1.date_admission, t1.appointment_status,
                        CASE
                            WHEN t1.req_status  = 2 AND t1.date_admission <= UNIX_TIMESTAMP(NOW()) THEN 0
                            ELSE 1
                        END AS st,
                        CASE
                            WHEN t1.req_status  = 2 AND t1.date_admission > UNIX_TIMESTAMP(NOW()) THEN 'registered'
                            WHEN t1.req_status  = 3 OR t1.req_status  = 8 THEN 'visited'
                            WHEN t1.req_status  = 9 THEN 'declined'
                            ELSE 'registered'
                        END AS statusName,
                        CASE
                            WHEN t1.req_status = 3 AND t1.date_admission > (UNIX_TIMESTAMP(NOW()) - 2592000) THEN 1
                            ELSE 0
                        END AS changeable
                    FROM request  t1
                    LEFT JOIN doctor t2 ON (t2.id = t1.req_doctor_id)
                    LEFT JOIN sector t3 ON (t3.id = t1.req_sector_id)
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

            $xml .= "<Client id=\"".$row -> clientId."\"><![CDATA[".$row -> client_name."]]></Client>";
            $xml .= "<ClientPhone>".$row -> client_phone."</ClientPhone>";

            $xml .= "<CrDate>".date("d.m.y",$row -> req_created )."</CrDate>";
            $xml .= "<CrTime>".date("H:i",$row -> req_created )."</CrTime>";
            if ( !empty($row -> date_admission) ) {
                $xml .= "<AppointmentDate>".date("d.m.y",$row -> date_admission )."</AppointmentDate>";
                $xml .= "<AppointmentTime>".date("H:i",$row -> date_admission )."</AppointmentTime>";
            }

            $xml .= "<Status>".$row -> statusName."</Status>";
            $xml .= "<Changeable>".$row -> changeable."</Changeable>";
            $xml .= "<Type>".$row -> req_type."</Type>";
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

?>