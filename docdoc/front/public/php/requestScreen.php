<?php

require_once dirname(__FILE__) . "/../include/header.php";
require_once LIB_PATH . 'php/models/doctor.class.php';
require_once dirname(__FILE__) . "/../lib/php/clinic.php";
require_once LIB_PATH . "../lib/php/russianTextUtils.class.php";

initDomXML();

$bookId = isset($bookId) ? checkField($bookId, "t", null) : null;

$xmlString = '';
$xmlString .= '<dbInfo>';

$doctorId = (isset($_REQUEST) && array_key_exists('doctor', $_REQUEST)) ? intval($_REQUEST['doctor']) : false;
$clinicId = (isset($_REQUEST) && array_key_exists('clinicId', $_REQUEST)) ? intval($_REQUEST['clinicId']) : false;

$requestBtnType =
	(isset($_REQUEST) && array_key_exists('requestBtnType', $_REQUEST)) ? $_REQUEST['requestBtnType'] : 'requestSelectDoctor';

$formType = in_array($requestBtnType, ['requestCardFullDoctor', 'requestCardFullClinic']) ? 'FullForm' : 'ShortForm';
$xmlString .= "<FormType>{$formType}</FormType>";

$xmlString .= '<requestBtnType>' . $requestBtnType . '</requestBtnType>';
if ($requestBtnType != 'requestSelectDoctor') {
	if (in_array($requestBtnType, ['requestCardFullDoctor', 'requestCardShortDoctor'])) {
		$formKind = 'DoctorForm';
	} elseif (in_array($requestBtnType, ['requestCardFullClinic', 'requestCardShortClinic'])) {
		$formKind = 'ClinicForm';
	}
	$xmlString .= "<formKind>{$formKind}</formKind>";
}

if ($doctorId) {
	$doctor = new Doctor($doctorId);
	$data = $doctor->data;
	$xmlString .= '<Doctor>' . arrayToXML($data) . '</Doctor>';

	if (!$clinicId) {
		$clinicId = $doctor->getDefaultClinicId($doctorId, false);
	}
}

if ($clinicId) {
	$xmlString .= '<Clinic>' . getClinicByAliasXML($clinicId) . '</Clinic>';
}

if (!empty($bookId)) {
	$sql = "SELECT t1.request_id AS requestId, t1.request_api_id AS bookId
				FROM request_4_remote_api t1
				INNER JOIN request t2 ON t2.req_id=t1.request_id
				WHERE request_api_id='$bookId' AND t2.req_status IN (0,1,2,3,6,7)";
	$result = query($sql);
	if (num_rows($result) == 1) {
		$row = fetch_object($result);
		$requestId = $row->requestId;
		$bookId = $row->bookId;
		$xmlString .= '<RequestId>' . $requestId . '</RequestId>';
		$xmlString .= '<BookId>' . $bookId . '</BookId>';
	}
}

$xmlString .= '</dbInfo>';

setXML($xmlString);

Yii::app()->runController('page/old/template/' . (isset($isThanks) ? 'requestThanks' : 'formRequestMain') . '/mode/headerSimple');
