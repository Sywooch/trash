<?php
	require_once dirname(__FILE__)."/../../lib/php/user.class.php";
	require_once dirname(__FILE__)."/../../include/common.php";
	require_once dirname(__FILE__)."/../../lib/php/validate.php";
	require_once dirname(__FILE__)."/../../lib/php/mail.php";


	$report = "";

	$user = new user();
	$user -> checkRight4page(array('ADM'),'simple');
	$userId = $user -> idUser;

	$id			= (isset($_POST['id'])) ? checkField($_POST['id'], "i", 0) : '0';
	$lastName 	= (isset($_POST['lastName'])) ? checkField($_POST['lastName'], "t", "") : '';
	$firstName 	= (isset($_POST['firstName'])) ? checkField($_POST['firstName'], "t", "") : '';
	$phone 		= (isset($_POST['phone'])) ? checkField($_POST['phone'], "t", "") : '';
	$skype 		= (isset($_POST['skype'])) ? checkField($_POST['skype'], "t", "") : '';
	$email		= (isset($_POST['email'])) ? checkField($_POST['email'], "t", "") : '';
	$status		= (isset($_POST['status'])) ? checkField($_POST['status'], "t", "enable") : 'enable';
	$stream     = (isset($_POST['operatorStream'])) ? checkField($_POST['operatorStream'], "i", 0) : 0;

	$login		= (isset($_POST['login'])) ? checkField($_POST['login'], "t", "") : '';
	$passwd		= (isset($_POST['passwd'])) ?  checkField($_POST['passwd'], "t", "") : '';
	$sendInv	= (isset($_POST['sendInv'])) ? checkField($_POST['sendInv'], "i", 0): 0;
	$rights		= (isset($_POST['Right'])) ? $_POST['Right'] : array();


	$status_old = 0;
	switch ($status) {
		case 'enable' : $status_old = 0;break;
		case 'disable' : $status_old = 1;break;
	}
	
	$result = query("START TRANSACTION");
	if ( $id > 0 )  {
		$sql = "UPDATE `user` SET
					user_lname = '".$lastName."',
					user_fname = '".$firstName."',
					user_email = '".$email."',
					skype = '".$skype."',
					phone = '".$phone."',
					status = '".$status."',
					operator_stream = '".$stream."'
				WHERE user_id=".$id;
		// echo $sql;
		//$log = new msgLog($sql, 1, $userId);
		$result = query($sql);
		if(!$result) {
			$result = query("rollback");
			echo htmlspecialchars(json_encode(array('error'=>'ошибка изменения данных')), ENT_NOQUOTES);
			exit;
		}

		setRights ($id, $rights);  // установка прав
		
		$msg = "Модификация данных пользователя id = $id";
		$log = new logger();
		$log -> setLog($user->idUser, 'U_USR', $msg);
		
	} else {
		/*		Новая запись	*/
		$sql = "INSERT INTO
					`user`
				SET
					user_lname 	= '".$lastName."',
					user_fname 	= '".$firstName."',
					user_email 	= '".$email."',
					user_status = '".$status_old."',
					user_role 	= 0,
					skype = '".$skype."',
					phone = '".$phone."',
					operator_stream = '".$stream."',
					user_login 	= '".$login."',
					user_password = '".md5($passwd)."'";
			//echo $sql;
		$result = query($sql);
		
		if(!$result) {
			$result = query("rollback");
			echo htmlspecialchars(json_encode(array('error'=>'ошибка добавления данных')), ENT_NOQUOTES);
			exit;
		}
		$id = legacy_insert_id();
		
		setRights ($id, $rights); 	// установка прав

		// Отправка уведомления
		if ($sendInv == 1) {
			$subj	=	"Уведомление о регистрации в системе";

			$mailBody = "<div><strong>Уведомление о регистрации в системе</strong></div>";
			$mailBody .= "<div style=\"margin: 20px 0 5px 0\">Ваши учётные данные:</div>";
			$mailBody .= "<div style=\"margin: 0 0 10px 0\">	логин: $login<br>
								пароль: $passwd</div>";
			$mailBody .= "<div>Для входа в систему, пройдите по ссылке <a href=\"http://".SERVER_BACK."\">http://".SERVER_BACK."</a> </div>";
			$mailBody .= "<div style=\"margin: 20px 0 5px 0\"><em>Система автоматических уведомлений.</em></div>";

			emailQuery::addMessage([
				"emailTo" => $email,
				"subj"    => $subject,
				"message" => $mailBody
			]);
		}
		
			
		
		$msg = "Заведение пользователя login = $login / id = $id";
		$log = new logger();
		$log -> setLog($user->idUser, 'C_USR', $msg);
	}

	$result = query("commit");

	echo htmlspecialchars(json_encode(array('status'=>'success')), ENT_NOQUOTES);





	function setRights ($id, $rights=array()) {
		$sql = "DELETE FROM right_4_user WHERE user_id=".$id;
		//echo $sql."<br>";
		$result = query($sql);
		if(!$result) {
			$result = query("rollback");
			echo htmlspecialchars(json_encode(array('error'=>'ошибка удаления прав')), ENT_NOQUOTES);
			exit;
		}
		
		foreach ($rights as $data=>$key) {
			$sql = "INSERT INTO right_4_user (right_id,user_id) VALUES (".intval($key).", ".intval($id).")";
			//echo $sql."<br>";
			$result = query($sql);
			if(!$result) {
				$result = query("rollback");
				echo htmlspecialchars(json_encode(array('error'=>'ошибка добавления прав')), ENT_NOQUOTES);
				exit;
			}
		}
	}
