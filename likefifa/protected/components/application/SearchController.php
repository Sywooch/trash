<?php

use likefifa\components\Seo;
use likefifa\components\helpers\ListHelper;
use likefifa\models\CityModel;

abstract class SearchController extends FrontendController
{

	/**
	 * Минимально количество записей на странице
	 *
	 * @var int
	 */
	const MIN_COUNT = 10;

	/**
	 * Размер пачки для dataprovider в map
	 */
	const MAP_PACK_SIZE = 50;

	/**
	 * Количество айтемов для одной страницы в map
	 */
	const MAP_PAGE_SIZE = 30;

	/**
	 * Выводит топ 10
	 *
	 * @link self::TOP10_FIRST_LEVEL
	 * @link self::TOP10_SECOND_LEVEL
	 *
	 * @param int
	 */
	protected $top10 = 0;

	/**
	 * В случае поиска с гео - добавляем мастеров по той же услуге/под услуге без гео
	 *
	 * @var int
	 */
	const TOP10_FIRST_LEVEL = 1;

	/**
	 * В случае когда у нас мало мастеров по под услуге (аппаратный маникюр) - добавляем мастеров по услуге (маникюр)
	 *
	 * @var int
	 */
	const TOP10_SECOND_LEVEL = 2;

	/**
	 * Идентификаторы мастеров
	 *
	 * @param array
	 */
	private $dataProviderIds = '';

	/**
	 * Установить модель для поиска по умолчанию.
	 * Фактически, вызываются FrontendController::forMasters() или
	 * FrontendController::forSalons() в зависимости от контроллера.
	 *
	 * @return SearchController
	 */
	abstract protected function forDefault();

	/**
	 * Получить класс модели для поиска.
	 * TODO: Объединить этот метод с forDefault()
	 *
	 * @return string
	 */
	abstract protected function getModelClass();

	/**
	 * Получить дополнительную критерию для поиска (она будет смержена с основной).
	 *
	 * @param string $action
	 * @param array  $params
	 *
	 * @return array
	 */
	abstract protected function getAdditionalCriteria($action, $params);

	abstract protected function getAdditionalOrCriteria($action, $params);

	/**
	 * Задает СЕО
	 *
	 * @param string   $action     действие
	 * @param string[] $params     параметры
	 * @param string   $pageString страница с номером
	 *
	 * @return SearchController
	 */
	protected function setTitleAndMeta($action, $params, $pageString = null)
	{
		$seo = new Seo($params["city"]);

		switch ($this->searchEntity) {
			case "masters":
				$seo->setForMaster($action, $params, $pageString);
				break;
			case "salons":
				$seo->setForSalons($action, $params, $pageString);
				break;
		}

		$this->pageTitle = $seo->pageTitle;
		$this->pageSubheader = $seo->pageSubheader;
		$this->metaKeywords = $seo->metaKeywords;
		$this->metaDescription = $seo->metaDescription;
		$this->pageHeader = $seo->pageHeader;
	}

	abstract protected function getModelPlurals();

	abstract public function actionIndex($model);

	protected function parseNumericValue($value)
	{
		$value = rawurldecode($value);
		$value = explode(',', $value);
		$value = array_filter(
			$value,
			function ($v) {
				return is_numeric($v);
			}
		);
		$value = array_map('intval', $value);
		return array_unique($value);
	}

	protected function loadSpecialization($id)
	{
		return LfSpecialization::model()->with('services')->findByRewrite($id);
	}

	/**
	 * @param LfSpecialization $spec
	 * @param string           $id
	 *
	 * @return LfService
	 */
	protected function loadService($spec, $id)
	{
		return LfService::model()->findBySpecAndRewrite($spec, $id);
	}

	protected function loadStations($ids)
	{
		return UndergroundStation::model()->findAllByRewrite(explode(',', rawurldecode($ids)));
	}

	protected function loadArea($id)
	{
		return AreaMoscow::model()->findByRewrite($id);
	}

	protected function loadDistricts($ids)
	{
		return DistrictMoscow::model()->findAllByRewrite(explode(',', rawurldecode($ids)));
	}

