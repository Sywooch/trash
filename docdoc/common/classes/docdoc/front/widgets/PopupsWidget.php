<?php

namespace dfs\docdoc\front\widgets;

use dfs\docdoc\models\DistrictModel;
use dfs\docdoc\models\SectorModel;
use dfs\docdoc\models\StationModel;
use dfs\docdoc\objects\Phone;


/**
 * Class PopupsWidget
 */
class PopupsWidget extends \CWidget
{
	/**
	 * Префикс для идентификатора кеширования
	 *
	 * @var string
	 */
	public $cachePrefix = 'PopupsWidget';

	/**
	 * Время кеширования
	 *
	 * @var int
	 */
	public $cacheDuration = 3600;

	/**
	 * Признак главной страницы
	 *
	 * @var bool
	 */
	public $isMainPage = false;

	/**
	 * Признак мобильной страницы
	 *
	 * @var bool
	 */
	public $isMobile = false;

	/**
	 * Отображаемый телефон в попап-окне
	 *
	 * @var Phone
	 */
	public $phoneForPage = null;


	/**
	 * Список специальностей
	 *
	 * @var SectorModel[]
	 */
	protected $specialityList = null;

	/**
	 * Список станций
	 *
	 * @var StationModel[]
	 */
	protected $stationList = null;

	/**
	 * Список районов
	 *
	 * @var DistrictModel[]
	 */
	protected $districtList = null;

	/**
	 * Список областей и ид станций
	 *
	 * @var array
	 */
	protected $areaData = null;


	/**
	 * Запуск виджета формы заявки
	 */
	public function run()
	{
		if (\Yii::app()->city->isMoscow()) {
			$cityId = \Yii::app()->city->getCityId();

			$this->specialityList = SectorModel::model()
				->active()
				->simple()
				->inCity($cityId)
				->ordered()
				->cache(3600)
				->findAll();

			$this->stationList = StationModel::model()
				->inCity($cityId)
				->ordered()
				->cache(3600)
				->findAll();

			$this->districtList = DistrictModel::model()
				->with('area')
				->inCity($cityId)
				->cache(3600)
				->findAll(['order' => 't.id_area, t.name']);

			$stationIds = [];
			$areaStationIds = [];

			$sd = StationModel::model()->findStationForDistrict($cityId);
			foreach ($sd as $v) {
				$id = $v['station_id'];
				$stationIds[$v['area_id']][$v['district_id']][$id] = $id;
				$areaStationIds[$v['area_id']][$id] = $id;
			}

			$this->areaData = [];
			foreach ($this->districtList as $district) {
				$areaId = $district->id_area;
				if (!isset($this->areaData[$areaId])) {
					$this->areaData[$areaId] = [
						'area'           => $district->area,
						'areaStationIds' => isset($areaStationIds[$areaId]) ? $areaStationIds[$areaId] : null,
						'stationIds'     => isset($stationIds[$areaId]) ? $stationIds[$areaId] : null,
					];
				}
			}
		}

		$cacheParams = [
			'duration' => $this->cacheDuration,
		];

		if ($this->beginCache($this->getCacheId(), $cacheParams)) {
			$this->render('popups', [
				'isMainPage' => $this->isMainPage,
				'isMobile' => $this->isMobile,
				'phoneForPage' => $this->phoneForPage,
				'specialityList' => $this->specialityList,
				'stationList' => $this->stationList,
				'districtList' => $this->districtList,
				'areaData' => $this->areaData,
			]);

			$this->endCache();
		}
	}

	/**
	 * Идентификатор для кеширования виджета
	 *
	 * @return string
	 */
	protected function getCacheId()
	{
		return $this->cachePrefix . '_' .
			\Yii::app()->city->getCityId() . '_' .
			($this->isMainPage ? '1_' : '0_') .
			($this->isMobile ? '1_' : '0_') .
			($this->phoneForPage ? $this->phoneForPage->getNumber() : '');
	}
}
