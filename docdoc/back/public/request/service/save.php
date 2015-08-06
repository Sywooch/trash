<?php

use dfs\docdoc\models\RequestModel;
use dfs\docdoc\models\RequestHistoryModel;
use dfs\docdoc\objects\Rejection;
use dfs\docdoc\models\DoctorClinicModel;


require_once dirname(__FILE__) . "/../../lib/php/user.class.php";
require_once dirname(__FILE__) . "/../../include/common.php";
require_once dirname(__FILE__) . "/../../lib/php/validate.php";
require_once dirname(__FILE__) . "/../php/requestAcionLib.php";
require_once dirname(__FILE__) . "/../../lib/php/request.class.php";
require_once dirname(__FILE__) . "/../php/requestLib.php";
require_once __DIR__ . "/../../lib/php/models/DocRequest.php";
require_once __DIR__ . "/../../lib/php/RequestInterface.php";


$report = "";

$user = new user();
$user->checkRight4page(array('ADM', 'OPR', 'SOP', 'LIS'), 'simple');
$userId = $user->idUser;


$id = (isset($_POST['requestId'])) ? checkField($_POST['requestId'], "i", 0) : '0';
$ownerId = (isset($_POST['owner'])) ? checkField($_POST['owner'], "i", $userId) : $userId;

$kind = isset($_POST['kind']) ? checkField($_POST['kind'], "i", 0) : 0;

$status = (isset($_POST['status'])) ? checkField($_POST['status'], "t", "") : '';
$statusSel = (isset($_POST['statusSel'])) ? checkField($_POST['statusSel'], "i", "") : '';
$chManual = (isset($_POST['chManual'])) ? checkField($_POST['chManual'], "i", 0) : 0;

$clinic = (isset($_POST['clinicId'])) ? checkField($_POST['clinicId'], "i", 0) : 0;
$chManualClinic = (isset($_POST['chManualClinic'])) ? checkField($_POST['chManualClinic'], "i", 0) : 0;

$clientId = (isset($_POST['clientId'])) ? checkField($_POST['clientId'], "i", 0) : 0;
$client = (isset($_POST['clientName'])) ? checkField($_POST['clientName'], "t", "") : '';
$clientPhone = (isset($_POST['clientPhone'])) ? checkField($_POST['clientPhone'], "t", "") : '';
$addClientPhone = isset($_POST['addClientPhone']) ? checkField($_POST['addClientPhone'], "t", "") : '';
$clientComment = (isset($_POST['clientComment'])) ? checkField($_POST['clientComment'], "t", "") : '';
$clientCity = (isset($_POST['clientCity'])) ? checkField($_POST['clientCity'], "i", 1) : 1;

$comment = (isset($_POST['requestComment'])) ? checkField($_POST['requestComment'], "t", "") : '';

$callDate = (isset($_POST['recallDate'])) ? checkField($_POST['recallDate'], "t", "") : '';
$callHour = (isset($_POST['recallHour'])) ? checkField($_POST['recallHour'], "t", "00") : '00';
$callMin = (isset($_POST['recallMin'])) ? checkField($_POST['recallMin'], "t", "00") : '00';

$apointmentDate = (isset($_POST['apointmentDate'])) ? checkField($_POST['apointmentDate'], "t", "") : '';
$apointmentHour = (isset($_POST['apointmentHour'])) ? checkField($_POST['apointmentHour'], "t", "00") : '00';
$apointmentMin = (isset($_POST['apointmentMin'])) ? checkField($_POST['apointmentMin'], "t", "00") : '00';
$appStatus = (isset($_POST['appointmentStatus'])) ? checkField($_POST['appointmentStatus'], "t", '') : '';
$isRejection = (isset($_POST['isRejection'])) ? checkField($_POST['isRejection'], "i", 0) : 0;
$rejectReason = (isset($_POST['rejectReason'])) ? checkField($_POST['rejectReason'], "i", 0) : 0;

$selectedDoctor = (isset($_POST['selectedDoctor'])) ? checkField($_POST['selectedDoctor'], "i", 0) : 0;
$shSectorId = (isset($_POST['shSectorId'])) ? checkField($_POST['shSectorId'], "i", 0) : 0;
$shClinicId = (isset($_POST['shClinicId'])) ? checkField($_POST['shClinicId'], "i", 0) : 0;
$req_status = (isset($_POST['req_status'])) ? checkField($_POST['req_status'], "i", 0) : 0;

