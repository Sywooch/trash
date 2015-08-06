<?php

require_once dirname(__FILE__)."/../include/header.php";
require_once dirname(__FILE__)."/../lib/php/dictionary.php";
require_once dirname(__FILE__)."/lib/libraryLib.php";

initDomXML();

$xmlString = '<dbInfo>';

if(isset($section)) {
    $section = getSection($section);
    if(count($section) > 0) {
        $articles = getArticles($section['Id'], 1);
        $xmlString .= '<ArticleSection>' . arrayToXML($section) . '</ArticleSection>';
        $xmlString .= getIllnessLikeXML($section['SpecId']);
    } else
        $this->pageError('Раздел статей не найден');
}
if(isset($articles))
    $xmlString .= '<ArticleList>' . arrayToXML($articles) . '</ArticleList>';

$xmlString .= getSpecializationListXML(null, Yii::app()->city->getCityId());

$xmlString .= '</dbInfo>';
setXML($xmlString);

Yii::app()->runController('page/old/template/librarySection');
