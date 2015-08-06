<?php
use dfs\docdoc\models\ClinicAdminModel;

require_once dirname(__FILE__) . "/../../lib/php/user.class.php";
require_once dirname(__FILE__) . "/../../include/common.php";
require_once dirname(__FILE__) . "/../../lib/php/mail.php";
require_once dirname(__FILE__) . "/../../lib/php/validate.php";
require_once dirname(__FILE__) . "/../../lib/php/emailQuery.class.php";


$user = new user();
$user->checkRight4page(array('ADM', 'CNM', 'ACM'), 'simple');

$clinic_admin_id = (isset($_POST['adminId'])) ? checkField($_POST['adminId'], "i", 0) : 0;
$id = (isset($_POST['id'])) ? checkField($_POST['id'], "i", 0) : '0';
$parentId = (isset($_POST['parentId'])) ? checkField($_POST['parentId'], "i", 0) : 0;
$email = (isset($_POST['adminEmail'])) ? checkField($_POST['adminEmail'], "t", "") : '';
$passwd = (isset($_POST['passwd'])) ? checkField($_POST['passwd'], "t", "") : '';
$sendInv = (isset($_POST['sendInv'])) ? checkField($_POST['sendInv'], "i", 0) : 0;

if (strlen(trim($passwd)) <= 7) {
	echo htmlspecialchars(json_encode(array('error' => 'Короткий пароль')), ENT_NOQUOTES);
	exit;
}

if ($clinic_admin_id > 0) {

	if (!$admin = ClinicAdminModel::model()->findByPk($clinic_admin_id)) {
		echo htmlspecialchars(json_encode(array('error' => 'Администратор не найден')), ENT_NOQUOTES);
		exit;
	}

	//смена только мыла может быть
	!empty($passwd) && $admin->passwd = $passwd;

} else {
	$admin = new ClinicAdminModel();
	$admin->passwd = $passwd;
}

$admin->email = $email;
$admin->clinics = array_merge($admin->clinics, [$id]);

if (!$admin->save()) {
	$errors = $admin->getErrors();
	$errors_to_json = [];

	foreach ($errors as $field => $field_errors) {
		foreach ($field_errors as $e) {
			$errors_to_json[] = $e;
		}
	}

	echo htmlspecialchars(json_encode(array('error' => $errors_to_json)), ENT_NOQUOTES);
	exit;
}

if ($sendInv == 1) {

	$mailBody = "<div>Добрый день!</div>";
	$mailBody = "<div><strong>Уведомление о смене пароля на портале docdoc.ru</strong></div>";
	$mailBody .= "<div>Логин: $email</div>";
	$mailBody .= "<div>Временный пароль: $passwd</div>";
	$mailBody .=
		"<div>Изменить пароль Вы можете в своем  <a href=\"http://" .
		SERVER_FRONT .
		"/lk/login\">личном кабинете</a>.</div>";
	$mailBody .=
		"<div><br><em>Система автоматических уведомлений.</em><br>Сервис по поиску врачей <a href=\"http://" .
		SERVER_FRONT .
		"\">" .
		SERVER_FRONT .
		"</a><br>Телефон:" .
		GeneralPhone .
		"</div>";

	$params = array(
		"emailTo" => $email,
		"message" => $mailBody,
		"subj"    => "Ваши учетные данные на портале docdoc.ru изменены"
	);
	if (!($id = emailQuery::addMessage($params))) {
		echo htmlspecialchars(json_encode(array('error' => 'Ошибка добавления в E-mail очередь')), ENT_NOQUOTES);
		exit;
	}
}

echo htmlspecialchars(json_encode(array('status' => 'success', 'id' => $id, 'parentId' => $parentId)), ENT_NOQUOTES);