$diagnostics	= isset($_POST['subdiagnostica']) ? $_POST['subdiagnostica'] : array();
$diagnosticsOther = isset($_POST['diagnosticaName']) ? checkField($_POST['diagnosticaName'], "t", "") : '';

$isTransfer = (isset($_POST['isTransfer'])) ? checkField($_POST['isTransfer'], "i", "") : "";
$isCallLater = isset($_POST['isCallLater']) ? checkField($_POST['isCallLater'], "i", 0) : 0;

$metroList = (isset ($_POST['shMetro'])) ? rtrim(trim($_POST['shMetro']), ',') : '';
$metroList = (!empty($metroList)) ? explode(",", $metroList) : array();
$shHome = (isset($_POST['shHome'])) ? checkField($_POST['shHome'], "i", 0) : 0;


$isOpinion = (isset ($_POST['isOpinion'])) ? $_POST['isOpinion'] : array();
$isAppointment = (isset ($_POST['isAppointment'])) ? $_POST['isAppointment'] : array();
$isVisit = (isset ($_POST['isVisit'])) ? $_POST['isVisit'] : array();

$typeView = isset($_POST['typeView']) ? $_POST['typeView'] : 'default';

$sqlAdd = "";


$requestModel = ($id > 0) ? RequestModel::model()->findByPk($id) : new RequestModel();
$requestModel->setScenario(RequestModel::SCENARIO_OPERATOR);

/*	Валидация	*/

$clinicToParam = 0;
if ($clinic > 0 && $chManualClinic > 0) {
	// если изменили клинику руками
	$clinicToParam = $clinic;
} else if ($shClinicId > 0) {
	// выбрали врача из поиска
	$clinicToParam = $shClinicId;
} else if ($clinic > 0) {
	// удалиди врача из поиска, клиника осталась
	$clinicToParam = $clinic;
}

$client = mb_convert_case($client, MB_CASE_TITLE, "UTF-8");
$phone = formatPhone4DB($clientPhone);
$addClientPhone = formatPhone4DB($addClientPhone);


$attr = array();
$attr['id_city'] = $clientCity;
$attr['clinic_id'] = $clinicToParam;
$attr['client_name'] = $client;
$attr['call_later_time'] = (!empty($callDate)) ? strtotime($callDate . " " . $callHour . ":" . $callMin) : "";
$attr['date_admission'] = (!empty($apointmentDate)) ? strtotime($apointmentDate . " " . $apointmentHour . ":" . $apointmentMin) : "";
$attr['appointment_status'] = $appStatus === 'yes' ? 1 : 0;
$attr['req_doctor_id'] = $selectedDoctor;
$attr['diagnostics_other'] = $diagnosticsOther;
$attr['is_transfer'] = $isTransfer;
$attr['req_departure'] = $shHome;
$attr['reject_reason'] = $rejectReason;
$attr['req_user_id'] = $ownerId;
$attr['client_phone'] = $phone;
$attr['add_client_phone'] = $addClientPhone;
$attr['call_later_time'] = $isCallLater ? time() : $attr['call_later_time'];
$attr['kind'] = $kind;
//в базе diagnostics_id is null нету, вместо  null, 0. делаю так же
$attr['diagnostics_id'] = intval(array_shift($diagnostics));

//не пускаю 0 в базу, хотя такие строки есть
$shSectorId = intval($shSectorId);
$shSectorId && $attr['req_sector_id'] = $shSectorId;

$interface = new RequestInterface($typeView);
if ($interface->isListener()) {
	$attr['is_transfer'] = 1;
	if (!empty($attr['call_later_time'])) {
		$attr['is_hot'] = 1;
		$attr['req_user_id'] = 0;
	}
}

if ($requestModel === null) {
	echo htmlspecialchars(json_encode(array('error' => "Не найдена заявка с ID={$id}")), ENT_NOQUOTES);
	exit;
}

$result = query("START TRANSACTION");

$requestModel->setAttributes($attr);


if ($isRejection) {
	if (!$rejectReason = $requestModel->setRejectStatus($rejectReason)) {
		modelError($requestModel);
	}
	if ($rejectReason == Rejection::REASON_NOT_COME) {
		$statusSel = RequestModel::STATUS_NOT_CAME;
		$appStatus = "";
	}
}

