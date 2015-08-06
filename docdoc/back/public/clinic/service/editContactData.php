<?php
use dfs\docdoc\models\ClinicAdminModel;

require_once dirname(__FILE__) . "/../../lib/php/user.class.php";
require_once dirname(__FILE__) . "/../../include/common.php";
require_once dirname(__FILE__) . "/../../lib/php/validate.php";
require_once dirname(__FILE__) . "/../../lib/php/mail.php";
require_once dirname(__FILE__) . "/../../lib/php/emailQuery.class.php";


$report = "";

$user = new user();
$user->checkRight4page(array('ADM', 'CNM', 'SOP', 'ACM'), 'simple');
$userId = $user->idUser;

$clinic_admin_id = (isset($_POST['adminId'])) ? checkField($_POST['adminId'], "i", 0) : 0;

$id = (isset($_POST['id'])) ? checkField($_POST['id'], "i", 0) : '0';
$parentId = (isset($_POST['parentId'])) ? checkField($_POST['parentId'], "i", 0) : 0;
$email = (isset($_POST['adminEmail'])) ? checkField($_POST['adminEmail'], "t", "") : '';
$passwd = (isset($_POST['passwd'])) ? checkField($_POST['passwd'], "t", "") : '';
$sendInv = (isset($_POST['sendInv'])) ? checkField($_POST['sendInv'], "i", 0) : 0;

$fname = (isset($_POST['firstName'])) ? checkField($_POST['firstName'], "t", "") : '';
$lname = (isset($_POST['lastName'])) ? checkField($_POST['lastName'], "t", "") : '';
$mname = (isset($_POST['middleName'])) ? checkField($_POST['middleName'], "t", "") : '';

$phone = (isset($_POST['phone'])) ? checkField($_POST['phone'], "t", "") : '';
$cellPhone = (isset($_POST['cellPhone'])) ? checkField($_POST['cellPhone'], "t", "") : '';
$adminComment = (isset($_POST['adminOperatorComment'])) ? checkField($_POST['adminOperatorComment'], "t", "") : '';

/*	Валидация	*/
if (!checkEmail($email)) {
	echo htmlspecialchars(json_encode(array('error' => 'Ошибки в поле Email')), ENT_NOQUOTES);
	exit;
}

if (($clinic_admin_id == 0 && strlen(trim($passwd)) <= 7) ||
	($clinic_admin_id > 0 && !empty($passwd) && strlen(trim($passwd)) <= 7)
) {
	echo htmlspecialchars(json_encode(array('error' => 'Короткий пароль')), ENT_NOQUOTES);
	exit;
}

$phone = modifyPhone($phone);
$cellPhone = modifyPhone($cellPhone);

if ($id > 0) {

	if ($clinic_admin_id > 0) {

		if (!$admin = ClinicAdminModel::model()->findByPk($clinic_admin_id)) {
			echo htmlspecialchars(json_encode(array('error' => 'Администратор не найден')), ENT_NOQUOTES);
			exit;
		}

		!empty($passwd) && $admin->passwd = $passwd; //пароль может быть пустым, значит не меняется
	} else {
		$admin = new ClinicAdminModel();
		$admin->passwd = $passwd;
	}

	$admin->email = $email;
	$admin->fname = $fname;
	$admin->lname = $lname;
	$admin->mname = $mname;
	$admin->phone = $phone;
	$admin->cell_phone = $cellPhone;
	$admin->admin_comment = $adminComment;
	$admin->clinics = [$id];

	$is_new = $admin->getIsNewRecord();

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
	} elseif ($is_new && $sendInv == 1) {
		$mailBody = "<div>Добрый день!</div>";
		$mailBody =
			"<div><strong>Вам открыт доступ к личному кабинету на портале docdoc.ru</strong>. Ваши данные для авторизации:</div>";
		$mailBody .= "<div>логин: $email</div>";
		$mailBody .= "<div>пароль: $passwd</div>";
		$mailBody .=
			"<div>Изменить пароль Вы можете в своем <a href=\"http://" .
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
			"subj"    => "Ваши учетные данные на портале docdoc.ru"
		);

		if (!emailQuery::addMessage($params)) {
			echo htmlspecialchars(
				json_encode(array('error' => 'Ошибка добавления в E-mail очередь')),
				ENT_NOQUOTES
			);
			exit;
		}

		$msg = "Изменение данных администора для клиники id = $id";
		$log = new logger();
		$log->setLog($user->idUser, 'U_ADM', $msg);
	}

} else {
	echo htmlspecialchars(json_encode(array('error' => 'Не передан идентификатор клиники')), ENT_NOQUOTES);
	exit;
}

echo htmlspecialchars(json_encode(array('status' => 'success', 'id' => $id, 'parentId' => $parentId)), ENT_NOQUOTES);
