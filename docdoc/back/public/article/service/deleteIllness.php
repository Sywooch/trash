<?php
	require_once dirname(__FILE__)."/../../include/common.php";
	require_once dirname(__FILE__)."/../../lib/php/models/illness.class.php";


	$report = "";

	$user = new user();
	$user -> checkRight4page(array('ADM', 'ACM'),'simple');
	$userId = $user -> idUser;

	
	$id	= (isset($_POST['id'])) ? checkField($_POST['id'], "i", 0) : '0';

	if ( $id > 0 ) {
		$result = query("START TRANSACTION");
		
		$illness = new Illness($id);
		
		if ( !$illness -> delete () ) {
			$result = query("rollback");
			setException("Ошибка удаления заболевания");
		};
		
		$msg = "Удаление статьи \"".$illness->data['Name']."\" id = $id";
		$log = new logger();
		$log -> setLog($user->idUser, 'D_ILL', $msg);
		
		$result = query("commit");

		unset($illness);
		
		echo htmlspecialchars(json_encode(array('status'=>'success')), ENT_NOQUOTES);
	} else {
		echo htmlspecialchars(json_encode(array('error'=>'Отсутствует идентификатор')), ENT_NOQUOTES);
	}