if (!$requestModel->getIsNewRecord()) {

	if (
		$requestModel->kind == RequestModel::KIND_DIAGNOSTICS
		&&
		$requestModel->diagnostics_id > 0
		&&
		$requestModel->clinic !== null
		&&
		$requestModel->source_type == RequestModel::SOURCE_PARTNER
	) {
		if (!$requestModel->clinic->hasDiagnostic($requestModel->diagnostics_id)) {
			$requestModel->addHistory('Клиника не платит за эту диагностику', RequestHistoryModel::LOG_TYPE_COMMENT);
		}
	}
} else {
	if ($interface->isListener()) {
		$requestModel->for_listener = 1;
		$requestModel->req_type = RequestModel::TYPE_CALL_TO_DOCTOR;
	}
	$requestModel->enter_point = RequestModel::ENTER_POINT_OPERATOR;
}

if (!$requestModel->save()) {
	modelError($requestModel);
}

//  ################   #########################	#########################
//  ################   #########################	#########################

//		Добавление врача в клинику

if ($clinic > 0 && $chManualClinic > 0 && $clinic != $shClinicId && $requestModel->req_doctor_id == $selectedDoctor) {
	setDoctorToAnotherClinic($clinic, $requestModel->req_doctor_id);
	$requestModel->addHistory("Изменена клиника у врача (" . $clinic . ", другой адрес приёма)", RequestHistoryModel::LOG_TYPE_CHANGE_STATUS);
}

//  ################   Изменение статусов #########################

if ($user->checkRight4userByCode(array('ADM', 'SOP')) && $chManual == 1) {
	$requestModel->saveStatus($statusSel);
} elseif ($appStatus === 'no') {
	$requestModel->saveStatus(RequestModel::STATUS_CAME_UNDEFINED);
} elseif (!$isRejection) {
	$statusParams = [];

	if(Yii::app()->request->getParam('multiply_create')){
		$statusParams['multiply_create'] = Yii::app()->request->getParam('multiply_create');
	}

	$reqStatus = $requestModel->getStatusForOperatorAction($statusParams);

	if ($reqStatus !== false) {
		$requestModel->saveStatus($reqStatus);
	} else {
		showError('Недостаточно прав для выполнения операции');
	}
}

setMetro($requestModel->req_id, $metroList);

$IsOpinionInRequest = false;
foreach ($requestModel->request_record as $r) {
	if ($r->isOpinion == 'yes') {
		$IsOpinionInRequest = true;
	}
}

/**
 * Отзыв в записях
 */
// Опредение названия файла записи
if (count($isOpinion) == 1) {
	foreach ($isOpinion as $key => $value)
		$opinionArray = explode("_", $key, 2);

	$pathRecord = trim($opinionArray[1], "'");
}

// Отзывы в записях
// Если отмечена запись и записи существуют
if (count($isOpinion) == 1 && count($requestModel->request_record) > 0) {
	$changeIsOpinion = false;
	foreach ($requestModel->request_record as $r) {
		if ($r->record == $pathRecord && $r->isOpinion == 'no') {
			setRecordOpinion($requestModel->req_id, $pathRecord);
		}
	}
} else if (count($isOpinion) == 0 && count($requestModel->request_record) > 0 && $IsOpinionInRequest) {
	// Если нет отмеченных записей, записи существуют и есть отмеченная запись, то удалить отметку
	deleteRecordOpinion($requestModel->req_id);
}


/**
 * Признак "пациент дошёл"
 */
foreach ($requestModel->request_record as $r) {
	if ($r->isVisit == 'yes') {
		if (!isset($isVisit[$r->record_id]) || empty($isVisit[$r->record_id]))
			$r->isVisit = 'no';
			$r->save();
			$requestModel->addHistory("Удалён признак пациент дошёл в аудиозаписи(" . $r->record . ")", RequestHistoryModel::LOG_TYPE_CHANGE_STATUS);
	} else {
		if (isset($isVisit[$r->record_id]) && $isVisit[$r->record_id] == 'yes') {
			$r->isVisit = 'yes';
			$requestModel->addHistory("Установлен признак пациент дошёл в аудиозаписи (" . $r->record . ")", RequestHistoryModel::LOG_TYPE_CHANGE_STATUS);
			$r->save();
		}
	}
}


