<?php
use dfs\docdoc\models\DoctorClinicModel;

function getDoctorListXML ($params=array(), $cityId = 1) {
    $xml = "";
    $sqlAdd = " t2.city_id = ".$cityId." AND t3.req_status IN (3,4,6) ";
    $addJoin = "";
    $sqlSort = " ORDER BY t1.id";
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
                $sqlAdd .= " AND t2.id IN (".$subSQL.") ";
            } else
                $sqlAdd .= " AND t2.id is null " ;
        }
        if	( isset($params['name']) && !empty ($params['name'])  )  {
            $sqlAdd .= " AND LOWER(t1.name) LIKE  '%".mb_strtolower($params['name'])."%' ";
        }
        if	( isset($params['shSector']) && !empty ($params['shSector'])  )  {
            $subSQL = "SELECT DISTINCT ds.doctor_id
                       FROM doctor_sector ds
                       LEFT JOIN sector s ON s.id=ds.sector_id
                       WHERE LOWER(s.name) LIKE '%".mb_strtolower($params['shSector'])."%'";
            $sqlAdd .= " AND t1.id IN (".$subSQL.") ";
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
                t1.id,  t1.name as FullName, t1.status, t1.image,
                t2.name as clinicFull, t2.short_name as clinicShort, t2.id as clinicId,
                COUNT(t3.req_id) AS countRequests,
                COUNT(t4.id) AS countReviews,
                CASE
                    WHEN t1.status = 3 THEN 'shown'
                    WHEN t1.status = 4 THEN 'hidden'
                    WHEN t1.status = 6 THEN 'inModeration'
                END AS statusName
            FROM doctor  t1
            LEFT JOIN doctor_4_clinic d4c ON (t1.id = d4c.doctor_id and d4c.type = " . DoctorClinicModel::TYPE_DOCTOR . ")
            LEFT JOIN clinic t2 ON (d4c.clinic_id = t2.id)
            LEFT JOIN request t3 ON (t3.req_doctor_id = t1.id)
            LEFT JOIN doctor_opinion t4 ON (t4.doctor_id = t1.id)
            ".$addJoin."
            WHERE ".$sqlAdd. "
            GROUP BY t1.id
            ".$sqlSort;


    //echo $sql;
    if ( isset($params['step']) && intval($params['step']) > 0 ) $step = $params['step'];
    if ( isset($params['startPage']) && intval($params['startPage']) > 0 ) $startPage = $params['startPage'];

    if ( $withPager ) {
        list($sql, $str) = pager( $sql, $startPage, $step, "loglist"); // функция берется из файла pager.xsl с тремя параметрами. параметр article тут не нужен
        $xml .= $str;
    }
    //echo $str."<br/>";

    $result = query($sql);
    if (num_rows($result) > 0) {
        $xml .= "<DoctorList>";
        while ($row = fetch_object($result)) {
            $xml .= "<Element id=\"".$row -> id."\">";
            $xml .= "<Name>".$row -> FullName."</Name>";
            $xml .= "<OpinionCount>".getOpinionCountByDoctorId ( $row -> id )."</OpinionCount>";
            $xml .= "<RequestCount>".$row -> countRequests."</RequestCount>";
            $xml .= "<Status>".$row -> statusName."</Status>";
            $xml .= "<IMG>".$row -> image."</IMG>";
            $xml .= getSectorByDoctorIdXML ($row -> id);
            $xml .= "</Element>";
        }
        $xml .= "</DoctorList>";
    }
    return $xml;
}

