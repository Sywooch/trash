<?php
require_once dirname(__FILE__)."/../include/common.php";
require_once dirname(__FILE__)."/../lib/php/validate.php";
require_once dirname(__FILE__)."/../lib/php/mail.php";

$id = (isset($_POST['adminId'])) ? checkField($_POST['adminId'], "i", 0) : 0;
$currentPasswd = (isset($_POST['currentPassword'])) ? checkField($_POST['currentPassword'], "h", '') : '';
$newPasswd = (isset($_POST['newPassword'])) ? checkField($_POST['newPassword'], "h", '') : '';
$repeatPasswd = (isset($_POST['repeatPassword'])) ? checkField($_POST['repeatPassword'], "h", '') : '';

if ( $adminId == 0 ) setException ("Не передан идентификатор");
if ( $currentPasswd == '' || $newPasswd == '' || $repeatPasswd == '' ) setException ("Не передан пароль");

if($newPasswd == $repeatPasswd){
    $sql = "SELECT * FROM clinic_admin WHERE clinic_admin_id=".$id;
    $result = query($sql);
    $admin = fetch_object($result);

    if($admin->passwd == md5($currentPasswd)){
        $sql = "UPDATE clinic_admin SET passwd='".md5($newPasswd)."' WHERE clinic_admin_id=".$id;
        queryJS ($sql, 'Ошибка зименения пароля');

        $subject = "DocDoc.ru - Изменение пароля";
        $message = "<div>Добрый день!</div>";
        $message .= "<div>Вы поменяли пароль для входа в личный кабинет. Новый пароль для Вашего аккаунта: <strong>$newPasswd</strong></div>";
        sendMessage($subject, $message, array($admin->email));
    } else {
        setException('Неверно набран текущий пароль');
    }

    setSuccess();

} else {
    setException('Не совпадают новые пароли');
}

?>