/*	Добавление комментария к заявке	*/
if (!empty($comment)) {
	$requestModel->addHistory($comment, RequestHistoryModel::LOG_TYPE_COMMENT);
}

$result = query("commit");

/**
 * Запись на приём к врачу (признак)
 *   +
 * сохранение клиента
 */
$requestModel->saveAppointmentByRecords($isAppointment);


$requestFilterparam = isset(Yii::app()->session['requestFilter']) ? (array) Yii::app()->session['requestFilter'] : array();
$requestFilterparam['type'] = $typeView;
$requestStr = "?" . http_build_query($requestFilterparam);

//booking
$bookingErrors = [];

if(isset($_POST['slotId'])){
	$slotId = Yii::app()->request->getPost('slotId');
	!$slotId && $slotId = null; //unbook

	try{
		if(!$requestModel->book($slotId)){
			foreach ($requestModel->getErrors() as $err) {
				foreach($err as $er){
					$bookingErrors[] = $er;
				}
			}
			($requestModel->getErrors());
		}
	} catch (\Exception $e){
		$bookingErrors[] = $e->getMessage();
	}
}


// JSON ответ сервера
$response = [
	'status' => 'success',
	'id' => $requestModel->req_id,
	'errors' => $bookingErrors,
];

if (!$user->checkRight4userByCode(array('ADM'))) {
	$request = $user->operator_stream ? RequestModel::model()->findRequestByOperatorStream($user->operator_stream) : null;
	$response['redirect'] = 'yes';
	$response['url'] = $request ? "/request/request.htm?type=$typeView&id={$request->req_id}" : "/request/index.htm$requestStr";
}

echo json_encode($response);


function setMetro($id, $stationNameList = array())
{
	$id = intval($id);

	if ($id > 0) {
		$sql = "DELETE FROM request_station WHERE request_id = $id ";
		queryJS($sql, "Ошибка удаления станций метро");

		foreach ($stationNameList as $key => $station) {
			$sql = "SELECT distinct id FROM underground_station WHERE LOWER(name) LIKE LOWER('%" . trim($station) . "%')";
			$result = query($sql);
			if (num_rows($result) > 0) {
				while ($row = fetch_object($result)) {
					$sql = "REPLACE INTO request_station SET
							request_id = $id,
							station_id = " . $row->id;
					queryJS($sql, "Ошибка добавления станций метро: " . $row->id);

				}
			}

		}

	}
}

function setRecordOpinion($id, $path)
{
	global $userId;

	$sql = "UPDATE request_record SET isOpinion = 'no'  WHERE request_id = $id ";
	queryJS($sql, "Ошибка изменения признака отзыва у записи");


	$sql = "UPDATE request_record SET isOpinion = 'yes'  WHERE request_id = $id AND record = '" . $path . "'";
	queryJS($sql, "Ошибка добавления признака отзыва у записи");
	saveLogJS($id, "Добавлен признак отзыва в заявке (" . $path . ")", $userId, 3);
}

function deleteRecordOpinion($id)
{
	global $userId;

	$sql = "UPDATE request_record SET isOpinion = 'no'  WHERE request_id = $id ";
	queryJS($sql, "Ошибка изменения признака отзыва у записи");
	saveLogJS($id, "Удален признак отзыва в заявке", $userId, 3);
}


function setDoctorToAnotherClinic($clinicId, $docrorId)
{

	// Необходимо учесть расписание врача !!!!!!!!!!!!! в последующкем
	global $userId;

	$clinicId = intval($clinicId);
	$docrorId = intval($docrorId);

	if ($clinicId > 0 && $docrorId > 0) {
		$sql = "REPLACE INTO doctor_4_clinic SET doctor_id = $docrorId, clinic_id = $clinicId, type = " . DoctorClinicModel::TYPE_DOCTOR;
		queryJS($sql, "Ошибка изменения клиник у записи " . $sql);
	}

}

/**
 * @param RequestModel $requestModel
 */
function modelError($requestModel)
{
	if (!$requestModel->hasErrors()) {
		return;
	}

	$msg = '';
	foreach ($requestModel->getErrors() as $e) {
		$msg .= implode("<br/>", $e);
	}

	showError($msg);
}

/**
 * @param string $msg
 */
function showError($msg)
{
	echo htmlspecialchars(json_encode(array('error' => $msg)), ENT_NOQUOTES);
	exit;
}
