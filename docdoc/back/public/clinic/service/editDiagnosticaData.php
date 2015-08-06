<?php

use dfs\docdoc\models\ClinicModel;
use dfs\docdoc\models\DiagnosticClinicModel;

require_once dirname(__FILE__) . "/../../lib/php/user.class.php";
require_once dirname(__FILE__) . "/../../include/common.php";
require_once dirname(__FILE__) . "/../../lib/php/validate.php";

$report = "";

$user = new user();
$user->checkRight4page(array('ADM', 'CNM', 'SOP', 'ACM'), 'simple');
$userId = $user->idUser;

$id = (isset($_POST['id'])) ? checkField($_POST['id'], "i", 0) : '0';
$parentId = (isset($_POST['parentId'])) ? checkField($_POST['parentId'], "i", 0) : 0;
$diagnosticList = (isset($_POST['diagnosticPrice'])) ? $_POST['diagnosticPrice'] : array();
$diagnosticSpecialList = (isset($_POST['diagnosticSpecialPrice'])) ? $_POST['diagnosticSpecialPrice'] : array();
$priceListForOnline = (isset($_POST['priceForOnline'])) ? $_POST['priceForOnline'] : [];


if ($id > 0) {
	$diagnostics = [];
	foreach ($diagnosticList as $key => $price) {
		$diagnostic = new DiagnosticClinicModel();
		$diagnostic->diagnostica_id = $key;
		$diagnostic->price = $price;
		$diagnostic->special_price = $diagnosticSpecialList[$key];
		$diagnostic->price_for_online = $priceListForOnline[$key];
		$diagnostics[] = $diagnostic;
	}

	$clinic = ClinicModel::model()->findByPk($id);
	$clinic->saveDiagnostics($diagnostics);

	$msg = "Изменение стоимости исследований для ДЦ / id = $id";
	$log = new logger();
	$log->setLog($user->idUser, 'A_DCP', $msg);

} else {
	echo htmlspecialchars(json_encode(array('error' => 'Не передан идентификатор клиники')), ENT_NOQUOTES);
	exit;
}

echo htmlspecialchars(json_encode(array('status' => 'success', 'id' => $id, 'parentId' => $parentId)), ENT_NOQUOTES);
