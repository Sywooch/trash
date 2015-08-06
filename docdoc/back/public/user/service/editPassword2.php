<?php
	require_once dirname(__FILE__)."/../../lib/php/user.class.php";
	require_once dirname(__FILE__)."/../../lib/php/errorLog.php";
	require_once dirname(__FILE__)."/../../include/common.php";
	require_once dirname(__FILE__)."/../../lib/php/mail.php";	


	$user = new user();	 
	$user -> checkRight4page(array('ADM'),'simple');
	
	$id			= (isset($_POST['id'])) ? intval($_POST['id']) : '0';
	$passwd		= (isset($_POST['passwd'])) ? trim($_POST['passwd']): '';
	$sendInv	= (isset($_POST['sendInv'])) ? intval($_POST['sendInv']): '';

	$result = query("START TRANSACTION");
	if ( $id > 0  && !empty($passwd) )  { 
		$sql = "UPDATE BackUser SET 
					passwd = '".md5($passwd)."'
				WHERE userId=".$id;
		$result = query($sql);
		if(!$result) {
			$result = query("rollback");
			header("HTTP/1.0 404 Not Found");
		}
		
		// Отправка уведомления
		if ($sendInv == 1) {
			$subj	=	"Уведомление о смене пароля в SovetiDamam backOffice";
			
			$mailBody = "<div><strong>Уведомление о смене пароля в SovetiDamam backOffice</strong></div>";
			$mailBody .= "<div>Ваш новый пароль: $passwd</div>"; 
			$mailBody .= "<div><em>Система автоматических уведомлений.</em></div>";
			
			$currUser = new user();
			$currUser ->  getUserById($id);
			$mailTo[] = $currUser -> email;
			
//			echo $currUser -> email;
			sendMessage ($subj, $mailBody, $mailTo) ;
		}
 	} else {
		$result = query("rollback");
		header("HTTP/1.0 404 Not Found");
	}	 					  
	
	$result = query("commit");

//	header ("Location: /user/user.htm?id=".$id."&mode=edit");
	echo "success";
