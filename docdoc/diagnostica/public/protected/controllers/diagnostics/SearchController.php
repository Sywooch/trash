<?php
use dfs\docdoc\models\StationModel;
use dfs\docdoc\models\DistrictModel;
use dfs\docdoc\models\ClosestDistrictModel;
use dfs\docdoc\models\AreaModel;
use dfs\docdoc\models\RegCityModel;
use dfs\docdoc\models\ClinicModel;
use dfs\docdoc\models\DiagnosticaModel;
use dfs\docdoc\models\DiagnosticaSubtypeModel;
use dfs\docdoc\models\ContractModel;
use dfs\docdoc\diagnostica\models\Diagnostica;
use dfs\docdoc\extensions\DateTimeUtils;

/**
 * Class SearchController
 */
class SearchController extends FrontendController
{

	public $isFromSearch = false;
	public $searchUrl = null;
	public $hiddenFromSearch = false;
	public $advertisements = array();

	/**
	 * @var array
	 */
	public $districtsIds = array();

	/**
	 * @var RegCityModel
	 */
	public $regCity = null;

	/**
	 * @var array
	 */
	private $_scopes = array();

	/**
	 * @var string
	 */
	private $_order = 'price';

	/**
	 * @var string
	 */
	private $_direction = 'asc';

	/**
	 * @var array
	 */
	private $_stationIds = array();

	/**
	 * @var array
	 */
	private $_nearestScopes = null;

	public function actionCustom()
	{
		return $this->actionIndex();
	}

	/**
	 * Страница поиска диагностических центров
	 * Краткая анкета клиники
	 *
	 * @param string $rewriteName Алиас исследовния
	 * @param string $rewriteNameArea Алиас округа
	 * @param string $rewriteNameDistrict Алиас района
	 * @param string $rewriteNameStation Алиас станции метро
	 * @param string $rewriteNameCity Алиас города Подмосковья
	 *
	 * @throws CHttpException
	 */
	public function actionIndex(
		$rewriteName = null,
		$rewriteNameArea = null,
		$rewriteNameDistrict = null,
		$rewriteNameStation = null,
		$rewriteNameCity = null
	)
	{
		$session = Yii::app()->session;
		$session['resultsUrl'] = Yii::app()->request->url;

		/**
		 * Костыль для исключения неверных ссылок со станциями
		 * @link https://docdoc.atlassian.net/browse/DD-506
		 */
		$queryStr = Yii::app()->request->getQueryString();
		if (strpos($queryStr, 'stations') !== false) {
			throw new CHttpException(404, "Станции метро указаны некорректно.");
		}

		$data = $this->getSearchData(compact(
			'rewriteName',
			'rewriteNameArea',
			'rewriteNameDistrict',
			'rewriteNameStation',
			'rewriteNameCity'
		));

		$this->render('index', $data);
	}