function getDoctorByIdXML ( $id = 0 ) {
    $xml = "";

    $id = intval ($id);

    if ( $id > 0 ) {
        $sql = "SELECT
						t1.id, t1.clinic_id,
						t1.name, t1.phone, t1.phone_appointment, t1.email,
						t1.sex, t1.addNumber,
						t1.category_id, t1.degree_id, t1.rank_id,
						t1.rating, t1.total_rating, t1.rating_education as edu, t1.rating_ext_education as ext_edu, t1.rating_experience as exp, t1.rating_academic_degree as ac_deg, t1.rating_clinic as cln,t1.rating_opinion as opin,
						t1.note as description, t1.image, t1.status,
						DATE_FORMAT( t1.created,'%d.%m.%Y') AS crDate,
						t1.experience_year, t1.departure, t1.price, t1.special_price,
						t1.text, t1.text_spec, t1.text_association, t1.text_education, t1.text_degree, t1.text_course, t1.text_experience,
						t1.note, t1.openNote, t1.rewrite_name as alias,
						t2.name as clinic, t2.id as clinicId
					FROM doctor  t1
					LEFT JOIN clinic t2 ON (t1.clinic_id = t2.id)
					WHERE
						t1.id = $id";
        //echo $sql;
        $result = query($sql);
        if (num_rows($result) == 1) {
            $row = fetch_object($result);
            $xml .= "<Doctor id=\"".$row -> id."\">";
            $xml .= "<CrDate>".$row -> crDate."</CrDate>";
            $xml .= "<Clinic id=\"".$row -> clinicId."\">".$row -> clinic."</Clinic>";
            $xml .= getClinicPhonesXML($row -> clinicId);
            $xml .= "<Name>".$row -> name."</Name>";
            if ( !empty($row -> alias) ) {
                $alias = $row -> alias;
            } else {
                $alias = getAlias4Doctor( $row -> name );
            }
            $xml .= "<Alias>".$alias."</Alias>";

            $xml .= "<Rating>".$row -> rating."</Rating>";
            $xml .= "<IntegralRating edu=\"".$row -> edu."\" ext_edu=\"".$row -> ext_edu."\" exp=\"".$row -> exp."\" ac_deg=\"".$row -> ac_deg."\"  cln=\"".$row -> cln."\" opin=\"".$row -> opin."\" >".$row -> total_rating."</IntegralRating>";

            $xml .= "<Sex>".$row -> sex."</Sex>";
            $img = explode(".", $row -> image);
            if ( count($img) == 2) {
                $xml .= "<IMG>".$img[0]."_small.".$img[1]."</IMG>";
            }
            $xml .= "<Phone>".$row -> phone."</Phone>";
            $xml .= "<AddPhoneNumber>".$row -> addNumber."</AddPhoneNumber>";
            $xml .= "<PhoneAppointment>".$row -> phone_appointment."</PhoneAppointment>";
            $xml .= "<CategoryId>".$row -> category_id."</CategoryId>";
            $xml .= "<DegreeId>".$row -> degree_id."</DegreeId>";
            $xml .= "<RankId>".$row -> rank_id."</RankId>";

            $xml .= "<Education><![CDATA[".$row -> text_education."]]></Education>";
            $xml .= "<Association><![CDATA[".$row -> text_association."]]></Association>";
            $xml .= "<Degree><![CDATA[".$row -> text_degree."]]></Degree>";
            $xml .= "<ExperienceYear>".$row -> experience_year."</ExperienceYear>";

            $xml .= "<Price>".$row -> price."</Price>";
            $xml .= "<SpecialPrice>".$row -> special_price."</SpecialPrice>";
            $xml .= "<Departure>".$row -> departure."</Departure>";
            $xml .= "<Email>".$row -> email."</Email>";
            $xml .= "<Status>".$row -> status."</Status>";

            $xml .= "<Description><![CDATA[".$row -> description."]]></Description>";
            $xml .= "<TextCommon><![CDATA[".$row -> text."]]></TextCommon>";
            $xml .= "<TextSpec><![CDATA[".$row -> text_spec."]]></TextSpec>";
            $xml .= "<TextAssoc><![CDATA[".$row -> text_association."]]></TextAssoc>";
            $xml .= "<TextEdu><![CDATA[".$row -> text_education."]]></TextEdu>";
            $xml .= "<TextDegree><![CDATA[".$row -> text_degree."]]></TextDegree>";
            $xml .= "<TextCourse><![CDATA[".$row -> text_course."]]></TextCourse>";
            $xml .= "<TextExperience><![CDATA[".$row -> text_experience."]]></TextExperience>";

            $xml .= getSectorByDoctorIdXML ( $row -> id );
            $xml .= getDoctorMetroListXML( $row -> id );
            $xml .= getEducationListXML ( $row -> id );
            $xml .= "</Doctor>";
        }
    }

    return $xml;
}

