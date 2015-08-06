<?php
use dfs\docdoc\objects\Rejection;
use dfs\docdoc\models\DistrictModel;
use dfs\docdoc\models\RequestModel;
use dfs\docdoc\models\SipChannelModel;

require_once dirname(__FILE__) . "/../include/header.php";
require_once dirname(__FILE__) . "/../lib/php/validate.php";
require_once dirname(__FILE__) . "/php/requestLib.php";
require_once dirname(__FILE__) . "/../lib/php/request.class.php";
require_once dirname(__FILE__) . "/../lib/php/serviceFunctions.php";
require_once __DIR__ . "/../lib/php/RequestInterface.php";
require_once __DIR__ . "/../lib/php/models/DocRequest.php";
require_once __DIR__ . "/../lib/php/commonDict.php";

$type = isset($_GET['type']) ? $_GET['type'] : 'default';
$interface = new RequestInterface($type);

$user = new user();
$user->checkRight4page($interface->getAllowedRoles());
$userId = $user->idUser;

SipChannelModel::checkActiveRequest($userId, [ 'type' => $type ]);

pageHeader(dirname(__FILE__) . "/xsl/request.xsl");

$id = isset($_GET["id"]) ? checkField($_GET["id"], "i", 0) : 0;
$isAssign = !isset($_GET['isAssign']) || $_GET['isAssign'] !== '0';

$addActions = $interface->isCallCenter() || $interface->isDefault() ? true : false;
$xmlString = '<srvInfo>';
$xmlString .= "<TypeView>{$type}</TypeView>";
$xmlString .= "<AddActions>{$addActions}</AddActions>";
$xmlString .= '<Date>' . date("d.m.Y") . '</Date>';
$xmlString .= $user->getUserXML();
$xmlString .= '<Id>' . $id . '</Id>';
$xmlString .= '<Date>' . date("d.m.Y") . '</Date>';
$xmlString .= '<Hour>' . date("H") . '</Hour>';
$xmlString .= getCityXML();
$xmlString .= getCityListXML();

$session = Yii::app()->session;

$requestFilterparam = isset($session['requestFilter']) ? (array) $session['requestFilter'] : array();
$requestStr = "?" . http_build_query($requestFilterparam);

$xmlString .= '<RequestFilterString><![CDATA[' . rtrim($requestStr, "?") . ']]></RequestFilterString>';
$xmlString .= '</srvInfo>';
setXML($xmlString);

if (isset($session['error']) && !empty($session['error'])) {
	$xmlString = '<errorInfo>';
	$xmlString .= '<Error>' . $session['error'] . '</Error>';
	$xmlString .= '</errorInfo>';
	unset($session['error']);
	setXML($xmlString);
}


$xmlString = '<dbInfo>';

if ($id > 0) {

	$request = new request();
	$request->getRequest($id);
	$request->getRequestWithThisPhoneNumber();
	if ($user->checkRight4userByCode([ 'OPR', 'SOP', 'LIS' ])) {
		if ($isAssign) {
			$request->assignUser($userId);
		}
		$requestModel = RequestModel::model()->findByPk($id);
		$requestModel->openRequest();
	}

	//операторы не имеют права редактировать заявки в конечных статусах
	if (($user->checkRight4userByCode(['OPR', 'LIS']))
		&& in_array(
			$request->attributes['Status'],
			[RequestModel::STATUS_REJECT, RequestModel::STATUS_CAME, RequestModel::STATUS_NOT_CAME])
		&& !$user->checkRight4userByCode(['ADM', 'SOP'])
	) {

		$xmlString .= "<DisableRequest>1</DisableRequest>";
	} else {
		$xmlString .= "<DisableRequest>0</DisableRequest>";
	}

	$xmlString .= getRequestByIdXML($id);
	$xmlString .= "<AnotherRequest>" . arrayToXML($request->attributes['AnotherRequest']) . "</AnotherRequest>";
	$xmlString .= "<RejectReasons>" . arrayToXML(Rejection::getReasons()) . "</RejectReasons>";
	$xmlString .= getOpinionListByRequestIdXML($id);
} else {
	$kind = $interface->isDiagListener() ? DocRequest::KIND_DIAGNOSTICS : DocRequest::KIND_DOCTOR;
	$xmlString .= "<Request>";
	$xmlString .= "<Kind>{$kind}</Kind>";
	$xmlString .= "</Request>";
}
$cityId = isset($request->attributes['CityId']) ? $request->attributes['CityId'] : getCityId();
$items = DistrictModel::model()
	->inCity($cityId)
	->findAll();
$districts = [];
foreach ($items as $item) {
	$districts[] = $item->getData();
}
$xmlString .= "<DistrictList>" . arrayToXML($districts) . "</DistrictList>";

$xmlString .= getQueueUserXML($userId);
$xmlString .= getStatus4RequestXML();
$xmlString .= getType4RequestXML();
$xmlString .= getSourceType4RequestXML();
$xmlString .= getOperatorListXML();
$xmlString .= getSectorListXML();
$xmlString .= getAction4RequestHistoryXML();
$xmlString .= getKindsXML();
$xmlString .= getDiagnosticList();
$xmlString .= dictionaryXML('BillingStatusList', RequestModel::getBillingStatusList());
$xmlString .= dictionaryXML('PartnerStatusList', RequestModel::getPartnerStatuses());
$xmlString .= '</dbInfo>';
setXML($xmlString);

pageFooter();
