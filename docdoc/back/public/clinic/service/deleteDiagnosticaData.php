<?php
	require_once dirname(__FILE__)."/../../lib/php/user.class.php";
	require_once dirname(__FILE__)."/../../include/common.php";
	require_once dirname(__FILE__)."/../../lib/php/validate.php";


	$report = "";

	$user = new user();
	$user -> checkRight4page(array('ADM', 'ACM', 'SOP', 'CNM' ),'simple');
	$userId = $user -> idUser;

	
	$id	= (isset($_GET['id'])) ? checkField($_GET['id'], "i", 0) : '0';
	$clinic_id	= (isset($_GET['clinicId'])) ? checkField($_GET['clinicId'], "i", 0) : '0';

	if ( $id > 0 && $clinic_id > 0) {
		$result = query("START TRANSACTION");
		
		$sql = "DELETE FROM diagnostica4clinic WHERE diagnostica_id = $id AND clinic_id = $clinic_id";
		queryJS ($sql, 'Ошибки при удалении исследования');
		
		$msg = "Удаление исследования $id из ДЦ id = $clinic_id";
		$log = new logger();
		$log -> setLog($user->idUser, 'D_DCD', $msg);
		
		$result = query("commit");
		echo htmlspecialchars(json_encode(array('status'=>'success')), ENT_NOQUOTES);
	} else {
		echo htmlspecialchars(json_encode(array('error'=>'Отсутствует идентификатор')), ENT_NOQUOTES);
	}
