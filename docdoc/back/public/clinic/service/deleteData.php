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
		
		$sql = "DELETE FROM underground_station_4_clinic WHERE clinic_id = $id ";
		queryJS ($sql, 'Ошибки при удалении связи клиники с метро');
		
		$sql = "DELETE FROM clinic_phone WHERE clinic_id = $id ";
		queryJS ($sql, 'Ошибки при удалении телефонов клиники');
		
		$sql = "SELECT clinic_admin_id FROM admin_4_clinic WHERE clinic_id = $id ";
		$result = queryJS ($sql, 'Ошибки при получении данных об администраторах');
		if (num_rows($result) == 1 ) {
			$row = fetch_object($result);
			$sql = "DELETE FROM clinic_admin WHERE clinic_admin_id = ".$row -> clinic_admin_id;
			queryJS ($sql, 'Ошибки при удалении администраторов клиники');
		}
		
		$sql = "DELETE FROM admin_4_clinic WHERE clinic_id = $id ";
		queryJS ($sql, 'Ошибки при удалении администраторов клиники');
		
		$sql = "DELETE FROM clinic_address WHERE clinic_id = $id ";
		queryJS ($sql, 'Ошибки при удалении адресов клиники');
		
		$sql = "DELETE FROM diagnostica4clinic WHERE clinic_id = $id ";
		queryJS ($sql, 'Ошибки при удалении исследований ДЦ');
		
		$sql = "DELETE FROM `clinic` WHERE id=".$id;
		queryJS ($sql, 'Клиника не может быть удалена');

		$msg = "Удаление клиники клиники id = $id";
		$log = new logger();
		$log -> setLog($user->idUser, 'D_CLN', $msg);
		
		$result = query("commit");
		echo htmlspecialchars(json_encode(array('status'=>'success')), ENT_NOQUOTES);
	} else {
		echo htmlspecialchars(json_encode(array('error'=>'Отсутствует идентификатор')), ENT_NOQUOTES);
	}
