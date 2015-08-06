<?php
	use dfs\docdoc\models\ClinicModel;


	require_once dirname(__FILE__)."/../../lib/php/user.class.php";
	require_once dirname(__FILE__)."/../../include/common.php";
	require_once dirname(__FILE__)."/../../lib/php/validate.php";


	$report = "";

	$user = new user();
	$user -> checkRight4page(array('ADM','CNM','SOP','ACM','OPR'),'simple');
	$userId = $user -> idUser;

	
	$id			= (isset($_POST['id'])) ? checkField($_POST['id'], "i", 0) : '0';
	$author		= (isset($_POST['author'])) ? checkField($_POST['author'], "t", "cont") : "cont";
	$doctorId 	= (isset($_POST['doctorId'])) ? checkField($_POST['doctorId'], "i", 0) : 0;
	
	$rating_qul		= (isset($_POST['rating_qul'])) ? checkField($_POST['rating_qul'], "i", '') : '';
	$rating_att		= (isset($_POST['rating_att'])) ? checkField($_POST['rating_att'], "i", '') : '';
	$rating_room	= (isset($_POST['rating_room'])) ? checkField($_POST['rating_room'], "i", '') : '';
	$rating_color	= (isset($_POST['ratingColor'])) ? $_POST['ratingColor'] : '';
	
	$client 	= (isset($_POST['client'])) ? checkField($_POST['client'], "t", "") : '';
	$clientId	= (isset($_POST['clientId'])) ? checkField($_POST['clientId'], "i", 0) : 0;
	$phone		= (isset($_POST['phone'])) ? checkField($_POST['phone'], "t", "") : '';
	
	$requestId	= (isset($_POST['requestId'])) ? checkField($_POST['requestId'], "i", 0) : '';
	$description= (isset($_POST['description'])) ? checkField($_POST['description'], "h", "") : '';
	$operComment= (isset($_POST['operatorComment'])) ? checkField($_POST['operatorComment'], "h", "") : '';
	
	
	$allowed	= (isset($_POST['allowed'])) ? checkField($_POST['allowed'], "i", 0) : 0;
	$origin	= (isset($_POST['origin'])) ? checkField($_POST['origin'], "t", 'original') : 'original';
	$oldStatus	= (isset($_POST['oldStatus'])) ? checkField($_POST['oldStatus'], "i", 0) : 0;
	
	$status = 'disable';
	switch ($allowed) {
		case 2: {$status = 'disable';} break;
		case 1: {$status = 'enable';} break;
		case 0: {$status = 'hidden';} break;
	}
	
	$is_fake = 1;
	switch ($origin) {
		case 'editor': {$is_fake = 0;} break;
		case 'original': {$is_fake = 1;} break;
		case 'combine': {$is_fake = 2;} break;
	}
	
	/*	Валидация	*/
	if ( !($doctorId > 0) ) {
		echo htmlspecialchars(json_encode(array('error'=>'Необходимо выбрать врача ')), ENT_NOQUOTES);
		exit;
	}
	
	$phone = modifyPhone($phone);
	
	
	$result = query("START TRANSACTION");
	
	$sqlAdd = "";
	if ( $requestId == 0) { 
		$sqlAdd .= " request_id  = null, "; 
	} else if ( $requestId != '' ) { 
		$sqlAdd .= " request_id = '".$requestId."', "; 
	}
	
	if ( $oldStatus  != $allowed && $allowed == 1 ) { $sqlAdd .= " date_publication =".time().", "; }
	if ( $rating_color == '' ) {
		$sqlAdd .= " rating_color = null, ";
	} else {
		$sqlAdd .= " rating_color = '".$rating_color."', ";
	}
	
	if ( $rating_qul == '' ) {
		$sqlAdd .= " rating_qualification = null, ";
	} else {
		$sqlAdd .= " rating_qualification = '".$rating_qul."', ";
	}
	if ( $rating_att == '' ) {
		$sqlAdd .= " rating_attention = null, ";
	} else {
		$sqlAdd .= " rating_attention = '".$rating_att."', ";
	}
	if ( $rating_room == '' ) {
		$sqlAdd .= " rating_room = null, ";
	} else {
		$sqlAdd .= " rating_room = '".$rating_room."', ";
	}
	
	
	if ( $id > 0 )  {
		$sql = "UPDATE `doctor_opinion` SET
					doctor_id = '".$doctorId."',
					author = '".$author."',
					name = '".$client."',
					phone = '".$phone."',
					origin = '".$origin."',
					status = '".$status."',
					is_fake = '".$is_fake."',
					operatorComment = '".$operComment."',
					allowed = '".$allowed."', "
					.$sqlAdd."
					text = '".$description."'
				WHERE id=".$id;
		// echo $sql;
		queryJS ($sql, 'Ошибка изменения данных');

		$msg = "Модификация отзыва id = $id";
		$log = new logger();
		$log -> setLog($user->idUser, 'U_OPN', $msg);
		
	} else {
		/*		Новая запись	*/
		$sql = "INSERT INTO `doctor_opinion` SET
					doctor_id = '".$doctorId."',
					author = '".$author."',
					name = '".$client."',
					phone = '".$phone."',
					origin = '".$origin."',
					allowed = '".$allowed."',
					status = '".$status."',
					is_fake = '".$is_fake."',
					operatorComment = '".$operComment."',
					created = now(), "
					.$sqlAdd."
					text = '".$description."'";
		//echo $sql;
		queryJS ($sql, 'Ошибка добавления данных');
		$id = legacy_insert_id();

		$msg = "Создание отзыва id = $id";
		$log = new logger();
		$log -> setLog($user->idUser, 'C_OPN', $msg);
	}

	ClinicModel::updateDoctor($doctorId);
	
	$result = query("commit");

	echo htmlspecialchars(json_encode(array('status'=>'success', 'id' => $id)), ENT_NOQUOTES);
