<?php

require_once dirname(__FILE__) . "/../include/header.php";
require_once dirname(__FILE__) . "/../lib/php/dictionary.php";

initDomXML();

$xmlString = '';
$xmlString .= '<dbInfo>';
$xmlString .= getSpecializationListXML(null, Yii::app()->city->getCityId());
$xmlString .= '</dbInfo>';

setXML($xmlString);

Yii::app()->runController('page/old/template/popupSpeciality/withoutLayout/yes');
