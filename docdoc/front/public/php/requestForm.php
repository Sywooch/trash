<?php

require_once dirname(__FILE__) . "/../include/header.php";
require_once LIB_PATH.'php/models/doctor.class.php';
require_once LIB_PATH . "../lib/php/russianTextUtils.class.php";

initDomXML();

$id = isset($_GET['id']) ? $_GET['id'] : 0;
$doctor = new Doctor($id);
$data = $doctor->data;

$xmlString = '';
$xmlString .= '<dbInfo>';
$xmlString .= '<Doctor>' . arrayToXML($data) . '</Doctor>';
$xmlString .= '</dbInfo>';

setXML($xmlString);

Yii::app()->runController('page/old/template/requestFormScreen/withoutLayout/yes');
