<?php
use dfs\docdoc\objects\Coordinate;

require_once dirname(__FILE__) . "/../include/header.php";
require_once LIB_PATH . "../lib/php/validate.php";
require_once dirname(__FILE__) . "/../lib/php/diagnostica.php";

pageHeader(dirname(__FILE__) . "/xsl/mapAdd.xsl", "noHead");

$startPage = (isset($_GET["startPage"])) ? checkField($_GET["startPage"], "i") : 0;
$diagnostica = (isset($_GET["diagnostica"])) ? checkField($_GET["diagnostica"], "t", "") : "";
$subDiagnostica = (isset($_GET["subDiagnostica"])) ? checkField($_GET["subDiagnostica"], "i", 0) : 0;
$sortRS = (isset($_GET["sortRS"])) ? checkField($_GET["sortRS"], "t", "non") : "non";
$coord = (isset($_GET["coordinats"])) ? $_GET["coordinats"] : array();

$coordinats = Coordinate::yandexBounds($coord);

$step = 15;

$xmlString = '<srvInfo>';
$xmlString .= '<StartPage>' . $startPage . '</StartPage>';
$xmlString .= '<Step>' . $step . '</Step>';
$xmlString .= '<SortRS>' . $sortRS . '</SortRS>';
$xmlString .= '<Diagnostica>' . $diagnostica . '</Diagnostica>';
$xmlString .= '<subDiagnostica>' . $subDiagnostica . '</subDiagnostica>';
$xmlString .= '</srvInfo>';
setXML($xmlString);

$params = array();
$params['step'] = $step;
$params['startPage'] = $startPage;
$params['sortRS'] = $sortRS;
$params['diagnostica'] = $diagnostica;
$params['subDiagnostica'] = $subDiagnostica;
$params['coord'] = (!is_null($coordinats)) ? $coordinats : [];

$xmlString = '<dbInfo>';

$xmlString .= '<DCenterList>' . arrayToXML(getDiagnosticList($params)) . '</DCenterList>';
unset($params['step']);
$allClinics = getDiagnosticList($params, false);
$xmlString .= "<DCenterListAll>" . arrayToXML($allClinics) . "</DCenterListAll>";
$xmlString .= '<Pager>' . arrayToXML(getPages(count($allClinics), $step, $startPage)) . '</Pager>';
$xmlString .= getDiagnosticDict();
$xmlString .= '</dbInfo>';
setXML($xmlString);

pageFooter('noHead');
