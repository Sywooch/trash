<?php
require_once dirname(__FILE__) . "/../include/header.php";
require_once dirname(__FILE__) . "/../lib/php/dictionary.php";
require_once dirname(__FILE__) . "/lib/libraryLib.php";
require_once LIB_PATH . "../lib/php/models/doctor.class.php";

initDomXML();

$section = isset($section) ? getSection($section) : null;
$article = isset($article) ? getArticleByAlias($article) : null;

$city = Yii::app()->city->getCityId();

$xmlString = '<dbInfo>';

if (!empty($article)) {
	$xmlString .= '<Article>' . arrayToXML($article) . '</Article>';
} else
	$this->pageError('Статья не найдена');

if (!empty($section)) {
	$xmlString .= '<SectorId>' . $section['SpecId'] . '</SectorId>';
	$xmlString .= '<ArticleSection>' . arrayToXML($section) . '</ArticleSection>';
	$params = array();
	$params['speciality'] = $section['SpecId'];
	$params['city'] = $city;
	$params['start'] = 0;
	$params['count'] = 5;
	$params['orderType'] = 'rating';
	$params['orderDir'] = 'desc';
	$doctors = Doctor::getItems($params);
	$xmlString .= '<DoctorList>' . arrayToXML($doctors) . '</DoctorList>';
	$xmlString .= '<DoctorCount>' . Doctor::getCount($params) . '</DoctorCount>';
} else if (empty($article))
	$this->pageError();

$xmlString .= getIllnessLikeXML($section['SpecId']);
$xmlString .= getSpecializationListXML(null, $city);

$xmlString .= '</dbInfo>';

setXML($xmlString);

Yii::app()->runController('page/old/template/libraryArticle');
