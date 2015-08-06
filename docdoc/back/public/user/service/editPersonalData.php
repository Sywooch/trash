<?php	 
	require_once dirname(__FILE__)."/../../lib/php/user.class.php";
	require_once dirname(__FILE__)."/../../lib/php/validate.php";
	require_once dirname(__FILE__)."/../../include/common.php";	
	

	$user = new user();	 
	$id = $user -> idUser;
	
	$lastName 	= (isset($_POST['lastName'])) ? checkField($_POST['lastName'], "t", "") : '';  
	$firstName 	= (isset($_POST['firstName'])) ? checkField($_POST['firstName'], "t", "") : ''; 
	$phone 		= (isset($_POST['phone'])) ? checkField($_POST['phone'], "t", "") : ''; 
	$skype 		= (isset($_POST['skype'])) ? checkField($_POST['skype'], "t", "") : '';
	$email		= (isset($_POST['email'])) ? checkField($_POST['email'], "t", "") : '';  
	
		
	if ( $user -> checkLoginUser() ) {
		$result = query("START TRANSACTION");
		
		$sql = "UPDATE `user` SET 
					user_lname = '".$lastName."', 
					user_fname = '".$firstName."', 
					phone = '".$phone."', 
					skype = '".$skype."',
					user_email = '".$email."'
				WHERE user_id=".$id;
		//echo $sql;
		$result = query($sql);
		if(!$result) {
			$result = query("rollback");
			echo htmlspecialchars(json_encode(array('error'=>'Ошибка изменения данных')), ENT_NOQUOTES);
			exit;
		}
		
		$user -> getUserById($user -> idUser);
		$user -> setUser ($user);		
		
		$msg = "Изменение персональных данных login = ".$user -> login."  / id = ".$user -> idUser;
		$log = new logger();
		$log -> setLog($user -> idUser, 'U_PRS', $msg);
		
		$result = query("commit");

		echo htmlspecialchars(json_encode(array('status'=>'success')), ENT_NOQUOTES);
	} else {
		echo htmlspecialchars(json_encode(array('error'=>'Не подтвердилась авторизация')), ENT_NOQUOTES);
	}
