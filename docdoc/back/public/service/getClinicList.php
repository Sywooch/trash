<?php
require_once dirname(__FILE__) . "/../include/common.php";

use \dfs\docdoc\models\ClinicModel;


header('Content-Type: text/html; charset=utf-8');
$city = getCityId();
$clinic = isset($_GET['q']) ? checkField($_GET['q'], "t", "") : "";

$items = ClinicModel::model()
	->active()
	->inCity($city)
	->searchByName($clinic)
	->findAll(array('order' => 'name'));

foreach ($items as $item) {
	print $item->short_name . "|" . $item->id . "\n";
}
