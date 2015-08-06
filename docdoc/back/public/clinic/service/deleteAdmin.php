<?php
	require_once dirname(__FILE__)."/../../lib/php/user.class.php";
	require_once dirname(__FILE__)."/../../include/common.php";
	require_once dirname(__FILE__)."/../../lib/php/validate.php";


	$report = "";

	$user = new user();
	$user -> checkRight4page(array('ADM', 'ACM'),'simple');
	$userId = $user -> idUser;

	
	$id			= (isset($_POST['id'])) ? checkField($_POST['id'], "i", 0) : '0';
	$parentId	= (isset($_POST['parentId'])) ? checkField($_POST['parentId'], "i", 0) : 0;

	if ( $id > 0 ) {
		$result = query("START TRANSACTION");
		
		$sql = "SELECT clinic_admin_id FROM admin_4_clinic WHERE clinic_id = $id ";
		$result = queryJS ($sql, 'Ошибки при получении данных об администраторах');
		if (num_rows($result) == 1 ) {
			$row = fetch_object($result);
			$sql = "DELETE FROM clinic_admin WHERE clinic_admin_id = ".$row -> clinic_admin_id;
			queryJS ($sql, 'Ошибки при удалении администраторов клиники');
		}
		
		$sql = "DELETE FROM admin_4_clinic WHERE clinic_id = $id ";
		queryJS ($sql, 'Ошибки при удалении администраторов клиники');
		
		$msg = "Удаление администора из клиники id = $id";
		$log = new logger();
		$log -> setLog($user->idUser, 'D_ADM', $msg);
			
		$result = query("commit");
		echo htmlspecialchars(json_encode(array('status'=>'success', 'parentId' => $parentId)), ENT_NOQUOTES);
	} else {
		echo htmlspecialchars(json_encode(array('error'=>'Отсутствует идентификатор')), ENT_NOQUOTES);
	}
