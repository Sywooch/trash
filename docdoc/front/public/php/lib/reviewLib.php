<?php
use dfs\docdoc\models\DoctorClinicModel;

require_once dirname(__FILE__)."/../../lib/php/dateconvertionLib.php";

function getReviewListXML ($params=array(), $cityId = 1) {
    $xml = "";
    $sqlAdd = " t4.city_id = ".$cityId." ";
    $sqlSort = " ORDER BY t1.created DESC, t1.id";
    $startPage = 1;
    $step = 25;
    $withPager = true;

    if (count($params) > 0) {

        if	( isset($params['withPager']) )  {
            $withPager = $params['withPager'];
        }

        if	( isset($params['clinic']) )  {
            if ($params['clinic'] >= 0  ) {
                $subSQL = "SELECT id FROM clinic WHERE id= ".$params['clinic']." OR parent_clinic_id=".$params['clinic'];
                $sqlAdd .= " AND t4.id IN (".$subSQL.") ";
            } else
                $sqlAdd .= " AND t4.id is null " ;
        }
        if	( isset($params['crDateFrom']) && !empty ($params['crDateFrom'])  )  {
            $sqlAdd .= " AND UNIX_TIMESTAMP(t1.created)) >= ".(strtotime(convertDate2DBformat($params['crDateFrom']))+86400)." " ;
        }
        if	( isset($params['crDateTill']) && !empty ($params['crDateTill'])  )  {
            $sqlAdd .= " AND UNIX_TIMESTAMP(t1.created) <= ".(strtotime(convertDate2DBformat($params['crDateTill']))+86400)." " ;
        }
        if	( isset($params['shDoctor']) && !empty ($params['shDoctor'])  )  {
            $sqlAdd .= " AND UPPER(doctor) LIKE '%".mb_strtoupper($params['shDoctor'])."%' ";
        }

        if ( isset($params['sortBy']) && !empty ($params['sortBy']))  {
            switch ($params['sortBy']) {
                case 'doctor'     : $sortBy= " FullName "; break;
                case 'reviews'    : $sortBy= " countReviews "; break;
                case 'requests'   : $sortBy= " countRequests "; break;
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
					t1.id, t1.doctor_id, t1.request_id,
					t1.name as client, t1.phone, t1.age,
					t1.rating_qualification, t1.rating_attention, t1.rating_room, t1.rating_color,
					t1.allowed, t1.lk_status, t1.is_fake, t1.author,
					DATE_FORMAT( t1.created,'%d.%m.%Y') AS crDate,
					t1.date_publication AS pubDate,
					t1.text,
					t1.status, t1.origin,
					t2.name as doctor
				FROM doctor_opinion  t1
				LEFT JOIN doctor t2  ON (t2.id = t1.doctor_id)
				LEFT JOIN doctor_4_clinic t3 ON (t3.doctor_id=t2.id and t3.type = " . DoctorClinicModel::TYPE_DOCTOR . ")
				LEFT JOIN clinic t4  ON (t4.id = t3.clinic_id)
				WHERE ".$sqlAdd. "
				GROUP BY t1.id
				".$sqlSort;


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
        $xml .= "<ReviewList>";
        while ($row = fetch_object($result)) {
            $xml .= "<Element id=\"".$row -> id."\">";
            $xml .= "<DoctorId>".$row -> doctor_id."</DoctorId>";
            $xml .= "<Doctor id=\"".$row -> doctor_id."\">".$row -> doctor."</Doctor>";

            $xml .= "<CrDate>".$row -> crDate."</CrDate>";
            if ( $row -> pubDate ) {
                $xml .= "<PubDate>".date("d.m.Y",$row -> pubDate )."</PubDate>";
            }

            $xml .= "<RatingQlf>".$row -> rating_qualification."</RatingQlf>";
            $xml .= "<RatingAtt>".$row -> rating_attention."</RatingAtt>";
            $xml .= "<RatingRoom>".$row -> rating_room."</RatingRoom>";

            $xml .= "<Note><![CDATA[".$row -> text."]]></Note>";
            $xml .= "</Element>";
        }
        $xml .= "</ReviewList>";
    }
    return $xml;
}

