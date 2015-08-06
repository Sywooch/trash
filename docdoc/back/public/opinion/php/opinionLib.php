<?php
use dfs\docdoc\models\DoctorClinicModel;

require_once dirname(__FILE__) . "/../../lib/php/validate.php";
require_once dirname(__FILE__) . "/../../lib/php/dateconvertionLib.php";
require_once dirname(__FILE__) . "/../../doctor/php/doctorLib.php";
require_once dirname(__FILE__) . "/../../lib/php/rating.php";

function getOpinionListXML($params = array(), $cityId = 1)
{
	$xml = "";
	$sqlAdd = " t4.city_id = " . $cityId . " ";
	$startPage = 1;
	$step = 50;
	$withPager = true;

	if (count($params) > 0) {

		if (isset($params['withPager'])) {
			$withPager = $params['withPager'];
		}

		if (isset($params['author']) && !empty($params['author'])) {
			$sqlAdd .= " AND t1.author = '" . $params['author'] . "' ";
		}
		if (isset($params['allow']) && $params['allow'] != '') {
			$sqlAdd .= " AND t1.allowed = " . $params['allow'] . " ";
		}
		if (isset($params['origin']) && $params['origin'] != '') {
			$sqlAdd .= " AND t1.origin = '" . $params['origin'] . "' ";
		}
		if (isset($params['crDateFrom']) && !empty ($params['crDateFrom'])) {
			$sqlAdd .= " AND date(t1.created) >= date('" . convertDate2DBformat($params['crDateFrom']) . "') ";
		}
		if (isset($params['crDateTill']) && !empty ($params['crDateTill'])) {
			$sqlAdd .= " AND date(t1.created) <= date('" . convertDate2DBformat($params['crDateTill']) . "') ";
		}
		if (isset($params['shDoctorId']) && !empty ($params['shDoctorId'])) {
			$sqlAdd .= " AND t1.doctor_id = " . $params['shDoctorId'] . " ";
		}
		if (isset($params['sector']) && $params['sector'] > 0) {
			$sqlAdd .= " AND sec4doc.sector_id = " . $params['sector'] . " ";
		}
		if (isset($params['rating_color'])) {
			$sqlAdd .= " AND t1.rating_color = " . $params['rating_color'] . " ";
		}

		if (isset($params['id']) && !empty ($params['id'])) {
			$sqlAdd = " t1.id = '" . $params['id'] . "'";
		}


		if (isset($params['sortBy'])) {
			switch ($params['sortBy']) {
				case 'crDate'        :
					$sortBy = " t1.created ";
					break;
				case 'pubDate'        :
					$sortBy = " t1.date_publication ";
					break;
				case 'doctor'        :
					$sortBy = " t2.name ";
					break;
				case 'name'        :
					$sortBy = " t1.name ";
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
			$sqlSort = " ORDER BY t1.created DESC, t1.id ";
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
				LEFT JOIN doctor_sector sec4doc  ON (sec4doc.doctor_id = t1.doctor_id)
				LEFT JOIN doctor_4_clinic t3 ON (t3.doctor_id=t2.id and t3.type = " . DoctorClinicModel::TYPE_DOCTOR . ")
				LEFT JOIN clinic t4  ON (t4.id = t3.clinic_id)
				WHERE " . $sqlAdd . "
				GROUP BY t1.id" . $sqlSort;


	//echo $sql;
	if (isset($params['step']) && intval($params['step']) > 0) $step = $params['step'];
	if (isset($params['startPage']) && intval($params['startPage']) > 0) $startPage = $params['startPage'];

	if ($withPager) {
		list($sql, $str) = pager($sql, $startPage, $step, "loglist"); // функция берется из файла pager.xsl с тремя параметрами. параметр article тут не нужен
		$xml .= $str;
		//echo $str."<br/>";
	}

	$result = query($sql);
	if (num_rows($result) > 0) {
		$xml .= "<OpinionList>";
		while ($row = fetch_object($result)) {
			$xml .= "<Element id=\"" . $row ->id . "\">";
			$xml .= "<DoctorId>" . $row ->doctor_id . "</DoctorId>";
			$xml .= "<RequestId>" . $row ->request_id . "</RequestId>";

			$xml .= "<Doctor id=\"" . $row ->doctor_id . "\">" . $row ->doctor . "</Doctor>";

			$xml .= "<CrDate>" . $row ->crDate . "</CrDate>";
			if ($row ->pubDate) {
				$xml .= "<PubDate>" . date("d.m.Y", $row ->pubDate) . "</PubDate>";
			}
			$xml .= "<Client><![CDATA[" . $row ->client . "]]></Client>";
			$xml .= "<Age>" . $row ->age . "</Age>";
			$xml .= "<Phone>" . formatPhone($row ->phone) . "</Phone>";
			if (!empty($row ->phone)) {
				$xml .= getRequestByPhoneXML($row ->phone);
			}

			$xml .= "<RatingQlf>" . $row ->rating_qualification . "</RatingQlf>";
			$xml .= "<RatingAtt>" . $row ->rating_attention . "</RatingAtt>";
			$xml .= "<RatingRoom>" . $row ->rating_room . "</RatingRoom>";
			$rating = array($row ->rating_qualification, $row ->rating_attention, $row ->rating_room);
			$xml .= "<RatingColor recomend=\"" . opinionColor($rating) . "\">" . $row ->rating_color . "</RatingColor>";

			$xml .= "<Note><![CDATA[" . $row ->text . "]]></Note>";

			$xml .= "<Allow>" . $row ->allowed . "</Allow>";
			$xml .= "<LKstatus>" . $row ->lk_status . "</LKstatus>";
			$xml .= "<IsFake>" . $row ->is_fake . "</IsFake>";
			$xml .= "<Author>" . $row ->author . "</Author>";
			$xml .= "<Status>" . $row ->status . "</Status>";
			$xml .= "<Origin>" . $row ->origin . "</Origin>";
			$xml .= "</Element>";
		}
		$xml .= "</OpinionList>";
	}
	return $xml;
}


function getOpinionByIdXML($id = 0)
{
	$xml = "";

	$id = intval($id);

	if ($id > 0) {
		$sql = "SELECT
					t1.id, t1.doctor_id, t1.request_id, 
					t1.name as client, t1.phone, t1.age, 
					t1.rating_qualification, t1.rating_attention, t1.rating_room, t1.rating_color, 
					t1.allowed, t1.lk_status, t1.is_fake, t1.author,
					DATE_FORMAT( t1.created,'%d.%m.%Y') AS crDate,
					t1.date_publication AS pubDate,
					t1.text, t1.operatorComment,
					t1.status, t1.origin,
					t2.name as doctor
				FROM doctor_opinion  t1
				LEFT JOIN doctor t2 ON (t2.id = t1.doctor_id)
				WHERE 
					t1.id = $id";
		//echo $sql;
		$result = query($sql);
		if (num_rows($result) == 1) {
			$row = fetch_object($result);
			$xml .= "<Opinion id=\"" . $row ->id . "\">";
			$xml .= "<Doctor id=\"" . $row ->doctor_id . "\" >" . $row ->doctor . "</Doctor>";
			if (!empty($row ->doctor_id)) {
				$xml .= getSectorByDoctorIdXML($row ->doctor_id);
			}
			$rquestId = (intval($row ->request_id) > 0) ? intval($row ->request_id) : '';
			$xml .= "<RequestId>" . $rquestId . "</RequestId>";

			$xml .= "<CrDate>" . $row ->crDate . "</CrDate>";
			if ($row ->pubDate) {
				$xml .= "<PubDate>" . date("d.m.Y", $row ->pubDate) . "</PubDate>";
			}
			$xml .= "<Client>" . $row ->client . "</Client>";
			$xml .= "<ClientName>" . $row ->client . "</ClientName>";

			$xml .= "<Age>" . $row ->age . "</Age>";
			$xml .= "<ClientPhone>" . formatPhone($row ->phone) . "</ClientPhone>";
			$xml .= "<Phone>" . formatPhone($row ->phone) . "</Phone>";
			if (!empty($row ->phone)) {
				$xml .= getRequestByPhoneXML($row ->phone);
			}


			$xml .= "<RatingQlf>" . $row ->rating_qualification . "</RatingQlf>";
			$xml .= "<RatingAtt>" . $row ->rating_attention . "</RatingAtt>";
			$xml .= "<RatingRoom>" . $row ->rating_room . "</RatingRoom>";
			$rating = array($row ->rating_qualification, $row ->rating_attention, $row ->rating_room);
			$xml .= "<RatingColor recomend=\"" . opinionColor($rating) . "\">" . $row ->rating_color . "</RatingColor>";

			$xml .= "<Note><![CDATA[" . $row ->text . "]]></Note>";
			$xml .= "<OperatorComment><![CDATA[" . $row ->operatorComment . "]]></OperatorComment>";

			$xml .= "<Allow>" . $row ->allowed . "</Allow>";
			$xml .= "<LKstatus>" . $row ->lk_status . "</LKstatus>";
			$xml .= "<IsFake>" . $row ->is_fake . "</IsFake>";
			$xml .= "<Status>" . $row ->status . "</Status>";
			$xml .= "<Origin>" . $row ->origin . "</Origin>";
			$xml .= "<Author>" . $row ->author . "</Author>";
			$xml .= "</Opinion>";
		}
	}

	return $xml;
}

/**
 * Получение xml со списком заявок, связанных с отзывом
 *
 * @param $phone
 *
 * @return string
 */
function getRequestByPhoneXML($phone)
{
	$xml = "";

	$phone = modifyPhone($phone);

	if (!empty($phone)) {
		$command = \Yii::app()
			->db
			->createCommand()
			->select("req_id")
			->from("request")
			->where(
				"client_phone = :phone
					OR add_client_phone = :phone",
				[":phone" => $phone]
			)
			->order("req_created DESC");
		$requests = $command->queryAll();
		$xml .= "<RequestList>";
		foreach ($requests as $request) {
			$xml .= "<Request id='" . $request['req_id'] . "'>" . $request['req_id'] . "</Request>";
		}
		$xml .= "</RequestList>";
	}

	return $xml;
}


function getAudio4RequestXML($id)
{
	$xml = "";

	$id = intval($id);

	if ($id > 0) {
		$sql = "SELECT
						t1.request_id as id, t1.record, 
						DATE_FORMAT( t1.crDate,'%d.%m.%Y') AS crDate,
						t1.duration, t1.comments as note
					FROM request_record t1
					WHERE 
						t1.request_id = " . $id . "
					ORDER BY record";
		//echo $sql."<br/>";
		$result = query($sql);
		if (num_rows($result) > 0) {
			$xml .= "<RcordList>";
			while ($row = fetch_object($result)) {
				$xml .= "<Record>";
				$xml .= "<Path>" . $row ->record . "</Path>";
				$xml .= "<Duration>" . $row ->duration . "</Duration>";
				$xml .= "<Note>" . $row ->note . "</Note>";
				$xml .= "<CrDate>" . $row ->crDate . "</CrDate>";
				$xml .= "</Record>";
			}
			$xml .= "</RcordList>";
		}
	}

	return $xml;

}


function getRatingColorDictXML()
{
	$xml = "";

	$xml .= "<RatingColorDict>";
	$xml .= "<Element id=\"1\">Положительный</Element>";
	$xml .= "<Element id=\"0\">Нейтрайльный</Element>";
	$xml .= "<Element id=\"-1\">Отрицательный</Element>";
	$xml .= "</RatingColorDict>";

	return $xml;

}


/*	******************** API *************		*/

function getOpinionListXML4API($params = array())
{
	$xml = "";
	$sqlAdd = "";
	$startPage = 1;
	$step = 50;

	$sqlAdd = " t1.allowed = 1 "; // Только активные

	if (isset($params['doctor']) && !empty ($params['doctor'])) {
		$sqlAdd .= " AND t1.doctor_id = " . intval($params['doctor']) . " ";

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
					LEFT JOIN clinic t3  ON (t3.id = t2.clinic_id)
					WHERE " . $sqlAdd . "
					ORDER BY t1.created DESC, t1.id";


		//echo $sql;

		$result = query($sql);
		if (num_rows($result) > 0) {
			$xml .= "<OpinionList>";
			while ($row = fetch_object($result)) {
				$xml .= "<Element>";
				$xml .= "<Id>" . $row ->id . "</Id>";
				if ($row ->pubDate) {
					$xml .= "<PubDate>" . date("d.m.Y", $row ->pubDate) . "</PubDate>";
				}
				$xml .= "<Client><![CDATA[" . checkField($row ->client, "t", '') . "]]></Client>";
				$xml .= "<RatingQlf>" . $row ->rating_qualification . "</RatingQlf>";
				$xml .= "<RatingAtt>" . $row ->rating_attention . "</RatingAtt>";
				$xml .= "<RatingRoom>" . $row ->rating_room . "</RatingRoom>";

				$xml .= "<Text><![CDATA[" . checkField($row ->text, "t", '') . "]]></Text>";
				$xml .= "</Element>";
			}
			$xml .= "</OpinionList>";
		}
	}
	return $xml;
}