	/**
	 * Преобразует параметры для url когда выбран только округ
	 * Пример ссылки /salons/districts/cao/
	 *
	 * @see https://docdoc.megaplan.ru/task/1002967/card/
	 *
	 * @return void
	 */
	private function _changeParamsForArea()
	{
		if (Yii::app()->request->getQuery("specialization") === "districts") {
			$service = Yii::app()->request->getQuery("service");
			if ($service) {
				if (in_array($service, AreaMoscow::model()->getAreaList())) {
					unset($_GET["specialization"]);
					unset($_GET["service"]);
					$_GET["area"] = $service;
				}
			}
		}
	}

	/**
	 * Преобразует параметры для url когда выбран только город
	 * Пример ссылки /masters/city/zheleznodorozhnij/
	 *
	 * @see https://docdoc.megaplan.ru/task/1003365/card/
	 *
	 * @return bool
	 */
	private function _changeParamsByCity()
	{
		if (Yii::app()->request->getQuery("specialization") !== "city") {
			return false;
		}

		$city = Yii::app()->request->getQuery("service");
		if (!$city) {
			return false;
		}

		unset($_GET["specialization"]);
		unset($_GET["service"]);
		$_GET["city"] = $city;

		return true;
	}

	/**
	 * Парсим параметры поиска.
	 * Метод универсален для всех типов поиска, что следует учитывать
	 * при формировании URL.
	 *
	 * @return array
	 */
	protected function getSearchParams()
	{
		$redirectLink = Seo::checkAndRedirectEmptyService(
			Yii::app()->request->getQuery("service"),
			$this->id
		);
		if ($redirectLink) {
			$this->redirect($redirectLink);
		}

		$this->_changeParamsForArea();
		$this->_changeParamsByCity();

		$specialization =
			!empty($_GET['specialization'])
				? $this->loadSpecialization($_GET['specialization'])
				: null;

		if (!$specialization) {
			unset($_GET['specialization']);
		} else {
			unset($_GET['speciality']);
		}

		$speciality = Yii::app()->request->getQuery("speciality");

		$service =
			$specialization && !empty($_GET['service'])
				? $this->loadService($specialization, $_GET['service'])
				: null;

		if (!$service) {
			unset($_GET['service']);
		}

		$hasDeparture =
			!empty($_GET['hasDeparture']);

		$stations =
			!empty($_GET['stations'])
				? $this->loadStations($_GET['stations'])
				: null;

		if (!$stations) {
			unset($_GET['stations']);
		}

		$area =
			!empty($_GET['area'])
				? $this->loadArea($_GET['area'])
				: null;

		if (!$area) {
			unset($_GET['area']);
		}

		$districts =
			!empty($_GET['districts'])
				? $this->loadDistricts($_GET['districts'])
				: null;

		if (!$districts) {
			unset($_GET['districts']);
		}

		$city = CityModel::model()->getModelByRewriteName(Yii::app()->request->getQuery("city"));
		if (!$city) {
			unset($_GET['city']);
		} else {
			if (!$city->isMoscow()) {
				$districts = null;
				$area = null;
				$stations = null;
			}
		}

		$showAll = !empty($_GET['all']);
		unset($_GET['all']);

		$sorting = empty($_GET['sorting']) || !$service ? 'rating_composite' : $_GET['sorting'];
		if (empty($_GET['direction'])) {
			$direction = $sorting === 'price' ? 'asc' : 'desc';
		} else {
			$direction = $_GET['direction'];
		}
		$reverseDirection = $direction === 'asc' ? 'desc' : 'asc';

		$query = !empty($_GET['query']) ? $_GET['query'] : null;

		$this->specialization = $specialization;
		$this->service = $service;
		$this->hasDeparture = $hasDeparture;

		return compact(
			'city',
			'specialization',
			'service',
			'hasDeparture',
			'stations',
			'area',
			'districts',
			'showAll',
			'sorting',
			'direction',
			'reverseDirection',
			'query',
			'speciality'
		);
	}