function getSectorByDoctorIdXML ( $id ) {
    $xml = "";

    $id = intval ($id);

    $sql = "SELECT
					t1.id, t1.name as title
				FROM sector t1, doctor_sector t2
				WHERE t2.sector_id = t1.id
					AND t2.doctor_id = $id";
    $result = query($sql);
    if (num_rows($result) > 0) {
        $xml .= "<SectorList>";
        while ($row = fetch_object($result)) {
            $xml .= "<Sector id=\"".$row -> id."\">".$row -> title."</Sector>";
        }
        $xml .= "</SectorList>";
    }

    return $xml;
}

function getOpinionCountByDoctorId ( $id ) {
    $id = intval ($id);

    $sql = "SELECT count( t1.id ) as cnt
			FROM doctor_opinion  t1
			WHERE t1.doctor_id = ".$id;
    //echo $sql;
    $result = query($sql);
    if (num_rows($result) == 1) {
        $row = fetch_object($result);
        return $row -> cnt;
    }

    return '';
}

function getRequestCountByDoctorId ( $id ) {
    $id = intval ($id);

    $sql = "SELECT count( t1.req_id ) as cnt
		    FROM request  t1
			WHERE t1.req_status=3
			    AND t1.req_doctor_id = ".$id;
    //echo $sql;
    $result = query($sql);
    if (num_rows($result) == 1) {
        $row = fetch_object($result);
        return $row -> cnt;
    }

    return '';
}


function getDoctorMetroListXML ( $id ) {
    $xml = "";

    $id = intval ($id);

    $sql = "SELECT
                t1.id, t1.name
            FROM underground_station t1
            LEFT JOIN underground_station_4_clinic t2 ON t2.undegraund_station_id=t1.id
            LEFT JOIN doctor_4_clinic t3 ON t3.clinic_id=t2.clinic_id and t3.type = " . DoctorClinicModel::TYPE_DOCTOR . "
            WHERE t3.doctor_id=$id
            GROUP BY t1.id";
    $result = query($sql);
    if (num_rows($result) > 0) {
        $xml .= "<MetroList>";
        while ($row = fetch_object($result)) {
            $xml .= "<Element id=\"".$row -> id."\">".$row -> name."</Element>";
        }
        $xml .= "</MetroList>";
    }

    return $xml;
}



function getDegreeDictXML ( ) {
    $xml = "";

    $sql = "SELECT
					t1.degree_id as id, t1.title
				FROM degree_dict t1
				ORDER BY t1.degree_id";
    $result = query($sql);
    if (num_rows($result) > 0) {
        $xml .= "<DegreeDict>";
        while ($row = fetch_object($result)) {
            $xml .= "<Element id=\"".$row -> id."\">".$row -> title."</Element>";
        }
        $xml .= "</DegreeDict>";
    }

    return $xml;
}

function getCategoryDictXML ( ) {
    $xml = "";

    $sql = "SELECT
					t1.category_id as id, t1.title
				FROM category_dict t1
				ORDER BY t1.category_id";
    $result = query($sql);
    if (num_rows($result) > 0) {
        $xml .= "<CategoryDict>";
        while ($row = fetch_object($result)) {
            $xml .= "<Element id=\"".$row -> id."\">".$row -> title."</Element>";
        }
        $xml .= "</CategoryDict>";
    }

    return $xml;
}

