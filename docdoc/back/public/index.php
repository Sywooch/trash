<?php

use dfs\docdoc\components\Version;

require_once dirname(__FILE__)."/lib/php/user.class.php";
require_once dirname(__FILE__)."/include/header.php";
require_once dirname(__FILE__)."/doctor/php/doctorLib.php";
require_once dirname(__FILE__)."/doctor/php/doctorStat.php";
require_once dirname(__FILE__)."/clinic/php/clinicStat.php";
require_once dirname(__FILE__)."/opinion/php/opinionStat.php";


$user = new user();
$userId = $user -> idUser;

$session = Yii::app()->session;

if ( $userId > 0 && !isset($session['errorMsg']) ) {
	pageHeader(dirname(__FILE__)."/xsl/index.xsl");
} else {
	pageHeader(dirname(__FILE__)."/xsl/noLogin.xsl",'noLogin');
}

$version = new Version();

$xmlString  = '<srvInfo>';
$xmlString .= "<UserId>{$userId}</UserId>";
$xmlString .= "<Version>{$version->getCurrent()}</Version>";
$xmlString .= "<VersionImage>{$version->getImageUrl()}</VersionImage>";

// Ошибки авторизации
$xmlString .= getErrUser2XML();

$login = ( isset($session["login"]) ) ? $session["login"] : '';
$xmlString .= "<Login>".$login."</Login>";
$xmlString .= '</srvInfo>';
setXML($xmlString);

// Обнуляем ошибки в сессии
$session->remove('errorMsg');


if ( $userId > 0  ) {
	$xmlString  = '<dbInfo>';
	$xmlString .= $user -> getUserXML(); //

	$xmlString .= getStatusDictXML();
	$xmlString .= getDoctorStatisticXML(getCityId());


	$xmlString .= getStatusDict4ClinicXML();
	$xmlString .= getClinicStatisticXML('clinic', getCityId());
	$xmlString .= getClinicStatisticXML('center', getCityId());


	$xmlString .= getOpinionStatisticXML(getCityId());

	$xmlString .= '</dbInfo>';
	setXML($xmlString);
}

pageFooter('simple');


/*	Ошибки авторизации	*/
function getErrUser2XML () {
	$xml = '';

	$errorMsg = Yii::app()->session['errorMsg'];
	if ($errorMsg && is_array($errorMsg)) {
		$xml .= '<Errors>';
		foreach ($errorMsg as $mess) {
			$xml .= '<ErrorMess>'.$mess.'</ErrorMess>';
		}
		$xml .= '</Errors>';
	}
	return $xml;
}