	/**
	 * Получение данных для страницы поиска
	 *
	 * @param array $params
	 *
	 * @return array
	 * @throws CHttpException
	 */
	public function getSearchData($params)
	{
		$city = Yii::app()->city;
		Yii::app()->seo->addParam('city', $city->getCity());

		$this->_scopes = array(
			'onlyDiagnostic',
			'active',
			'inCity' => array($city->getCityId()),
		);

		if (isset($params['hasDiscountOnOnline'])) {
			$this->_scopes['hasDiscountOnOnline'] = true;
		}

		if (isset($params['hasOnline'])) {
			$this->_scopes['withTariffs'] = [[ContractModel::TYPE_DIAGNOSTIC_ONLINE]];
		}

		$params['rewriteName'] = isset($params['rewriteName']) ? $params['rewriteName'] : null;
		$params['rewriteNameCity'] = isset($params['rewriteNameCity']) ? $params['rewriteNameCity'] : null;
		$params['rewriteNameArea'] = isset($params['rewriteNameArea']) ? $params['rewriteNameArea'] : null;
		$params['rewriteNameDistrict'] = isset($params['rewriteNameDistrict']) ? $params['rewriteNameDistrict'] : null;
		$params['rewriteNameStation'] = isset($params['rewriteNameStation']) ? $params['rewriteNameStation'] : null;

		$this->searchByDiagnostic($params['rewriteName']);
		$this->searchByRegCity($params['rewriteNameCity']);
		$this->searchByArea($params['rewriteNameArea']);
		$this->searchByDistrict($params['rewriteNameDistrict']);
		$this->searchByStation($params['rewriteNameStation']);

		$orders = [];
		if (!is_null($this->parentDiagnostic)) {
			$orders = [
				'price' => ['по стоимости', 'asc'],
			];
		}

		$this->_stationIds = array();
		if ($this->stations) {
			foreach ($this->stations as $item) {
				$this->_stationIds[] = $item->id;
			}
		}

		Yii::app()->session['searchResultsUrl'] = Yii::app()->request->requestUri;

		$this->_direction = isset($_GET['direction']) ? $_GET['direction'] : (Yii::app()->session['direction'] ? : 'desc');

		$criteria = new CDbCriteria();
		$criteria->scopes = $this->_scopes;
		$this->searchSorting($criteria);

		$clinics = ClinicModel::model()->findAll($criteria);

		$bestClinics = null;

		if (!$this->isLandingPage) {
			$clinics = $this->addNearestStations($clinics);

			$paidCount = 0;
			foreach ($clinics as $clinic) {
				if (!$clinic->show_in_advert) {
					break;
				}
				$paidCount++;
			}

			if ($paidCount < 4) {
				$clinics = $this->addAdvertisement($clinics, 1);
				$clinics = $this->addAdvertisement($clinics, 5);
			}

			$bestClinics = $this->findBestClinics($clinics);
		}

		$dataProvider = $this->clinicsDataProvider($clinics);
		$bestClinicsDataProvider = $bestClinics ? new CArrayDataProvider($bestClinics) : null;

		$count = count($clinics);

		$vars = array(
			'near' => true,
			'orders' => $orders,
			'order' => $this->_order,
			'direction' => $this->_direction,
			'oppDirection' => ($this->_direction === 'asc') ? 'desc' : 'asc',
			'dataProvider' => $dataProvider,
			'bestClinicsDataProvider' => $bestClinicsDataProvider,
			'stationIds' => $this->_stationIds,
			'diagnostics' => $this->diagnostics,
			'diagnostic' => $this->diagnostic ? $this->diagnostic : ($this->parentDiagnostic ? $this->parentDiagnostic : null),
			'count' => $count,
		);

		$this
			->buildBaseUrl($vars)
			->buildDiagnostics($vars)
			->buildGeoLinks($vars, $this->_stationIds, $params)
			->buildPager($vars, $dataProvider);

		return $vars;
	}

	/**
	 * Поиск по диагностике
	 *
	 * @param string $alias
	 * @return bool
	 * @throws CHttpException
	 */
	public function searchByDiagnostic($alias)
	{

		if (in_array($alias, array('station', 'area', 'district'))) {
			$url = parse_url(Yii::app()->request->url);
			$params = explode('/', $url['path']);
			$alias = $params[1];
		}

		if (empty($alias)) {
			if (isset(Yii::app()->session['diagnostic'])) {
				unset(Yii::app()->session['diagnostic']);
			}
			return false;
		}

		$diagnostic = DiagnosticaModel::model()->searchByAlias($alias)->find();

		if (empty($diagnostic)) {
			throw new CHttpException(404, 'Диагностика не найдена.');
		}

		if ($diagnostic->parent_id > 0) {
			$this->oneDiagnostic = true;
			$this->diagnostic = Diagnostica::model()->findByPk($diagnostic->id);
			$this->parentDiagnostic = Diagnostica::model()->findByPk($diagnostic->parent_id);
		} else {
			$this->parentDiagnostic = Diagnostica::model()->findByPk($diagnostic->id);
		}


		$this->_scopes['searchByDiagnostics'] = array(array($diagnostic->id), false);
		Yii::app()->seo->addParam('diagnostic', $diagnostic->id);
		Yii::app()->session['diagnostic'] = $diagnostic->id;

		return true;

	}

	/**
	 * Поиск по городу-спутнику
	 *
	 * @param string $alias
	 * @return bool
	 * @throws CHttpException
	 */
	public function searchByRegCity($alias)
	{
		if (empty($alias)) {
			return false;
		}

		$city = RegCityModel::model()
			->inCity(Yii::app()->city->getCityId())
			->searchByAlias($alias)
			->find();

		if (is_null($city)) {
			throw new CHttpException(404, 'Диагностика не найдена.');
		}

		$this->_scopes['searchByStations'] = array($city->getStationIds());
		Yii::app()->seo->addParam('regCity', $city->id);

		$this->stations = $city->stations;
		$this->regCity = $city;

		return true;
	}

