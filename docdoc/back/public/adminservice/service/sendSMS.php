<?php
use dfs\docdoc\models\SmsQueryModel;

require_once dirname(__FILE__) . "/../../lib/php/user.class.php";
require_once dirname(__FILE__) . "/../../lib/php/smsQuery.class.php";
require_once dirname(__FILE__) . "/../../include/common.php";
require_once dirname(__FILE__) . "/../../lib/php/validate.php";


$user = new user();
$user->checkRight4page(array('ADM'), 'simple');

$id = (isset ($_POST['id'])) ? checkField($_POST['id'], "i", 0) : '0';

if ($id > 0 && $smsModel = SmsQueryModel::model()->findByPk($id)) {
	if ($smsModel->sendSms()) {
		$msg = "Ручная отправка SMS из очереди (" . $id . ")";
	} else {
		$smsModel->saveStatus(SmsQueryModel::STATUS_ERROR_CONNECT);
		echo htmlspecialchars(json_encode(array('status' => 'error_connect')), ENT_NOQUOTES);
		exit;
	}
	echo htmlspecialchars(json_encode(array('status' => 'sended')), ENT_NOQUOTES);
	exit;

} else {
	echo htmlspecialchars(json_encode(array('error' => 'Не переданн идентификатор')), ENT_NOQUOTES);
	exit;
}
