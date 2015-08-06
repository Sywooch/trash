<?php

use dfs\docdoc\models\StationModel;
use dfs\docdoc\models\DistrictModel;
use dfs\docdoc\models\SectorModel;
use dfs\docdoc\models\StreetModel;
use dfs\docdoc\models\RatingStrategyModel;

require_once dirname(__FILE__) . "/../include/header.php";
require_once LIB_PATH . 'php/models/doctor.class.php';

initDomXML();

$xmlString = '<dbInfo>';
$urlsXml = '<urls></urls>';

$countDoctors = isset($landing) ? 9999 : 10;

$session = Yii::app()->session;
$sessionKeys = ['speciality', 'stations', 'area', 'district', 'regCity', 'street', 'searchWord', 'clinicListParams'];
foreach ($sessionKeys as $key)
	$session->remove($key);

$host = Yii::app()->city->host();
$resultsUrl = Yii::app()->request->url;
$session['resultsUrl'] = "http://{$host}{$resultsUrl}";

$cityId = Yii::app()->city->getCityId();
$params['city'] = $cityId;

$departure = isset($departure);
$kidsReception = isset($kidsReception);
$orderType = isset($orderType) ? $orderType : null;
$orderDir = isset($orderDir) ? $orderDir : null;


//параметры из класса Page, которые влияют на состояние страницы
$cacheKey = md5(print_r($params,1));
$doctorListCache = Yii::app()->cache->get("Page.DoctorList.{$cacheKey}." . \Yii::app()->rating->getId(RatingStrategyModel::FOR_DOCTOR));

