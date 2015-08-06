<?php
/**
 * Created by PhpStorm.
 * User: ndunaev
 * Date: 02.12.14
 * Time: 16:16
 */

namespace dfs\docdoc\front\widgets\partner;

use dfs\docdoc\extensions\TextUtils;
use dfs\docdoc\models\ClinicModel;
use SebastianBergmann\Exporter\Exception;
use \Yii;
use \CPagination;
use \CHtml;
use dfs\docdoc\front\widgets\LinkPagerWidget;

/**
 * Class ItemList
 *
 * Абстрактный список врачей или клиник с пагинартором, фильтрами
 *
 * @package dfs\docdoc\front\widgets\partner
 *
 * @property \dfs\docdoc\front\controllers\WidgetController $owner
 *
 */
abstract class ItemList extends PartnerWidget
{
	/**
	 * #############################################
	 * параметры, которые берутся из адресной строки
	 * #############################################
	 */


	/**
	 * Имя специальности врача
	 *
	 * @var  string
	 */
	public $sector = null;

	/**
	 * Количество элементов в списке
	 *
	 * @var int
	 */
	public $limit = 10;

	/**
	 * Номер страницы в пагинаторе
	 *
	 * @var int
	 */
	public $page = 1;

	/**
	 * Массив клиник, которые выводить в списке (врачей из которых нужно выводить в списке)
	 *
	 * @var int[]
	 */
	public $clinics = [];

	/**
	 * ############################
	 * внутренние свойств виджета
	 * ###########################
	 */

	/**
	 * Количество клиник в списке
	 * @var int
	 */
	protected $count = 0;

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
	 * Пагинатор
	 *
	 * @var string
	 */
	protected $pager = '';

	/**
	 * Подготовленный список клиник
	 *
	 * @var array
	 */
	protected $itemList = [];

	/**
	 * Имя Scope для выборки сектора
	 *
	 * @var string
	 */
	protected $sectorScope = 'byRewriteSpecName';

	/**
	 * Имя совйства со специальностью
	 *
	 * @var string
	 */
	protected $sectorSpecialityAttribute = 'spec_name';

	/**
	 * По какому rewrite_name искать сектор
	 *
	 * @var string
	 */
	protected $sectorRewriteNameAttribute = 'rewrite_spec_name';

	/**
	 * Имя выпадающего списка со специальностями
	 *
	 * @var string
	 */
	protected $sectorListFieldName = 'dd_spec_list';

	/**
	 * имя списка со станциями метро
	 *
	 * @var string
	 */
	protected $stationListFieldName = 'dd_clinic_station_list';

	/**
	 * Идентификатор пейджера
	 *
	 * @var string
	 */
	protected $pagerId = 'dd_pager';

	/**
	 * Устанавливает сообщение для лучших врачей или клиник
	 *
	 * @return void
	 */
	abstract protected function setMessageForBest();

	/**
	 * инициализация виджета
	 */
	public function init()
	{
		parent::init();

		$this->stationList = $this->getStationList();
		$this->stationModel = $this->getStationFromParam();
		$this->districtModel = $this->getDistrictFromParam();
		$this->sectorList = $this->getSectorsList($this->sectorSpecialityAttribute,  $this->sectorRewriteNameAttribute);
		$this->sectorModel = $this->getSectorFromParam($this->sector, $this->sectorScope);
	}

	/**
	 * Получение выборки списка клиник
	 *
	 * @param bool $withGeo использовать ли ГЕО (используется когда не нашлось врачей, даже с ближайшими)
	 *
	 * @return \CActiveRecord
	 */
	abstract protected function getModel($withGeo = true);


	/**
	 * @param \CActiveRecord $model
	 * @return \CActiveRecord
	 */
	protected function applyLimit($model)
	{
		if ($this->limit  > 20) {
			$this->limit = 20;
		}

		if ($this->limit <= 0) {
			$this->limit = 10;
		}

		$criteria = new \CDbCriteria();
		$criteria->limit = $this->limit;
		$model->getDbCriteria()->mergeWith($criteria);

		return $model;
	}

	/**
	 * @param \CActiveRecord $model
	 * @return \CActiveRecord | \dfs\docdoc\models\DoctorModel | \dfs\docdoc\models\ClinicModel
	 */
	protected function applyOrder($model)
	{
		return $model;
	}

	/**
	 * Загрузка виджета со списком клиник/врачей
	 *
	 */
	public function loadWidget()
	{
		/**
		 * @var \CActiveRecord | \dfs\docdoc\models\DoctorModel | \dfs\docdoc\models\ClinicModel $model
		 */
		$model = $this->getModel();
		$model->cache(3600);
		$model = $this->applyLimit($model);
		$model = $this->applyOrder($model);

		$modelClone = clone $model;
		$this->count = $modelClone->count();

		if ($this->count < $this->limit && !empty($this->stationModel)) {
			$items = $model->findAllForStationsWithClosest([$this->stationModel->id], $this->limit);
			$this->_setItemsWithBest($items);
		} else if ($this->count < $this->limit && !empty($this->districtModel)) {
			$items = $model->findAllForDistrictsWithClosest([$this->districtModel->id], $this->limit);
			$this->_setItemsWithBest($items);
		} else {
			$pages = new CPagination($this->count);
			$pages->pageSize = $this->limit;
			$pages->applyLimit($model->getDbCriteria());
			$this->pager = $this->owner->widget(
				LinkPagerWidget::class,
				[
					'pages' => $pages,
					'cssFile' => false,
					'htmlOptions'=> [
						'id' => $this->pagerId,
						'class' => 'dd-pagination'
					]
				],
				true
			);

			$this->setItemList($model->findAll());
		}
	}