function getRankDictXML ( ) {
    $xml = "";

    $sql = "SELECT
					t1.rank_id as id, t1.title
				FROM rank_dict t1
				ORDER BY t1.rank_id";
    $result = query($sql);
    if (num_rows($result) > 0) {
        $xml .= "<RankDict>";
        while ($row = fetch_object($result)) {
            $xml .= "<Element id=\"".$row -> id."\">".$row -> title."</Element>";
        }
        $xml .= "</RankDict>";
    }

    return $xml;
}


function getEducationListXML ( $id ) {
    $xml = "";

    $sql = "SELECT
					t1.education_id as id, t1.year,
					t2.title, t2.type
				FROM education_4_doctor t1, education_dict t2
				WHERE
					t1.education_id = t2.education_id
					AND t1.doctor_id = ".$id."
				ORDER BY t1.year";
    $result = query($sql);
    if (num_rows($result) > 0) {
        $xml .= "<EducationList>";
        while ($row = fetch_object($result)) {
            $xml .= "<Element id=\"".$row -> id."\"  year=\"".$row -> year."\"  type=\"".$row -> type."\" typeCh=\"".typeCh($row -> type)."\">".$row -> title."</Element>";
        }
        $xml .= "</EducationList>";
    }

    return $xml;
}

function typeCh ( $type ) {
    $str_type = "";

    switch ( $type ) {
        case 'university' : {$str_type = "В"; $str_type_name= 'ВУЗ';} break;
        case 'college' : {$str_type = "К"; $str_type_name= 'Колледж';} break;
        case 'traineeship' : {$str_type = "О"; $str_type_name= 'Ординатура';} break;
        case 'graduate' : {$str_type = "А"; $str_type_name= 'Аспирантура';} break;
        case 'internship' : {$str_type = "И"; $str_type_name= 'Интернатура';} break;
        default : $str_type = "-";
    }
    return $str_type;

}


function getAlias4Doctor ( $str ) {
    $str = trim($str);
    if ( !empty($str) ) {
        $aliasArr = explode(" ", $str);

        $alias = translit ($aliasArr[0]);
        if ( isset($aliasArr[1]) ) $alias .= "_".translit ($aliasArr[1]);


        //echo getAliasCount($alias);
        $alias = ereg_replace("^[a-zA-Z_]$",'',$alias);
        $alias = rtrim($alias,'+');
        $aliasNew = $alias;

        $i = 1;
        while ( getAliasCount($aliasNew) != 0) {
            $aliasNew = $alias."_".$i;
            $i++;
        }

        return $aliasNew;
    } else return;
}


function getAliasCount ($alias) {
    $cnt = 0;

    $sql="SELECT COUNT(rewrite_name) AS cnt FROM `doctor` WHERE LOWER(rewrite_name) = LOWER('".$alias."')";
    //echo $sql;
    $result = query($sql);
    $row = fetch_object($result);
    $cnt = $row -> cnt;

    return $cnt;
}



function getNextPhoneNumber () {
    $phNumber = DeltaAN;

    $sql="SELECT max(addNumber)+1 AS cnt FROM `doctor`";
    //echo $sql;
    $result = query($sql);
    $row = fetch_object($result);
    if ( !empty($row -> cnt) ) {
        $phNumber = $row -> cnt;
    }

    return $phNumber;
}


