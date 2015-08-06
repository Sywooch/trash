<?php
	require_once dirname(__FILE__)."/../../lib/php/user.class.php";
	require_once dirname(__FILE__)."/../../include/common.php";
	require_once dirname(__FILE__)."/../../lib/php/validate.php";


	$report = "";

	$user = new user();
	$user -> checkRight4page(array('ADM','OPR','SOP'),'simple');
	$userId = $user -> idUser;

	
	$id			= (isset($_POST['id'])) ? checkField($_POST['id'], "i", 0) : '0';
	$client	 	= (isset($_POST['client'])) ? checkField($_POST['client'], "t", "") : '';
	$phone	 	= (isset($_POST['phone'])) ? checkField($_POST['phone'], "t", "") : '';

	/*	Валидация	*/
	if ( $id <= 0 ) { setExeption ("Не передан идентификатор");	}
	if ( empty($client) ) { setExeption ("Не заполнено поле ФИО"); }
	if ( empty($phone) ) { setExeption ("Не заполнено поле Телефон"); }
	
	$phone = modifyPhone($phone);
	
	$result = query("START TRANSACTION");
	$sql = "";
	if ( $id > 0 )  {
		$sql = "UPDATE `request` SET client_name = '".$client."', client_phone='".$phone."'  WHERE req_id=".$id;
		//$log = new msgLog($sql, "status", $userId); 
		queryJS ($sql, 'Ошибка изменения данных заявки');
		
		$txt = "Изменены данные клиента";
		$sql = "INSERT INTO `request_history` SET 
					request_id = ".$id.", 
					created = now(), 
					action = 3, 
					user_id = ".$userId.", 
					text = '".$txt."'";
		//$log = new msgLog($sql, "history", $userId); 
		queryJS ($sql, 'Ошибка записи истории заявки');

		
	} else {
		echo htmlspecialchars(json_encode(array('error'=>'Не передан идентификатор')), ENT_NOQUOTES);
		exit;
	}

	
	
	$result = query("commit");

	echo htmlspecialchars(json_encode(array('status'=>'success', 'id' => $id)), ENT_NOQUOTES);

	
	function setExeption ( $mess ) {
		echo htmlspecialchars(json_encode(array('error' => $mess)), ENT_NOQUOTES);
		exit;
	}
