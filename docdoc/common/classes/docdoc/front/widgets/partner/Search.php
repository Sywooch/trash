<?php
/**
 * Created by PhpStorm.
 * User: ndunaev
 * Date: 02.12.14
 * Time: 16:16
 */

namespace dfs\docdoc\front\widgets\partner;

use CHtml;
use Yii;

/**
 * Class Modal
 *
 * @package dfs\docdoc\front\widgets\partner
 *
 */
class Search extends PartnerWidget
{
	/**
	 * имя виджета
	 * @var string
	 */
	public $name = 'Search';

	/**
	 * #############################################
	 * параметры, которые берутся из адресной строки
	 * #############################################
	 */

	/**
	 * Имя специализации клиники
	 *
	 * @var string
	 */
	public $sector = null;


	/**
	 * ############################
	 * внутренние свойств виджета
	 * ###########################
	 */

	/**
	 * Список специальностей
	 *
	 * @var array
	 */
	protected $sectorList = [];

	/**
	 * Список станций
	 *
	 * @var array
	 */
	protected $stationList = [];

	/**
	 * Список районов
	 *
	 * @var array
	 */
	protected $districtList = [];

	/**
	 * Название специальности, которое нужно выводит в списке
	 *
	 * уролог или урология
	 *
	 * Переопределяется на уровне конфига для шаблона
	 *
	 * @var string
	 */
	public $specName = 'name';

	/**
	 * Тип поиска по специальности или по клинике
	 *
	 * уролог или урология
	 *
	 * Переопределяется на уровне конфига для шаблона
	 *
	 * @var string
	 */
	public $searchType = 'doctor';

	/**
	 * инициализация виджета
	 */
	public function init()
	{
		parent::init();

		$this->sectorList = $this->getSectorsList($this->specName, 'rewrite_name');
		$this->sectorModel = $this->getSectorFromParam($this->sector, 'byRewriteName');

		if ($this->isDistrict()) {
			$this->districtList = $this->getDistrictList();
			$this->districtModel = $this->getDistrictFromParam();
		} else {
			$this->stationList = $this->getStationList();
			$this->stationModel = $this->getStationFromParam();
		}

		if ($this->searchType === 'clinic') {
			$this->specName = 'spec_name';
		}
	}

	/**
	 * Действия при загрузке виджета
	 *
	 */
	public function loadWidget()
	{

	}

	/**
	 * список станций
	 *
	 * @return string
	 */
	public function getSectorListField()
	{
		return CHtml::dropDownList(
			'dd_spec_list',
			!$this->sectorModel ?: $this->sectorModel->rewrite_name,
			$this->sectorList,
			['empty' => 'Выберите направление']
		);
	}

	/**
	 * параметры для формы редиректа
	 *
	 * @return string
	 */
	public function getFormBaseWidgetParamsForRedirect()
	{
		return '
			<input type="hidden" name="pid" value="' . $this->partner->id . '" />
			<input type="hidden" name="city" value="' . $this->city . '" />
			<input type="hidden" name="widget" value="' . $this->name . '" />
			<input type="hidden" name="template" value="' . $this->template . '" />
			<input type="hidden" name="searchType" value="' . $this->searchType . '" />
			<input type="hidden" name="spec_name" value="' . $this->specName . '" />
		';
	}


	/**
	 * Перенаправление трафика с помощью виджета
	 *
	 */
	public function redirectWidget()
	{
		$url = "";

		if ($this->searchType == 'clinic') {
			if ($this->isDistrict()) {
				if ($this->sectorModel !== null) {
					$url .= "/clinic/spec/" . $this->sectorModel->rewrite_spec_name;
					$url .= ($this->districtModel !== null) ? "/district/" . $this->districtModel->rewrite_name : "";
				} else {
					$url = ($this->districtModel !== null) ? "/district/" . $this->districtModel->rewrite_name : "/clinic";
				}
			} else {
				if ($this->sectorModel !== null) {
					$url .= "/clinic/spec/" . $this->sectorModel->rewrite_spec_name;
					$url .= ($this->stationModel !== null) ? "/" . $this->stationModel->rewrite_name : "";
				} else {
					$url = ($this->stationModel !== null) ? "/search/stations/" . $this->stationModel->id : "/clinic";
				}
			}
		}

		if ($this->searchType == 'doctor') {
			if ($this->isDistrict()) {
				if ($this->sectorModel !== null) {
					$url = "/doctor/" . $this->sectorModel->rewrite_name;
					$url .= ($this->districtModel !== null) ? "/district/" . $this->districtModel->rewrite_name : "";
				} else {
					$url = ($this->districtModel !== null) ? "/district/" . $this->districtModel->rewrite_name : "/doctor";
				}
			} else {
				if ($this->sectorModel !== null) {
					$url = "/doctor/" . $this->sectorModel->rewrite_name;
					$url .= ($this->stationModel !== null) ? "/" . $this->stationModel->rewrite_name : "";
				} else {
					$url = ($this->stationModel !== null) ? "/search/stations/" . $this->stationModel->id : "/doctor";
				}
			}
		}

		$url .= "?pid=" . $this->partner->id;

		Yii::app()->city->redirect($url);
	}

}