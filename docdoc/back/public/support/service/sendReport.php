<?php
use dfs\docdoc\models\SmsQueryModel;

require_once dirname(__FILE__)."/../../include/common.php";
require_once dirname(__FILE__)."/../../lib/php/smsQuery.class.php";
require_once dirname(__FILE__)."/../../lib/php/emailQuery.class.php";

$user = new user();
$id = $user -> idUser;

$subj = (isset($_POST['subj'])) ? checkField($_POST['subj'], "t", "") : '';
$problem = (isset($_POST['problem'])) ? checkField($_POST['problem'], "t", "") : '';
$page = (isset($_POST['page'])) ? checkField($_POST['page'], "t", "") : '';
$isCritical = (isset($_POST['isCritical'])) ? checkField($_POST['isCritical'], "e", "no", false, array("yes", "no")) : 'no';
$category = (isset($_POST['category'])) ? checkField($_POST['category'], "t", "other") : 'other';
	

$criticalText  = ($isCritical == 'yes') ? "КРИТИЧНО!  " : "";

if (empty($subj)) {
	switch ($category) {
		case 'backend' :
			$subj = "[backend] " . $criticalText . "Ошибка со стороны БО";
			break;
		case 'docdoc' :
			$subj = "[docdoc.ru] " . $criticalText . "Ошибка на сайте docdoc.ru";
			break;
		case 'diagnostica' :
			$subj = "[diagnostica] " . $criticalText . "Ошибка на сайте diagnostica.docdoc.ru";
			break;
		case 'phones' :
			$subj = "[phones] " . $criticalText . "Ошибка телефонии";
			break;
		case 'office_offer' :
			$subj = "[office_offer] " . $criticalText . "Офис/Предложения";
			break;
		case 'anonymous' :
			$subj = "[anonymous] " . $criticalText . "Анонимно";
			break;
		case 'other':
		default :
			$subj = "[backend] " . $criticalText . "Какая-то неопределенная ошибка";
	}
}
	$subj .= " / ".$user->userLastName." ".$user->userFirstName;
	

	if ( $user -> checkLoginUser() && $category !== 'anonymous') {
		$result = query("START TRANSACTION");
		
		$sql = "INSERT INTO site_problrem SET 
					user_id = ".$id.", 
					cr_date = NOW(),
					is_critical = '".$isCritical."',  
					page = '".$page."',
					subj = '".$subj."', 
					problem_text = '".$problem."'";
		queryJS($sql, "Ошибка записи в БД");
		$reportId = legacy_insert_id();
		$result = query("commit");
		

		// Отправка SMS
		if ($isCritical == 'yes') {
			foreach ($ADMIN_SMS_PHONE as $phones) {
				$message = "Критичная ошибка на " . SERVER_BACK . " #" . $reportId . ". " . $user->userLastName;
				SmsQueryModel::sendSmsToNumber($phones, $message, SmsQueryModel::TYPE_ERROR_MSG, true);
			}
		}

		// Отправка мейла
		$mailBody = "";
		if ($isCritical == 'yes') {
			$mailBody .= "<H2>Критично</H2>";   	
		}
		$mailBody .= "<div>Ошибка # ".$reportId."</div>";
		$mailBody .= "<H2>$subj</H2>";      
		$mailBody .= "<div>$problem</div>";   					
		$mailBody .= "<div>Ссылка: $page</div>";
		$mailBody .= "<div>Отправитель: ".$user->userLastName." ".$user->userFirstName."</div>";
		
		if ($isCritical == 'yes') {
			$subj .= " КРИТИЧНО";
		}
		$mailBody .= "<div>Дата: ".date("d.m.Y H:i")."</div>";
		
		$params = array(
			"message" => $mailBody,
			"subj" => $subj
		);

		emailQuery::sendEMails(CALL_CENTER_SUPPORT_EMAIL, $params);

		echo htmlspecialchars(json_encode(array('status'=>'success')), ENT_NOQUOTES);
	} elseif($user -> checkLoginUser() && $category == 'anonymous'){
        $mailBody = "";
        if ($isCritical == 'yes') {
            $mailBody .= "<H2>Критично</H2>";
        }
        $mailBody .= "<div>Анонимно</div>";
        $mailBody .= "<H2>$subj</H2>";
        $mailBody .= "<div>$problem</div>";
        $mailBody .= "<div>Ссылка: $page</div>";
        $mailBody .= "<div>Отправитель: ".$user->userLastName." ".$user->userFirstName."</div>";

        if ($isCritical == 'yes') {
            $subj .= " КРИТИЧНО";
        }
        $mailBody .= "<div>Дата: ".date("d.m.Y H:i")."</div>";

        $params = array(
            "message" => $mailBody,
            "subj" => $subj
        );
		
        emailQuery::sendEMails(TOP_SUPPORT_EMAIL, $params);

        echo htmlspecialchars(json_encode(array('status'=>'success')), ENT_NOQUOTES);

    }else {
		echo htmlspecialchars(json_encode(array('error'=>'Не подтвердилась авторизация')), ENT_NOQUOTES);
	}

