<?php

use dfs\docdoc\models\PartnerModel;
use dfs\docdoc\models\RequestModel;


require_once dirname(__FILE__) . "/../include/header.php";
require_once __DIR__ . "/../lib/php/RequestInterface.php";
require_once dirname(__FILE__) . "/php/requestLib.php";
require_once __DIR__ . "/../lib/php/commonDict.php";

$requestParams = getRequestParams();
$params = $requestParams['params'];
$filterParams = $requestParams['filters'];

$filterParams['typeView'] = RequestInterface::VIEW_PARTNERS;

$interface = new RequestInterface($filterParams['typeView']);
$user = new user();
$user->checkRight4page($interface->getAllowedRoles());
$userId = $user->idUser;

pageHeader(dirname(__FILE__) . "/xsl/partnerRequests.xsl");

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

$partnerStatuses = array();
foreach (RequestModel::getPartnerStatuses() as $key => $name) {
	$partnerStatuses[] = [ 'Id' => $key, 'Name' => $name ];
}

$billingStatuses = array();
foreach (RequestModel::getBillingStatusList() as $key => $name) {
	$billingStatuses[] = [ 'Id' => $key, 'Name' => $name ];
}

$xmlString .= getQueueUserXML($userId);
$xmlString .= "<TypeDict>" . arrayToXML($typeDict) . "</TypeDict>";
$xmlString .= "<PartnerStatusDict>" . arrayToXML($partnerStatuses) . "</PartnerStatusDict>";
$xmlString .= "<BillingStatusDict>" . arrayToXML($billingStatuses) . "</BillingStatusDict>";
$xmlString .= getRequestListXML($params, null);
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