	protected function applySeoText($seoText, $pageString = '')
	{
		if (!$seoText) {
			return;
		}

		if ($seoText->page_title) {
			$this->pageTitle = $seoText->page_title . $pageString;
		}
		if ($seoText->meta_keywords) {
			$this->metaKeywords = $seoText->meta_keywords;
		}
		if ($seoText->meta_description) {
			$this->metaDescription = $seoText->meta_description;
		}
	}

	protected function getPage()
	{
		return isset($_GET['page']) ? intval($_GET['page']) : 0;
	}

	public function actionRedirect()
	{
		$this->forDefault();
		$params = $this->getSearchParams();
		$mode = isset($_GET['mode']) ? $_GET['mode'] : 'custom';

		$url = null;
		switch ($mode) {
			case 'gallery':
				$url =
					$this->createGalleryUrl(
						$params['specialization'],
						$params['service']
					);
				break;

			case 'map':
				$url =
					$this->createMapUrl(
						$params['specialization'],
						$params['service'],
						$params['hasDeparture']
					);
				break;

			default:
				if ($params['query'] && !$params['specialization'] && !$params['service']) {
					$url =
						$this->createQueryUrl($params['query']);
				} else {
					$url =
						$this->createSearchUrl(
							$params['specialization'],
							$params['service'],
							$params['hasDeparture'],
							$params['stations'],
							$params['area'],
							$params['districts'],
							$params['city'],
							null,
							null,
							null,
							$params['speciality']
						);
					break;
				}
		}

		if ($url) {
			$this->redirect($url);
		}
	}

