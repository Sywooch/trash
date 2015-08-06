<?php
	require_once dirname(__FILE__) . "/../include/common.php";

	$clinic = (isset($_POST['clinic'])) ? checkField($_POST['clinic'], "st", "") : '';
	$email = (isset($_POST['email'])) ? checkField($_POST['email'], "t", "") : '';
	$mode = (isset($_POST['mode'])) ? checkField($_POST['mode'], "e", '', false, array('doctor', 'clinic')) : '';

	$session = Yii::app()->session;

	$name 	= (isset($session['registerName'])) ? $session['registerName'] : '';
	$phone 	= (isset($session['registerPhone'])) ? $session['registerPhone'] : '';

	$textComment = "";

	

	//Валидация
	if ( empty($clinic) && $mode == 'clinic' ) {
		$session['error'] = "Введите название клиники, пожалуйста";
	    header("Location:/register/step2");
	    exit;
	} else if ( empty($clinic) ) {
		$clinic = 'Частный врач '.$name.". ";
		$textComment = "Принимает дома";
	}
	
	if ( !empty($clinic)) {
		$session['registerClinicName'] = $clinic;
	}
	if ( empty($email) ) {
		$session['error'] = "Укажите свой e-mail адрес, пожалуйста";
		header("Location:/register/step2");
		exit;
	} else {
		$session['registerEmail'] = $email;
	} 
	if ( !checkEmail($email) ) {
		$session['error'] = "Ошибка в e-mail адресе";
		header("Location:/register/step2");
		exit;
	} else {
		$session['registerEmail'] = $email;
	} 
	
	
	if  ( (!isset($_POST['agreed']) || intval($_POST['agreed']) != 'on') && $mode == 'doctor') {
		$session['error'] = "Не приняты условия договора оферты";
	    header("Location:/register/step2");
	    exit;
	}  
	
	$result = query("START TRANSACTION");
	
	$sqlAdd = "";
	
	if ($mode == 'doctor') {
	 	$sqlAdd .= ", isPrivatDoctor = 'yes', isClinic = 'no'"; 
    } elseif($mode == 'clinic') {
        $sqlAdd .= ", isClinic = 'yes', isPrivatDoctor = 'no'"; 
    }
    
	if ( !empty($email) && !empty($phone) ) {
		$sql = "INSERT INTO clinic SET
	                name = '" . $clinic . "',
	                contact_name = '" . $name . "',
	                phone = '" . $phone . "',
	                email = '".$email."',
	                operator_comment = '".$textComment."',
	                city_id = " . Yii::app()->city->getCityId() . ",
	                status = 1" . $sqlAdd;
		//echo $sql."<br>";
	    $result = query($sql);
	    if(!$result) {
	    	$result = query("rollback");
			$session['error'] = "SQL Error";
	        header("Location:/register/step2");
	    }
	    $clinicId = legacy_insert_id();
	}

	$text  = "<div>Регистрация врача и/или клиники на сайте docdoc</div>";
	$text .= "ФИО: ".$session['registerName']."<br>";
	$text .= "Телефон: ".$session['registerPhone']."<br>";
	$text .= "Представляет: ".(($session['registerMode'] == 'clinic')? "клинику":"себя")."<br>";
	$text .= "Клиника: ".$clinic."<br>";
	$text .= "E-mail: <a href=\"mailto:".$session['registerEmail']."\">".$session['registerEmail']."</a><br>";
	
	$text .= "<a href=\"http://".SERVER_BACK."/clinic/index.htm?id=".$clinicId."\">карточка клиники</a><br>";
	
	$mailBody = "<div>$text</div>";   					
	$subj =	"[docdoc.ru] Регистрация врача или клиники";
	
	$params = array(
		"emailTo" => Yii::app()->params['email']['clinic-registr'],
		"message" => $mailBody,
		"subj" => $subj
	);

	if ( !($id = emailQuery::addMessage($params)) ) {
		echo "Ошибка добавления в E-mail очередь<br>";
	}

	$result = query("commit");

	$session->remove('registerClinicId');
	$session->remove('registerDoctorId');
	$session->remove('registerName');
	$session->remove('registerPhone');
	$session->remove('registerEmail');
	$session->remove('registerClinicName');
	$session->remove('registerMode');

	header("Location:/register/proceed");
