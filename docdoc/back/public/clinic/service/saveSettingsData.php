<?php
use dfs\docdoc\models\ClinicModel;
use dfs\docdoc\models\DoctorClinicModel;

require_once dirname(__FILE__) . "/../../include/common.php";
require_once dirname(__FILE__) . "/../php/clinicLib.php";
require_once dirname(__FILE__) . "/../../lib/php/rating.php";


$report = "";

$user = new user();
$user ->checkRight4page(array('ADM', 'CNM', 'SOP', 'ACM'), 'simple');
$userId = $user ->idUser;

$id = (isset($_POST['id'])) ? checkField($_POST['id'], "i", 0) : '0';
$parentId = (isset($_POST['parentId'])) ? checkField($_POST['parentId'], "i", 0) : 0;
$yaAPI = (isset($_POST['yaAPI_Show'])) ? checkField($_POST['yaAPI_Show'], "e", 'no', false, array('yes', 'no')) : 'no';
$scheduleShow = (isset($_POST['scheduleShow'])) ? checkField($_POST['scheduleShow'], "e", 'no', false, array('enable', 'disable')) : 'disable';
$scheduleForDoctors = (isset($_POST['scheduleForDoctors'])) ? checkField($_POST['scheduleForDoctors'], "i", 0) : 0;
$sendSMS = (isset($_POST['sendSMS'])) ? checkField($_POST['sendSMS'], "e", 'no', false, array('yes', 'no')) : 'no';
$resultSetSortPosition = (isset($_POST['resultSetSortPosition'])) ? checkField($_POST['resultSetSortPosition'], "i", 99) : 99;

$scheduleWkd = (isset($_POST['wkDay'])) ? $_POST['wkDay'] : array();
$scheduleFrom = (isset($_POST['wkDay_From'])) ? $_POST['wkDay_From'] : array();
$scheduleTill = (isset($_POST['wkDay_Till'])) ? $_POST['wkDay_Till'] : array();

$rating = (isset($_POST['rating'])) ? $_POST['rating'] : array();

$showBilling = (isset($_POST['showBilling'])) ? checkField($_POST['showBilling'], "e", 'no', false, array('show', 'hide')) : 'hide';
$contractId = (isset($_POST['contractId'])) ? checkField($_POST['contractId'], "i", 0) : 0;
$diagContractId = (isset($_POST['diagContractId'])) ? checkField($_POST['diagContractId'], "i", 0) : 0;
$isDiagnostic = (isset($_POST['isDiagnostic'])) ? checkField($_POST['isDiagnostic'], "t", "no") : "no";

$price = (isset($_POST['price'])) ? $_POST['price'] : array();

$showInAdvertising = (isset($_POST['showInAdvertising'])) ? checkField($_POST['showInAdvertising'], "i", 0) : 0;

$emailReconciliation = (isset($_POST['email_reconciliation'])) ? checkField($_POST['email_reconciliation'], "t", "") : '';
$managerId = (isset($_POST['managerId'])) ? checkField($_POST['managerId'], "i", null) : null;


$discountOnlineDiag = isset($_POST['discountOnlineDiag']) ? checkField($_POST['discountOnlineDiag'], "i", 0) : 0;

$settings = array();
$settings['showBilling'] = $showBilling;
$settings['contractId'] = $contractId;

