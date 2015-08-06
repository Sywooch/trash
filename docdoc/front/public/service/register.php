<?php
	require_once dirname(__FILE__) . "/../include/common.php";
	
	$name = (isset($_POST['name'])) ? checkField($_POST['name'], "st", "") : '';
	$phone = (isset($_POST['phone'])) ? formatPhone4DB($_POST['phone']) : '';
	$mode = (isset($_POST['mode'])) ? checkField($_POST['mode'], "e", 'doctor', false, array('doctor', 'clinic')) : array();

	$session = Yii::app()->session;

	/* 	Валидация	 */
	if ( $name == "" ) {
		$session['error'] = "Введите, пожалуйста, свою фамилию и имя";
		header("Location:/register");exit;
	} else {
		$session['registerName'] = $name;
	} 
	if ( $phone == "" ) {
		$session['error'] = "Введите, пожалуйста, свой номер телефона";
		header("Location:/register");exit;
	}
	if ( strlen($phone) < 11 ) {
		$session['error'] = "Введите, пожалуйста, корректный номер телефона";
		header("Location:/register");exit;
	} else {
		$session['registerPhone'] = formatPhone($phone);
	}

	$session['registerMode'] = $mode;
	
	
	header("Location:/register/step2");
	exit;
?>