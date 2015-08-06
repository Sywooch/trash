<?php
use dfs\docdoc\models\CityModel;
use dfs\docdoc\models\DistrictModel;
use dfs\docdoc\components\WhiteLabel;

require_once dirname(__FILE__) . "/../include/common.php";

/** @var CHttpRequest $request */
$request = Yii::app()->request;

$specId = !empty($_POST['spec']) ? (int)$_POST['spec'] : 0;

if (!empty($_POST['stations'])) {

	if (!is_array($_POST['stations'])) {
		$stations = explode(',', $_POST['stations']);
	} else {
		$stations = $_POST['stations'];
	}

    $stations = array_map(function($v) {return (int)$v;}, $stations);

} else {
	$stations = array();
}

$dist = intval($request->getPost('dist'));

/** @var dfs\docdoc\components\City $city */
$city = Yii::app()->city;

$keywords = isset($_POST['keywords']) ? checkField($_POST['keywords'], "st", "") : '';

if ($keywords != '') {
	$url = '/contextSearch/keywords/' . $keywords;
} elseif ($specId > 0) {

	$spec = null;

	$sql = "SELECT rewrite_name FROM sector WHERE id=" . $specId;
	$result = query($sql);
	if (num_rows($result) == 1) {
		$row = fetch_object($result);
		$spec = $row->rewrite_name;
	}

	$url = '/doctor/' . $spec;

	if($city->getSearchType() == CityModel::SEARCH_TYPE_DISTRICT){
		$district = DistrictModel::model()->findByPk($dist);

		if($district instanceof DistrictModel){
			$url .= '/district/' . $district->rewrite_name;
		}

	} else {

		if (count($stations) == 1) {
			$sql = "SELECT rewrite_name FROM underground_station WHERE id=" . $stations[0];
			$result = query($sql);
			if (num_rows($result) == 1) {
				$row = fetch_object($result);
				$url = '/doctor/' . $spec . '/' . $row->rewrite_name;
			}
		} elseif (count($stations) > 1) {
			$url = '/doctor/' . $spec . '/stations/' . implode(',', $stations);
		}
	}


} elseif ($specId == 0) {
	$url = '/doctor';

	if($city->getSearchType() == CityModel::SEARCH_TYPE_DISTRICT){
		$district = DistrictModel::model()->findByPk($dist);

		if($district instanceof DistrictModel){
			$url = '/district/' . $district->rewrite_name;
		}
	} else {
		if(count($stations) > 0){
			$url = '/search/stations/' . implode(',', $stations);
		}
	}

} else {
	$url = '/doctor';
}

header('Location: ' . $url);
exit;

