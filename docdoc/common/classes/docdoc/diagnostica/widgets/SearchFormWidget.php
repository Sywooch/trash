<?php

namespace dfs\docdoc\diagnostica\widgets;

use dfs\docdoc\models\DiagnosticaModel;
use dfs\docdoc\models\AreaModel;
use dfs\docdoc\models\StationModel;
use dfs\docdoc\models\DistrictModel;
use \Yii;

/**
 * Class SearchFormWidget
 *
 * @property DiagnosticaModel|null  $diagnostic
 * @property DiagnosticaModel|null  $parentDiagnostic
 * @property AreaModel|null         $area
 * @property StationModel[]         $stations
 * @property DistrictModel[]        $districts
 *
 */
class SearchFormWidget extends \CWidget
{

	/**
	 * @var DiagnosticaModel|null
	 */
	public $diagnostic = null;
	/**
	 * @var DiagnosticaModel|null
	 */
	public $parentDiagnostic = null;
	/**
	 * @var AreaModel|null
	 */
	public $area = null;
	/**
	 * @var StationModel[] array
	 */
	public $stations = null;
	/**
	 * @var DistrictModel[] array
	 */
	public $districts = null;
	/**
	 * @var bool
	 */
	public $mainForm = false;

	/**
	 * @var bool
	 */
	public $isMobile = false;

	/**
	 * @var string
	 */
	public $geoType = 'station';

	/**
	 * Запуск виджета формы поиска
	 *
	 * @throws \CException
	 */
	public function run()
	{
		$diagnostic = null;
		$stationIds = array();
		$area = null;
		$districts = null;
		$stationsJson = null;

		if (!empty($this->diagnostic)) {
			$diagnostic = $this->diagnostic->id;
		} elseif (!empty($this->parentDiagnostic)) {
			$diagnostic = $this->parentDiagnostic->id;
		}

		if (!empty($this->stations)) {
			foreach ($this->stations as $item) {
				$stationIds[] = $item->id;
			}
		}

		if (!empty($this->districts)) {
			$districts = array();
			foreach ($this->districts as $item) {
				$districts[] = $item->id;
			}
			$districts = implode(',', $districts);
		}

		if (!is_null($this->area)) {
			$area = $this->area->id;
		}

		$geoDataJson = json_encode($this->getGeoItems());

		$geoValue = $this->geoType == 'district' ? $districts : implode(',', $stationIds);

		$diagnosticName = 'все диагностики';
		if (!is_null($this->diagnostic)) {
			$diagnosticName = $this->parentDiagnostic->reduction_name . ' ' . $this->diagnostic->name;
		} elseif (!is_null($this->parentDiagnostic)) {
			$diagnosticName = $this->parentDiagnostic->reduction_name ?: $this->parentDiagnostic->name;
		}

		$this->render('searchForm', [
			'diagnostic' => $diagnostic,
			'stationIds' => $stationIds,
			'area' => $area,
			'districts' => $districts,
			'geoDataJson' => $geoDataJson,
			'diagnosticName' => $diagnosticName,
			'geoValue' => $geoValue,
		]);
	}

	/**
	 * Получение массива станций
	 *
	 * @return array
	 */
	private function getGeoItems()
	{
		$items = StationModel::model()
			->inCity(Yii::app()->city->getCityId())
			->findAll(array('order' => 't.name'));

		if (count($items) === 0) {
			$this->geoType = 'district';
			$items = DistrictModel::model()
				->inCity(Yii::app()->city->getCityId())
				->findAll(array('order' => 't.name'));
		}

		$data = array();
		foreach ($items as $key => $item) {
			$data[$key]['id'] = $item->id;
			$data[$key]['label'] = $item->name;
			$data[$key]['value'] = $item->name;
		}

		return $data;
	}

}