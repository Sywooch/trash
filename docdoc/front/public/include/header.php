<?php
use dfs\docdoc\objects\Phone;
use dfs\docdoc\components\AppController;
use dfs\docdoc\components\DocDocStat;

require_once dirname(__FILE__) . "/common.php";
require_once dirname(__FILE__) . "/../lib/php/clinic.php";
require_once dirname(__FILE__) . "/../lib/php/getMetroListByAZ.php";


/**
 * Устанавливает xml c информацией о странице (название, описание, параметры)
 * и набором данных для отображения заголовка страницы
 *
 */
function getHeaderXML() {
	global $doc;

	$url = str_replace('/?', '?', $_SERVER['REQUEST_URI']);

	$xmlString = '<?xml version="1.0" encoding="utf-8"?>';
	$xmlString  = '<srvInfo>';
	$xmlString .= getSrvInfo();
	$xmlString .= '</srvInfo>';
	setXML($xmlString);

	$xmlString = '<dbHeadInfo>';

	$xmlString .= getDbHeadInfo();

	$xmlString .= '</dbHeadInfo>';
	setXML($xmlString);
}


/**
 * Получение XML для заголовка страницы с поиском врачей
 *
 * @return string
 */
function getSrvInfo() {
	$xmlString = '';

	$xmlString .= '<Conf>';
	$xmlString .= '<SocialKey>' . socialKey . '</SocialKey>';
	$xmlString .= '<StatisticKey>' . statisticKey . '</StatisticKey>';
	$xmlString .= '<ShowClinicName>' . (int)Yii::app()->getParams()['doctorCard']['showClinicName'] . '</ShowClinicName>';
	$xmlString .= '<ShowClinicPhone>' . (int)Yii::app()->getParams()['doctorCard']['showClinicPhone'] . '</ShowClinicPhone>';
	$xmlString .= "<AllowOnlineBooking>" . (int)Yii::app()->params['allowOnlineBooking'] . "</AllowOnlineBooking>";
	$xmlString .= '</Conf>';

	$xmlString .= '<URL><![CDATA[' . urldecode($_SERVER['REQUEST_URI']) . ']]></URL>';

	$url4pager = urldecode($_SERVER['REQUEST_URI']);
	$url4pager = preg_replace("'/page/[0-9]+'si",'',$url4pager);
	$url4pager = preg_replace('/\?.*/','',$url4pager);

	$xmlString .= '<URL4Pager><![CDATA[' . $url4pager . ']]></URL4Pager>';
	$xmlString .= '<RefURL><![CDATA[' . AppController::getInternalReferer() . ']]></RefURL>';
	$xmlString .= "<IsMobile>".(int)isMobileBrowser()."</IsMobile>";

	if(strpos($_SERVER['REQUEST_URI'],'/register') !== false || strpos($_SERVER['REQUEST_URI'],'/request') !== false)
		$hideSearch = 1;
	else
		$hideSearch = 0;
	$xmlString .= "<HideSearch>".$hideSearch."</HideSearch>";

	$url = explode("?", urldecode($_SERVER['REQUEST_URI']), 2);
	if ( isset($url[0]) && $url[0] == "/" )
		$xmlString .= "<IsMainPage>1</IsMainPage>";


	if(strpos($_SERVER['REQUEST_URI'],'/request') !== false)
		$xmlString .= "<IsRequestPage>1</IsRequestPage>";

	if(strpos($_SERVER['REQUEST_URI'],'/landing') !== false)
		$xmlString .= "<IsLandingPage>1</IsLandingPage>";


	$xmlString .= "<PageTitle>" . Yii::app()->seo->getTitle() . "</PageTitle>";

	$xmlString .= "<SearchParams>";
	$geo = array();

	$session = Yii::app()->session;

	// Округ
	if(isset($session['area']))
		$xmlString .= "<Area>". arrayToXML($session['area']) ."</Area>";

	// Район
	if (isset($session['district'])) {
		$xmlString .= "<District>". arrayToXML($session['district']) ."</District>";
		$geo[] = $session['district']['Name'];
	}

	// Город подмосковья
	if(isset($session['regCity']))
		$xmlString .= "<RegCity>". arrayToXML($session['regCity']) ."</RegCity>";

	if(isset($session['searchWord']))
		$xmlString .= "<SearchWord><![CDATA[".$session['searchWord']."]]></SearchWord>";

	// Search params
	if(isset($session['speciality'])) {
		$xmlString .= "<SelectedSpeciality>" . arrayToXML($session['speciality']) . "</SelectedSpeciality>";
	}

	if(isset($session['street'])) {
		$xmlString .= "<SelectedStreet>" . arrayToXML($session['street']) . "</SelectedStreet>";
	}

	if(isset($session['stations'])) {
		$xmlString .= "<SelectedStations>" . arrayToXML($session['stations']) . "</SelectedStations>";
		if (empty($geo)) {
			foreach ($session['stations'] as $station) {
				$geo[] = $station['Name'];
			}
		}
	}

	$xmlString .= '<Geo>' . implode(',', $geo) . '</Geo>';
	$xmlString .= "</SearchParams>";

	return $xmlString;
}


/**
 * Метод для формирования части заголовка xml с набором данных
 *
 * @return string возвращает xml с
 */
