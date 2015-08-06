<?php

use dfs\docdoc\models\QueueModel;

require_once dirname(__FILE__) . "/../include/header.php";
require_once __DIR__ . "/php/requestLib.php";

$user = new user();
$user ->checkRight4page(array('ADM', 'OPR', 'SOP'));
$userId = $user ->idUser;

pageHeader(dirname(__FILE__) . "/xsl/queue.xsl", "noHead");

$xmlString = '<srvInfo>';
$xmlString .= $user ->getUserXML();
$xmlString .= getQueueDict();
$xmlString .= getCityXML();
$xmlString .= '</srvInfo>';
setXML($xmlString);

$xmlString = '<dbInfo>';

$queueList = [];
foreach (QueueModel::getQueueNames() as $key => $item) {
	$queueList[] = [
		'id'    => $key,
		'name'  => $item,
	];
}

$xmlString .= getQueueUserXML($userId);
$xmlString .= getQueueList();
$xmlString .= '<QueueList>' . arrayToXML($queueList) . '</QueueList>';
$xmlString .= '</dbInfo>';
setXML($xmlString);

pageFooter("noHead");
