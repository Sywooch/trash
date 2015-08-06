<?php

use dfs\docdoc\models\PartnerModel;
use dfs\docdoc\models\RequestModel;
use dfs\docdoc\models\SipChannelModel;


require_once dirname(__FILE__) . "/../include/header.php";
require_once __DIR__ . "/../lib/php/RequestInterface.php";
require_once dirname(__FILE__) . "/php/requestLib.php";
require_once __DIR__ . "/../lib/php/commonDict.php";

$requestParams = getRequestParams();
$params = $requestParams['params'];
$filterParams = $requestParams['filters'];

$interface = new RequestInterface($filterParams['typeView']);
$user = new user();
$user->checkRight4page($interface->getAllowedRoles());
$userId = $user->idUser;

$lastSipRequestId = SipChannelModel::checkActiveRequest($userId, [ 'type' => $filterParams['typeView'] ]);

pageHeader(dirname(__FILE__) . "/xsl/index.xsl");

Yii::app()->session['requestFilter'] = $filterParams;

$xmlString = '<srvInfo>';
$xmlString .= '<Date>' . date("d.m.Y") . '</Date>';
$xmlString .= $user->getUserXML();
$xmlString .= getCityXML();
$xmlString .= getRequestFilterParamsXML($filterParams);
$filters = $interface->getFilters();
$xmlString .= "<Filter>";
foreach ($filters as $filter) {
	$xmlString .= "<{$filter}/>";
}
$xmlString .= "</Filter>";
$xmlString .= '<LastSipRequestId>' . $lastSipRequestId . '</LastSipRequestId>';
$xmlString .= '</srvInfo>';
setXML($xmlString);

$xmlString = '<dbInfo>';

$partners = array();
$items = PartnerModel::model()->findAll();
foreach ($items as $item) {
	array_push($partners, array(
		'Id'    => $item->id,
		'Name'  => $item->name,
	));
}
$xmlString .= "<PartnerList>" . arrayToXML($partners) . "</PartnerList>";

$typeDict = array();
foreach (RequestModel::getTypeNames() as $key => $item) {
	if (!($interface->isCallCenter() && $key == RequestModel::TYPE_CALL_TO_DOCTOR)) {
		array_push($typeDict, array('Id' => $key, 'Name' => $item));
	}
}
$xmlString .= getQueueUserXML($userId);
$xmlString .= "<TypeDict>" . arrayToXML($typeDict) . "</TypeDict>";
$xmlString .= getRequestListXML($params);
$xmlString .= getStatus4RequestXML();
$xmlString .= getSourceType4RequestXML();
$xmlString .= getDestinationPhonesXML();
$xmlString .= getOperatorListXML();
$xmlString .= getAction4RequestHistoryXML();
$xmlString .= getSectorListXML();
$xmlString .= getDiagnosticList();
$xmlString .= getQueueDict();
$xmlString .= getKindsXML();
$xmlString .= getCityListXML();

$xmlString .= '</dbInfo>';
setXML($xmlString);

pageFooter();
