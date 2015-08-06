<?php
use dfs\docdoc\models\DoctorClinicModel;

require_once dirname(__FILE__) . "/../../lib/php/user.class.php";
require_once dirname(__FILE__) . "/../../include/common.php";
require_once dirname(__FILE__) . "/../../lib/php/validate.php";
require_once dirname(__FILE__) . "/../php/requestAcionLib.php";


$report = "";

$user = new user();
$user->checkRight4page(array('ADM', 'OPR', 'SOP', 'LIS'), 'simple');
$userId = $user->idUser;


$id = (isset($_POST['id'])) ? checkField($_POST['id'], "i", 0) : '0';
$name = (isset($_POST['doctorName'])) ? checkField($_POST['doctorName'], "t", "") : '';
$sectorId = (isset($_POST['anotherSectorId'])) ? checkField($_POST['anotherSectorId'], "i", 0) : 0;
$clinicId = (isset($_POST['addDoctorClinicId'])) ? checkField($_POST['addDoctorClinicId'], "i", "") : "";
$comment = (isset($_POST['commentNewDoctor'])) ? checkField($_POST['commentNewDoctor'], "t", "") : '';

/*	Валидация	*/
if ($id <= 0) {
	setExeption("Не передан идентификатор запроса");
}
if (empty($clinicId)) {
	setExeption("Не передан идентификатор клиники");
}


$result = query("START TRANSACTION");
$sql = "";
if ($id > 0) {
	$sql = "INSERT INTO doctor SET name = '" . $name . "', clinic_id='" . $clinicId . "', note = '" . $comment . "', status = 7";
	queryJS($sql, 'Ошибка заведения врача');
	$doctor_id = legacy_insert_id();

	if (!empty($sectorId)) {
		$sql = "INSERT INTO doctor_sector SET sector_id='" . $sectorId . "',doctor_id= '" . $doctor_id . "'";
		queryJS($sql, 'Ошибка заведения специальности врача');
	}

	$sql = "INSERT INTO doctor_4_clinic SET doctor_id= '" . $doctor_id . "', clinic_id='" . $clinicId . "', type=" . DoctorClinicModel::TYPE_DOCTOR;
	queryJS($sql, 'Ошибка заведения врача в клинику');

	$sql = "UPDATE `request` SET req_doctor_id = '" . $doctor_id . "' WHERE req_id=" . $id;
	queryJS($sql, 'Ошибка изменения врача в запросе');


	$txt = "Добавлен новый врач:" . $name . "/" . $doctor_id . "";
	saveLog($id, $txt, $userId, 3, false);

} else {
	echo htmlspecialchars(json_encode(array('error' => 'Не передан идентификатор')), ENT_NOQUOTES);
	exit;
}


$result = query("commit");

echo htmlspecialchars(json_encode(array('status' => 'success', 'id' => $doctor_id)), ENT_NOQUOTES);


function setExeption($mess)
{
	echo htmlspecialchars(json_encode(array('error' => $mess)), ENT_NOQUOTES);
	exit;
}
