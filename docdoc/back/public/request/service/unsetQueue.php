<?php
use \dfs\docdoc\models\QueueModel;

require_once dirname(__FILE__) . "/../../lib/php/user.class.php";
require_once dirname(__FILE__) . "/../../include/common.php";
require_once dirname(__FILE__) . "/../../lib/asterisk/AsteriskManager.php";


new commonLog(Yii::app()->params['asterisk']['logFile'], "[Queue Remove Start]");

try {
	$report = "";

	$user = new user();
	$user->checkRight4page(array('ADM', 'OPR', 'SOP'), 'simple');
	$userId = $user->idUser;

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
		$queue = QueueModel::model()->byUser($userId)->find();
		if (is_null($queue)) {
			throw new Exception("Ошибка получения SIP канала");
		}

		try {
			$ast->queueRemove($queue->getQueueName(), 'SIP/' . $queue->SIP, uniqid());
		} catch (Net_AsteriskManagerException $e) {
			// Если астериск уже выбросил sip из очереди, то не реагируем на это
			if ($e->getCode() !== 602) {
				throw $e;
			}
		}

		if (!$queue->unregister()) {
			new commonLog(Yii::app()->params['asterisk']['logFile'], "[Error] Failed to change the queue in the DB");
			throw new Exception("Ошибка изменения записи в очереди БД");
		}

		$msg = "Выход из очереди. Оператор: " . $user->userLastName . " " . $user->userFirstName;
		$log = new logger();
		$log->setLog($user->idUser, 'U_QUE', $msg);

	} catch (PEAR_Exception $e) {
		new commonLog(Yii::app()->params['asterisk']['logFile'], "[Error] {$e->getMessage()}");
		throw new Exception("Ошибка выхода из очереди: {$e->getMessage()}");
	}

	echo json_encode(array('status' => 'success'));

} catch(Exception $e) {
	$error = array(
		'status'  => 'error',
		'message' => $e->getMessage(),
	);
	echo json_encode($error);
}
new commonLog(Yii::app()->params['asterisk']['logFile'], "[Queue Remove End]");
