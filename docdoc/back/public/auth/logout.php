<?php
	require_once dirname(__FILE__)."/../include/common.php";


	$user = new user();
	
	$result = query("START TRANSACTION");
	
	$msg = "Пользователь ".$user ->login." вышел из системы";
	$log = new logger();
	$log -> setLog($user->idUser, 'U_LOT', $msg);
	
	$result = query("commit");

	Yii::app()->session->remove('user');
	Yii::app()->user->logout();
	header("Location: /index.htm");
