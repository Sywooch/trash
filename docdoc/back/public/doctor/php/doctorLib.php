<?php
use dfs\docdoc\models\DoctorClinicModel;

require_once dirname(__FILE__) . "/../../lib/php/validate.php";
require_once dirname(__FILE__) . "/../../lib/php/translit.php";
require_once dirname(__FILE__) . "/../../lib/php/rating.php";

function getDoctorListXML($params = array(), $cityId = 1)
{
	$xml = "";
	$sqlAdd = " t2.city_id = " . $cityId . " ";
	//$sqlAdd = " 1=1 ";
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
		if (isset($params['clinic']) && intval($params['clinic']) > 0) {
			if (isset($params['branch']) && intval($params['branch']) == 1) {
				$sqlAdd .=
					" AND (d4c.clinic_id = " . $params['clinic'] . " OR t2.parent_clinic_id = " .
					$params['clinic'] .
					") ";
			} else {
				$sqlAdd .= " AND d4c.clinic_id = " . $params['clinic'] . " ";
			}
		}
		if (isset($params['noClinic']) && intval($params['noClinic']) == 1) {
			$sqlAdd = str_replace("t2.city_id = " . $cityId, " 1=1 ", $sqlAdd);
			$sqlAdd .= " AND d4c.clinic_id is null ";
		}
		if (isset($params['departure']) && intval($params['departure']) == 1) {
			$sqlAdd .= " AND t1.departure = 1 ";
		}
		if (isset($params['kidsReception']) && intval($params['kidsReception']) == 1) {
			$sqlAdd .= " AND t1.kids_reception = 1 ";
		}
		if (isset($params['sector']) && intval($params['sector']) > 0) {
			$sqlAdd .= " AND t3.sector_id = " . $params['sector'] . " ";
			$addJoin .= " LEFT JOIN doctor_sector t3 ON (t3.doctor_id = t1.id) ";
		}

		if (isset($params['id']) && !empty ($params['id'])) {
			$sqlAdd = " t1.id = '" . $params['id'] . "'";
		}

		if (isset($params['moderation']) && !empty ($params['moderation'])) {
			$sqlAdd .= " AND m.id IS NOT NULL";
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
					t1.id,  t1.name as FullName, t1.status, t1.phone, t1.image, 
					t1.total_rating, t1.rating, t1.rating_opinion, 
					CASE 
						WHEN t1.rating <> 0 THEN t1.rating 
						WHEN t1.total_rating <> 0 THEN t1.total_rating 
						ELSE 0
						END AS complexRating,
					t1.rating_internal,
					t1.email, t1.sex, t1.price, t1.special_price,
					DATE_FORMAT( t1.created,'%d.%m.%Y') AS crDate,
					t1.note, t1.openNote, t1.addNumber,
					t2.name as clinicFull, t2.short_name as clinicShort, t2.id as clinicId, t2.status as clStatus,
					m.id as moderation_id
				FROM doctor  t1
				LEFT JOIN  doctor_4_clinic d4c ON (t1.id = d4c.doctor_id and d4c.type = " . DoctorClinicModel::TYPE_DOCTOR . ")
				LEFT JOIN clinic t2 ON (d4c.clinic_id = t2.id)
				LEFT JOIN moderation m ON (m.entity_id = t1.id AND m.entity_class = 'DoctorModel')
				" . $addJoin . "
				WHERE " . $sqlAdd . "
			    GROUP BY t1.id" . $sqlSort;

	//echo $sql;
	if (isset($params['step']) && intval($params['step']) > 0) {
		$step = $params['step'];
	}
	if (isset($params['startPage']) && intval($params['startPage']) > 0) {
		$startPage = $params['startPage'];
	}

	if ($withPager) {
		list($sql, $str) =
			pager(
				$sql,
				$startPage,
				$step,
				"loglist"
			); // функция берется из файла pager.xsl с тремя параметрами. параметр article тут не нужен
		$xml .= $str;
	}
	//echo $str."<br/>";

	$result = query($sql);
	if (num_rows($result) > 0) {
		$xml .= "<DoctorList>";
		while ($row = fetch_object($result)) {
			$xml .= "<Element id=\"" . $row->id . "\">";
			$xml .= "<CrDate>" . $row->crDate . "</CrDate>";
			$xml .= "<Name>" . $row->FullName . "</Name>";
			$xml .= "<TotalRating>" . $row->total_rating . "</TotalRating>";
			$xml .= "<Rating>" . $row->rating . "</Rating>";
			$xml .= "<InternalRating>" . $row->rating_internal . "</InternalRating>";

			$xml .= "<complexRating>" . $row->complexRating . "</complexRating>";

			$total = getOpinionCount($row->id, 0);
			$positiv = getOpinionCount($row->id, 1);
			$negativ = getOpinionCount($row->id, -1);
			$xml .=
				"<OpinionRatingCalculate total=\"" .
				$total .
				"\"  positiv=\"" .
				$positiv .
				"\"  negativ=\"" .
				$negativ .
				"\">" .
				rating_opinion($positiv, $negativ, $total) .
				"</OpinionRatingCalculate>";
			$xml .= "<OpinionRating>" . $row->rating_opinion . "</OpinionRating>";

			$xml .= "<Price>" . $row->price . "</Price>";
			$xml .= "<SpecialPrice>" . $row->special_price . "</SpecialPrice>";
			$xml .= "<Phone>" . $row->phone . "</Phone>";
			$xml .= "<AddNumber>" . $row->addNumber . "</AddNumber>";
			$xml .= "<Email>" . $row->email . "</Email>";
			$xml .= "<Sex>" . $row->sex . "</Sex>";
			$xml .= "<Status>" . $row->status . "</Status>";
			$xml .= "<IMG>" . $row->image . "</IMG>";
			$xml .=
				"<Clinic id=\"" .
				$row->clinicId .
				"\" status=\"" .
				$row->clStatus .
				"\">" .
				((!empty($row->clinicShort)) ? $row->clinicShort : $row->clinicFull) .
				"</Clinic>";
			$xml .= getSectorByDoctorIdXML($row->id);
			$xml .= getClinicListByDoctorIdXML($row->id);
			$xml .= "<Opinion>" . getOpinionCountByDoctorId($row->id) . "</Opinion>";
			$xml .= "<OperatorComment><![CDATA[" . $row->note . "]]></OperatorComment>";
			$xml .= "<OperatorOpenComment><![CDATA[" . $row->openNote . "]]></OperatorOpenComment>";
			$xml .= "<ModerationId>" . $row->moderation_id . "</ModerationId>";
			$xml .= "</Element>";
		}
		$xml .= "</DoctorList>";
	}
	return $xml;
}

function getStatusDictXML()
{
	$xml = "";

	$xml .= "<StatusDict  mode='doctorDict'>";
	$xml .= "<Element id=\"1\">Регистрация</Element>";
	$xml .= "<Element id=\"2\">Новый</Element>";
	$xml .= "<Element id=\"3\">Активен</Element>";
	$xml .= "<Element id=\"4\">Заблокирован</Element>";
	$xml .= "<Element id=\"5\">К удалению</Element>";
	$xml .= "<Element id=\"6\">На модерации</Element>";
	$xml .= "<Element id=\"7\">Другой врач</Element>";
	$xml .= "</StatusDict>";

	return $xml;
}

function getEducationTypeDictXML()
{
	$xml = "";

	$xml .= "<EducationTypeDict>";
	$xml .= "<Element id=\"none\">Нет</Element>";
	$xml .= "<Element id=\"college\">Колледж</Element>";
	$xml .= "<Element id=\"university\">ВУЗ</Element>";
	$xml .= "<Element id=\"internship\">Интернатура</Element>";
	$xml .= "<Element id=\"traineeship\">Ординатура</Element>";
	$xml .= "<Element id=\"graduate\">Аспирантура</Element>";
	$xml .= "</EducationTypeDict>";

	return $xml;
}

function getDoctorByIdXML($id = 0)
{
	$xml = "";

	$id = intval($id);

	if ($id > 0) {
		$sql = "SELECT
						t1.id, t1.clinic_id,  
						t1.name, t1.phone, t1.phone_appointment, t1.email, 
						t1.sex, t1.addNumber, 
						t1.category_id, t1.degree_id, t1.rank_id,
						t1.rating, t1.total_rating, t1.rating_education as edu, t1.rating_ext_education as ext_edu, t1.rating_experience as exp, t1.rating_academic_degree as ac_deg, t1.rating_clinic as cln,t1.rating_opinion as opin, t1.rating_internal,
						t1.kids_reception, t1.kids_age_from, t1.kids_age_to,
						t1.note as description, t1.image, t1.status,  
						DATE_FORMAT( t1.created,'%d.%m.%Y') AS crDate,
						t1.experience_year, t1.departure, t1.price, t1.special_price,
						t1.text, t1.text_spec, t1.text_association, t1.text_education, t1.text_degree, t1.text_course, t1.text_experience, 
						t1.note, t1.openNote, t1.rewrite_name as alias,
						t2.name as clinic, t2.id as clinicId, t2.status as clStatus
					FROM doctor  t1
					LEFT JOIN clinic t2 ON (t1.clinic_id = t2.id)
					WHERE
						t1.id = $id";
		//echo $sql;
		$result = query($sql);
		if (num_rows($result) == 1) {
			$row = fetch_object($result);
			$xml .= "<Doctor id=\"" . $row->id . "\">";
			$xml .= "<CrDate>" . $row->crDate . "</CrDate>";
			$xml .=
				"<Clinic id=\"" . $row->clinicId . "\" status=\"" . $row->clStatus . "\">" . $row->clinic . "</Clinic>";
			$xml .= getClinicPhonesXML($row->clinicId);
			$xml .= "<Name>" . $row->name . "</Name>";
			if (!empty($row->alias)) {
				$alias = $row->alias;
			} else {
				$alias = getAlias4Doctor($row->name);
			}
			$xml .= "<Alias>" . $alias . "</Alias>";

			$xml .= "<Rating>" . $row->rating . "</Rating>";
			$xml .= "<InternalRating>" . $row->rating_internal . "</InternalRating>";
			$xml .=
				"<IntegralRating edu=\"" .
				$row->edu .
				"\" ext_edu=\"" .
				$row->ext_edu .
				"\" exp=\"" .
				$row->exp .
				"\" ac_deg=\"" .
				$row->ac_deg .
				"\"  cln=\"" .
				$row->cln .
				"\" opin=\"" .
				$row->opin .
				"\" >" .
				$row->total_rating .
				"</IntegralRating>";

			$total = getOpinionCount($row->id, 0);
			$positiv = getOpinionCount($row->id, 1);
			$negativ = getOpinionCount($row->id, -1);
			$xml .=
				"<OpinionRating total=\"" .
				$total .
				"\"  positiv=\"" .
				$positiv .
				"\"  negativ=\"" .
				$negativ .
				"\">" .
				rating_opinion($positiv, $negativ, $total) .
				"</OpinionRating>";

			$xml .= "<Sex>" . $row->sex . "</Sex>";
			$img = explode(".", $row->image);
			if (count($img) == 2) {
				$xml .= "<IMG>" . $img[0] . "_small." . $img[1] . "</IMG>";
			}
			$xml .= "<Phone>" . $row->phone . "</Phone>";
			$xml .= "<AddPhoneNumber>" . $row->addNumber . "</AddPhoneNumber>";
			$xml .= "<PhoneAppointment>" . $row->phone_appointment . "</PhoneAppointment>";
			$xml .= "<CategoryId>" . $row->category_id . "</CategoryId>";
			$xml .= "<DegreeId>" . $row->degree_id . "</DegreeId>";
			$xml .= "<RankId>" . $row->rank_id . "</RankId>";

			$xml .= "<Education><![CDATA[" . $row->text_education . "]]></Education>";
			$xml .= "<Association><![CDATA[" . $row->text_association . "]]></Association>";
			$xml .= "<Degree><![CDATA[" . $row->text_degree . "]]></Degree>";
			$xml .= "<ExperienceYear>" . $row->experience_year . "</ExperienceYear>";

			$xml .= "<Price>" . $row->price . "</Price>";
			$xml .= "<SpecialPrice>" . $row->special_price . "</SpecialPrice>";
			$xml .= "<Departure>" . $row->departure . "</Departure>";
			$xml .= "<Email>" . $row->email . "</Email>";
			$xml .= "<Status>" . $row->status . "</Status>";

			$xml .= "<KidsReception>" . $row->kids_reception . "</KidsReception>";
			$xml .= "<KidsAgeFrom>" . $row->kids_age_from . "</KidsAgeFrom>";
			$xml .= "<KidsAgeTo>" . $row->kids_age_to . "</KidsAgeTo>";

			$xml .= "<Description><![CDATA[" . $row->description . "]]></Description>";
			$xml .= "<TextCommon><![CDATA[" . $row->text . "]]></TextCommon>";
			$xml .= "<TextSpec><![CDATA[" . $row->text_spec . "]]></TextSpec>";
			$xml .= "<TextAssoc><![CDATA[" . $row->text_association . "]]></TextAssoc>";
			$xml .= "<TextEdu><![CDATA[" . $row->text_education . "]]></TextEdu>";
			$xml .= "<TextDegree><![CDATA[" . $row->text_degree . "]]></TextDegree>";
			$xml .= "<TextCourse><![CDATA[" . $row->text_course . "]]></TextCourse>";
			$xml .= "<TextExperience><![CDATA[" . $row->text_experience . "]]></TextExperience>";

			$xml .= "<OperatorComment><![CDATA[" . $row->note . "]]></OperatorComment>";
			$xml .= "<OperatorOpenComment><![CDATA[" . $row->openNote . "]]></OperatorOpenComment>";

			$xml .= getSectorByDoctorIdXML($row->id);
			$xml .= getDoctorMetroListXML($row->id);
			$xml .= getEducationListXML($row->id);
			$xml .= getClinicListByDoctorIdXML($row->id);
			$xml .= "</Doctor>";
		}
	}

	return $xml;
}

function getSectorByDoctorIdXML($id)
{
	$xml = "";

	$id = intval($id);

	$sql = "SELECT
					t1.id, t1.name as title
				FROM sector t1, doctor_sector t2
				WHERE t2.sector_id = t1.id
					AND t2.doctor_id = $id";
	$result = query($sql);
	if (num_rows($result) > 0) {
		$xml .= "<SectorList>";
		while ($row = fetch_object($result)) {
			$xml .= "<Sector id=\"" . $row->id . "\">" . $row->title . "</Sector>";
		}
		$xml .= "</SectorList>";
	}

	return $xml;
}

function getSectorByDoctorId($id)
{
	$resultArr = array();

	$id = intval($id);

	$sql = "SELECT
                    t1.id, t1.name as title
                FROM sector t1, doctor_sector t2
                WHERE t2.sector_id = t1.id
                    AND t2.doctor_id = $id";
	$result = query($sql);
	if (num_rows($result) > 0) {
		$k = 0;
		while ($row = fetch_object($result)) {
			$resultArr[$k]['Id'] = $row->id;
			$resultArr[$k]['Name'] = $row->title;
			$k++;
		}
	}

	return $resultArr;
}

function getOpinionCountByDoctorId($id)
{
	$id = intval($id);

	$sql = "SELECT
					count( t1.id ) as cnt
				FROM doctor_opinion  t1
				WHERE t1.doctor_id = " . $id;
	//echo $sql;
	$result = query($sql);
	if (num_rows($result) == 1) {
		$row = fetch_object($result);
		return $row->cnt;
	}

	return '';
}

function getDoctorMetroListXML($id)
{
	$xml = "";

	$id = intval($id);

	$sql = "SELECT
					t1.id, t1.name
				FROM underground_station t1
				INNER JOIN underground_station_4_clinic t2 ON t2.undegraund_station_id=t1.id
				INNER JOIN doctor_4_clinic t3 ON t3.clinic_id=t2.clinic_id
				WHERE t3.doctor_id=" . $id;

	$result = query($sql);
	if (num_rows($result) > 0) {
		$xml .= "<MetroList>";
		while ($row = fetch_object($result)) {
			$xml .= "<Element id=\"" . $row->id . "\">" . $row->name . "</Element>";
		}
		$xml .= "</MetroList>";
	}

	return $xml;
}

function getDoctorMetroList($id)
{
	$resultArr = array();

	$id = intval($id);

	$sql = "SELECT
					t1.id, t1.name
				FROM underground_station t1
				INNER JOIN underground_station_4_clinic t2 ON t2.undegraund_station_id=t1.id
				INNER JOIN doctor_4_clinic t3 ON t3.clinic_id=t2.clinic_id
				WHERE t3.doctor_id=" . $id;
	$result = query($sql);
	if (num_rows($result) > 0) {
		$k = 0;
		while ($row = fetch_object($result)) {
			$resultArr[$k]['Id'] = $row->id;
			$resultArr[$k]['Name'] = $row->name;
			$k++;
		}
	}

	return $resultArr;
}

function getDegreeDictXML()
{
	$xml = "";

	$sql = "SELECT
					t1.degree_id as id, t1.title
				FROM degree_dict t1
				ORDER BY t1.degree_id";
	$result = query($sql);
	if (num_rows($result) > 0) {
		$xml .= "<DegreeDict>";
		while ($row = fetch_object($result)) {
			$xml .= "<Element id=\"" . $row->id . "\">" . $row->title . "</Element>";
		}
		$xml .= "</DegreeDict>";
	}

	return $xml;
}

function getCategoryDictXML()
{
	$xml = "";

	$sql = "SELECT
					t1.category_id as id, t1.title
				FROM category_dict t1
				ORDER BY t1.category_id";
	$result = query($sql);
	if (num_rows($result) > 0) {
		$xml .= "<CategoryDict>";
		while ($row = fetch_object($result)) {
			$xml .= "<Element id=\"" . $row->id . "\">" . $row->title . "</Element>";
		}
		$xml .= "</CategoryDict>";
	}

	return $xml;
}

function getRankDictXML()
{
	$xml = "";

	$sql = "SELECT
					t1.rank_id as id, t1.title
				FROM rank_dict t1
				ORDER BY t1.rank_id";
	$result = query($sql);
	if (num_rows($result) > 0) {
		$xml .= "<RankDict>";
		while ($row = fetch_object($result)) {
			$xml .= "<Element id=\"" . $row->id . "\">" . $row->title . "</Element>";
		}
		$xml .= "</RankDict>";
	}

	return $xml;
}

function getEducationListXML($id)
{
	$xml = "";

	$sql = "SELECT
					t1.education_id as id, t1.year, 
					t2.title, t2.type
				FROM education_4_doctor t1, education_dict t2
				WHERE
					t1.education_id = t2.education_id
					AND t1.doctor_id = " . $id . "
				ORDER BY t1.year";
	$result = query($sql);
	if (num_rows($result) > 0) {
		$xml .= "<EducationList>";
		while ($row = fetch_object($result)) {
			$xml .=
				"<Element id=\"" .
				$row->id .
				"\"  year=\"" .
				$row->year .
				"\"  type=\"" .
				$row->type .
				"\" typeCh=\"" .
				typeCh($row->type) .
				"\">" .
				$row->title .
				"</Element>";
		}
		$xml .= "</EducationList>";
	}

	return $xml;
}

function typeCh($type)
{
	$str_type = "";

	switch ($type) {
		case 'university' :
		{
			$str_type = "В";
			$str_type_name = 'ВУЗ';
		}
			break;
		case 'college' :
		{
			$str_type = "К";
			$str_type_name = 'Колледж';
		}
			break;
		case 'traineeship' :
		{
			$str_type = "О";
			$str_type_name = 'Ординатура';
		}
			break;
		case 'graduate' :
		{
			$str_type = "А";
			$str_type_name = 'Аспирантура';
		}
			break;
		case 'internship' :
		{
			$str_type = "И";
			$str_type_name = 'Интернатура';
		}
			break;
		default :
			$str_type = "-";
	}
	return $str_type;

}

function getAlias4Doctor($str)
{
	$str = trim($str);
	if (!empty($str)) {
		$aliasArr = explode(" ", $str);

		$alias = translit($aliasArr[0]);
		if (isset($aliasArr[1])) {
			$alias .= "_" . translit($aliasArr[1]);
		}

		//echo getAliasCount($alias);
		//$alias = ereg_replace("^[a-zA-Z_]$",'',$alias);
		$alias = preg_replace("/^[\W]/", "", $alias);
		$alias = rtrim($alias, '+');
		$aliasNew = $alias;

		$i = 1;
		while (getAliasCount($aliasNew) != 0) {
			$aliasNew = $alias . "_" . $i;
			$i++;
		}

		return $aliasNew;
	} else {
		return;
	}
}

function getAliasCount($alias)
{
	$cnt = 0;

	$sql = "SELECT COUNT(rewrite_name) AS cnt FROM `doctor` WHERE LOWER(rewrite_name) = LOWER('" . $alias . "')";
	//echo $sql;
	$result = query($sql);
	$row = fetch_object($result);
	$cnt = $row->cnt;

	return $cnt;
}

function getNextPhoneNumber()
{
	$phNumber = DeltaAN;

	$sql = "SELECT max(addNumber)+1 AS cnt FROM `doctor`";
	//echo $sql;
	$result = query($sql);
	$row = fetch_object($result);
	if (!empty($row->cnt)) {
		$phNumber = $row->cnt;
	}

	return $phNumber;
}

function getOpinionCount($doctorId, $type = 0)
{
	$id = intval($doctorId);
	$countOpinion = 0;

	//$rating = array ($row -> rating_qualification, $row -> rating_attention, $row -> rating_room );
	//$rating = opinionColor( $ratingArray );

	if ($type == 0) {
		$sql = "SELECT
						count( t1.id ) as cnt
					FROM doctor_opinion  t1
					WHERE t1.doctor_id = " . $id . "
						AND t1.origin <> 'editor'
						AND NOT (t1.author = 'gues' AND t1.status = 'hidden')
						AND t1.status <> 'disable'";
		//echo $sql;
		$result = query($sql);
		if (num_rows($result) == 1) {
			$row = fetch_object($result);
			$countOpinion = $row->cnt;
		}
	} else {
		if ($type == 1) {
			$sql = "SELECT
						count( t1.id ) as cnt
					FROM doctor_opinion  t1
					WHERE t1.doctor_id = " . $id . "
						AND t1.origin <> 'editor'
						AND NOT (t1.author = 'gues' AND t1.status = 'hidden')
						AND t1.rating_color = 1
						AND t1.status <> 'disable'";
			//echo $sql;
			$result = query($sql);
			$countOpinion_1 = 0;
			if (num_rows($result) == 1) {
				$row = fetch_object($result);
				$countOpinion_1 = $row->cnt;
			}

			$sql = "SELECT
						count( t1.id ) as cnt
					FROM doctor_opinion  t1
					WHERE t1.doctor_id = " . $id . "
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
				$countOpinion_2 = $row->cnt;
			}
			$countOpinion = $countOpinion_1 + $countOpinion_2;

		} else {
			if ($type == -1) {
				$sql = "SELECT
						count( t1.id ) as cnt
					FROM doctor_opinion  t1
					WHERE t1.doctor_id = " . $id . "
						AND t1.origin <> 'editor'
						AND NOT (t1.author = 'gues' AND t1.status = 'hidden')
						AND t1.rating_color = -1
						AND t1.status <> 'disable'";
				//echo $sql;
				$result = query($sql);
				$countOpinion_1 = 0;
				if (num_rows($result) == 1) {
					$row = fetch_object($result);
					$countOpinion_1 = $row->cnt;
				}

				$sql = "SELECT
						count( t1.id ) as cnt
					FROM doctor_opinion  t1
					WHERE t1.doctor_id = " . $id . "
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
					$countOpinion_2 = $row->cnt;
				}
				$countOpinion = $countOpinion_1 + $countOpinion_2;
			}
		}
	}

	return $countOpinion;
}

