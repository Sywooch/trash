<?php
	require_once dirname(__FILE__)."/../../lib/php/user.class.php";
	require_once dirname(__FILE__)."/../../include/common.php";
	require_once dirname(__FILE__)."/../../lib/php/validate.php";


	$report = "";

	$user = new user();
	$user -> checkRight4page(array('ADM','CNM','SOP', 'ACM'),'simple');
	$userId = $user -> idUser;

	$id			= (isset($_POST['id'])) ? checkField($_POST['id'], "i", 0) : '0';
	$parentId	= (isset($_POST['parentId'])) ? checkField($_POST['parentId'], "i", 0) : 0;
	$diagnosticList	= (isset($_POST['diagnostica'])) ? $_POST['diagnostica'] : array();

	

	$result = query("START TRANSACTION");
	if ( $id > 0 )  {
		foreach ($diagnosticList as $key => $data ){
		
			$sql = "REPLACE INTO diagnostica4clinic SET diagnostica_id=".intval($data).", clinic_id = ".$id;
			$result = queryJS ($sql, 'Ошибка записи исследований');
		}
			
		$msg = "Добавление исследований для ДЦ / id = $id";
		$log = new logger();
		$log -> setLog($user->idUser, 'A_DCD', $msg);
		
	} else {
		echo htmlspecialchars(json_encode(array('error'=>'Не передан идентификатор клиники')), ENT_NOQUOTES);
		exit;
	}

	
	$result = query("commit");

	echo htmlspecialchars(json_encode(array('status'=>'success', 'id' => $id, 'parentId' => $parentId)), ENT_NOQUOTES);
