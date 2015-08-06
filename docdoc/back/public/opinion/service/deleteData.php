<?php
	require_once dirname(__FILE__)."/../../lib/php/user.class.php";
	require_once dirname(__FILE__)."/../../include/common.php";
	require_once dirname(__FILE__)."/../../lib/php/validate.php";


	$report = "";

	$user = new user();
	$user -> checkRight4page(array('ADM', 'ACM'),'simple');
	$userId = $user -> idUser;

	
	$id			= (isset($_POST['id'])) ? checkField($_POST['id'], "i", 0) : '0';

	if ( $id > 0 ) {
		$result = query("START TRANSACTION");
		
		$sql = "DELETE FROM doctor_opinion WHERE id = $id ";
		queryJS ($sql, 'Ошибки при удалении отзыва');

		$msg = "Удаление отзыва id = $id";
		$log = new logger();
		$log -> setLog($user->idUser, 'D_OPN', $msg);
		
		$result = query("commit");
		echo htmlspecialchars(json_encode(array('status'=>'success')), ENT_NOQUOTES);
	} else {
		echo htmlspecialchars(json_encode(array('error'=>'Отсутствует идентификатор')), ENT_NOQUOTES);
	}