	/**
	 * Получить основную критерию для выборки моделей в зависимости от типа поиска.
	 *
	 * @param string $action
	 * @param array  $params
	 *
	 * @return CDbCriteria
	 */
	protected function getCriteria($action, $params)
	{
		$criteria = array('condition' => array());

		switch ($action) {
			case 'custom':
				$criteria = array(
					'condition' => array(),
					'params'    => array(),
					'with'      => array("services"),
					'order'     => 'rating_composite DESC',
					'group'     => 't.id'
				);

				$groupId = LfGroup::model()->getIdByRewriteName($params["speciality"]);
				if (
					$params["speciality"]
					&& $this->searchEntity === "masters"
					&& $groupId
				) {
					$criteria["with"][] = "group";
					$criteria["condition"][] = "group.id = :groupId";
					$criteria["params"]["groupId"] = $groupId;
				}

				if ($this->searchEntity === "masters") {
					$criteria['condition'][] = "is_blocked != 1";
				}

				if ($params['hasDeparture']) {
					$criteria['condition'][] = 'has_departure = 1';
				}

				if ($params['city']) {
					$criteria['condition']['city_id'] = "t.city_id = :city_id";
					$criteria["params"]["city_id"] = $params['city']->id;
				} else {
					$criteria['condition']['city_id'] =
						"t.city_id IN (" . ListHelper::buildIdList(Yii::app()->activeRegion->getModel()->cities) . ")";
				}

				// ТОП 10
				if ($this->top10) {
					if ($this->dataProviderIds) {
						$dataProviderIds = array_unique($this->dataProviderIds);
						$idsString = implode(',', $dataProviderIds);
						$criteria['condition'][] = "t.id NOT IN ({$idsString})";
					}

					// Если есть станции и это МО - удаляем условие города и добавляем в выборку станции
					if ($params['stations'] && Yii::app()->activeRegion->isMO()) {
						unset($criteria['condition']['city_id']);
						if(isset($criteria['params']['city_id'])) {
							unset($criteria['params']['city_id']);
						}
						$criteria['condition'][] =
							'underground_station_id IN (' . ListHelper::buildIdList($params['stations']) . ')';
					}
					switch ($this->top10) {
						case self::TOP10_FIRST_LEVEL:
							if ($params['service']) {
								$criteria['condition'][] = 'filledPrices.service_id = ' . $params['service']->id;
								$criteria['with'][] = 'services';
								$criteria['with'][] = 'filledPrices';
							} else {
								if ($params['specialization']) {
									$criteria['condition'][] =
										'services.specialization_id = ' . $params['specialization']->id;
								}
							}
							break;
						case self::TOP10_SECOND_LEVEL:
							if ($params['specialization']) {
								$criteria['condition'][] =
									'services.specialization_id = ' . $params['specialization']->id;
							}
							break;
					}
				} // НЕ ТОП 10
				else {
					if ($params['stations']) {
						$criteria['condition'][] =
							'underground_station_id IN (' . ListHelper::buildIdList($params['stations']) . ')';
					}

					if ($params["area"] && !$params['districts']) {
						$area = $params["area"];
						$params['districts'] = $area->districts;
					}

					// Если поиск по районам, выбираются все метро из этих районом и ведется поиск по ним
					if ($params['districts']) {
						$stations_id_array = array();
						$stations_array = Yii::app()->db->createCommand()
							->select('underground_station_id')
							->from('district_underground_station')
							->where('district_moscow_id IN (' . ListHelper::buildIdList($params['districts']) . ')')
							->queryAll();
						if ($stations_array) {
							foreach ($stations_array as $station) {
								$stations_id_array[] = $station['underground_station_id'];
							}
							$stations_id_array = array_unique($stations_id_array);
							sort($stations_id_array);
							$stations_in = implode(',', $stations_id_array);
							$criteria['condition'][] = 'underground_station_id IN (' . $stations_in . ')';
						}
					}

					if ($params['service']) {
						$criteria['condition'][] = 'filledPrices.service_id = ' . $params['service']->id;
						$criteria['with'][] = 'services';
						$criteria['with'][] = 'filledPrices';
					} else {
						if ($params['specialization']) {
							$criteria['condition'][] = 'services.specialization_id = ' . $params['specialization']->id;
							$criteria['with'][] = 'services';
						}
					}
				}

				$criteria['order'] =
					($params['sorting'] === 'price' ? 'filledPrices.price' : 'rating_composite')
					. ' '
					. ($params['direction'] === 'asc' ? 'ASC' : 'DESC');

				$criteria['condition'][] = 't.is_published = 1';

				break;

			case 'gallery':
				$criteria = array(
					'condition' => array('t.image IS NOT NULL'),
					'params'    => array(),
					'with'      => array(
						'master' => [
							'scopes' => ['active'],
						],
						'price',
						'service',
						'master.specializations',
						'master.educations',
						'master.undergroundStation.undergroundLine'
					),
					'order'     => 't.sort DESC'
				);

				if ($params['city']) {
					$criteria['condition'][] = "master.city_id = :city_id";
					$criteria["params"]["city_id"] = $params['city']->id;
				} else {
					$criteria['condition'][] =
						"master.city_id IN (" . ListHelper::buildIdList(Yii::app()->activeRegion->getModel()->cities) . ")";
				}

				if ($params['service']) {
					$criteria['condition'][] = 'service.id = ' . $params['service']->id;
					$criteria['with'][] = 'service';
				} else {
					if ($params['specialization']) {
						$criteria['condition'][] = 'service.specialization_id = ' . $params['specialization']->id;
						$criteria['with'][] = 'service';
					}
				}
				break;

			case 'map':
				$criteria = array(
					'condition' => array('(t.map_lat IS NOT NULL AND t.map_lng IS NOT NULL)'),
					'params'    => array(),
					'with'      => array()
				);

				if ($this->searchEntity == 'masters' && $params['hasDeparture']) {
					$criteria['condition'][] = 'has_departure = 1';
				}
				if ($params['service']) {
					$criteria['condition'][] = 'filledPrices.service_id = ' . $params['service']->id;
					$criteria['with'][] = 'services';
					$criteria['with'][] = 'filledPrices';
				} else {
					if ($params['specialization']) {
						$criteria['condition'][] = 'services.specialization_id = ' . $params['specialization']->id;
						$criteria['with'][] = 'services';
						$criteria['group'] = 't.id';
					}
				}

				$criteria['condition'][] = 't.is_published = 1';

				$criteria['order'] =
					($params['sorting'] === 'price' && $params['service'] ? 'filledPrices.price' : 'rating_composite')
					. ' '
					. ($params['direction'] === 'asc' ? 'ASC' : 'DESC');
				break;
		}

		$criteria['condition'] =
			$criteria['condition']
				? implode(' AND ', $criteria['condition'])
				: '';

		$criteria = new CDbCriteria($criteria);
		$criteria->mergeWith($this->getAdditionalCriteria($action, $params));
		$criteria->mergeWith($this->getAdditionalOrCriteria($action, $params), false);
		return $criteria;
	}

