<?php
require_once dirname(__FILE__)."/../include/common.php";
require_once dirname(__FILE__)."/../lib/php/validate.php";
require_once dirname(__FILE__)."/../lib/php/mail.php";

$message = (isset($_POST['message'])) ? checkField($_POST['message'], "h", '') : '';
$clinicId = (isset($_POST['clinicId'])) ? checkField($_POST['clinicId'], "i", 0) : 0;

if ( $message == '' ) setException ("Не передано сообщение");

$subject = "[docdoc.ru]: вопрос от администратора клиники";
$body = "Вопрос от администратора клиники:<br>";
$body .= $message;
$email = 'info@docdoc.ru';

sendMessage($subject, $body, array($email));

setSuccess();

?>
