<?php

use dfs\docdoc\models\UserModel;
use dfs\docdoc\models\ClinicModel;

require_once dirname(__FILE__) . "/../include/header.php";
require_once dirname(__FILE__) . "/../lib/php/dateTimeLib.php";
require_once dirname(__FILE__) . "/php/clinicLib.php";
require_once dirname(__FILE__) . "/../lib/php/models/clinic.class.php";

$user = new user();
$user->checkRight4page(array('ADM', 'CNM', 'SOP', 'ACM', 'SAL'), 'simple');

$id = (isset($_GET["id"])) ? checkField($_GET["id"], "i", 0) : 0;
$parentId = (isset($_GET["parentId"])) ? checkField($_GET["parentId"], "i", 0) : 0;
$slide = (isset($_GET['slide'])) ? checkField($_GET['slide'], "i", '1') : '1';

pageHeader(dirname(__FILE__) . "/xsl/clinic.xsl", "noHead");

$xmlString = '<srvInfo>';
$xmlString .= '<Id>' . $id . '</Id>';
$xmlString .= '<FrontServer>' . SERVER_FRONT . '</FrontServer>';
$xmlString .= '<Random>' . rand(0, 1000) . '</Random>';
$xmlString .= '<ParentId>' . $parentId . '</ParentId>';
$xmlString .= '<Slide>' . $slide . '</Slide>';
$xmlString .= $user->getUserXML();
$xmlString .= getCityXML();
$xmlString .= "<ShowSettings>" . $user->checkRight4userByCode(['ADM', 'CNM', 'SOP', 'ACM']) . "</ShowSettings>";
$xmlString .= '</srvInfo>';
setXML($xmlString);

$xmlString = '<dbInfo>';
$clinic = new Clinic();
$clinic->getClinic($id);
$xmlString .= $clinic->getClinicXML();

$xmlString .= getClinicByIdXML($id);
if ($parentId > 0) {
	$xmlString .= '<ParentClinic>';
	$xmlString .= getClinicByIdXML($parentId);
	$xmlString .= '</ParentClinic>';
}
$xmlString .= getStatusDictXML();
$xmlString .= getDiagnosticList();
$xmlString .= weekDaysDictXML(true);
$xmlString .= ratingDictXML();
$xmlString .= districtDictXML();
$xmlString .= "<ContractDict>" . arrayToXML(getContractList()) . "</ContractDict>";

$users = CHtml::listData(UserModel::model()->enabled()->withRoles(['ACN'])->findAll(), "user_id", "user_login");
$clinic = ClinicModel::model()->findByPk($id);

$xmlString .= "<SelectManager>" . CHtml::dropDownList('managerId', $clinic->manager_id, $users, ['empty' => "..."]) . "</SelectManager>";
$xmlString .= '</dbInfo>';
setXML($xmlString);

pageFooter('simple');
