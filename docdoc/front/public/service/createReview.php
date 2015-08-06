<?php
use dfs\docdoc\models\DoctorOpinionModel;

require_once dirname(__FILE__) . "/../include/common.php";

$doctorId = (isset($_POST['doctorId'])) ? checkField($_POST['doctorId'], "i", 0) : 0;
$name = (isset($_POST['reviewName'])) ? checkField($_POST['reviewName'], "t", "") : '';
$phone = (isset($_POST['reviewPhone'])) ? checkField($_POST['reviewPhone'], "t", "") : '';
$ratingQualification = (isset($_POST['rating_qualification'])) ? checkField($_POST['rating_qualification'], "i", 0) : 0;
$ratingAttention = (isset($_POST['rating_attention'])) ? checkField($_POST['rating_attention'], "i", 0) : 0;
$ratingRoom = (isset($_POST['rating_room'])) ? checkField($_POST['rating_room'], "i", 0) : 0;
$text = (isset($_POST['reviewComment'])) ? checkField($_POST['reviewComment'], "t", "") : '';

// валидация
if ($doctorId <= 0 || empty($name) || empty($text)) {
	echo json_encode(array('status' => 'error', 'error' => "Data not valid"));
	exit;
}

if ($phone == '') {
	echo json_encode(array('status' => 'error', 'error' => "Phone not valid"));
	exit;
}

$opinion = new DoctorOpinionModel();
$opinion->setScenario(DoctorOpinionModel::SCENARIO_SITE);
$opinion->attributes = [
	'doctor_id'             => $doctorId,
	'name'                  => $name,
	'phone'                 => $phone,
	'rating_qualification'  => $ratingQualification,
	'rating_attention'      => $ratingAttention,
	'rating_room'           => $ratingRoom,
	'text'                  => $text,
];

if (!$opinion->save()) {
	foreach ($opinion->getErrors() as $field => $errors) {
		setException($errors[0]);
	}
}

echo json_encode(array('status' => 'success'));
exit;
