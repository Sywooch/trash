<?php

use dfs\docdoc\models\DoctorModel;

require_once dirname(__FILE__) . "/../../lib/php/user.class.php";
require_once dirname(__FILE__) . "/../../include/common.php";
require_once dirname(__FILE__) . "/../../lib/php/validate.php";
require_once dirname(__FILE__) . "/../../lib/php/rating.php";


$report = "";

$user = new user();
$user->checkRight4page(array('ADM', 'CNM', 'SOP', 'ACM'), 'simple');
$userId = $user->idUser;

$cityId = getCityId();


$id = (isset($_POST['id'])) ? checkField($_POST['id'], "i", 0) : '0';
$title = (isset($_POST['title'])) ? checkField($_POST['title'], "t", "") : '';
$alias = (isset($_POST['alias'])) ? checkField($_POST['alias'], "t", "") : '';
$sex = (isset($_POST['sex'])) ? checkField($_POST['sex'], "i", 0) : 0;
$email = (isset($_POST['email'])) ? checkField($_POST['email'], "t", "") : '';
$phone = (isset($_POST['phone'])) ? checkField($_POST['phone'], "t", "") : '';
$addPhone = (isset($_POST['addPhoneNumber'])) ? checkField($_POST['addPhoneNumber'], "i", "") : '';

$clinic_id = (isset($_POST['clinicId'])) ? checkField($_POST['clinicId'], "i", 0) : 0;

$metroList = (isset ($_POST['metro'])) ? rtrim(trim($_POST['metro']), ',') : '';
$metroList = (!empty($metroList)) ? explode(",", $metroList) : array();

$clinicList = (isset ($_POST['clinicId'])) ? $_POST['clinicId'] : array();

$status = (isset($_POST['status'])) ? checkField($_POST['status'], "i", 2) : 2;

$price = (isset($_POST['price'])) ? checkField($_POST['price'], "f", 0) : 0;
$special_price = (isset($_POST['special_price'])) ? checkField($_POST['special_price'], "f", 0) : 0;
$departure = (isset($_POST['departure'])) ? checkField($_POST['departure'], "i", 0) : 0;

$kids_reception = (isset($_POST['kids_reception'])) ? checkField($_POST['kids_reception'], "i", 0) : 0;
$kids_age_from = (isset($_POST['kids_age_from'])) ? checkField($_POST['kids_age_from'], "i", 0) : 0;
$kids_age_to = (isset($_POST['kids_age_to'])) ? checkField($_POST['kids_age_to'], "i", 0) : 0;
if ($kids_reception !== 0) {
	$kids_reception = 1;
}
if ($kids_age_from < 0 || $kids_age_from > 18) {
	$kids_age_from = 0;
}
if ($kids_age_to < 0 || $kids_age_to > 18) {
	$kids_age_to = 18;
}
if ($kids_age_to > 0 && $kids_age_from > $kids_age_to) {
	$kids_age_from = $kids_age_to;
}

$rating = (isset($_POST['rating'])) ? checkField($_POST['rating'], "f", 0) : 0;
$rating_edu = (isset($_POST['rating_edu'])) ? checkField($_POST['rating_edu'], "f", 0) : 0;
$rating_ext_edu = (isset($_POST['rating_ext_edu'])) ? checkField($_POST['rating_ext_edu'], "f", 0) : 0;
$rating_exp = (isset($_POST['rating_exp'])) ? checkField($_POST['rating_exp'], "f", 0) : 0;
$rating_ac_deg = (isset($_POST['rating_ac_deg'])) ? checkField($_POST['rating_ac_deg'], "f", 0) : 0;
$rating_cln = (isset($_POST['rating_cln'])) ? checkField($_POST['rating_cln'], "f", 0) : 0;
$rating_opin = (isset($_POST['rating_opin'])) ? checkField($_POST['rating_opin'], "f", 0) : 0;

$sectorList = (isset ($_POST['sector'])) ? rtrim(trim($_POST['sector']), ',') : '';
$sectorList = (!empty($sectorList)) ? explode(",", $sectorList) : array();

$categoryId = (isset($_POST['categoryId'])) ? checkField($_POST['categoryId'], "i", 0) : 0;
$degreeId = (isset($_POST['degreeId'])) ? checkField($_POST['degreeId'], "i", 0) : 0;
$rankId = (isset($_POST['rankId'])) ? checkField($_POST['rankId'], "i", 0) : 0;
$expYear = (isset($_POST['expYear'])) ? checkField($_POST['expYear'], "i", 0) : 0;