	public function findEntities($action, $params)
	{
		$className = $this->getModelClass();
		$criteria = $this->getCriteria($action, $params);
		$model = new $className;

		return $model->findAll($criteria);
	}

	public function getIds($action, $params)
	{
		$ids = array();
		$entities = $this->findEntities($action, $params);
		foreach ($entities as $entity) {
			$ids[] = $entity->id;
		}

		return $ids;
	}

	/**
	 * Создает CActiveDataProvider
	 *
	 * @param string   $class
	 * @param string   $action
	 * @param string[] $params
	 * @param int      $pageSize
	 * @param bool     $useStationSuggest // Поиск ближайших станций метро
	 *
	 * @return CActiveDataProvider
	 */
	protected function createDataProvider($class, $action, $params, $pageSize, $useStationSuggest = false)
	{
		$criteria = $this->getCriteria($action, $params);

		$dataProvider = new CActiveDataProvider($class, array(
			'criteria'   => $criteria,
			'pagination' => array(
				'pageSize' => $params['showAll'] ? 100 : $pageSize,
				'pageVar'  => 'page',
			),
		));

		// Поиск ближайших станций метро
		if (
			$useStationSuggest
			&& intval($dataProvider->getTotalItemCount()) < 10
		) {
			$params['stations'] = CMap::mergeArray(
				$params['stations'],
				UndergroundStation::model()
					->near(ListHelper::buildPropList('id', $params['stations']), 3)
					->findAll()
			);

			$criteria = $this->getCriteria($action, $params);
			$dataProvider = new CActiveDataProvider($this->getModelClass(), array(
				'criteria'   => $criteria,
				'pagination' => array(
					'pageSize' => $params['showAll'] ? 100 : $pageSize,
					'pageVar'  => 'page',
				),
			));

			// Идентификаторы мастеров для ТОП 10
			if ($dataProvider->getTotalItemCount() < self::MIN_COUNT) {
				$dataProviderIdArray = $this->getIds($this->action->id, $params);
				foreach ($dataProviderIdArray as $id) {
					$this->dataProviderIds[] = $id;
				}
			}
		}

		return $dataProvider;
	}

	/**
	 * Добавляет новый отзыв
	 *
	 * @param integer $id
	 *
	 * @throws CHttpException
	 */
	public function actionCreateOpinion($id)
	{
		$className = $this->getModelClass();
		$searchModel = new $className;
		$model = $searchModel->findByPk($id);
		if ($model == null) {
			throw new CHttpException(404, 'Мастер не найден');
		}

		$opinion = new LfOpinion;
		if (isset($_POST['LfOpinion'])) {
			$opinion->attributes = $_POST['LfOpinion'];

			switch ($className) {
				case 'LfMaster':
					$opinion->master_id = $model->id;
					break;
				case 'LfSalon':
					$opinion->salon_id = $model->id;
					break;
			}

			$opinion->created = time();
			$opinion->ga = Yii::app()->gaTracking->getUserId();

			if (!$opinion->save()) {
				echo CJSON::encode($opinion->getErrors());
				Yii::app()->end();
			}
		}
		echo CJSON::encode(['success' => true]);
	}

