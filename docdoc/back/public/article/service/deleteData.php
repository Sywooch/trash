<?php
	require_once dirname(__FILE__)."/../../include/common.php";
	require_once dirname(__FILE__)."/../../lib/php/models/article.class.php";


	$report = "";

	$user = new user();
	$user -> checkRight4page(array('ADM', 'ACM'),'simple');
	$userId = $user -> idUser;

	
	$id	= (isset($_POST['id'])) ? checkField($_POST['id'], "i", 0) : '0';

	if ( $id > 0 ) {
		$result = query("START TRANSACTION");
		
		$article = new Article($id);
		
		if ( !$article -> delete () ) {
			$result = query("rollback");
			setException("Ошибка удаления статьи");
		};
		
		$msg = "Удаление статьи \"".$article->data['Name']."\" id = $id";
		$log = new logger();
		$log -> setLog($user->idUser, 'D_ART', $msg);
		
		$result = query("commit");

		unset($article);
		
		echo htmlspecialchars(json_encode(array('status'=>'success')), ENT_NOQUOTES);
	} else {
		echo htmlspecialchars(json_encode(array('error'=>'Отсутствует идентификатор')), ENT_NOQUOTES);
	}
