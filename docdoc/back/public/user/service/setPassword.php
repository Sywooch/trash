<?php	   	  
	require_once dirname(__FILE__)."/../../lib/php/user.class.php";
	require_once dirname(__FILE__)."/../../include/common.php";
	require_once dirname(__FILE__)."/../../lib/php/validate.php";
	require_once dirname(__FILE__)."/../../lib/php/mail.php";	


	$out = "";
	
	$user = new user();	 
	$user -> checkRight4page(array('ADM'),'simple');
	
	$id			= (isset($_POST['id'])) ? checkField($_POST['id'], "i", 0) : '0';
	$passwd		= (isset($_POST['lastName'])) ? checkField($_POST['passwd'], "t", "") : '';   
	$sendInv	= (isset($_POST['sendInv'])) ? checkField($_POST['sendInv'], "i", ""): "";

	
	if ( $id > 0 && !empty($passwd))  {
		$result = query("START TRANSACTION");
	 
		$sql = "UPDATE `user` SET 
					user_password = '".md5($passwd)."'
				WHERE user_id=".$id;
		 //echo $sql;
		$result = query($sql);
		
		if(!$result) {
			$result = query("rollback");
			echo htmlspecialchars(json_encode(array('error'=>'пароль не установлен')), ENT_NOQUOTES);
			exit;
		}
		
		// Отправка уведомления

		if ($sendInv == 1) {
			$subj	=	"Уведомление о смене пароля";
			
			$mailBody = "<div><strong>Уведомление о смене пароля</strong></div>";
			$mailBody .= "<div>Ваш новый пароль: $passwd</div>"; 
			$mailBody .= "<div><em>Система автоматических уведомлений.</em></div>";
			
			$currUser = new user();
			$currUser ->  getUserById($id);
			$mailTo[] = $currUser -> email;
			
			if ( sendMessage ($subj, $mailBody, $mailTo) ) {
				$out = array('status'=>'success');
			} else {
				$out = array('error'=>'сообщение не отправлено');
			}
		} else {
			$out = array('status'=>'success');
		}
		
		$result = query("commit");
	} else {
		$out = array('error'=>'Идентификатор не определен');
	}	 					  

	echo htmlspecialchars(json_encode($out), ENT_NOQUOTES);
