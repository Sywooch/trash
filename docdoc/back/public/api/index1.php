<?php
use dfs\docdoc\api\components\ApiUserIdentity;
use dfs\docdoc\api\components\ApiFactory;

require_once dirname(__FILE__) . "/../include/common.php";
require_once dirname(__FILE__) . '/../lib/php/models/doctor.class.php';
require_once dirname(__FILE__) . '/../lib/php/models/clinic.class.php';
require_once dirname(__FILE__) . '/../lib/php/schedule.class.php';

if (!isset($_SERVER['PHP_AUTH_USER'])) {
	ApiUserIdentity::showBaseAuthWindow();
}

$login = $_SERVER['PHP_AUTH_USER'];
$password = isset($_SERVER['PHP_AUTH_PW']) ? $_SERVER['PHP_AUTH_PW'] : null;

$identity = new ApiUserIdentity($login, $password);

$params = [];
if (!$identity->authenticate()) {
	ApiUserIdentity::showBaseAuthWindow();
	exit;
} else {
	$params['partnerId'] = $identity->getId();
}

$params['rawData'] = file_get_contents("php://input");

try {
	$api = ApiFactory::getApi($_SERVER['REQUEST_URI'], $params);
	echo $api->run();
} catch (CException $e) {
	echo $e->getMessage();
}

foreach (Yii::app()->log->routes as $route) {
	if ($route instanceof CWebLogRoute) {
		// disable any weblogroutes
		$route->enabled = false;
	}
}

Yii::app()->end();