	/**
	 * Поиск по округу
	 *
	 * @param string $alias
	 * @return bool
	 * @throws CHttpException
	 */
	public function searchByArea($alias)
	{
		if (strpos(Yii::app()->request->url, "area/")) {
			$params = explode('/', Yii::app()->request->url);
			$key = array_search('area', $params);
			$alias = $params[$key + 1];
		}

		if (empty($alias)) {
			return false;
		}

		$area = AreaModel::model()
			->searchByAlias($alias)
			->find();

		if (is_null($area)) {
			throw new CHttpException(404, 'Диагностика не найдена.');
		}

		$this->_scopes['inDistricts'] = array($area->getDistrictIds());
		Yii::app()->seo->addParam('area', $area->id);

		$this->district = DistrictModel::model()->inArea($area->id)->findAll();
		$this->area = $area;

		return true;

	}

	/**
	 * Поиск по районам
	 *
	 * @param string $alias
	 * @return bool
	 * @throws CHttpException
	 */
	public function searchByDistrict($alias)
	{
		if ($url = Yii::app()->request->url) {
			$pattern = '~^(.+)/district/(?P<district>([0-9a-zA-Z_-]+))?(.+)?$~';
			preg_match($pattern, $url, $matches);
			if (isset($matches['district'])) {
				$alias = $matches['district'];
			}
		}

		$districts = array();
		if (!empty($alias)) {
			$district = DistrictModel::model()
				->inCity(Yii::app()->city->getCityId())
				->searchByAlias($alias)
				->find();

			if (is_null($district)) {
				throw new CHttpException(404, 'Диагностика не найдена.');
			}

			Yii::app()->seo->addParam('district', $district->id);

			$districts = array($district->id);
			$this->district = array($district);
		} elseif (isset($_GET['districts'])) {
			$items = DistrictModel::model()->findAllByPk($_GET['districts']);
			foreach ($items as $item) {
				$districts[] = $item->id;
			}
			$this->district = $items;
		}

		if (!empty($districts)) {
			$this->_scopes['inDistricts'] = array($districts);
			$this->districtsIds = $districts;
			$this->stations = StationModel::model()->searchByDistricts($districts)->findAll();
		}
		return true;

	}

	/**
	 * Поиск по станциям
	 *
	 * @param string $alias
	 * @return bool
	 * @throws CHttpException
	 */
	public function searchByStation($alias)
	{

		if ($url = Yii::app()->request->url) {
			$pattern = '~^(.+)/station/(?P<station>([0-9a-zA-Z_-]+))?(.+)?$~';
			preg_match($pattern, $url, $matches);
			if (isset($matches['station'])) {
				$alias = $matches['station'];
			}
		}

		$stations = array();
		if (!empty($alias)) {
			$station = StationModel::model()
				->inCity(Yii::app()->city->getCityId())
				->searchByAlias($alias)
				->find();

			if (is_null($station)) {
				throw new CHttpException(404, 'Диагностика не найдена.');
			}
			$stations = array($station->id);
			$this->stations = array($station);

		} elseif (isset($_GET['stations'])) {
			$stationFromGet = Yii::app()->request->getQuery("stations");

			foreach ($stationFromGet as $key => $val) {
				if (is_array($val)) {
					throw new CHttpException(404, "Станции метро указаны некорректно.");
				}
			}
			$items = StationModel::model()->findAllByPk($stationFromGet);
			foreach ($items as $item) {
				$stations[] = $item->id;
			}
			$this->stations = $items;
		}

		if (count($stations) > 0) {
			$this->_scopes['searchByStations'] = array($stations);
		}
		Yii::app()->seo->addParam('stations', $stations);

		return true;

	}

	/**
	 * Сортировка для выборки
	 *
	 * @param CDbCriteria $criteria
	 *
	 * @return $this
	 */
	private function searchSorting($criteria)
	{
		Yii::app()->seo->addParam('order', $this->_order);
		Yii::app()->seo->addParam('direction', $this->_direction);

		switch (isset($_GET['order']) ? $_GET['order'] : null) {
			case 'price':
				$this->_order = 'price';
				$criteria->scopes['sortByPrice'] = array(strtoupper($this->_direction));
				break;

			default:
				$this->_order = 'price';
				if (!empty($this->_stationIds)) {
					$criteria->scopes['sortByClosestStation'] = [$this->_stationIds];
				} else {
					$criteria->scopes['sortForCommerce'] = array();
				}
				break;
		}

		return $this;
	}

