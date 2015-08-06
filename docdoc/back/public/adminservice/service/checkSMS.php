<?php
use dfs\docdoc\models\SmsQueryModel;

require_once dirname(__FILE__) . "/../../lib/php/user.class.php";
require_once dirname(__FILE__) . "/../../lib/php/smsQuery.class.php";
require_once dirname(__FILE__) . "/../../include/common.php";


$user = new user();
$user->checkRight4page(array('ADM'), 'simple');


$id = Yii::app()->request->getPost('id');

if ($id > 0 && $sms = SmsQueryModel::model()->findByPk($id)) {
	if($sms->checkStatus()){
		$sms->saveStatus(SmsQueryModel::STATUS_DELIVERED);
	}

	if ($sms) {
		echo htmlspecialchars(json_encode(array('status' => $sms->status)), ENT_NOQUOTES);
		exit;
	} else {
		echo htmlspecialchars(json_encode(['error' => 'sms не найдено']), ENT_NOQUOTES);
		exit;
	}


} else {
	echo htmlspecialchars(json_encode(array('error' => 'Не переданн идентификатор')), ENT_NOQUOTES);
	exit;
}

