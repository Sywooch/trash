<?php
use dfs\docdoc\asterisk\AsteriskApi;
use dfs\docdoc\api\components\ApiUserIdentity;

require_once __DIR__ . "/../include/common.php";

if (!isset($_SERVER['PHP_AUTH_USER'])) {
	ApiUserIdentity::showBaseAuthWindow();
}

$login = $_SERVER['PHP_AUTH_USER'];
$password = isset($_SERVER['PHP_AUTH_PW']) ? $_SERVER['PHP_AUTH_PW'] : null;

if ($login !== Yii::app()->params['asterisk']['api']['login']
	|| $password !== Yii::app()->params['asterisk']['api']['password']) {
	ApiUserIdentity::showBaseAuthWindow();
	exit();
}

$params = array();
if (isset($_POST)) {
	foreach ($_POST as $key => $value) {
		$params[] = "{$key}={$value}";
	}
}
$params = implode('&', $params);
new commonLog(Yii::app()->params['asterisk']['api']['logFile'], "[Request Start] POST_DATA: {$params}");

$params = array();

$params['filename']         = isset($_POST['filename']) ? $_POST['filename'] : '';
$params['phone']            = isset($_POST['phone']) ? $_POST['phone'] : '';
$params['city']             = isset($_POST['city']) ? $_POST['city'] : '';
$params['channel']          = isset($_POST['channel']) ? $_POST['channel'] : '';
$params['destinationPhone'] = isset($_POST['destination_phone']) ? $_POST['destination_phone'] : null;
$params['requestId']        = isset($_POST['requestId']) ? $_POST['requestId'] : (isset($_POST['RequestId']) ? $_POST['RequestId'] : null);
$params['recordType']       = isset($_POST['record_type']) ? $_POST['record_type'] : null;
$params['sip']              = isset($_POST['sip']) ? $_POST['sip'] : null;
$params['queue']            = isset($_POST['queue']) ? $_POST['queue'] : null;

$response = false;

try {
	if (empty($_GET['action'])) {
		throw new Exception("Not found action");
	}

	$api = new AsteriskApi();
	$action = $_GET['action'];
	switch($action) {
		case 'request':
			$response = $api->createRequest($params);
			break;
		case 'record':
			$response = $api->addRecord($params);
			break;
		case 'channel':
			$response = $api->addChannel($params);
			break;
		default:
			throw new Exception("Not found such method: '{$action}''");
	}
} catch (Exception $e) {
	new commonLog(Yii::app()->params['asterisk']['api']['logFile'], "[Error] {$e->getMessage()}");
	header('HTTP/1.0 500 Internal Server Error', true, 500);
	header('Status: 500 Internal Server Error', true, 500);
	$response = 0;
}

new commonLog(Yii::app()->params['asterisk']['api']['logFile'], "[Request End] Response: {$response}");

echo (int)$response;