	/**
	 * Добавление в выборку ближайших клиник
	 *
	 * @param array $clinics
	 *
	 * @return array
	 */
	private function addNearestStations($clinics)
	{
		$countNearestClinics = 10 - count($clinics);
		if ($countNearestClinics > 1) {
			$nearestScopes = $this->getNearestScopes();
			if ($nearestScopes) {
				$criteria = new CDbCriteria();
				$criteria->scopes = $nearestScopes + $this->_scopes;
				$criteria->limit = $countNearestClinics;
				$this->searchSorting($criteria);

				$nearestClinics = ClinicModel::model()
					->except($this->clinicsIds($clinics))
					->findAll($criteria);

				$clinics = array_merge($clinics, $nearestClinics);
			}
		}
		return $clinics;
	}

	/**
	 * Получение критериев для поиска ближайших клиник
	 *
	 * @return array
	 */
	private function getNearestScopes()
	{
		if (is_null($this->_nearestScopes)) {
			$this->_nearestScopes = array();
			$nearestStations = empty($this->_stationIds) ? null : StationModel::model()->getNearestStationIds($this->_stationIds, 20);
			if (!empty($nearestStations)) {
				$this->_nearestScopes['searchByStations'] = array($nearestStations);
			}
			elseif (!empty($this->district)) {
				$ids = array();
				foreach ($this->district as $district) {
					$ids[] = $district->id;
				}

				$data = ClosestDistrictModel::model()
					->inDistricts($ids)
					->findAll();

				$districtIds = array();
				foreach ($data as $item) {
					$districtIds[] = $item->closest_district_id;
				}
				$this->_nearestScopes['inDistricts'] = array($districtIds);
			}
		}
		return $this->_nearestScopes;
	}

	/**
	 * Получить id клиник из массива
	 *
	 * @param array $clinics
	 *
	 * @return array
	 */
	private function clinicsIds($clinics)
	{
		$ids = array();
		foreach ($clinics as $clinic) {
			$ids[] = $clinic->id;
		}
		return $ids;
	}

	/**
	 * Добавление в выборку клиник платного объявления
	 * @param mixed $clinics
	 * @param integer $position
	 *
	 * @return mixed
	 */
	protected function addAdvertisement($clinics, $position)
	{
		$clinicIds = array();
		foreach ($clinics as $clinic) {
			$clinicIds[] = $clinic['id'];
		}
		$clinicIds = array_slice($clinicIds, 0, 10);
		$diagnostics = array();
		if (!empty(Yii::app()->session['diagnostic'])) {
			$diagnostic = Diagnostica::model()->findByPk(Yii::app()->session['diagnostic']);
			$items = Diagnostica::model()->findAll('parent_id = ' . $diagnostic->id);
			$diagnostics = array();
			$diagnostics[0] = $diagnostic->id;
			foreach ($items as $item) {
				$diagnostics[] = $item->id;
			}

		}

		$criteria = new CDbCriteria();
		$criteria->scopes = $this->getNearestScopes();
		$paidClinics = ClinicModel::model()
			->paidItems($diagnostics, $clinicIds, Yii::app()->city->getCityId())
			->withTariffs([ContractModel::TYPE_DIAGNOSTIC_ONLINE])
			->findAll($criteria);

		if (count($clinics) >= $position - 1 && !empty($paidClinics)) {
			if ($position <> 1 || empty($clinics) || ($clinics[$position - 1]['sort4commerce'] == 99 && $position == 1)) {
				$randomPaidClinic = ClinicModel::getRandomItem($paidClinics);
				$arrSlice = array_slice($clinics, $position - 1);
				array_unshift($arrSlice, $randomPaidClinic);
				array_splice($clinics, $position - 1, count($clinics), $arrSlice);
				$this->advertisements[] = $randomPaidClinic['id'];
			}
		}

		return $clinics;
	}

	/**
	 * Получение DataProvider для выборки
	 *
	 * @param array $clinics
	 *
	 * @throws CHttpException
	 * @return CArrayDataProvider
	 */
	private function clinicsDataProvider($clinics)
	{
		$dataProvider = new CArrayDataProvider($clinics, array(
			'pagination'     => array(
				'pageVar'  => 'page',
				'pageSize' => 10,
			),
			'totalItemCount' => count($clinics)
		));

		// нужно для определения текущей страницы, повторно запрос не выполняется
		if (!isset($dataProvider))
			throw new CHttpException(404, 'Диагностические центры не найдены.');

		$this->diagnosticCenterCount = $dataProvider->getTotalItemCount();

		return $dataProvider;
	}