$degree = (isset($_POST['degree'])) ? checkField($_POST['degree'], "t", "") : '';
$textSpec = (isset($_POST['textSpec'])) ? checkField($_POST['textSpec'], "h", "") : '';
$textAssoc = (isset($_POST['textAssoc'])) ? checkField($_POST['textAssoc'], "h", "") : '';
$textCource = (isset($_POST['textCource'])) ? checkField($_POST['textCource'], "t", "") : '';
$textCommon = (isset($_POST['textCommon'])) ? checkField($_POST['textCommon'], "t", "") : '';
$textExperience = (isset($_POST['textExperience'])) ? checkField($_POST['textExperience'], "t", "") : '';


$educationId = (isset($_POST['educationId'])) ? $_POST['educationId'] : array();
$educationYear = (isset($_POST['educationYear'])) ? $_POST['educationYear'] : array();

$comment = (isset($_POST['operatorComment'])) ? checkField($_POST['operatorComment'], "h", "") : '';
$openNote = (isset($_POST['openNote'])) ? checkField($_POST['openNote'], "h", "") : '';


/*	Валидация	*/
if (empty($title)) {
	echo htmlspecialchars(json_encode(array('error' => 'Необходимо ввести имя врача')), ENT_NOQUOTES);
	exit;
}
if ($clinic_id <= 0) {
	echo htmlspecialchars(json_encode(array('error' => 'Необходимо выбрать клинику')), ENT_NOQUOTES);
	exit;
}
if (!empty($email) && !checkEmail($email)) {
	echo htmlspecialchars(json_encode(array('error' => 'Ошибки в поле Email')), ENT_NOQUOTES);
	exit;
}
if ($expYear > 0 && !checkYear($expYear)) {
	echo htmlspecialchars(json_encode(array('error' => 'Ошибки в поле Год начала практики')), ENT_NOQUOTES);
	exit;
}
if ($rating > 5) {
	echo htmlspecialchars(json_encode(array('error' => 'Рейтинг не может быть больше 5')), ENT_NOQUOTES);
	exit;
}

if ($addPhone > 0 && $addPhone < 9000) {
	echo htmlspecialchars(json_encode(array('error' => 'Ошибочный добавочный номер')), ENT_NOQUOTES);
	exit;
}


$textSpec = clearStyle($textSpec);

$result = query("START TRANSACTION");
$sqlAdd = "";
if ($id > 0) {
	if (!empty($alias)) {
		$sqlAdd .= " rewrite_name = '" . $alias . "', ";
	}
	if (!empty($addPhone) && $addPhone >= 9000) {
		$sqlAdd .= " addNumber = '" . $addPhone . "', ";
	} else {
		$sqlAdd .= " addNumber = NULL, ";
	}

	if (empty($special_price)) {
		$sqlAdd .= " special_price = NULL, ";
	} else {
		$sqlAdd .= " special_price = '" . $special_price . "', ";
	}

	if (empty($price)) {
		$sqlAdd .= " price = NULL, ";
	} else {
		$sqlAdd .= " price = '" . $price . "', ";
	}

	$sql = "UPDATE `doctor` SET
					name = '" . $title . "',
					sex = '" . $sex . "',
					email = '" . $email . "',
					phone = '" . $phone . "',
					price = '" . $price . "',
					departure = '" . $departure . "',
					rating = '" . $rating . "',
					kids_reception = '" . $kids_reception . "',
					kids_age_from = '" . $kids_age_from . "',
					kids_age_to = '" . $kids_age_to . "',
					rating_education = '" . $rating_edu . "',
					rating_ext_education = '" . $rating_ext_edu . "',
					rating_experience = '" . $rating_exp . "',
					rating_academic_degree = '" . $rating_ac_deg . "',
					rating_clinic = '" . $rating_cln . "',
					rating_opinion = '" . $rating_opin . "',
					
					category_id = '" . $categoryId . "',
					degree_id = '" . $degreeId . "',
					rank_id = '" . $rankId . "',
					experience_year = '" . $expYear . "',
					
					text_degree = '" . $degree . "',
					text_association = '" . $textAssoc . "',
					text_spec = '" . $textSpec . "',
					text_course = '" . $textCource . "',
					text = '" . $textCommon . "',
					text_experience = '" . $textExperience . "',
					
					note = '" . $comment . "',
					openNote = '" . $openNote . "',
					" . $sqlAdd . "
					status = '" . $status . "'
				WHERE id=" . $id;
	// echo $sql;
	queryJS($sql, 'Ошибка изменения данных');

	$msg = "Модификация данных врача id = $id";
	$log = new logger();
	$log->setLog($user->idUser, 'U_DOC', $msg);

} else {
	/*		Новая запись	*/
	$sql = "INSERT INTO `doctor` SET
					name = '" . $title . "',
					sex = '" . $sex . "',
					email = '" . $email . "',
					phone = '" . $phone . "',
					price = '" . $price . "',
					departure = '" . $departure . "',
					kids_reception = '" . $kids_reception . "',
					kids_age_from = '" . $kids_age_from . "',
					kids_age_to = '" . $kids_age_to . "',
					rating = '" . $rating . "',
					rating_education = '" . $rating_edu . "',
					rating_ext_education = '" . $rating_ext_edu . "',
					rating_experience = '" . $rating_exp . "',
					rating_academic_degree = '" . $rating_ac_deg . "',
					rating_clinic = '" . $rating_cln . "',
					rating_opinion = '" . $rating_opin . "',
					
					category_id = '" . $categoryId . "',
					degree_id = '" . $degreeId . "',
					rank_id = '" . $rankId . "',
					experience_year = '" . $expYear . "',
					
					text_degree = '" . $degree . "',
					text_association = '" . $textAssoc . "',
					text_spec = '" . $textSpec . "',
					text_course = '" . $textCource . "',
					text = '" . $textCommon . "',
					text_experience = '" . $textExperience . "',
					
					note = '" . $comment . "',
					openNote = '" . $openNote . "',
					" . $sqlAdd . "
					status = '" . $status . "'";
	//echo $sql;
	queryJS($sql, 'Ошибка добавления данных');
	$id = legacy_insert_id();


	$msg = "Заведение врача id = $id";
	$log = new logger();
	$log->setLog($user->idUser, 'C_DOC', $msg);
}