function getDbHeadInfo()
{
	$xmlString = '';

	$xmlString .= '<ServerFront>' . SERVER_FRONT . '</ServerFront>';


	$session = Yii::app()->session;

	$session['autoSelectCity'] = false;

	/** @var dfs\docdoc\components\City $city */
	$city = Yii::app()->city;
	$cityId = $city->getCityId();

	$xmlString .= "<CurrentYear>" . date('Y', time()) . "</CurrentYear>";
	$xmlString .= "<City id='" . $cityId . "'>";
	$xmlString .= "<Name>" . $city->getTitle() . "</Name>";
	$xmlString .= "<NameInGenitive>" . $city->getTitle('genitive') . "</NameInGenitive>";
	$xmlString .= "<SearchType>" . $city->getSearchType() . "</SearchType>";
	$xmlString .= "<Diagnostica>" . $city->getDiagnosticUrl() . "</Diagnostica>";

	$xmlString .= "</City>";
	$xmlString .= "<City id='" . $cityId . "'>" . $city->getTitle() . "</City>"; // для поддержки старого
	$xmlString .= "<AutoSelectCity>" . (int)$session['autoSelectCity'] . "</AutoSelectCity>";

	$xmlString .= getHeadStatisticsXML();
	$xmlString .= getActiveCitiesXml();


	//генерируем SEO информацию
	Yii::app()->seo->seoInfo();

	$xmlString .= "<SEO>";
	$xmlString .= "<Title>" . Yii::app()->seo->getTitle() . "</Title>";
	$xmlString .= "<MetaKeywords>" . Yii::app()->seo->getMetaKeywords() . "</MetaKeywords>";
	$xmlString .= "<MetaDescription>" . Yii::app()->seo->getMetaDescription() . "</MetaDescription>";
	$xmlString .= "<Head>" . Yii::app()->seo->getHead() . "</Head>";
	$xmlString .= "<Texts>" . arrayToXML(Yii::app()->seo->getSeoTexts()) . "</Texts>";
	$xmlString .= "</SEO>";

	$sitePhone = (!is_null(Yii::app()->getParams()['phoneForABTest']) && $city->isMoscow())
		? new Phone(Yii::app()->getParams()['phoneForABTest'])
		: $city->getSitePhone();

	$trafficSource = \Yii::app()->trafficSource;
	$sitePhone = (isMobileBrowser() && !$trafficSource->isContext())
		? new Phone(Yii::app()->getParams()['phoneForMobile'])
		: $sitePhone;
	$siteOffice = $city->getSiteOffice();

	//если у нас заявка от патнера и у него есть телефон
	$referral = \Yii::app()->referral;
	$phone = $referral->getId() ? new Phone($referral->getPhone()): $sitePhone;

	$xmlString .= '<IsReferral>' . (int)$referral->getId() . '</IsReferral>';
	$xmlString .= '<IsAbTest>' . $referral->isABTest() . '</IsAbTest>';

	$session['phone'] = $phone->prettyFormat('');

	if (
		is_null($referral)
		|| $trafficSource->isContext()
	) {
		$xmlString .= "<ShowComagic>1</ShowComagic>";
	}

	$xmlString .= "<Phone>";
	$xmlString .= "<Short>" . $phone->prettyFormat('') . "</Short>";
	$xmlString .= "<Full>" . $phone->prettyFormat() . "</Full>";
	$xmlString .= "<Office digit=\"+" . $siteOffice . "\">" . $siteOffice->prettyFormat(). "</Office>";
	$xmlString .= "<General digit=\"+" . $sitePhone . "\">" . $sitePhone->prettyFormat() . "</General>";
	$xmlString .= "<Numerically>+" . $phone . "</Numerically>";
	$xmlString .= "</Phone>";
	$xmlString .= "<Emails>";
	$xmlString .= "<SupportEmail>" . Yii::app()->params['email']['support'] . "</SupportEmail>";
	$xmlString .= "<PublicEmail>" . Yii::app()->params['email']['public'] . "</PublicEmail>";
	$xmlString .= "</Emails>";
	$xmlString .= specialityGroupListXML($cityId, 3, getActiveSpecialityId());
	$xmlString .= "<MetroMapData>";
	$xmlString .= getMetroList($cityId, 4, getActiveMetroStationsIdsList());
	$xmlString .= getDistrictList($cityId);
	// $xmlString .= getMetroAlpabet_XML($cityId);
	// $xmlString .= getDistrict_XML($cityId);
	// $xmlString .= getArea_XML();
	$xmlString .= "</MetroMapData>";

	return $xmlString;
}

/**
 * Получение xml со статистикой
 *
 * @return string
 */
function getHeadStatisticsXML() {
	$stat = new DocDocStat(Yii::app()->params['DocDocStatisticFactor']);

	$xml = "<Statistics>";
	$xml .= "<RequestCount>{$stat->getRequestsCount()}</RequestCount>";
	$xml .= "<DoctorsCount>{$stat->getDoctorsCount()}</DoctorsCount>";
	$xml .= "<ReviewsCount>{$stat->getReviewsCount()}</ReviewsCount>";
	$xml .= "</Statistics>";

	return $xml;
}


/*
*/
function isMobileBrowser() {
	return Yii::app()->mobileDetect->isAdaptedMobile();
}

/**
 * Список активных Названий станций метро
 *
 * @return int[]
 */
function getActiveMetroStationsIdsList()
{
	$ret = array();
	if (!empty(Yii::app()->session['stations'])) {
		foreach(Yii::app()->session['stations'] as $station) {
			$ret[] = $station['Id'];
		}
	}

	return $ret;
}

/**
 * Активная профессия
 *
 * @return int|null
 */
function getActiveSpecialityId()
{
	return !empty(Yii::app()->session['speciality'])
		? Yii::app()->session['speciality']['Id']
		: null;
}