	/**
	 * Истанавливает список записей + добавляет его лучшими врачами, если он меньше $this->limit
	 *
	 * @param \CActiveRecord[]
	 *
	 * @return void
	 */
	private function _setItemsWithBest($items)
	{
		$this->setItemList($items);

		if (count($items) < $this->limit) {
			$model = $this->getModel(false);
			$model = $this->applyOrder($model);
			$model->getDbCriteria()->limit = $this->limit - $this->count;
			$this->setItemList($model->findAll(), true);
			$this->setMessageForBest();
		}

		$this->count = count($this->itemList);
	}

	/**
	 * Установка списка врачей/клиник
	 *
	 * @param \CActiveRecord[] $models
	 * @param bool             $isBest параметр используется для вывода лучших врачей
	 */
	abstract protected function setItemList($models, $isBest = false);


	/**
	 * Маппинг модели в массив для шаблона
	 *
	 * @param ClinicModel $c
	 * @param bool        $isBest параметр используется для вывода лучших клиник
	 *
	 * @return array
	 */
	public function listItemClinic(ClinicModel $c, $isBest = false)
	{
		$clinic = [];
		$clinic['id'] = $c->id;
		$clinic['url'] = "http://{$this->getHost()}/clinic/{$c->rewrite_name}?pid={$this->partner->id}";
		$clinic['logo'] = $c->getLogo();
		$clinicPhones = $this->partner->getClinicPhones();
		$partnerPhone = $this->partner->getPhoneNumber($this->cityModel->id_city);
		$clinic['phone'] = null;
		if ($clinicPhones && array_key_exists($c->id, $clinicPhones)) {
			$clinic['phone'] = $clinicPhones[$c->id]->prettyFormat("+7 ");
		} else if ($partnerPhone) {
			$clinic['phone'] = $partnerPhone->prettyFormat("+7 ");
		}
		$clinic['rating'] = round($c->rating_show, 1);
		$clinic['schedule'] = $c->getSchedule();
		$clinic['name'] = $c->name;

		$clinic['address'] = $c->getAddress();
		$clinic['price'] = $c->getMinPrice();
		$clinic['description'] = $c->getShortDescription($clinic['phone']);
		if ($this->isDistrict()) {
			$clinic['district'] = $this->getClinicDistrict($c);
		} else {
			$clinic['stations'] = $this->getClinicStations($c);
		}
		$clinic['isBest'] = $isBest;

		return $clinic;
	}

	/**
	 * Выпадающий список специальностей
	 *
	 * @return string
	 */
	public function getSectorListField()
	{
		return CHtml::dropDownList(
			$this->sectorListFieldName,
			!$this->sectorModel ?: $this->sectorModel->getAttribute($this->sectorRewriteNameAttribute),
			$this->sectorList,
			['empty' => 'Выберите направление', 'class' => 'dd_sector_list', 'id' => false]
		);
	}

	/**
	 * Найдено n клиник и центров (врачей)
	 *
	 * @param string[] $found ['Найдена', 'Найдено', 'Найдено']
	 * @param string[] $what ['клиника и центр', 'клиники и центра', 'клиник и центров']
	 * @return string
	 */
	public function getFoundNumText(array $found, array $what)
	{
		return TextUtils::caseForNumber($this->count, $found) .
			"&nbsp;<span>" . $this->count ."</span>&nbsp;" .
			TextUtils::caseForNumber($this->count,	$what);

	}

	/**
	 * Получение массива со списком клиник (врачей)
	 *
	 * @return array
	 */
	public function getItemList()
	{
		return $this->itemList;
	}

	/**
	 * Получить HTML пагинатора
	 *
	 * @return string
	 */
	public function getPager()
	{
		return $this->pager;
	}

	/**
	 * Станции метро для клиники
	 *
	 * @param ClinicModel $clinic
	 *
	 * @return array
	 */
	public function getClinicStations($clinic)
	{
		$sts = [];

		foreach ($clinic->stations as $st) {
			$station = [];
			$station['name'] = $st->name;
			$station['url'] = "http://{$this->getHost()}/search/stations/{$st->id}/?pid={$this->partner->id}";
			$station['lineId'] = $st->underground_line_id;

			$sts[] = $station;
		}

		return $sts;
	}

	/**
	 * Получает район для клиники
	 *
	 * @param ClinicModel $clinic
	 *
	 * @return array
	 */
	public function getClinicDistrict($clinic)
	{
		$district = $clinic->district;

		if (!$district) {
			return [];
		}

		return [
			"name" => $district->name,
			"url"  => "http://{$this->getHost()}/district/{$district->rewrite_name}/?pid={$this->partner->id}"
		];
	}
}