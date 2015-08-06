<?php

use dfs\docdoc\models\UserModel;


require_once dirname(__FILE__)."/../../lib/php/user.class.php";
require_once dirname(__FILE__)."/../../include/common.php";
require_once dirname(__FILE__)."/../../lib/php/validate.php";


$user = new user();
$user->checkRight4page(array('ADM', 'SOP'), 'simple');
$userId = $user->idUser;

$id			= (isset($_POST['id'])) ? checkField($_POST['id'], "i", 0) : 0;
$stream     = (isset($_POST['operatorStream'])) ? checkField($_POST['operatorStream'], "i", 0) : 0;

$result = false;

$userModel = UserModel::model()->findByPk($id);

if ($userModel)  {
	$userModel->operator_stream = $stream;
	$result = $userModel->save();
	if ($result) {
		$msg = "Изменение потока заявок оператора id = $id";
		$log = new logger();
		$log->setLog($user->idUser, 'U_USR', $msg);
	}
}

$response = $result ? ['status' => 'success'] : ['error' => 'ошибка изменения данных'];

echo htmlspecialchars(json_encode($response), ENT_NOQUOTES);
