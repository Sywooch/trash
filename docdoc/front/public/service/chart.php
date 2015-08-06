<?php
require_once dirname(__FILE__)."/../include/common.php";
require_once dirname(__FILE__)."/../lib/php/validate.php";
require_once dirname(__FILE__)."/../lib/php/report.class.php";

$type     = (isset($_POST['chartType'])) ? checkField($_POST['chartType'], "t", "") : '';
$clinic   = (isset($_POST['chartClinic'])) ? checkField($_POST['chartClinic'], "i", 0) : 0;
$dateFrom = (isset($_POST['crDateFrom'])) ? checkField($_POST['crDateFrom'], "t", "") : '';
$dateTill = (isset($_POST['crDateTill'])) ? checkField($_POST['crDateTill'], "t", "") : '';
$spec     = (isset($_POST['docSpec'])) ? checkField($_POST['docSpec'], "h", "") : '';
$doctor   = (isset($_POST['docName'])) ? checkField($_POST['docName'], "h", "") : '';

if(empty($type) || empty($clinic) || empty($dateFrom) || empty($dateTill))
    setException("Не переданы необходимые данные");

$report = new report($clinic, $dateFrom, $dateTill);
$report->spec = $spec;
$report->doctor = $doctor;

switch($type){
    case 'byCount': $result = $report->getCountRequestByMonth();break;
    case 'byPercentage': $result = $report->getPercentageRequestByMonth();break;
    case 'bySpec': $result = $report->getCountRequestBySpec();break;
    default: setException("Неверный тип графика");
}

echo json_encode($result);

?>