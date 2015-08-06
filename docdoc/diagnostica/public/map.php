<?php
require_once dirname(__FILE__) . "/include/header.php";
require_once LIB_PATH . "../lib/php/validate.php";
require_once dirname(__FILE__) . "/lib/php/diagnostica.php";

pageHeader(dirname(__FILE__) . "/map/xsl/map.xsl", "noFix");

$startPage = (isset($_GET["startPage"])) ? checkField($_GET["startPage"], "i") : 0;
$id = (isset($_GET["id"])) ? checkField($_GET["id"], "i", "") : "";
$diagnostica = (isset($_GET["diagnostic"])) ? checkField($_GET["diagnostic"], "i", 0) : 0;
$subDiagnostica = (isset($_GET["subDiagnostica"])) ? checkField($_GET["subDiagnostica"], "i", 0) : 0;
$sortRS = (isset($_GET["sortRS"])) ? checkField($_GET["sortRS"], "t", "non") : "non";

$city = Yii::app()->city->getCity();
$step = 15;

$xmlString = '<srvInfo>';
$xmlString .= '<StartPage>' . $startPage . '</StartPage>';
$xmlString .= '<Id>' . $id . '</Id>';
$xmlString .= '<Step>' . $step . '</Step>';
$xmlString .= '<SortRS>' . $sortRS . '</SortRS>';
$xmlString .= '<Diagnostica>' . $diagnostica . '</Diagnostica>';
$xmlString .= '<subDiagnostica>' . $subDiagnostica . '</subDiagnostica>';
$xmlString .= "<CityPrefix>{$city->prefix}</CityPrefix>";
$xmlString .= '</srvInfo>';
setXML($xmlString);

XMLload(dirname(__FILE__) . "/include/xml/counter.xml", $doc);

$params = array();
$params['id'] = $id;
$params['step'] = $step;
$params['startPage'] = $startPage;
$params['sortRS'] = $sortRS;
$params['diagnostica'] = $diagnostica;
$params['subDiagnostica'] = $subDiagnostica;


$xmlString = '<dbInfo>';

$xmlString .= '<DCenterList>' . arrayToXML(getDiagnosticList($params)) . '</DCenterList>';
unset($params['step']);
$allClinics = getDiagnosticList($params, false);
$xmlString .= "<DCenterListAll>" . arrayToXML($allClinics) . "</DCenterListAll>";
$xmlString .= '<Pager>' . arrayToXML(getPages(count($allClinics), $step, $startPage)) . '</Pager>';
$xmlString .= getDiagnosticDict();
$xmlString .= '</dbInfo>';
setXML($xmlString);

pageFooter('noFix');
