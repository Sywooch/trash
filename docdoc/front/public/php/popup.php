<?php

require_once dirname(__FILE__) . "/../include/header.php";

initDomXML();

$xmlString = '';
$xmlString .= '<dbInfo>';
$xmlString .= '</dbInfo>';

setXML($xmlString);

Yii::app()->runController('page/old/template/popupSpeciality/withoutLayout/yes');
