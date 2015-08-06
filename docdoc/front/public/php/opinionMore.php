<?php

require_once dirname(__FILE__) . "/../include/header.php";
require_once LIB_PATH.'php/models/doctor.class.php';

initDomXML();

$id = (isset($params['id']))? intval($params['id']) : 0;

$xmlString = '';
$xmlString .= '<dbInfo>';
$xmlString .= '<DoctorId>'.$id.'</DoctorId>';
if ( $id > 0 ) {
	$doctor = new Doctor();
	$doctor -> setId($id);
	$data = $doctor-> getReviewList();
	$xmlString .= '<ReviewList>'.arrayToXML($data).'</ReviewList>';
}

$xmlString .= '</dbInfo>';

setXML($xmlString);

Yii::app()->runController('page/old/template/opinionMore/withoutLayout/yes');
