<?php
use dfs\docdoc\models\SmsQueryModel;

require_once dirname(__FILE__) . "/../../lib/php/user.class.php";
require_once dirname(__FILE__) . "/../../lib/php/smsQuery.class.php";
require_once dirname(__FILE__) . "/../../include/common.php";
require_once dirname(__FILE__) . "/../../lib/php/validate.php";


$user = new user();
$user->checkRight4page(array('ADM'), 'simple');


$lineList = (isset ($_POST['line'])) ? $_POST['line'] : array();

if (count($lineList) > 0) {
	foreach ($lineList as $key => $data) {

		$smsModel = SmsQueryModel::model()->findByPk($key);

		if ($smsModel) {
			if ($smsModel->sendSms()) {
				$msg = "Ручная отправка SMS из очереди (" . intval($key) . ")";
				$log = new logger();
				$log->setLog($user->idUser, 'S_SMS', $msg);
			} else {
				$smsModel->saveStatus(SmsQueryModel::STATUS_ERROR_CONNECT);
				echo htmlspecialchars(json_encode(array('error' => 'Ошибка отправки SMS: ' . $key)), ENT_NOQUOTES);
				exit;
			}
		} else {
			continue;
		}
	}
	echo htmlspecialchars(json_encode(array('status' => 'success')), ENT_NOQUOTES);
} else {
	echo htmlspecialchars(json_encode(array('error' => 'Не переданны идентификаторы позиций')), ENT_NOQUOTES);
	exit;
}
