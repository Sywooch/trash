<?php
use dfs\docdoc\models\QueueModel;
use dfs\docdoc\models\RequestHistoryModel;

require_once dirname(__FILE__) . "/../include/common.php";
require_once dirname(__FILE__) . "/../lib/asterisk/AsteriskManager.php";


$logFile = Yii::app()->params['asterisk']['logFile'];

new commonLog($logFile, "[Call Start]");

$requestId = (isset($_GET['id'])) ? checkField($_GET['id'], "i", 0) : '0';
$phone = (isset($_GET['phone'])) ? formatPhone4DB(checkField($_GET['phone'], "t", "")) : '';
$userId = null;
$sip = null;

new commonLog($logFile, "RequestId: {$requestId}");

try {
	$user = new user();
	if (!($user->checkRight4userByCode(array('ADM', 'OPR', 'SOP')))) {
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

	$ast = new Net_AsteriskManager();

	try {
		$ast->connect();
	}
	catch (PEAR_Exception $e) {
		$ast = null;
		new commonLog($logFile, "[Error] {$e->getMessage()}");
		throw new Exception("Ошибка подключения к астериску: {$e->getMessage()}");
	}

	try {
		$ast->originateCall(
			$phone,
			"SIP/" . $sip,
			Yii::app()->params['asterisk']['context'],
			$userId . " " . $user->login . " " . "<" . $sip . ">",
			1,
			60000,
			[ 'RequestId' => $requestId ]
		);
	}
	catch (PEAR_Exception $e) {
		new commonLog($logFile, "[Error] {$e->getMessage()}");
		throw new Exception('Ошибка при исходящем вызове: ' . ($e->getCode() == 603 ? 'не запущен софтфон' : $e->getMessage()));
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
	$history->text = "Звонок клиенту (исходящий) на номер $phone, SIP=$sip, {$result['status']}" .
		(empty($result['message']) ? '' : ' (' . $result['message'] . ')');
	$history->save();
}

new commonLog($logFile, "[Call End]");

echo json_encode($result);