	/**
	 * Выводит на экран каталог мастеров.
	 * Либо страницу мастера, обращаясь к методу $this->actionIndex
	 *
	 * @return mixed
	 */
	public function actionCustom()
	{
		$className = $this->getModelClass();
		$searchModel = new $className;
		$model = !empty($_GET['specialization']) ? $searchModel->findByRewrite($_GET['specialization']) : null;

		if ($model) {
			return $this->actionIndex($model);
		}

		Yii::app()->session['searchUrl'] = Yii::app()->request->requestUri;

		$params = $this->getSearchParams();

		if ($params["specialization"]) {
			Yii::app()->session['specializationId'] = $params["specialization"]->id;
		}

		if ($params["area"] && !$params['districts']) {
			$area = $params["area"];
			$params['districts'] = $area->districts;
		}

		$count = !empty($_GET['showAll']) ? 100 : 10;

		$dataProvider = $this->createDataProvider($this->getModelClass(), $this->action->id, $params, $count, true);

		$page = $this->getPage();
		$pageString = $page > 1 ? ' - страница ' . $page : '';

		$this->setTitleAndMeta($this->action->id, $params, $pageString);
		$seoText = null;
		if (!$params['stations'] && !$params['districts']) {
			$seoText =
				LfSeoText::model()->getModel(
					$params["city"],
					$params['specialization'],
					$params['service'],
					false,
					$this->getModelClass(),
					$page
				);
		}

		if ($params['speciality']) {
			$articles = Article::model()->findBySpeciality($params['speciality']);
		} else {
			$articles = Article::model()->findBySpecAndService($params['specialization'], $params['service']);
		}

		$this->applySeoText($seoText);

		if ($this->searchEntity == 'salons') {
			$seoText = $this->getSalonsSeoText($params);
		}

		$top10 = $this->_getTop10($dataProvider->totalItemCount);

		$serviceName = $this->getServiceName($params);
		$stationsName = ListHelper::buildNameList($params['stations']);
		$districtsName = ListHelper::buildNameList($params['districts']);

		$this->render(
			'custom',
			array_merge(
				$params,
				compact(
					'seoText',
					'dataProvider',
					'top10',
					'articles',
					'showAll',
					'serviceName',
					'stationsName',
					'districtsName'
				)
			)
		);
	}

	/**
	 * Получает записи топ 10
	 *
	 * В случае поиска с гео - добавляем мастеров по той же услуге/под услуге без гео
	 * В случае когда у нас мало мастеров по под услуге (аппаратный маникюр) - добавляем мастеров по услуге (маникюр)
	 *
	 * @param int $count количество обычных записей (не топ 10)
	 *
	 * @return CActiveDataProvider|null
	 */
	private function _getTop10($count)
	{
		if ($count >= self::MIN_COUNT) {
			return null;
		}

		$params = $this->getSearchParams();

		if (!$this->top10) {
			$this->dataProviderIds = $this->getIds($this->action->id, $params);
		}

		// Если мы в МО - добавляем выборку по ближайшим
		if(Yii::app()->activeRegion->isMO()) {
			if($params['city']) {
				$params['stations'] = CMap::mergeArray(
					$params['stations'],
					$params['city']->nearStations
				);
			} else {
				$params['stations'] = CMap::mergeArray(
					$params['stations'],
					UndergroundStation::getEnds()
				);
			}
		}

		$this->top10 = self::TOP10_SECOND_LEVEL;
		if ($params["stations"] || $params["districts"]) {
			$this->top10 = self::TOP10_FIRST_LEVEL;
		}

		$top10 =
			$this->createDataProvider(
				$this->getModelClass(),
				$this->action->id,
				$params,
				10,
				!Yii::app()->activeRegion->isMO()
			);

		if ($top10->totalItemCount < self::MIN_COUNT) {
			$this->top10 = self::TOP10_SECOND_LEVEL;

			$top10 =
				$this->createDataProvider(
					$this->getModelClass(),
					$this->action->id,
					$params,
					10,
					true
				);
		}

		if (
			($top10->totalItemCount >= self::MIN_COUNT || $this->top10 == self::TOP10_SECOND_LEVEL)
			&& $top10->totalItemCount > 0
		) {
			return $top10;
		}

		return null;
	}

	public function actionGallery()
	{

		Yii::app()->session['searchUrl'] = Yii::app()->request->requestUri;

		$params = $this->getSearchParams();

		$count = !empty($_GET['showAll']) ? 100 : 40;

		$dataProvider = $this->createDataProvider('LfWork', $this->action->id, $params, $count);

		$page = $this->getPage();
		$this->setTitleAndMeta($this->action->id, $params);

		$seoText =
			LfSeoText::model()->getModel(
				$params["city"],
				$params['specialization'],
				$params['service'],
				true,
				'LfMaster',
				$page
			);

		$this->applySeoText($seoText);

		$this->render(
			'gallery',
			array_merge(
				$params,
				compact(
					'seoText',
					'dataProvider',
					'showAll'
				)
			)
		);
	}

