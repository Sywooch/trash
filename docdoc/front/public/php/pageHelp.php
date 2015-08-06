<?php
require_once dirname(__FILE__)."/../include/header.php";
require_once LIB_PATH.'php/models/doctor.class.php';

initDomXML();

$cityId = Yii::app()->city->getCityId();

$xmlString = '<dbInfo>';
$xmlString .= '<SectorList>'.arrayToXML(Doctor::getSpecialities($cityId)).'</SectorList>';
$xmlString .= '</dbInfo>';

setXML($xmlString);

Yii::app()->runController('page/old/template/pageHelp');
