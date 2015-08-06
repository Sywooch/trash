<?php
require_once dirname(__FILE__) . "/../include/header.php";
require_once __DIR__ . "/php/requestLib.php";

$user = new user();
$user ->checkRight4page(array('ADM', 'OPR', 'SOP'));
$userId = $user ->idUser;

pageHeader(dirname(__FILE__) . "/xsl/unsetqueue.xsl", "noHead");

$xmlString = '<srvInfo>';
$xmlString .= getQueueUserXML($userId);
$xmlString .= getQueueDict();
$xmlString .= getCityXML();
$xmlString .= '</srvInfo>';
setXML($xmlString);

pageFooter("noHead");
