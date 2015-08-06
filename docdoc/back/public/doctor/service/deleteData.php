<?php
use dfs\docdoc\models\DoctorClinicModel;

	require_once dirname(__FILE__)."/../../lib/php/user.class.php";
	require_once dirname(__FILE__)."/../../include/common.php";
	require_once dirname(__FILE__)."/../../lib/php/validate.php";


	$report = "";

	$user = new user();
	$user -> checkRight4page(array('ADM', 'ACM'),'simple');
	$userId = $user -> idUser;

	
	$id	= (isset($_POST['id'])) ? checkField($_POST['id'], "i", 0) : '0';

	if ( $id > 0 ) {
		$result = query("START TRANSACTION");
		
		$sql = "DELETE FROM education_4_doctor WHERE doctor_id = $id ";
		queryJS ($sql, 'Ошибки при удалении связи врача и образовательных учреждений');
		
		$sql = "DELETE FROM doctor_4_clinic WHERE doctor_id = $id and type = " . DoctorClinicModel::TYPE_DOCTOR;
		queryJS ($sql, 'Ошибки при удалении врача из клиники');
		
		$sql = "DELETE FROM doctor_sector WHERE doctor_id = $id ";
		queryJS ($sql, 'Ошибки при удалении специальностей врача');
		
		$sql = "DELETE FROM doctor WHERE id=".$id;
		queryJS ($sql, 'Доктор не может быть удален');

		$msg = "Удаление врача id = $id";
		$log = new logger();
		$log -> setLog($user->idUser, 'D_DOC', $msg);
		
		$result = query("commit");
		echo htmlspecialchars(json_encode(array('status'=>'success')), ENT_NOQUOTES);
	} else {
		echo htmlspecialchars(json_encode(array('error'=>'Отсутствует идентификатор')), ENT_NOQUOTES);
	}
