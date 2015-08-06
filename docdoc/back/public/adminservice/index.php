<?php
use dfs\docdoc\models\SmsQueryModel;

require_once dirname(__FILE__) . "/../include/header.php";
require_once dirname(__FILE__) . "/../lib/php/validate.php";
require_once dirname(__FILE__) . "/../lib/php/smsQuery.class.php";
require_once dirname(__FILE__) . "/../lib/php/croneLocker.php";
require_once dirname(__FILE__) . "/../include/croneList.php";
require_once __DIR__ . "/../request/php/requestLib.php";

$user = new user();
$user ->checkRight4page(array('ADM'));

pageHeader(dirname(__FILE__) . "/xsl/index.xsl");

$xmlString = '<srvInfo>';
$xmlString .= $user ->getUserXML();
$xmlString .= '</srvInfo>';
setXML($xmlString);


$xmlString = '<dbInfo>';

$sql = "SELECT COUNT(idMail) AS cnt FROM mailQuery";
$result = query($sql);
$row = fetch_object($result);

$xmlString .= "<MailCount>" . $row ->cnt . "</MailCount>";


$xmlString .= "<SMSQuery>" . ((checkSMSquery()) ? "start" : "stop") . "</SMSQuery>";
$balance = SmsQueryModel::getBalance();
$xmlString .= "<SMSBalance id=\"" . SMS_GateId . "\">" . (($balance) ? $balance : "error") . "</SMSBalance>";

$xmlString .= "<CroneList>";

$croneList = new croneList;
$confStr = $croneList->cronList;

for ($i = 0; $i < count($confStr); $i++) {

	$str = $croneList->getConfig($confStr[$i]);
	if (1 || in_array($str['name'], array('croneBalance', 'croneSMS', 'croneSMSchecker'))) {

		$line = array(
			$str['name'],
			getCroneStatusParam('isAvailable', LOCK_FILE_CRONE_DIR . $str['file'], 'notExists'),
			getCroneStatusParam('maxLockedTry', LOCK_FILE_CRONE_DIR . $str['file'], 0),
			getCroneStatusParam('isAvailableGlobal', LOCK_FILE_CRONE_DIR . $str['file'], 'notExists')
		);
		//$xmlString .= '<Element isAvailable="'.boll2str($line[1]).'"  countFailTry="'.intval($line[2]).'" isAvailableGlobal="'.($line[3]=='true'?'On':'Off').'">'.$line[0].'</Element>';
		$xmlString .= '<Element isAvailable="' . boll2str($line[1]) . '"  countFailTry="' . intval($line[2]) . '" isAvailableGlobal="' . $line[3] . '">' . $line[0] . '</Element>';
	}
}

$xmlString .= "</CroneList>";

$xmlString .= getQueueList();
$xmlString .= '</dbInfo>';
setXML($xmlString);

pageFooter();

function boll2str($x)
{
	return (is_bool($x) ? ($x ? "true" : "false") : $x);
}
