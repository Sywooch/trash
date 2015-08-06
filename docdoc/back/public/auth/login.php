<?php
	include_once   dirname(__FILE__)."/../include/common.php"; 
	include_once   dirname(__FILE__)."/../lib/php/validate.php";


	$user = new user();
	
	$login = checkField ($_POST["login"], "txt", "", true);
	$passwd = checkField ($_POST["passwd"], "txt", "", true);

	$user -> logIn( $login, $passwd );
	if ( $user -> idUser > 0 ) {
		$msg = "Пользователь ".$user ->login." авторизовался";
		$log = new logger();
		$log -> setLog($user->idUser, 'U_ATH', $msg);
	}
		
  	if ( isset(Yii::app()->session['url'])) {
		header ("Location: ".Yii::app()->session['url']);
		exit;
	}
		
	header("Location: /index.htm");