	/**
	 * Найти лучшие клиники в категории
	 *
	 * @param array $clinics
	 *
	 * @return array
	 */
	private function findBestClinics($clinics)
	{
		$countBestClinics = 10 - count($clinics);
		if ($countBestClinics < 1) {
			return null;
		}

		$criteria = new CDbCriteria();

		$criteria->scopes = array(
			'onlyDiagnostic',
			'active',
			'inCity' => $this->_scopes['inCity'],
		);
		if (!empty($this->_scopes['searchByDiagnostics'])) {
			$criteria->scopes['searchByDiagnostics'] = $this->_scopes['searchByDiagnostics'];
		}
		$criteria->limit = 5;

		$bestClinics = ClinicModel::model()
			->except($this->clinicsIds($clinics))
			->theBest()
			->findAll($criteria);

		return $bestClinics;
	}

	/**
	 * Pager
	 *
	 * @param array &$vars
	 * @param CArrayDataProvider $dataProvider
	 *
	 * @return $this
	 */
	private function buildPager(&$vars, $dataProvider)
	{
		$vars['firstPage'] = true;
		if (isset($_GET['page']) && $_GET['page'] > 1) {
			$vars['firstPage'] = false;
			$vars['prevArrow'] = '&larr;';
			Yii::app()->seo->addParam('page', $_GET['page']);
		} else {
			$vars['prevArrow'] = '';
		}
		$pageCount = $dataProvider->getPagination()->pageCount;
		if (isset($_GET['page']) && $_GET['page'] < $pageCount) {
			$vars['nextArrow'] = '&rarr;';
		} else {
			$vars['nextArrow'] = '';
		}
		return $this;
	}

	/**
	 * Гео-ссылки
	 *
	 * @param array &$vars
	 * @param array $stationIds
	 * @param array $params
	 *
	 * @return $this
	 */
	private function buildGeoLinks(&$vars, $stationIds, $params)
	{
		$sqlArea = null;
		$sqlStations = null;
		$connection = Yii::app()->db;

		if (!empty($this->stations) && !empty($params['rewriteNameStation'])) {

			$stationIdsStr = implode(',', $stationIds);

			$sqlArea = "(
					SELECT t1.id_area FROM district AS t1
					INNER JOIN district_has_underground_station AS t2 ON t1.id=t2.id_district
					WHERE t2.id_station IN ($stationIdsStr)
					LIMIT 1
				)";

			$sqlStations = "SELECT t1.name, t1.rewrite_name FROM underground_station AS t1
				INNER JOIN area_underground_station AS t2 ON t1.id=t2.station_id
				WHERE t2.area_id=(
					SELECT area_id FROM area_underground_station WHERE station_id IN ($stationIdsStr) LIMIT 1
				)
				AND t1.id NOT IN ($stationIdsStr)
				GROUP BY t1.rewrite_name
				LIMIT 4";
		}
		elseif (!empty($this->district) && !empty($params['rewriteNameDistrict'])) {
			$sql = "SELECT id_area FROM district WHERE id=" . $this->district[0]->id;
			$areaMoscow = $connection->createCommand($sql)->queryScalar();

			$sqlArea = $areaMoscow;

			$sqlStations = "SELECT t1.name, t1.rewrite_name FROM underground_station AS t1
				INNER JOIN district_has_underground_station AS t2 ON t1.id=t2.id_station
				WHERE t2.id_district IN (
					SELECT id FROM district WHERE id_area=$areaMoscow
				)
				LIMIT 4";
		}

		if ($sqlArea && $sqlStations)
		{
			$sqlAreas = "SELECT t1.name,t1.rewrite_name,t2.rewrite_name AS area_rewrite_name FROM district AS t1
				INNER JOIN area_moscow AS t2 ON t2.id=t1.id_area
				WHERE t1.id_area=$sqlArea
				GROUP BY t1.rewrite_name
				LIMIT 4";

			$vars['areasTagList'] = $connection->createCommand($sqlAreas)->queryAll();
			$vars['stationsTagList'] = $connection->createCommand($sqlStations)->queryAll();
		}