function getOpinionCount ( $doctorId, $type = 0 ) {
    $id = intval($doctorId);
    $countOpinion = 0;

    //$rating = array ($row -> rating_qualification, $row -> rating_attention, $row -> rating_room );
    //$rating = opinionColor( $ratingArray );

    if ( $type == 0) {
        $sql = "SELECT
						count( t1.id ) as cnt
					FROM doctor_opinion  t1
					WHERE t1.doctor_id = ".$id."
						AND t1.origin <> 'editor'
						AND NOT (t1.author = 'gues' AND t1.status = 'hidden')
						AND t1.status <> 'disable'";
        //echo $sql;
        $result = query($sql);
        if (num_rows($result) == 1) {
            $row = fetch_object($result);
            $countOpinion = $row -> cnt;
        }
    } else if ( $type == 1 ) {
        $sql = "SELECT
						count( t1.id ) as cnt
					FROM doctor_opinion  t1
					WHERE t1.doctor_id = ".$id."
						AND t1.origin <> 'editor'
						AND NOT (t1.author = 'gues' AND t1.status = 'hidden')
						AND t1.rating_color = 1
						AND t1.status <> 'disable'";
        //echo $sql;
        $result = query($sql);
        $countOpinion_1 = 0;
        if (num_rows($result) == 1) {
            $row = fetch_object($result);
            $countOpinion_1 = $row -> cnt;
        }

        $sql = "SELECT
						count( t1.id ) as cnt
					FROM doctor_opinion  t1
					WHERE t1.doctor_id = ".$id."
						AND t1.origin <> 'editor'
						AND NOT (t1.author = 'gues' AND t1.status = 'hidden')
						AND t1.rating_color IS NULL
						AND t1.rating_qualification >= 4
						AND t1.rating_attention >= 4
						AND t1.rating_room > 0
						AND t1.status <> 'disable'";
        //echo $sql;
        $result = query($sql);
        $countOpinion_2 = 0;
        if (num_rows($result) == 1) {
            $row = fetch_object($result);
            $countOpinion_2 = $row -> cnt;
        }
        $countOpinion = $countOpinion_1 + $countOpinion_2;

    } else if ( $type == -1 ) {
        $sql = "SELECT
						count( t1.id ) as cnt
					FROM doctor_opinion  t1
					WHERE t1.doctor_id = ".$id."
						AND t1.origin <> 'editor'
						AND NOT (t1.author = 'gues' AND t1.status = 'hidden')
						AND t1.rating_color = -1
						AND t1.status <> 'disable'";
        //echo $sql;
        $result = query($sql);
        $countOpinion_1 = 0;
        if (num_rows($result) == 1) {
            $row = fetch_object($result);
            $countOpinion_1 = $row -> cnt;
        }

        $sql = "SELECT
						count( t1.id ) as cnt
					FROM doctor_opinion  t1
					WHERE t1.doctor_id = ".$id."
						AND t1.origin <> 'editor'
						AND NOT (t1.author = 'gues' AND t1.status = 'hidden')
						AND t1.status <> 'disable'
						AND t1.rating_color IS NULL
						AND
						( 	t1.rating_qualification <= 2
							OR
							(
								t1.rating_qualification <= 3 AND
								t1.rating_attention <= 3 AND
								t1.rating_room <= 3
							)
						)
					";
        //echo $sql;
        $result = query($sql);
        $countOpinion_2 = 0;
        if (num_rows($result) == 1) {
            $row = fetch_object($result);
            $countOpinion_2 = $row -> cnt;
        }
        $countOpinion = $countOpinion_1 + $countOpinion_2;
    }

    return $countOpinion;
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

function getClinicListByDoctorIdXML ( $id ) {
    $xml = "";

    $id = intval ($id);

    if ( $id > 0 ) {
        $sql = "SELECT
						t1.clinic_id as id,
						t2.name, t2.short_name
					FROM doctor_4_clinic t1, clinic t2
					WHERE
						t1.clinic_id = t2.id
						AND
						t1.type = " . DoctorClinicModel::TYPE_DOCTOR . "
						AND
						t1.doctor_id = ".$id;
        $result = query($sql);
        if (num_rows($result) > 0) {
            $xml .= "<ClinicList>";
            while ($row = fetch_object($result)) {
                $xml .= "<Element id=\"".$row -> id."\">".$row -> name."</Element>";
            }
            $xml .= "</ClinicList>";
        }
    }

    return $xml;
}


