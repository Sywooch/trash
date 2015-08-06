<?php
use \dfs\docdoc\models\QueueModel;

require_once dirname(__FILE__) . "/../../include/common.php";
require_once dirname(__FILE__) . "/../../lib/asterisk/AsteriskManager.php";


new commonLog(Yii::app()->params['asterisk']['logFile'], "[Queue Add Start]");

try {
	$report = "";

	$user = new user();
	$user->checkRight4page(array('ADM', 'OPR', 'SOP'), 'simple');
	$userId = $user->idUser;

	$number = isset($_GET['number']) ? checkField($_GET['number'], "i", 0) : '0';
	$asteriskPool = isset($_GET['queue']) ? $_GET['queue'] : QueueModel::QUEUE_DEFAULT;

	if ($number <= 0) {
		new commonLog(Yii::app()->params['asterisk']['logFile'], "[Error] Not transferred SIP number");
		throw new Exception('Не передан номер канала');
	}

	$params = array(
		'server' => Yii::app()->params['asterisk']['host'],
		'port'   => Yii::app()->params['asterisk']['port'],
	);
	$ast = new Net_AsteriskManager($params);
	try {
		$ast->connect();
	} catch (PEAR_Exception $e) {
		new commonLog(Yii::app()->params['asterisk']['logFile'], "[Error] {$e->getMessage()}");
		throw new Exception("Ошибка подключения к астериску: {$e->getMessage()}");
	}

	try {
		$queue = QueueModel::model()->register($asteriskPool, $number, $userId);
		if (is_null($queue)) {
			new commonLog(Yii::app()->params['asterisk']['logFile'], "[Error] Such SIP channel is busy");
			throw new Exception("Такой номер SIP уже используется другим оператором");
		}
		$queryName = $queue->getQueueName();
		$ast->queueAdd($queryName, 'SIP/' . $number, null, uniqid());

		$msg = "Регистрация в очереди. Оператор: {$user->userLastName} {$user->userFirstName}";
		$log = new logger();
		$log->setLog($user->idUser, 'R_QUE', $msg);

	} catch (PEAR_Exception $e) {
		new commonLog(Yii::app()->params['asterisk']['logFile'], "[Error] {$e->getMessage()}");
		throw new Exception("Ошибка регистрации в очереди: {$e->getMessage()}");
	}

	echo json_encode(array(
		'status'    => 'success',
		'queryName' => $queryName,
		'sip'       => $number,
		'queueName' => $queue->getName(),
	));

} catch(Exception $e) {
	$error = array(
		'status'  => 'error',
		'message' => $e->getMessage(),
	);
	echo json_encode($error);
}

new commonLog(Yii::app()->params['asterisk']['logFile'], "[Queue Add End]");