if ($doctorListCache === false) {
	if (isset($speciality)) {
		$spec = getSpeciality($speciality);
		if (count($spec) > 0) {
			$session['speciality'] = $spec;
			$params['speciality'] = $spec['Id'];
		} else {
			Page::pageError();
		}
	}

	if (isset($regCity)) {
		$params['regCityAlias'] = $regCity;
		$regCity = getRegCity($regCity);
		if (count($regCity) > 1) {
			$session['regCity'] = $regCity;
			$stationsIds = getStationsByParams(array('regCity' => $regCity['Id']));
			$session['stations'] = getStations($stationsIds);
		} else {
			Page::pageError();
		}
	}

	if (isset($area)) {
		$area = getArea($area);
		if (count($area) > 0) {
			$session['area'] = $area;
			$stations = getStationsByParams(array('area' => $area['Id']));
		} else {
			Page::pageError();
		}
	}

	$districtModel = null;

	if (isset($district)) {
		$districtModel = DistrictModel::model()
			->cache(3600)
			->searchByAlias($district)
			->inCity($cityId)
			->find();

		if ($districtModel) {
			$districtData = $districtModel->getData();
			$session['district'] = $districtData;
			$params['district_id'] = $districtData['Id'];
		} else {
			Page::pageError();
		}
	}

	if (isset($streetAlias)) {
		$streetData = StreetModel::model()
			->cache(3600)
			->searchByAlias($streetAlias)
			->inCity($cityId)
			->find();

		if ($streetData !== null) {
			$session['street'] = $streetData;
			$params['street_id'] = $streetData->street_id;
		} else {
			Page::pageError();
		}
	}

	if (isset($stations)) {
		if (!is_array($stations)) {
			$params['stations'] = explode(',', $stations);
		} else {
			$params['stations'] = $stations;
		}

		if (count($params['stations']) == 1) {
			$stationModel = StationModel::model()->findByPk($params['stations']);
			if (is_null($stationModel)) {
				Page::pageError();
			}
		}

		if (isset($near)) {
			if ($near == 0) {
				$params['near'] = 'strict';
			}
		}

		$session['stations'] = getStations($params['stations']);
	}

	if (isset($stationAlias)) {
		if (isset($near)) {
			if ($near == 0) {
				$params['near'] = 'strict';
			}
		}

		$station = getStationByAlias($stationAlias, $params['city']);
		if (count($station) > 0) {
			$params['stations'] = array($station['Id']);
			$session['stations'] = array($station);
		} else {
			Page::pageError();
		}

	}

	$session['doctorListParams'] = [
		'page' => isset($page) ? $page : null,
		'order' => $orderType,
		'direction' => $orderDir,
	];

	// Колонки специальностей в нижем сео-блоке
	if (isset($station) && isset($params['speciality'])) {
		$specs = SectorModel::getItemsByStation($station['Id']);
		foreach ($specs as $key => $item) {
			$specs[$key]['InPlural'] = RussianTextUtils::wordInNominative($item['name'], true);
		}
		if (!empty($specs)) {
			$xmlString .= "<SpecialitiesByStation>" . getColumnsXML($specs) . "</SpecialitiesByStation>";
		}
	}

	//если выбран специалист и переданы станции, определяем ближайшие станции
	$NearestStations = null;
	$NearestDistricts = null;
	if (isset($params['stations']) && isset($spec['Id'])) {
		$NearestStations = StationModel::model()->getNearestStations($params['stations'], 4);
		$NearestDistricts = DistrictModel::model()->getNearestDistricts($params['stations'], 4);
	}

	if (isset($params["na-dom"])) {
		if (!empty($districtModel)) {
			$params['departure'] = "1";
		}
	}

	if (isset($params['deti'])) {
		if (!empty($districtModel)) {
			$params['kidsReception'] = '1';
		}
	}

	$xmlString .= '<Params>';

	if ($orderType !== null && $orderDir !== null) {
		$params['orderType'] = $orderType;
		$params['orderDir'] = $orderDir;
		$xmlString .= '<Sort>';
		$xmlString .= '<Type>' . $orderType . '</Type>';
		$xmlString .= '<Direction>' . $orderDir . '</Direction>';
		$xmlString .= '</Sort>';
	} else {
		$xmlString .= '<Sort>';
		$xmlString .= '<Type></Type>';
		$xmlString .= '<Direction></Direction>';
		$xmlString .= '</Sort>';
	}

	if ($departure) {
		$params['departure'] = $departure;
		$xmlString .= '<Departure>1</Departure>';
	} else {
		$xmlString .= '<Departure>0</Departure>';
	}

	$xmlString .= "<Filter>";

	if (isset($_GET['filter']) && is_array($_GET['filter'])) {
		if(in_array('booking', $_GET['filter'])){
			$params['booking'] = true;
			$xmlString .= '<Booking>1</Booking>';
		} else {
			$xmlString .= '<Booking>0</Booking>';
		}
	}

	$xmlString .= "</Filter>";

	if (!empty($spec["Id"]) && in_array($spec["Id"], SectorModel::$adultIds)) {
		$xmlString .= '<KidsReception>none</KidsReception>';
	} else {
		if ($kidsReception) {
			$params['kidsReception'] = $kidsReception;
			$xmlString .= '<KidsReception>1</KidsReception>';
		} else {
			$xmlString .= '<KidsReception>0</KidsReception>';
		}
	}

	if (isset($near)) {
		if ($near == 0) {
			$xmlString .= '<Near>0</Near>';
		} else {
			$xmlString .= '<Near>1</Near>';
		}
	} else {
		$near = 1;
		$xmlString .= '<Near>1</Near>';
	}

	$xmlString .= '</Params>';

	$params['page'] = isset($page) ? $page : 1;

	if (isset($searchWord)) {
		$params['searchWord'] = $keywords = checkField($searchWord, "t", "", false, 100);
		$params['searchWord'] = preg_replace("[/page/[0-9]]", '', $params['searchWord']);
		$session['searchWord'] = $params['searchWord'];
	}

	$params['near'] = 'strict';
	if (isset($landing)) {
		$params['clinicIds'] = Yii::app()->getParams()['clinicsForLanding'];
		$params['limitByClinic'] = 3;
		$page = 1;
	}

	$params['schedule'] = true;

	$doctors = Doctor::getItems($params, true);

	$count = count($doctors['data']);
	$addDoctors = array();
	if ($near == 1 && !isset($page)) {
		if ($count < $countDoctors) {
			$exceptionIds = array();
			foreach ($doctors['data'] as $item) {
				$exceptionIds[] = $item['Id'];
			}

			if (count($exceptionIds) > 0) {
				$params['exceptionIds'] = $exceptionIds;
			}
			$params['near'] = 'closest';
			$params['start'] = 0;
			$params['count'] = $countDoctors - $count;

			if (isset($regCity)) {
				$params['regCityAlias'] = null;
			}

			$addDoctors = Doctor::getItems($params);
		}
	};
	$allDoctors = array_merge($doctors['data'], $addDoctors);

	// Top best doctors
	if (isset($spec['Id']) && !isset($page)) {
		if (count($allDoctors) < $countDoctors) {
			$exceptionIds = array();
			foreach ($allDoctors as $item) {
				$exceptionIds[] = $item['Id'];
			}

			$params = array();
			$params['city'] = $cityId;
			if (count($exceptionIds) > 0) {
				$params['exceptionIds'] = $exceptionIds;
			}
			$params['start'] = 0;
			$params['count'] = $countDoctors - count($allDoctors);
			$params['orderType'] = 'rating';
			$params['orderDir'] = 'desc';
			$params['speciality'] = $spec['Id'];
			if (isset($landing)) {
				$params['clinicIds'] = Yii::app()->getParams()['clinicsForLanding'];
			}
			$params['schedule'] = true;
			$topDoctors = Doctor::getItems($params);
		}
	}

	$landing = isset($landing) ? 1 : 0;
	$context = isset($context) ? 1 : 0;
	$xmlString .= '<IsContextAdv>' . $context . '</IsContextAdv>';
	$xmlString .= '<IsLandingPage>' . $landing . '</IsLandingPage>';
	$xmlString .= '<Pager>' . arrayToXML($doctors['pages']) . '</Pager>';
	$xmlString .= '<DoctorCount>' . (count($addDoctors) + $doctors['total']) . '</DoctorCount>';
	if (isset($spec) && !$landing) {
		$xmlString .= getIllnessLikeXML($spec['Id']);
	}
	$xmlString .= '<DoctorList>' . arrayToXML($allDoctors) . '</DoctorList>';

	if (isset($spec['Alias'])) {
		$xmlString .= '<SpecialityAlias>' . $spec['Alias'] . '</SpecialityAlias>';
	}

	if (isset($topDoctors)) {
		$xmlString .= '<BestDoctorList>' . arrayToXML($topDoctors) . '</BestDoctorList>';
	}

	if (isset($NearestStations)) {
		$xmlString .= '<NearestStations>' . arrayToXML($NearestStations) . '</NearestStations>';
	}

	if (isset($NearestDistricts)) {
		$xmlString .= '<NearestDistricts>' . arrayToXML($NearestDistricts) . '</NearestDistricts>';
	}

	$xmlString .= getSpecializationListXML(null, $cityId);

	if (isset($spec) && !empty($spec['Id'])) {
		$specModel = SectorModel::model()->findByPk($spec['Id']);
		$xmlString .= "<RelatedSpecList>" . arrayToXML($specModel->getRelatedSpecialities()) . "</RelatedSpecList>";
	}

	// Формируем ссылки, чтобы использовать их в фильтрах

	$baseUrl = empty($spec) ? '/search' : '/doctor/' . $spec['Alias'];

	if (!empty($regCity)) {
		$baseUrl .= '/city/' . $regCity['Alias'];
	}
	elseif (!empty($area)) {
		$baseUrl .= '/area/' . $area['Alias'];

		if(!empty($districtModel)){
			$baseUrl .= '/' . $districtModel->rewrite_name;
		}
	}
	elseif (!empty($districtModel)) {
		$baseUrl .= '/district/' . $districtModel->rewrite_name;
	}
	elseif (!empty($stations)) {
		$baseUrl .= '/stations/' . (is_array($stations) ? implode(',', $stations) : $stations);
	}
	elseif (!empty($station)) {
		$baseUrl .= '/' . $station['Alias'];
	}

	$orderSuffix = $orderType ? '/order/' . $orderType . '/direction/' . $orderDir : '';

	$urls = [ 'Base' => $baseUrl ];

	if ($departure) {
		if ($kidsReception) {
			$urls['Departure'] = $baseUrl . '/deti' . $orderSuffix;
			$urls['KidsReception'] = $baseUrl . '/na-dom' . $orderSuffix;
			$baseUrl .= '/na-dom/deti';
		} else {
			$urls['Departure'] = $baseUrl . $orderSuffix;
			$urls['KidsReception'] = $baseUrl . '/na-dom/deti' . $orderSuffix;
			$baseUrl .= '/na-dom';
		}
	} else {
		if ($kidsReception) {
			$urls['Departure'] = $baseUrl . '/na-dom/deti' . $orderSuffix;
			$urls['KidsReception'] = $baseUrl . $orderSuffix;
			$baseUrl .= '/deti';
		} else {
			$urls['Departure'] = $baseUrl . '/na-dom' . $orderSuffix;
			$urls['KidsReception'] = $baseUrl . '/deti' . $orderSuffix;
		}
	}

	$urls['OrderPrice'] = $baseUrl . '/order/price/direction/' . ($orderDir === 'asc' && $orderType == 'price' ? 'desc' : 'asc');
	$urls['OrderExperience'] = $baseUrl . '/order/experience/direction/' . ($orderDir === 'desc' && $orderType == 'experience' ? 'asc' : 'desc');
	$urls['OrderRating'] = $baseUrl . '/order/rating/direction/' . ($orderDir === 'desc' && $orderType == 'rating' ? 'asc' : 'desc');

	$xmlString .= '</dbInfo>';
	$urlsXml = '<urls>' . arrayToXML($urls) . '</urls>';

	$doctorListCache['xmlString'] = $xmlString;
	$doctorListCache['urls'] = $urlsXml;

	$location = [];
	$locType = '';
	if (isset($districtData)) {
		$location[] = $districtData['Name'];
		$locType = 'Area';
	} elseif (isset($area)) {
		$location[] = $area['Name'];
		$locType = 'Area';
	} elseif (isset($station)) {
		$location[] = $station['Name'];
		$locType = 'Metro';
	} elseif (isset($session['stations'])) {
		foreach ($session['stations'] as $item) {
			$location[] = $item['Name'];
		}
		$locType = 'Metro';
	}

	$eventParams = [
		'City'     => Yii::app()->city->getTitle(),
		'Spec'     => empty($spec) ? 'Любая' : $spec['Name'],
		'Location' => implode(', ', $location),
		'LocType'  => $locType,
		'LocMulti' => count($location) > 1,
	];
	$doctorListCache['eventParams'] = $eventParams;
	$doctorListCache['session'] = [];

	foreach ($session->getKeys() as $key) {
		if (in_array($key, $sessionKeys)) {
			$doctorListCache['session'][$key] = $session->get($key);
		}
	}


	Yii::app()->cache->set("Page.DoctorList.{$cacheKey}", $doctorListCache, 3600);

} else {
	$xmlString = $doctorListCache['xmlString'];
	$urlsXml = $doctorListCache['urls'];
	$eventParams = $doctorListCache['eventParams'];
	foreach ($doctorListCache['session'] as $k => $v) {
		$session[$k] = $v;
	}
}

//сохраняем количество найденных докторов в глобальных переменных приложения, чтобы можно было в заголовке страницы его использовать
Yii::app()->params['docFoundNum'] = 10; //count($allDoctors);


setXML($xmlString);
setXML($urlsXml);


Yii::app()->params['globalTrack'] = [
	'Name' => 'SearchPage',
	'Params' => json_encode($eventParams),
];

Yii::app()->runController('page/old/template/doctorSearch');
