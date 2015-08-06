<?php

use dfs\components\Controller;

/**
 * Class SearchController
 */
class SearchController extends Controller
{
	/**
	 * Поиск врачей
	 *
	 * @throws CHttpException
	 */
	public function actionSearch()
	{
		$apiDto  = new ApiDto();
		$request = Yii::app()->request;

		$page            = $request->getQuery("page", 1);
		$order           = $request->getQuery("order", Yii::app()->params->default_sort);
		$direction       = $request->getQuery("direction", 'desc');
		$speciality      = $request->getQuery('speciality');
		$stationId       = $request->getQuery('stationId');
		$districtId      = $request->getQuery('districtId');
		$station         = $request->getQuery('station');
		$area            = $request->getQuery('area');
		$deti            = $request->getQuery('deti');
		$deti && $deti = 1;
		$nadom           = $request->getQuery('nadom');
		$nadom && $nadom = 1;
		
		$areaId          = null;
		$areaModel       = null;
		$specialityId    = null;
		$specialityModel = null;
		$stationModel    = null;
		$districtModel   = null;
		$district        = $request->getQuery("district");

		if ($speciality) {
			$specialityModel = $apiDto->getSpecialityByAlias($speciality);
			if (!$specialityModel) {
				throw new CHttpException(404, 'Специальность не найдена');
			}
			$specialityId = $specialityModel->getId();
		}

		if ($stationId) {
			if(strpos($stationId, ',') === false){
				$stationModel = $apiDto->getMetroById($stationId);

				if (!$stationModel) {
					throw new CHttpException(404, 'Станция метро не найдена');
				}

				$stationId = $stationModel->getId();
			}
		}

		if ($station) {
			$stationModel = $apiDto->getMetroByAlias($station);
			if (!$stationModel) {
				throw new CHttpException(404, 'Станция метро не найдена');
			}
			$stationId = $stationModel->getId();
		}

		if ($district) {
			$districtModel = $apiDto->getDistrictByAlias($district);
			if (!$districtModel) {
				throw new CHttpException(404, "Район не найден: {$district}");
			}
			$districtId = $districtModel->getId();
		}

		if ($area) {
			$areaModel = $apiDto->getAreaByAlias($area);
			if (!$areaModel) {
				throw new CHttpException(404, "Округ не найден: {$area}");
			}
			$areaId = $areaModel->getId();
		}

		$session = new CHttpSession;
		$session->open();
		$session['select_metro'] = $stationModel;
		$session['selectDistrict'] = $districtModel;
		$session['select_spec'] = $specialityModel;

		$typeSearch = strpos(Yii::app()->request->requestUri, 'landing') ? 'landing' : null;

		$doctors = $apiDto->getDoctors(
			$specialityId,
			$stationId,
			$districtId,
			$areaId,
			$order,
			$direction,
			$page,
			$typeSearch,
			$nadom,
			$deti
		);

		$city = Yii::app()->city->getModel();

		$eventParams = [
			'City'     => $city->getName(),
			'Spec'     => $specialityModel ? $specialityModel->getName() : 'Любая',
			'Location' => $stationModel ? $stationModel->getName() : null,
			'LocType'  => $stationModel ? 'Metro' : null,
			'LocMulti' => false,
		];
		Yii::app()->mixpanel->addTrack('SearchPage', $eventParams);

		Yii::app()->session['back_url_search'] = Yii::app()->request->url;

		$ratingParams = [];
		if (isset($specialityModel)) {
			$ratingParams['speciality'] = $specialityModel->getAlias();
		}

		if (isset($stationModel)) {
			if (isset($specialityModel)) {
				$ratingParams['station'] = $stationModel->getAlias();
			}
		}

		if ($stationId && !isset($ratingParams['station'])) {
			$ratingParams['stationId'] = $stationId;
		}

		if ($districtId) {
			$ratingParams['district'] = $districtModel->getAlias();
		}

		if ($area) {
			$ratingParams['area'] = $areaModel->getAlias();
		}

		if($deti !== null){
			$ratingParams['deti'] = 'deti';
		}

		if($nadom !== null){
			$ratingParams['nadom'] = 'na-dom';
		}

		$metroList = array();
		$districtList = array();
		if ($city->hasMetro()) {
			$metroList = $apiDto->getMetroList();
		} else {
			$districtList = $apiDto->getDistrictList();
		}

		$this->render(
			'search',
			[
				'total'        => $doctors['count'],
				'doctors'      => $doctors['doctors'],
				'speciality'   => $specialityModel,
				'metro'        => $stationModel,
				'cityId'       => Yii::app()->city->getModel()->getId(),
				'page'         => $page,
				'order'        => $order,
				'city'         => $city,
				'ratingParams' => $ratingParams,
				'specialityList' => $apiDto->getSpecialityList(),
				'metroList'      => $metroList,
				'districtList'   => $districtList,
			]
		);
	}

	/**
	 * Список всех врачей
	 *
	 * @param int $page
	 */
	public function actionAll($page = 1)
	{
		$apiDto = new ApiDto();
		$city = Yii::app()->city->getModel();

		$doctors = $apiDto->getDoctorsAll($page);

		$this->render('search', [
				'total'   => $doctors['count'],
				'doctors' => $doctors['doctors'],
				'cityId'  => $city->getId(),
				'page'    => $page,
				'city'    => $city
			]);
	}
} 