function getClinicPhonesXML($id)
{
	$xml = "";

	$id = intval($id);

	$sql = "SELECT
					t1.phone_id as id, t1.number_p, t1.label
				FROM clinic_phone t1
				WHERE 
					t1.clinic_id=$id";
	$result = query($sql);
	if (num_rows($result) > 0) {
		$xml .= "<PhoneList>";
		while ($row = fetch_object($result)) {
			$xml .= "<Element id=\"" . $row->id . "\">";
			$xml .= "<Phone>" . $row->number_p . "</Phone>";
			$xml .= "<PhoneFormat>" . formatPhone($row->number_p) . "</PhoneFormat>";
			$xml .= "<Label>" . $row->label . "</Label>";
			$xml .= "</Element>";
		}
		$xml .= "</PhoneList>";
	}

	return $xml;
}

function getClinicListByDoctorIdXML($id)
{
	$xml = "";

	$id = intval($id);

	if ($id > 0) {
		$sql = "SELECT
						t1.clinic_id as id,
						t2.name, t2.short_name
					FROM doctor_4_clinic t1, clinic t2
					WHERE 
						t1.clinic_id = t2.id
						AND
						t1.doctor_id = " . $id;
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

/*	******************** API *************		*/

function getDoctorListXML4API($params = array())
{
	$xml = "";
	$sqlAdd = "";
	$limit = "";
	$addJoin = "";
	$startPage = 1;
	$step = 100;
	$withPager = true;

	if (isset($params['city'])) {
		$sqlAdd = " t2.city_id = " . intval($params['city']) . " ";
	} else {
		$sqlAdd = " t2.city_id = 1 ";
	}

	$sqlAdd .= " AND t1.status = 3 "; // Только активные

	if (count($params) > 0) {
		if (isset($params['name']) && !empty ($params['name'])) {
			$sqlAdd .= " AND LOWER(t1.name) LIKE  '%" . strtolower($params['name']) . "%' ";
		}
		if (isset($params['alias']) && !empty ($params['alias'])) {
			$sqlAdd .= " AND LOWER(t1.rewrite_name) LIKE  '" . strtolower($params['alias']) . "' ";
		}
		if (isset($params['clinic']) && intval($params['clinic']) > 0) {
			if (isset($params['branch']) && intval($params['branch']) == 1) {
				$sqlAdd .=
					" AND (t1.clinic_id = " . $params['clinic'] . " OR t2.parent_clinic_id = " .
					$params['clinic'] .
					") ";
			} else {
				$sqlAdd .= " AND t1.clinic_id = " . $params['clinic'] . " ";
			}
		}
		if (isset($params['departure']) && intval($params['departure']) == 1) {
			$sqlAdd .= " AND t1.departure = 1 ";
		}
		if (isset($params['speciality']) && intval($params['speciality']) > 0) {
			$sqlAdd .= " AND t3.sector_id = " . intval($params['speciality']) . " ";
			$addJoin .= " LEFT JOIN doctor_sector t3 ON (t3.doctor_id = t1.id) ";
		}

		if (isset($params['id']) && !empty ($params['id'])) {
			$sqlAdd = " t1.id = '" . $params['id'] . "'";
		}

		if (isset($params['stations']) && count($params['stations']) > 0) {

			$params['stations'] = array_map(
				function ($v) {
					return (int)$v;
				},
				$params['stations']
			);

			$sqlAdd .= " t4.undegraund_station_id IN (" . implode(',', $params['stations']) . ")";
			$addJoin .= " LEFT JOIN underground_station_4_clinic t4 ON (t4.clinic_id = t2.id) ";
		}

		if (isset($params['limit'])) {
			$limit .= " LIMIT " . $params['limit'];
		}

	}

	$sql = "SELECT
					t1.id,  t1.name as FullName, t1.status, t1.phone, t1.image, t1.rewrite_name,
					t1.total_rating, t1.rating, t1.rating_opinion, 
					t1.email, t1.sex, t1.price, t1.special_price,
					DATE_FORMAT( t1.created,'%d.%m.%Y') AS crDate,
					t1.note, t1.addNumber, t1.text, t1.experience_year, t1.departure,
					t1.category_id, t1.degree_id, t1.rank_id,
					t2.name as Clinic, t2.id as clinicId
				FROM doctor  t1
				LEFT JOIN doctor_4_clinic d4c ON (d4c.doctor_id=t1.id and d4c.type = " . DoctorClinicModel::TYPE_DOCTOR . "
				LEFT JOIN clinic t2 ON (d4c.clinic_id = t2.id)" . $addJoin . "
				WHERE " . $sqlAdd . "
				ORDER BY t1.created DESC, t1.id
				$limit";

	//echo $sql;

	$result = query($sql);
	if (num_rows($result) > 0) {
		$xml .= "<DoctorList>";
		while ($row = fetch_object($result)) {
			$xml .= "<Element>";
			$xml .= "<Id>" . $row->id . "</Id>";
			$xml .= "<Name>" . $row->FullName . "</Name>";
			if (!empty($row->rewrite_name)) {
				$xml .= "<Alias>" . $row->rewrite_name . "</Alias>";
			}
			$xml .= "<TotalRating>" . $row->total_rating . "</TotalRating>";
			$xml .= "<Rating>" . $row->rating . "</Rating>";
			$xml .= "<Price>" . $row->price . "</Price>";
			$xml .= "<SpecialPrice>" . $row->special_price . "</SpecialPrice>";
			$xml .= "<AddNumber>" . $row->addNumber . "</AddNumber>";
			$xml .= "<Sex>" . (($row->sex == 2) ? 'f' : 'm') . "</Sex>";
			$xml .= "<IMG_small>http://docdoc.ru/img/doctorsNew/" . $row->id . "_small.jpg</IMG_small>";
			$xml .= "<IMG_med>http://docdoc.ru/img/doctorsNew/" . $row->id . "_med.jpg</IMG_med>";
			/*
							$xml .= "<ClinicList>";
							$xml .= "<Clinic id=\"".$row -> clinicId."\">".$row -> Clinic."</Clinic>";
							$xml .= "</ClinicList>";
			*/
			$xml .= getSectorByDoctorIdXML($row->id);
			$xml .= "<OpinionCount>" . getOpinionCountByDoctorId($row->id) . "</OpinionCount>";
			$xml .= getDoctorMetroListXML($row->id);
			$xml .= "<TextAbout><![CDATA[" . checkField($row->text, "t", '') . "]]></TextAbout>";
			$xml .= "<ExperienceYear>" . $row->experience_year . "</ExperienceYear>";
			$xml .= "<Departure>" . $row->departure . "</Departure>";
			$xml .= "<CategoryId>" . $row->category_id . "</CategoryId>";
			$xml .= "<DegreeId>" . $row->degree_id . "</DegreeId>";
			$xml .= "<RankId>" . $row->rank_id . "</RankId>";
			$xml .= "</Element>";
		}
		$xml .= "</DoctorList>";
	}
	return $xml;
}

function getDoctorListJSON4API($params = array())
{
	$xml = "";
	$sqlAdd = "";
	$addJoin = "";
	$startPage = 1;
	$step = 100;
	$withPager = true;

	if (isset($params['city'])) {
		$sqlAdd = " t2.city_id = " . intval($params['city']) . " ";
	} else {
		$sqlAdd = " t2.city_id = 1 ";
	}

	$sqlAdd .= " AND t1.status = 3 "; // Только активные

	if (count($params) > 0) {
		if (isset($params['name']) && !empty ($params['name'])) {
			$sqlAdd .= " AND LOWER(t1.name) LIKE  '%" . strtolower($params['name']) . "%' ";
		}
		if (isset($params['alias']) && !empty ($params['alias'])) {
			$sqlAdd .= " AND LOWER(t1.rewrite_name) LIKE  '" . strtolower($params['alias']) . "' ";
		}
		if (isset($params['clinic']) && intval($params['clinic']) > 0) {
			if (isset($params['branch']) && intval($params['branch']) == 1) {
				$sqlAdd .=
					" AND (t1.clinic_id = " . $params['clinic'] . " OR t2.parent_clinic_id = " .
					$params['clinic'] .
					") ";
			} else {
				$sqlAdd .= " AND t1.clinic_id = " . $params['clinic'] . " ";
			}
		}
		if (isset($params['departure']) && intval($params['departure']) == 1) {
			$sqlAdd .= " AND t1.departure = 1 ";
		}
		if (isset($params['speciality']) && intval($params['speciality']) > 0) {
			$sqlAdd .= " AND t3.sector_id = " . intval($params['speciality']) . " ";
			$addJoin .= " LEFT JOIN doctor_sector t3 ON (t3.doctor_id = t1.id) ";
		}

		if (isset($params['id']) && !empty ($params['id'])) {
			$sqlAdd = " t1.id = '" . $params['id'] . "'";
		}

		if (isset($params['stations']) && count($params['stations']) > 0) {

			$params['stations'] = array_map(
				function ($v) {
					return (int)$v;
				},
				$params['stations']
			);

			$sqlAdd .= " t4.undegraund_station_id IN (" . implode(',', $params['stations']) . ")";
			$addJoin .= " LEFT JOIN underground_station_4_clinic t4 ON (t4.clinic_id = t2.id) ";
		}

	}

	$sql = "SELECT
                        t1.id,  t1.name AS FullName, t1.status, t1.phone, t1.image, t1.rewrite_name,
                        t1.total_rating, t1.rating, t1.rating_opinion,
                        t1.email, t1.sex, t1.price, t1.special_price,
                        DATE_FORMAT( t1.created,'%d.%m.%Y') AS crDate,
                        t1.note, t1.addNumber, t1.text, t1.experience_year, t1.departure,
                        t1.category_id, t1.degree_id, t1.rank_id,
                        cdict.title AS category, ddict.title AS degree, rdict.title AS rank,
                        t2.name as Clinic, t2.id as clinicId
                    FROM doctor  t1
                    LEFT JOIN doctor_4_clinic d4c ON (d4c.doctor_id=t1.id and d4c.type = " . DoctorClinicModel::TYPE_DOCTOR . ")
                    LEFT JOIN clinic t2 ON (d4c.clinic_id = t2.id)" . $addJoin . "
                    LEFT JOIN category_dict cdict ON cdict.category_id=t1.category_id
                    LEFT JOIN degree_dict ddict ON ddict.degree_id=t1.degree_id
                    LEFT JOIN rank_dict rdict ON rdict.rank_id=t1.rank_id
                    WHERE " . $sqlAdd . "
                    ORDER BY t1.created DESC, t1.id";

	//echo $sql;

	$resultSql = query($sql);
	if (num_rows($resultSql) > 0) {

		$k = 0;
		$result = array();
		while ($row = fetch_object($resultSql)) {
			$result[$k]['Id'] = $row->id;
			$result[$k]['Name'] = $row->FullName;
			if (!empty($row->rewrite_name)) {
				$result[$k]['Alias'] = $row->rewrite_name;
			}
			if (!empty($row->rating)) {
				$result[$k]['Rating'] = $row->rating;
			} else {
				$result[$k]['Rating'] = $row->total_rating;
			}

			$result[$k]['Price'] = $row->price;
			$result[$k]['SpecialPrice'] = $row->special_price;
			$result[$k]['AddNumber'] = $row->addNumber;
			$result[$k]['Sex'] = ($row->sex == 2) ? 'f' : 'm';
			$result[$k]['IMG_small'] = "http://docdoc.ru/img/doctorsNew/" . $row->id . "_small.jpg";
			$result[$k]['IMG_med'] = "http://docdoc.ru/img/doctorsNew/" . $row->id . "_med.jpg";
			$result[$k]['OpinionCount'] = getOpinionCountByDoctorId($row->id);
			$result[$k]['TextAbout'] = checkField($row->text, "t", '');
			$result[$k]['ExperienceYear'] = $row->experience_year;
			$result[$k]['Departure'] = $row->departure;
			$result[$k]['Category'] = $row->category;
			$result[$k]['Degree'] = $row->degree;
			$result[$k]['Rank'] = $row->rank;
			$result[$k]['Specialities'] = getSectorByDoctorId($row->id);
			$result[$k]['Stations'] = getDoctorMetroList($row->id);

			$k++;
		}
	}
	return array('DoctorList' => $result);
}

function getDoctorCountXML4API()
{
	$xml = "";
	$sqlAdd = "";
	$limit = "";
	$addJoin = "";
	$startPage = 1;
	$step = 100;
	$withPager = true;

	if (isset($params['city'])) {
		$sqlAdd = " t2.city_id = " . intval($params['city']) . " ";
	} else {
		$sqlAdd = " t2.city_id = 1 ";
	}

	$sqlAdd .= " AND t1.status = 3 "; // Только активные

	if (count($params) > 0) {

		if (isset($params['speciality']) && intval($params['speciality']) > 0) {
			$sqlAdd .= " AND t3.sector_id = " . intval($params['speciality']) . " ";
			$addJoin .= " LEFT JOIN doctor_sector t3 ON (t3.doctor_id = t1.id) ";
		}

		if (isset($params['stations']) && count($params['stations']) > 0) {

			$params['stations'] = array_map(
				function ($v) {
					return (int)$v;
				},
				$params['stations']
			);

			$sqlAdd .= " t4.undegraund_station_id IN (" . implode(',', $params['stations']) . ")";
			$addJoin .= " LEFT JOIN underground_station_4_clinic t4 ON (t4.clinic_id = t2.id) ";
		}

	}

	$sql = "SELECT COUNT(*) AS cnt
				FROM doctor t1
				LEFT JOIN doctor_4_clinic d4c ON (d4c.doctor_id=t1.id and d4c.type = " . DoctorClinicModel::TYPE_DOCTOR . ")
				LEFT JOIN clinic t2 ON (d4c.clinic_id = t2.id)" . $addJoin . "
				WHERE " . $sqlAdd;

	//echo $sql;

	$result = query($sql);
	if (num_rows($result) > 0) {
		$row = fetch_object($result);
		$xml .= "<DoctorSelected>" . $row->cnt . "</DoctorSelected>";
	}
	return $xml;
}

function getDoctorByIdXML4API($id = 0)
{
	$xml = "";

	$id = intval($id);

	if ($id > 0) {
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
						t1.note, t1.rewrite_name as alias,
						t2.name as clinic, t2.id as clinicId
					FROM doctor  t1
					LEFT JOIN clinic t2 ON (t1.clinic_id = t2.id)
					WHERE
						t1.id = $id";
		//echo $sql;
		$result = query($sql);
		if (num_rows($result) == 1) {
			$row = fetch_object($result);
			$xml .= "<Doctor>";
			$xml .= "<Id>" . $row->id . "</Id>";
			$xml .= "<Name>" . $row->name . "</Name>";
			if (!empty($row->alias)) {
				$alias = $row->alias;
			} else {
				$alias = getAlias4Doctor($row->name);
			}
			$xml .= "<Alias>" . $alias . "</Alias>";

			$xml .= (!empty($row->rating)) ? "<Rating>" . $row->rating . "</Rating>"
				: "<Rating>" . $row->total_rating . "</Rating>";
			$xml .= "<Sex>" . (($row->sex == 2) ? 'f' : 'm') . "</Sex>";
			/*
			$img = explode(".", $row -> image);
			if ( count($img) == 2) {
				$xml .= "<IMG>".$img[0]."_small.".$img[1]."</IMG>";
			}*/
			$xml .= "<IMG_small>http://docdoc.ru/img/doctorsNew/" . $row->id . "_small.jpg</IMG_small>";
			$xml .= "<IMG_med>http://docdoc.ru/img/doctorsNew/" . $row->id . "_med.jpg</IMG_med>";

			$xml .= "<AddPhoneNumber>" . $row->addNumber . "</AddPhoneNumber>";
			$xml .= "<CategoryId>" . $row->category_id . "</CategoryId>";
			$xml .= "<DegreeId>" . $row->degree_id . "</DegreeId>";
			$xml .= "<RankId>" . $row->rank_id . "</RankId>";

			$xml .= "<Education><![CDATA[" . checkField($row->text_education, "t", '') . "]]></Education>";
			$xml .= "<Association><![CDATA[" . checkField($row->text_association, "t", '') . "]]></Association>";
			$xml .= "<Degree><![CDATA[" . checkField($row->text_degree, "t", '') . "]]></Degree>";
			$xml .= "<ExperienceYear>" . $row->experience_year . "</ExperienceYear>";

			$xml .= "<Price>" . $row->price . "</Price>";
			$xml .= "<SpecialPrice>" . $row->special_price . "</SpecialPrice>";
			$xml .= "<Departure>" . $row->departure . "</Departure>";

			$xml .= "<Description><![CDATA[" . checkField($row->description, "t", '') . "]]></Description>";
			$xml .= "<TextCommon><![CDATA[" . checkField($row->text, "t", '') . "]]></TextCommon>";
			$xml .= "<TextSpec><![CDATA[" . checkField($row->text_spec, "t", '') . "]]></TextSpec>";
			$xml .= "<TextAssoc><![CDATA[" . checkField($row->text_association, "t", '') . "]]></TextAssoc>";
			$xml .= "<TextEdu><![CDATA[" . checkField($row->text_education, "t", '') . "]]></TextEdu>";
			$xml .= "<TextDegree><![CDATA[" . checkField($row->text_degree, "t", '') . "]]></TextDegree>";
			$xml .= "<TextCourse><![CDATA[" . checkField($row->text_course, "t", '') . "]]></TextCourse>";
			$xml .= "<TextExperience><![CDATA[" . checkField($row->text_experience, "t", '') . "]]></TextExperience>";

			$xml .= getSectorByDoctorIdXML($row->id);
			$xml .= getDoctorMetroListXML($row->id);
			$xml .= getEducationListXML($row->id);
			$xml .= "</Doctor>";
		}
	}

	return $xml;
}
