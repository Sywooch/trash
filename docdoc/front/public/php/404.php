<?php

require_once dirname(__FILE__) . "/../include/header.php";
require_once dirname(__FILE__) . "/../lib/php/dictionary.php";

header('HTTP/1.0 404 Not Found');

initDomXML();

$xmlString = '';
$xmlString .= '<dbInfo>';
$xmlString .= '<ErrorMessage>'. $message .'</ErrorMessage>';
$xmlString .= '</dbInfo>';

setXML($xmlString);

Yii::app()->runController('page/old/template/404');