setClinic($id, $clinicList);
setSector($id, $sectorList);
setEducation($id, $educationId, $educationYear);

$result = query("commit");

// Сохраняем итоговые рейтинги для врача
$doctorModel = DoctorModel::model()->findByPk($id);
if (is_null($doctorModel)) {
	echo htmlspecialchars(json_encode(array('error' => 'Не удалось найти врача')), ENT_NOQUOTES);
	exit;
}
$doctorModel->save(false);

echo htmlspecialchars(json_encode(array('status' => 'success', 'id' => $id)), ENT_NOQUOTES);

function setClinic($doctorId, $clinicList = array())
{
	if (count($clinicList) > 0 && $doctorId > 0) {
		if ($doctor = \dfs\docdoc\models\DoctorModel::model()->findByPk($doctorId)) {
			$doctor->setClinics($clinicList);
		}
	}
}

function setSector($id, $sectorList = array())
{
	$id = intval($id);

	if ($id > 0 && count($sectorList) > 0) {
		$sql = "DELETE FROM doctor_sector WHERE doctor_id = $id ";
		queryJS($sql, 'Ошибка удаления специальности');

		foreach ($sectorList as $key => $data) {
			$sql = "SELECT id FROM sector WHERE LOWER(TRIM(name)) LIKE LOWER('" . trim($data) . "') LIMIT 1";
			$result = query($sql);
			if (num_rows($result)) {
				$row = fetch_object($result);
				$sql = "INSERT INTO doctor_sector SET
								doctor_id = $id,
								sector_id = " . $row->id;
				queryJS($sql, 'Ошибка добавления станции специальности: ' . $data);
			} else {
				echo htmlspecialchars(json_encode(array('error' => 'Такой специальности не существует, выберите другую')), ENT_NOQUOTES);
				exit();
			}
		}

	}

}


function setEducation($id, $educationId = array(), $educationYear = array())
{
	$id = intval($id);

	if ($id > 0) {
		$sql = "DELETE FROM education_4_doctor WHERE doctor_id = $id ";
		queryJS($sql, 'Ошибка удаления образования');
	}
	if ($id > 0 && count($educationId) > 0) {


		foreach ($educationId as $key => $data) {
			$sql = "INSERT INTO education_4_doctor SET
							doctor_id = $id,
							education_id = " . intval($data);
			if (isset($educationYear [$key]) && !empty($educationYear [$key])) {
				$sql .= ", year = '" . trim($educationYear [$key]) . "'";
			}
			queryJS($sql, 'Ошибка добавления образования: ' . $data);
		}

	}

}