if ($id > 0) {

	// Сохранение рейтинга
	if (count($rating) == 4) {
		$i = 0;
		$newRatingArray = array();
		foreach ($rating as $line) {
			$newRatingArray[$i] = checkField($line, "f", 1);
			$i++;
		}
	}
	$rating4Clinic = setRating($id, $newRatingArray);

	foreach ($price as $cost => $data) {
		$settings["price$cost"] = checkField($data, "f", "");
	}
	setSettings($id, $settings);

	if ($isDiagnostic == 'yes') {
		$diagSettings = array();
		$diagSettings['showBilling'] = $showBilling;
		$diagSettings['contractId'] = $diagContractId;
		setDiagSettings($id, $diagSettings);
	}


	// Сохранение расписания работы клиники
	if (count($scheduleWkd) > 0 && count($scheduleFrom) > 0 && count($scheduleTill) > 0)
		$schedule = array();
	foreach ($scheduleWkd as $key => $data) {
		$weekday = checkField($data, "i", "");
		$timeFrom = checkField($scheduleFrom[$key], "time", "");
		$timeTill = checkField($scheduleTill[$key], "time", "");

		if ($timeFrom != '' && $timeTill != '' && $weekday >= 0 && $weekday <= 7) {
			array_push($schedule, array($weekday, $timeFrom, $timeTill));
		}
	}

	setSchedule($id, $schedule);

	// Сохранение прочих настроек
	$clinic = ClinicModel::model()->findByPk($id);
	if (is_null($clinic)) {
		echo htmlspecialchars(json_encode(array('error' => 'Не удалось найти клинику')), ENT_NOQUOTES);
		exit;
	}

	$clinic->sort4commerce = $resultSetSortPosition;
	$clinic->open_4_yandex = $yaAPI;
	$clinic->schedule_state = $scheduleShow;
	$clinic->scheduleForDoctors = $scheduleForDoctors;
	$clinic->sendSMS = $sendSMS;
	$clinic->show_in_advert = $showInAdvertising;
	$clinic->discount_online_diag = $discountOnlineDiag;
	$clinic->email_reconciliation = $emailReconciliation;
	$clinic->manager_id = $managerId;
	$notifyEmails = (array)Yii::app()->request->getPost('notify_emails', []);

	// Обновляем внутренние рейтинги для варчей, если имзенился рейтинг клиники
	if ($rating4Clinic <> $clinic->rating_total) {
		foreach ($clinic->doctors as $doctor) {
			$doctor->save(false);
		}
	}

	//ансечу пустые
	foreach($notifyEmails as $k => $v){
		if(!trim($v)){
			unset($notifyEmails[$k]);
		}
	}

	if($notifyEmails){
		$clinic->notify_emails = implode(',', $notifyEmails);
	} else {
		$clinic->notify_emails = null;
	}

	$notifyPhones = (array)Yii::app()->request->getPost('notify_phones', []);

	//ансечу пустые
	foreach($notifyPhones as $k => $v){
		if(!trim($v)){
			unset($notifyPhones[$k]);
		}
	}

	if($notifyPhones){
		$clinic->notify_phones = implode(',', $notifyPhones);
	} else {
		$clinic->notify_phones = null;
	}

	if (!$clinic->save()) {
		$errors = [];
		foreach ($clinic->getErrors() as $items) {
			foreach ($items as $e) {
				$errors[] = $e;
			}
		}
		echo htmlspecialchars(json_encode([
			'error' => 'Не удалось сохранить данные. ' . implode('. ', $errors),
		]), ENT_NOQUOTES);
		exit;
	}

	$msg = "Изменение настроек клиники id = $id";
	$log = new logger();
	$log ->setLog($user->idUser, 'U_CST', $msg);

} else {
	echo htmlspecialchars(json_encode(array('error' => 'Не передан идентификатор клиники')), ENT_NOQUOTES);
	exit;
}

echo htmlspecialchars(json_encode(array('status' => 'success', 'id' => $id, 'parentId' => $parentId)), ENT_NOQUOTES);


/**
 *
 * Сохранения рейтинга клиники
 */
function setRating($clinicId, $rating = array())
{
	$ratingCons = ratingDict();

	$rating1 = $rating[0];
	$rating2 = $rating[1];
	$rating3 = $rating[2];
	$rating4 = $rating[3];
	$ratingTotal = $rating1 * $ratingCons[0]['weight'] + $rating2 * $ratingCons[1]['weight'] + $rating3 * $ratingCons[2]['weight'] + $rating4 * $ratingCons[3]['weight'];

	$sql = "UPDATE clinic SET
						rating_1 = " . $rating1 . ",
						rating_2 = " . $rating2 . ",
						rating_3 = " . $rating3 . ",
						rating_4 = " . $rating4 . ",
						rating_total = '" . $ratingTotal . "'
					WHERE id =  " . $clinicId;
	//$log = new msgLog($sql);
	queryJS($sql, 'Ошибка изменения рейтинга клиники');
	return $ratingTotal;
}

