<?php
require_once dirname(__FILE__)."/../include/common.php";

$message = (isset($_POST['message'])) ? checkField($_POST['message'], "t", '') : '';
$doctorId = (isset($_POST['doctorId'])) ? checkField($_POST['doctorId'], "i", 0) : 0;
$type = (isset($_POST['type'])) ? checkField($_POST['type'], "h", '') : '';

if ( $doctorId == 0 ) setException ("Не передан идентификатор врача");
if ( $type == "" ) setException ("Не передан тип жалобы");

$msg = '';
switch ($type) {
    case 'complainPrice': $msg = 'Неверная стоимость приема.';break;
    case 'complainAddress': $msg = 'Неправильный адрес.';break;
    case 'complainDoctor': $msg = 'Врач не принимает.';break;
    case 'complainOther': {
        if ( $message == '' ) setException ("Не передано сообщение");
        $msg = $message;
        break;
    }
}



if (!empty($msg)) {
    $sql = "SELECT t1.id, t1.name, t1.rewrite_name FROM doctor t1 WHERE id=".$doctorId;
    $result = query($sql);
    $doctor = fetch_object($result);
}

if (!empty($doctor->rewrite_name)) {
    $doctorURL = $doctor->rewrite_name;
} else {
    $doctorURL = $doctor->id;
}

$subject = "[docdoc.ru]: пожаловались на врача";

$body = "Врач: ".$doctor->name." (#".$doctor->id.")<br>";
$body .= "Сообщение: ".$msg."<br><br>";
$body .= "<a href='http://".SERVER_FRONT."/doctor/".$doctorURL."'>Ссылка на профиль на сайте</a><br>";
$body .= "<a href='http://".SERVER_BACK."/doctor/index.htm?id=".$doctor->id."'>Ссылка на профиль в БО</a><br><br>";
$body .= "IP-адрес: ".(array_key_exists('HTTP_X_REAL_IP', $_SERVER) ? $_SERVER['HTTP_X_REAL_IP'] : $_SERVER['REMOTE_ADDR'] );


$params = array(
    "emailTo" => Yii::app()->params['email']['support'],
    "message" => $body,
    "subj" => $subject,
    "priority" => 5
);
@emailQuery::addMessage($params);

$params['emailTo'] = Yii::app()->params['email']['contact'];
@emailQuery::addMessage($params); 

setSuccess();
?>