	/**
	 * Отображает карту
	 *
	 * @return void
	 */
	public function actionMap()
	{
		$params = $this->getSearchParams();
		$params['sorting'] = 'price';

		$this->render(
			'map',
			$params
		);
	}

	/**
	 * Возвращает данные для левой колонки на карте
	 *
	 * @param null $page
	 */
	public function actionLoadData($page = null)
	{
		$params = $this->getSearchParams();

		$pageSize = self::MAP_PAGE_SIZE;

		$params['sorting'] = 'price';
		$dataProvider = $this->createDataProvider($this->getModelClass(), 'map', $params, $pageSize);
		$dataProvider->pagination->setCurrentPage($page);

		$this->renderPartial(
			'map_aside_data',
			array_merge(
				$params,
				compact('dataProvider', 'page', 'pageSize')
			)
		);
	}

	/**
	 * Возвращает список точек для карты
	 */
	public function actionMapPoints()
	{
		$params = $this->getSearchParams();
		$params['sorting'] = 'price';
		$dataProvider = $this->createDataProvider($this->getModelClass(), 'map', $params, 999);

		$points = [];

		/** @var LfSalon|LfMaster $item */
		foreach ($dataProvider->getData() as $item) {
			$point = [
				'id'     => $item->id,
				'name'   => $item->getFullName(),
				'coords' => $item->hasAttribute('salon_id') && !empty($item->salon_id)
						? [$item->salon->map_lat, $item->salon->map_lng]
						: [$item->map_lat, $item->map_lng],
			];
			array_push($points, $point);
		}
		echo CJSON::encode($points);
	}

	public function actionQuery()
	{
		$params = $this->getSearchParams();

		$this->pageTitle = 'Поиск мастеров по запросу "' . htmlspecialchars(strip_tags($params['query'])) . '"';

		$this->render('query', $params);
	}

	public function actionCard($id)
	{
		$model = CActiveRecord::model($this->getModelClass())->findByPk($id);
		if ($model) {
			$this->renderPartial('card', compact('model'));
		}
	}

	/**
	 * Получает количество записей и выводит на экран
	 *
	 * @param array $params
	 *
	 * @return void
	 */
	protected function getCount($params)
	{
		$dataProvider =
			$this->createDataProvider(
				$this->getModelClass(),
				isset($_GET['type']) ? $_GET['type'] : 'custom',
				$params,
				10,
				true
			);
		$count = intval($dataProvider->getTotalItemCount());
		$response = array(
			'count' => $count,
			'word'  => su::caseForNumber($count, $this->getModelPlurals()),
		);
		echo json_encode($response);
		Yii::app()->end();
	}

	/**
	 * Возвращает ссылку для пагинатора левой колонки на карте
	 *
	 * @TODO подумать над более элегантным способом
	 *
	 * @param $currentPage
	 *
	 * @return string
	 */
	public function getAsidePagerLink($currentPage)
	{
		$currentUrl = Yii::app()->request->requestUri;
		$loadPathExists = strstr($currentUrl, 'loadData');
		$pagePathExists = strstr($currentUrl, 'page=');

		if ($loadPathExists == false) {
			if (strstr($currentUrl, '?')) {
				$delimiter = '&amp;';
				$path = explode('?', $currentUrl);
				$path[0] .= 'loadData/';
				$currentUrl = implode('?', $path);
			} else {
				$delimiter = 'loadData?';
			}
			$currentUrl .= $delimiter;
		}

		if ($pagePathExists) {
			$currentUrl = preg_replace(
				'/page=\d+/iu',
				'page=' . ($currentPage + 1),
				$currentUrl
			);
		} else {
			$currentUrl .= 'page=' . ($currentPage + 1);
		}

		return $currentUrl;
	}
}