		return $this;
	}

	/**
	 * Данные по диагностике
	 *
	 * @param array &$vars
	 *
	 * @return $this
	 */
	function buildDiagnostics(&$vars)
	{
		$vars['childDiagnostics'] = array();
		if (!is_null($this->parentDiagnostic)) {
			$vars['childDiagnostics'] = Diagnostica::model()->
				ordered()->
				findAll("parent_id=" . $this->parentDiagnostic->id);
		}

		$vars['parentDiagnostics'] = DiagnosticaModel::model()
			->onlyParents()
			->findAll(array(
					'order' => 'sort, name'
				));

		$vars['diagnosticSubtypes'] = [];
		if (!is_null($this->parentDiagnostic)) {
			$vars['diagnosticSubtypes'] = DiagnosticaSubtypeModel::model()
				->byDiagnostic($this->parentDiagnostic->id)
				->findAll([
					'order' => 'priority'
				]);
		}

		$this->diagnosticTree = Diagnostica::model()->getTree();

		return $this;
	}

	/**
	 * Определение базового урла
	 *
	 * @param array &$vars
	 *
	 * @return $this
	 */
	private function buildBaseUrl(&$vars)
	{
		$url = parse_url(Yii::app()->request->url);
		$params = explode('/', $url['path']);
		$path = '';
		if (isset($params[1]))
		{
			$path = '/' . $params[1];
			if (($this->oneDiagnostic && $this->diagnostic && isset($params[2])) || $params[1] == 'kliniki') {
				$path .= '/' . $params[2];
			} elseif (empty($params[2])) {
				$path = "kliniki{$path}";
			}
		}
		$vars['baseUrl'] = Yii::app()->baseUrl . $path;
		return $this;
	}

	public function actionKliniki($rewriteName)
	{
		return $this->actionIndex($rewriteName);
	}

	public function actionArea($rewriteName, $rewriteNameDistrict = null, $rewriteNameDiagnostic = null)
	{
		return $this->actionIndex($rewriteNameDiagnostic, $rewriteName, $rewriteNameDistrict);
	}

	public function actionRedirect()
	{
		$diagnostic =
			!empty($_POST['diagnostic']) ? Diagnostica::model()->findByPk($_POST['diagnostic']) : null;

		$geoValue =
			!empty($_POST['geoValue']) ? array_map('intval', explode(',', $_POST['geoValue'])) : null;

		$area = !empty($_POST['areaMoscow']) ? AreaMoscow::model()->findByPk($_POST['areaMoscow']) : null;

		$district = !empty($_POST['districtMoscow'])
			? array_map('intval', explode(',', $_POST['districtMoscow'])) : null;

		$geoType = Yii::app()->request->getParam('geoType');

		$urlParams = array();

		if ($diagnostic) {
			if ($diagnostic->parent_id <> 0) {
				$parentDiag = Diagnostica::model()->findByPk($diagnostic->parent_id);
				$parentRewriteName = str_replace("/", "", $parentDiag->rewrite_name) . '/';
			} else {
				$parentRewriteName = '';
			}
			if (is_null($geoValue)) {
				if ($area) {
					if ($area) {
						$baseUrl =
							Yii::app()->homeUrl .
							$parentRewriteName .
							str_replace("/", "", $diagnostic->rewrite_name) .
							'/area/' .
							$area->rewrite_name .
							'/';
						if (count($district) == 1) {
							$district = DistrictModel::model()->findByPk($district[0]);
							$baseUrl .= $district->rewrite_name . '/';
						} elseif (count($district) > 1) {
							$count =
								DistrictModel::model()->count(
									"id_area=" . $area->id . " AND id NOT IN (" . implode(',', $district) . ")"
								);
							if ($count > 0) {
								$urlParams['districts'] = $district;
							}
						}
					}
					$this->redirect($this->createUrl($baseUrl, $urlParams));
				} elseif ($district) {
					$district = DistrictModel::model()->findByPk($district[0]);
					$baseUrl =
						Yii::app()->homeUrl .
						$parentRewriteName .
						str_replace("/", "", $diagnostic->rewrite_name) .
						'/district/' .
						$district->rewrite_name .
						'/';
					$this->redirect($this->createUrl($baseUrl, $urlParams));
				} else {
					$urlParams['rewriteName'] = str_replace("/", "", $diagnostic->rewrite_name);
					$this->redirect(
						Yii::app()->homeUrl . $parentRewriteName . str_replace("/", "", $diagnostic->rewrite_name)
					);
				}
			} else {
				$baseUrl = Yii::app()->homeUrl . $parentRewriteName . str_replace("/", "", $diagnostic->rewrite_name);
				if ($geoType == 'station') {
					if (count($geoValue) == 1) {
						$station = UndergroundStation::model()->findByPk($geoValue[0]);
						$urlParams['station'] = $station->rewrite_name;
					} else {
						$urlParams['stations'] = $geoValue;
					}
					$this->redirect($this->createUrl($baseUrl, $urlParams), true);
				} elseif ($geoType == 'district') {
					$district = DistrictModel::model()->findByPk($geoValue[0]);
					if (is_null($district)) {
						throw new CHttpException(404, 'Диагностические центры не найдены.');
					}
					$this->redirect($baseUrl . "/district/" . $district->rewrite_name);
				}
			}
		} else {
			if (!is_null($geoValue)) {
				if ($geoType == 'station') {
					if (count($geoValue) === 1) {
						$station = StationModel::model()->findByPk($geoValue[0]);
						$baseUrl = 'diagnostics' . "/station/{$station->rewrite_name}";
					} else {
						$urlParams['stations'] = $geoValue;
						$baseUrl = 'diagnostics' . '/search/custom';
					}
				} elseif ($geoType == 'district') {
					$district = DistrictModel::model()->findByPk($geoValue[0]);
					$baseUrl = 'diagnostics' . "/district/{$district->rewrite_name}";
				}
			} else {
				if ($area) {
					$baseUrl = '/search/area/' . $area->rewrite_name . '/';
					if (count($district) === 1) {
						$district = DistrictModel::model()->findByPk($district[0]);
						$baseUrl = 'diagnostics' . "/district/{$district->rewrite_name}";
					} elseif (count($district) > 1) {
						$count =
							DistrictModel::model()->count(
								"id_area=" . $area->id . " AND id NOT IN (" . implode(',', $district) . ")"
							);
						if ($count > 0) {
							$urlParams['districts'] = $district;
						}
					}

				} elseif($district) {
					$district = DistrictModel::model()->findByPk($district[0]);
					$baseUrl = '/district/' . $district->rewrite_name . '/';
				} else {
					$baseUrl = '/kliniki/';
				}
			}

			$this->redirect($this->createUrl($baseUrl, $urlParams), true);
		}
	}

	public function actionGuess($rewriteName)
	{
		if (!strpos(Yii::app()->request->requestUri, 'kliniki')) {
			$this->actionIndex($rewriteName);
		} else {
			$items = Diagnostica::model()->findAll();
			$diagnostics = array();
			foreach ($items as $item) {
				$diagnostics[] = str_replace('/', '', $item->rewrite_name);
			}
			//			$diagnostics = array('uzi','komputernaya-tomografiya','mrt','rentgen','dupleksnoe-skanirovanie','func-diagnostika','endoskopicheskie-issledovaniya');

			if (strpos(Yii::app()->request->requestUri, 'page') || in_array($rewriteName, $diagnostics)) {
				$this->actionIndex($rewriteName);
			} else {
				$this->actionView($rewriteName);
			}
		}
	}

	/**
	 * Страница лендинга
	 *
	 * @param string $rewriteNameDiagnostic Алиас диагностики
	 * @param string $rewriteName Алиас поддиагностики
	 *
	 * @throws CHttpException
	 */
	public function actionLanding($rewriteNameDiagnostic = null, $rewriteName = null)
	{
		$session = Yii::app()->session;
		$session['resultsUrl'] = Yii::app()->request->url;

		$this->isLandingPage = true;

		$data = $this->getSearchData([
			'rewriteName'           => $rewriteName ?: $rewriteNameDiagnostic,
			'hasDiscountOnOnline'   => true,
			'hasOnline'             => true,
		]);

		$diagnosticId = $this->diagnostic ? $this->diagnostic->id : $this->parentDiagnostic->id;
		$data['maxDiscount'] = ClinicModel::getMaxDiscountOnlineDiag($diagnosticId);
		$data['discountExpires'] = DateTimeUtils::timeToDate(strtotime('sunday next week'));

		$this->render('landing', $data);
	}

	/**
	 * Генерация страницы полной анкеты клиники
	 *
	 * @param $rewriteName
	 * @throws CHttpException
	 */
	public function actionView($rewriteName)
	{
		Yii::app()->seo->setAction('viewAction');

		$model = $this->loadModel($rewriteName);
		$this->clinicId = $model->id;

		if (
			// ...и только если мы пришли со страницы поиска
			!empty(Yii::app()->request->urlReferrer) && ($referrer = Yii::app()->request->urlReferrer) &&
			parse_url($referrer, PHP_URL_HOST) === parse_url(Yii::app()->request->hostInfo, PHP_URL_HOST) &&
			(
				strpos($referrer, '/kliniki') || strpos($referrer, '/search')
			)
		) {
			$this->isFromSearch = true;
			$this->searchUrl = $referrer;
		}

		// FIXME невозможно сохранить модель из-за CArAdvancedBehavior
		/* $model->view_count++;
		  $model->save(); */
		$diagnostic = Diagnostica::model()->rewrite_name;

		$diagModels = $model->cache(3600)->diagnosticClinics;
		$diagnosticPrices = CHtml::listData($diagModels, 'diagnostica_id', 'price');
		$diagnosticSpecialPrices = CHtml::listData($diagModels, 'diagnostica_id', 'special_price');
		$onlinePrices = CHtml::listData($diagModels, 'diagnostica_id', 'price_for_online');
		$this->profilePage = false;

		$items = DiagnosticaModel::model()->onlyParents()->findAll();
		foreach ($items as $item) {
			$parentDiagnostics[$item->id] = $item->getShortName();
		}
		$childDiagnostics = array();

		$this->hiddenFromSearch = true;

		Yii::app()->seo->addParam('clinicName', $model->name);

		$this->render(
			'view',
			array(
				'model'                   => $model,
				'diagnostic'              => $diagnostic,
				'diagnosticPrices'        => $diagnosticPrices,
				'diagnosticSpecialPrices' => $diagnosticSpecialPrices,
				'onlinePrices'            => $onlinePrices,
				'parentDiagnostics'       => $parentDiagnostics,
				'childDiagnostics'        => $childDiagnostics,
				'nearestClinics'          => $model->nearestClinics(15, true),
				'refUrl'                  => $this->getRefUrlFromSession(),
			)
		);
	}

	protected function loadModel($rewriteName)
	{
		$model =
			ClinicModel::model()->find(
				is_numeric($rewriteName) ? 't.id = :rewriteName' : 't.rewrite_name = :rewriteName',
				compact('rewriteName')
			);

		if ($model === null || (intval($model->status) !== 3)) {
			throw new CHttpException(404, 'Диагностический центр не найден.');
		}

		return $model;
	}

	/**
	 * Сформировать ссылку на список клиник
	 *
	 * @return string
	 */
	protected function getRefUrlFromSession()
	{
		$session = \Yii::app()->session;

		$refUrl = '/kliniki/';
		if (!is_null(Yii::app()->request->urlReferrer)) {
			$refUrl = Yii::app()->request->urlReferrer;
		}

		if (isset($session['resultsUrl'])) {
			$refUrl = $session['resultsUrl'];
		}

		return $refUrl;
	}

	public function actionGuess1($rewriteName)
	{
		if (strpos(Yii::app()->request->requestUri, 'kliniki') === false) {
			$this->actionIndex($rewriteName);
		} else {
			$this->actionView($rewriteName);
		}
	}

	public function actionGetDiagnosticChilds()
	{
		$id = (int)$_POST['id'];
		$criteria = new CDbCriteria();
		$criteria->condition = "parent_id=$id";
		$criteria->order = "sort, name";
		$items = Diagnostica::model()->findAll($criteria);

		$diagnostics = array();
		foreach ($items as $key => $item) {
			$diagnostics[$key]['id'] = $item->id;
			$diagnostics[$key]['name'] = $item->name;
		}

		echo json_encode($diagnostics);
	}

	protected function getStations($areaAlias, $districtAlias, $districtIds = array())
	{
		$stations = array();

		if (!empty($areaAlias)) {
			$area = AreaMoscow::model()->find("rewrite_name LIKE '$areaAlias'");
			$this->area = $area;

			if ($area) {
				if (!empty($districtAlias)) {
					$district =
						DistrictModel::model()->findAll(
							"rewrite_name LIKE '$districtAlias' AND id_area=" . $area->id
						);
					$this->district = $district;
					if ($district) {
						$sql = "SELECT id_station AS station_id
								FROM district_has_underground_station
								WHERE id_district=" . $district[0]->id;
					}
				} elseif (!empty($districtIds)) {
					$districtCriteria = new CDbCriteria;
					$districtCriteria->addInCondition("id", $_GET['districts']);
					$districtCriteria->addCondition("id_area=" . $area->id);
					$district = DistrictModel::model()->findAll($districtCriteria);
					$this->district = $district;

					if ($district) {
						$ids = array();
						foreach ($district as $item) {
							$ids[] = $item->id;
						}
						$ids = implode(",", $ids);
						$sql = "SELECT id_station AS station_id
								FROM district_has_underground_station
								WHERE id_district IN (" . $ids . ")";
					}
				} else {
					$district = DistrictModel::model()->findAll("id_area=" . $area->id);
					$this->district = $district;
					$sql = "SELECT t1.id_station AS station_id FROM district_has_underground_station AS t1
							INNER JOIN underground_station AS t2 ON t2.id=t1.id_station
							WHERE id_district IN (
								SELECT id FROM district
								WHERE id_area=" . $area->id . "
							)
							GROUP BY t2.rewrite_name";
				}
			}

			if (isset($sql)) {
				$items = Yii::app()->db->createCommand($sql)->queryAll();
				foreach ($items as $item) {
					$stations[] = $item['station_id'];
				}
			}

		}

		return $stations;
	}
}
