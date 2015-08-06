<?php
use dfs\docdoc\models\QueueModel;
use dfs\docdoc\models\RequestModel;
use dfs\docdoc\models\RequestHistoryModel;

require_once dirname(__FILE__) . "/../include/common.php";
require_once dirname(__FILE__) . "/../lib/asterisk/AsteriskManager.php";


$logFile = Yii::app()->params['asterisk']['logFile'];

new commonLog($logFile, "[Transfer Start]");

$requestId = isset($_GET['id']) ? checkField($_GET['id'], "i", 0) : '0';
$phone = isset($_GET['phone']) ? formatPhone4DB(checkField($_GET['phone'], "t", "")) : '';
$phone_from = isset($_GET['phoneFrom']) ? checkField($_GET['phoneFrom'], "t", "") : '';
$clinicId = isset($_GET['clinicId']) ? checkField($_GET['clinicId'], "i", 0) : 0;
$userId = null;
$sip = null;

new commonLog($logFile, "RequestId: {$requestId}");

try {
	$user = new user();
	if (!($user->checkRight4userByCode(array('ADM', 'OPR', 'SOP', 'LIS')))) {
		new commonLog($logFile, "[Error] No permission to perform the operation");
		throw new Exception('Нет прав для совершения операции');
	}

	$userId = $user->idUser;
	$queue = QueueModel::model()->byUser($userId)->find();
	if (!$queue) {
		new commonLog($logFile, "[Error] Not registered in the queue");
		throw new Exception('Необходимо зарегестрироваться в очереди');
	}

	$sip = $queue->SIP;
	$sipChannel = $queue->channel;
	if (!$sipChannel) {
		new commonLog($logFile, "[Error] Not found channel");
		throw new Exception("Ошибка при переводе в клинику: не найден канал");
	}

	$request = RequestModel::model()->findByPk($requestId);

	if (!$request) {
		new commonLog($logFile, "[Error] Not found request");
		throw new Exception("Заявка не найдена, возможно заявка ещё не сохранена");
	}

	if (!$request->req_doctor_id) {
		$request->clinic_id = $clinicId;
	}
	$request->transferred_clinic_id = $clinicId;

	if (!$request->save()) {
		new commonLog($logFile, "[Error] Fail request save");
		throw new Exception("Не удалось установить id переведенной клиники");
	}

	$ast = new Net_AsteriskManager();

	try {
		$ast->connect();
	}
	catch (PEAR_Exception $e) {
		$ast = null;
		new commonLog($logFile, "[Error] " . $e->getMessage());
		throw new Exception("Ошибка подключения к астериску: {$e->getMessage()}");
	}

	try {
		$ast->transerCall(
			$phone,
			$sipChannel->channel,
			Yii::app()->params['asterisk']['context'],
			"{$userId} {$user->login} <{$sip}>",
			1,
			60000
		);
	}
	catch (PEAR_Exception $e) {
		new commonLog($logFile, "[Error] {$e->getMessage()}");
		throw new Exception("Ошибка при переводе в клинику: {$e->getMessage()}");
	}

	$result = [ 'status' => 'success' ];
}
catch (Exception $e) {
	$result = [
		'status' => 'error',
		'message' => $e->getMessage(),
	];
}

if (!empty($ast)) {
	$ast->close();
}

if ($requestId) {
	$history = new RequestHistoryModel();
	$history->request_id = $requestId;
	$history->user_id = $userId;
	$history->action = RequestHistoryModel::LOG_TYPE_ACTION;
	$history->text = "Перевод в клинику, номер $phone, SIP=$sip, {$result['status']}" .
		(empty($result['message']) ? '' : ' (' . $result['message'] . ')');
	$history->save();
}

new commonLog($logFile, "[Transfer End]");

echo json_encode($result);