/**
 *
 * Сохранения настроек клиники
 */
function setSettings($clinicId, $settings = array())
{
	$ratingCons = ratingDict();

	$sqlStr = "";
	if (isset($settings["contractId"])) $sqlStr .= emptyToNull($settings["contractId"], "contract_id");
	if (isset($settings["price1"])) $sqlStr .= emptyToNull($settings["price1"], "price_1", "0.00");
	if (isset($settings["price2"])) $sqlStr .= emptyToNull($settings["price2"], "price_2", "0.00");
	if (isset($settings["price3"])) $sqlStr .= emptyToNull($settings["price3"], "price_3", "0.00");


	$sql = "SELECT settings_id FROM clinic WHERE id = " . $clinicId;
	$result = query($sql);
	$settingsId = fetch_object($result)->settings_id;
	if (!empty($settingsId)) {
//			echo "settingsId =".$settingsId;
		$sql = "UPDATE clinic_settings SET
						" . $sqlStr . "
						show_billing = '" . $settings["showBilling"] . "'
					WHERE settings_id =  " . $settingsId;
		//echo $sql;
		queryJS($sql, "Ошибка изменения настроек клиники");
	} else {
		$sql = "INSERT INTO clinic_settings SET
						" . $sqlStr . "
						show_billing = '" . $settings["showBilling"] . "'";
		queryJS($sql, "Ошибка сохранения настроек клиники");
		$settingsId = legacy_insert_id();
		$sql = "UPDATE clinic SET settings_id = " . $settingsId . " WHERE id = " . $clinicId;
		queryJS($sql, "Ошибка изменения иденификатора настроек клиники");
	}

	return true;
}


/**
 *
 * Сохранения настроек клиники
 */
function setDiagSettings($clinicId, $settings = array())
{
	$ratingCons = ratingDict();

	$sqlStr = "";
	if (isset($settings["contractId"])) $sqlStr .= emptyToNull($settings["contractId"], "contract_id", 0);

	$sql = "SELECT diag_settings_id FROM clinic WHERE id = " . $clinicId;
	$result = query($sql);
	$settingsId = fetch_object($result)->diag_settings_id;
	if (!empty($settingsId)) {
//			echo "settingsId =".$settingsId;
		$sql = "UPDATE diagnostica_settings SET
						" . $sqlStr . "
						show_billing = '" . $settings["showBilling"] . "'
					WHERE settings_id =  " . $settingsId;
		//echo $sql;
		queryJS($sql, "Ошибка изменения настроек клиники");
	} else {
		$sql = "INSERT INTO diagnostica_settings SET
						" . $sqlStr . "
						show_billing = '" . $settings["showBilling"] . "'";
		queryJS($sql, "Ошибка сохранения настроек клиники");
		$settingsId = legacy_insert_id();
		$sql = "UPDATE clinic SET diag_settings_id = " . $settingsId . " WHERE id = " . $clinicId;
		queryJS($sql, "Ошибка изменения иденификатора настроек клиники");
	}

	return true;
}


/**
 *
 * Сохранение расписания работы клингики
 */
function setSchedule($clinicId, $schedule = array())
{
	$sql = "DELETE FROM clinic_schedule WHERE clinic_id = " . $clinicId;
	queryJS($sql, 'Ошибка удаления старого расписания клиники');

	foreach ($schedule as $line) {
		if (count($line) == 3) {
			$sql = "REPLACE INTO clinic_schedule SET
						clinic_id = " . $clinicId . ",
						week_day = " . $line[0] . ",
						start_time = '" . $line[1] . "',
						end_time = '" . $line[2] . "'";
			queryJS($sql, 'Ошибка добавления нового расписания клиники');
		}
	}


}
