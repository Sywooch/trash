<?php

namespace dfs\docdoc\diagnostica\widgets;

use dfs\docdoc\models\DiagnosticaModel;
use dfs\docdoc\models\StationModel;
use dfs\docdoc\models\DistrictModel;

/**
 * Class GeoLinksWidget
 */
class GeoLinksWidget extends \CWidget
{
	/**
	 * Количество элементов в списке
	 */
	const COUNT_IN_LIST = 4;

	/**
	 * @var integer|null;
	 */
	public $diagnosticId = null;

	/**
	 * @var StationModel[];
	 */
	public $stations = [];

	/**
	 * @var DistrictModel[]
	 */
	public $districts = [];

	/**
	 * @var DiagnosticaModel|null
	 */
	private $_diagnostic = null;

	/**
	 * Запуск виджета формы заявки
	 */
	public function run()
	{
		$this->_diagnostic = DiagnosticaModel::model()->findByPk($this->diagnosticId);
		$vars = [
			'stationLinks' => $this->getStationLinks(),
			'districtLinks' => $this->getDistrictLinks(),
		];

		$this->render('geoLinks', $vars);
	}

	/**
	 * Получаем список ссылок станций
	 */
	public function getStationLinks()
	{
		$links = [];

		$diagnosticUrl = !is_null($this->_diagnostic) ? $this->_diagnostic->getUrl() : "";

		$stationId = !empty($this->stations) ? $this->stations[0]->id : null;
		$stations = StationModel::model()->getNearestStations([$stationId], self::COUNT_IN_LIST);
		foreach ($stations as $item) {
			$links[] = [
				'name' => $item['Name'],
				'href' => "{$diagnosticUrl}/station/{$item['RewriteName']}/",
			];
		}

		return $links;
	}

	/**
	 * Получаем список ссылок районов
	 */
	public function getDistrictLinks()
	{
		$links = [];

		$diagnosticUrl = !is_null($this->_diagnostic) ? $this->_diagnostic->getUrl() : "";

		$stationId = !empty($this->stations) ? $this->stations[0]->id : null;
		$districtModel = !empty($this->districts) ? $this->districts[0] : new DistrictModel;
		$districts = $districtModel->getNearestDistricts([$stationId], self::COUNT_IN_LIST);

		foreach ($districts as $item) {
			if (\Yii::app()->city->isMoscow() && $diagnosticUrl !== '') {
				$href = "{$diagnosticUrl}/area/{$item['Area']}/{$item['RewriteName']}/";
			} else {
				$href = "{$diagnosticUrl}/district/{$item['RewriteName']}/";
			}

			$links[] = [
				'name' => $item['DistrictName'],
				'href' => $href,
			];
		}

		return $links;
	}
}
