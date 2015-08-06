<?php
require_once dirname(__FILE__) . "/../include/header.php";
require_once dirname(__FILE__) . "/../lib/php/dictionary.php";

initDomXML();

$session = Yii::app()->session;

$session->remove('speciality');
$session->remove('stations');
$session->remove('area');
$session->remove('district');
$session->remove('regCity');
$session->remove('searchWord');

$xmlString = '<dbInfo>';
$xmlString .= specialityGroupListXML(Yii::app()->city->getCityId(), 5);
$xmlString .= '</dbInfo>';
setXML($xmlString);

Yii::app()->params['globalTrack'] = [
	'Name' => 'HomePage',
	'Params' => json_encode([
		'City' => Yii::app()->city->getTitle()
	]),
];

Yii::app()->runController('page/old/template/index');
