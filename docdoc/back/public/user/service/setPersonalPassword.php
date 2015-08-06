<?php
	require_once dirname(__FILE__)."/../../lib/php/user.class.php";
	require_once dirname(__FILE__)."/../../include/common.php";
	require_once dirname(__FILE__)."/../../lib/php/validate.php";


	$user = new user();	 
	
	$passwd 	= (isset($_POST['passwd'])) ? checkField($_POST['passwd'], "t", "") : '';  
	$passwd2 	= (isset($_POST['passwd2'])) ? checkField($_POST['passwd2'], "t", "") : '';  

	// Поверка введенных данных
	if ( $passwd == $passwd2 ) {
		 	if ( $user -> checkLoginUser() ){ 
				$sql = "UPDATE `user` SET
							user_password = '".md5($passwd)."'
					WHERE user_id=".$user -> idUser;
				 //echo $sql;
				$result = query($sql);
				if(!$result) { 
					$msg['status'] = "Error";
				} else {
					$msg['status'] = "success";
				}
			}
		//}		
	} else {  
		$msg['pass_err'] = "Пароли не совпадают";
 		$msg['status'] = "Error";
	}
	
	echo htmlspecialchars(json_encode( $msg ), ENT_NOQUOTES);
