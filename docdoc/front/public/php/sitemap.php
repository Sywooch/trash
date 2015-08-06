<?php
use dfs\docdoc\models\StreetModel;
use dfs\docdoc\models\SectorModel;

require_once dirname(__FILE__) . "/../include/header.php";
require_once dirname(__FILE__) . "/../lib/php/dictionary.php";
require_once dirname(__FILE__) . '/lib/sitemapLib.php';
require_once LIB_PATH . 'php/models/doctor.class.php';

initDomXML();

$cityId = Yii::app()->city->getCityId();

$xmlString = '<dbInfo>';

if (isset($speciality)) {
	$spec = Doctor::getSpec($speciality);
	if (!$spec) {
		Page::pageError('Специальность не найдена');
	}
	$xmlString .= '<SelectedSpec id="' . $spec->id . '">';
	if ($entity == 'doctor') {
		$xmlString .= '<Name>' . $spec->name . '</Name>';
		$xmlString .= '<Alias>' . $spec->alias . '</Alias>';
		if (in_array($spec->id, SectorModel::$adultIds)) {
			$xmlString .= '<KidsReception>no</KidsReception>';
		} else {
			$xmlString .= '<KidsReception>yes</KidsReception>';
		}
	} else {
		$xmlString .= '<Name>' . $spec->clinicTitle . '</Name>';
		$xmlString .= '<Alias>' . $spec->specAlias . '</Alias>';
	}
	if (isset($entity))
		$xmlString .= '<Entity>' . $entity . '</Entity>';
	$xmlString .= '</SelectedSpec>';
	//$xmlString .= getAreas($cityId);
	$xmlString .= getDistrictListXML($cityId);
	$xmlString .= getStationListXML($cityId);
	$xmlString .= getRegCityListXML($cityId);
} elseif (isset($street)) {
	$streets = StreetModel::model()
		->inCity($cityId)
		->hasClinics()
		->findAll();
	$xmlString .= '<StreetList>';
	foreach ($streets as $street) {
		$xmlString .= '<Element>' .
			'<street_id>' . $street->street_id . '</street_id>' .
			'<title>' . $street->title . '</title>' .
			'<rewrite_name>' . $street->rewrite_name . '</rewrite_name>' .
			'</Element>';
	}
	$xmlString .= '</StreetList>';
} else {
	$xmlString .= '<SpecList>' . arrayToXML(SectorModel::getItemsByCity($cityId, false)) . '</SpecList>';
	$xmlString .= getClinicListXML($cityId);
}

$xmlString .= '</dbInfo>';

setXML($xmlString);

Yii::app()->runController('page/old/template/sitemap');
