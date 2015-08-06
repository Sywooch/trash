<?php
use \dfs\docdoc\models\QueueModel;
require_once dirname(__FILE__) . "/../../lib/php/user.class.php";
require_once dirname(__FILE__) . "/../../include/common.php";


$user = new user();
$user->checkRight4page(array('ADM'), 'simple');

$sip = (isset($_GET["sip"])) ? checkField($_GET["sip"], "i", 0) : 0;

try {
	if ($sip == 0) {
		throw new Exception("Не передан SIP");
	}
	$queue = QueueModel::model()->findByPk($sip);
	if (is_null($queue)) {
		throw new Exception("Не найден такой SIP");
	}
	if (!$queue->unregister()) {
		throw new Exception("Не удалилось");
	}
	echo htmlspecialchars(json_encode(array('status' => 'success')), ENT_NOQUOTES);

} catch (Exception $e) {
	echo htmlspecialchars(json_encode(array('error' => $e->getMessage())), ENT_NOQUOTES);
}
