<?php

/**
 * Created by PhpStorm.
 * User: ndunaev
 * Date: 02.12.14
 * Time: 16:16
 */

namespace dfs\docdoc\front\widgets\partner;


use dfs\docdoc\models\CityModel;
use dfs\docdoc\models\ClinicModel;
use dfs\docdoc\models\ContractModel;
use dfs\docdoc\models\DoctorClinicModel;
use dfs\docdoc\models\DistrictModel;
use dfs\docdoc\models\SectorModel;
use dfs\docdoc\models\StationModel;
use dfs\docdoc\models\PartnerSectorMappingModel;
use SebastianBergmann\Exporter\Exception;
use Yii;
use CHtml;


/**
 * Class PartnerWidget
 *
 * @package dfs\docdoc\front\widgets\partner
 *
 * @property \dfs\docdoc\front\controllers\WidgetController $owner
 */
abstract class PartnerWidget extends \CWidget
{
	/**
	 * тип виджета - врачи
	 *
	 * @var string
	 */
	const TYPE_DOCTOR = 'Doctor';

	/**
	 * тип виджета - диагностика
	 *
	 * @var string
	 */
	const TYPE_DIAGNOSTIC = 'Diagnostic';

	/**
	 * #############################################
	 * параметры, которые берутся из адресной строки
	 * #############################################
	 */

	/**
	 * Идентификатор виджета, который задается на клиенте
	 *
	 * @var string
	 */
	public $id = null;

	/**
	 * имя шаблона для виджета
	 * @var string
	 */
	public $template = null;

	/**
	 * имя виджета
	 * @var string
	 */
	public $name = null;

	/**
	 * Тема виджета
	 *
	 * @var string
	 */
	public $theme = null;

	/**
	 * Подтягивать стили или нет
	 *
	 * @var bool
	 */
	public $noStyle = false;

	/**
	 * @var string | null
	 */
	public $srcPath = null;

	/**
	 * имя города для виджета
	 *
	 * @var null
	 */
	public $city = 'msk';

	/**
	 * разрешена ли онлайн запись для этого виджета
	 *
	 * @var int
	 */
	public $allowOnline = 0;

	/**
	 * Тип виджета врачи / диагностика
	 *
	 * @var string
	 */
	public $type = self::TYPE_DOCTOR;


	/**
	 * ############################
	 * внутренние свойств виджета
	 * ###########################
	 */

	/**
	 * Параметры конфигурации для данного шаблона
	 *
	 * @var array
	 */
	protected $config = [];

	/**
	 * Партнер
	 *
	 * @var \dfs\docdoc\models\PartnerModel
	 */
	protected $partner = null;

	/**
	 * Город для виджета
	 *
	 * @var \dfs\docdoc\models\CityModel
	 */
	protected $cityModel = null;

	/**
	 * Направление клиники, которое было определено на основанииадресной строки, переданного параметра spec
	 *
	 * @var  \dfs\docdoc\models\SectorModel
	 */
	protected $sectorModel = null;

	/**
	 * Модель станции, которая была указана в адресной строке
	 *
	 * @var  \dfs\docdoc\models\StationModel
	 */
	protected $stationModel = null;

	/**
	 * Модель района, которая была указана в адресной строке
	 *
	 * @var  DistrictModel
	 */
	protected $districtModel = null;

	/**
	 * Абривиатура станции метро
	 *
	 * @var  string
	 */
	public $station = null;

	/**
	 * Абривиатура района
	 *
	 * @var  string
	 */
	public $district = null;

	/**
	 * Список специальностей или диагностик
	 *
	 * @var  array
	 */
	public $specialities = [];

	/**
	 * Сообщение для лучших врачей или клиник
	 *
	 * @var string
	 */
	public $messageForBest = "";

	/**
	 * Дефолтный __set ищет сеттер и выдает Excetion, если не нашел.
	 * В виджетах мы присваиваем все свойства, которые есть в виджете, а те, которых нет, игнорируем
	 * Поэтому переопределяем метод
	 *
	 * @param string $name
	 * @param mixed  $value
	 *
	 * @return mixed|null
	 */
	public function __set($name,$value)
	{
		return null;
	}

	/**
	 * инициализация виджета
	 *
	 * @throws \Exception
	 */
	public function init()
	{
		$this->partner = Yii::app()->referral->getPartner();
		if ($this->partner === null) {
			throw new Exception('Partner not found');
		}

		//для партнера можно переопределить свойства виджета
		$widgetConfig = $this->partner->getWidgetConfig($this->name);
		if ($widgetConfig !== null) {
			foreach ($widgetConfig as $k => $v) {
				$this->$k = $v;
			}
		}

		$configFile = $this->owner->getViewFile($this->name . "/conf");
		if (!empty($configFile)) {
			$config = include $configFile;

			if (isset($config[$this->template])) {
				$this->config = $config[$this->template];
			}
		}

		//на уровне кофига можно переопределить свойства виджета
		if (isset($this->config['attributes'])) {
			foreach ($this->config['attributes'] as $k => $v) {
				$this->$k = $v;
			}
		}

		if (!empty($this->city)) {
			$this->cityModel = CityModel::model()->byRewriteName($this->city)->find();
		}

		if ($this->cityModel === null) {
			$this->cityModel = CityModel::model()->byRewriteName('msk')->find();
		}
	}

	/**
	 * Получение CSS-файлов, от которых зависит отображение данного виджета
	 *
	 * @return string[]
	 */
	public function getCss()
	{
		$css = (isset($this->config['css']) && !$this->noStyle) ? $this->config['css'] : [];

		if (!empty($this->theme)) {
			$css[] = 'themes/' . $this->theme . '_css';
		}

		return $css;
	}

	/**
	 * Получение CSS-файлов, от которых зависит отображение данного виджета
	 *
	 * @return string[]
	 */
	public function getCssContent()
	{
		$css = $this->getCss();
		$cssContent = [];
		foreach ($css as $fileName) {
			$cssFile = $this->owner->getViewFile($fileName);
			$cssContent[$fileName] = !empty($cssFile) ? $this->renderFile($cssFile, null, true) : "";
		}
		return $cssContent;
	}

	/**
	 * Получение JS-файла, от которого зависит поведение данного виджета
	 *
	 * @return string
	 */
	public function getJs()
	{
		return isset($this->config['js']) ? $this->config['js'] : null;
	}

	/**
	 * старт виджета
	 */
	public function run()
	{
		header("Content-type: application/javascript");

		$content = $this->renderContent();

		$this->owner->render('wrapper',
			[
				'content' => $content,
				'id' => $this->id,
			]
		);

		$this->owner->disableTrace();

		\Yii::app()->end();
	}

	/**
	 * Рендеринг котнента
	 *
	 * @return null|string
	 */
	protected function renderContent()
	{
		$content = null;
		//получаем контент
		$viewFile = $this->owner->getViewFile($this->name . "/" . $this->template);

		if ($viewFile) {
			$content = $this->renderFile($viewFile, null, true);
		}

		$content = \CJSON::encode(["content" => $content,  "styles" => $this->getCssContent(), "css" => $this->getCss()]);

		//получаем JS
		$jsFile = $this->owner->getViewFile($this->getJs());

		if (!empty($jsFile)) {
			$js = $this->renderFile($jsFile, null, true);
			$content = mb_substr($content, 0, -1) . ", onload: function(widget) { {$js} }}";
		}

		return $content;
	}

	/**
	 * Получает модель специальности из адресной строки или по партнеру
	 *
	 * @param string $sectorName
	 * @param string $method
	 *
	 * @return SectorModel|null
	 */
	protected function getSectorFromParam($sectorName, $method = 'byRewriteName')
	{
		$sector = null;

		if (!empty($sectorName)) {
			$sector = SectorModel::model()->cache(3600)->$method($sectorName)->find();
		}

		if ($sector === null) {
			$sectors = !empty($sectorName) ? [$sectorName] : [];

			if (!empty($this->srcPath)) {
				$partnerSector = explode("/", trim($this->srcPath, "/"));
				if (count($partnerSector) > 1) {
					$sectors[] = $partnerSector[1];
				}
			}
			$mapping = PartnerSectorMappingModel::model()
				->cache(3600)
				->with('sector')
				->byPartner($this->partner->id)
				->inSectors($sectors)
				->find();
			if ($mapping !== null) {
				$sector = $mapping->sector;
			}
		}

		return $sector;
	}

	/**
	 * Получение станции из входящих параметров
	 *
	 * @return StationModel | null
	 */
	protected function getStationFromParam()
	{
		if (isset($this->station)) {
			return StationModel::model()->searchByAlias($this->station)->find();
		}

		return null;
	}

	/**
	 * Получение региона
	 *
	 * @return DistrictModel | null
	 */
	protected function getDistrictFromParam()
	{
		if (isset($this->district)) {
			return DistrictModel::model()->searchByAlias($this->district)->find();
		}

		return null;
	}

	/**
	 * Информация о секторе
	 *
	 * @return array|null
	 */
	public function getSectorInfo()
	{
		return $this->sectorModel ? $this->sectorModel->getAttributes() : null;

	}

	/**
	 * Список специальностей
	 *
	 * @param string $text
	 * @param string $value
	 *
	 * @return array
	 */
	protected function getSectorsList($text = 'name', $value = 'rewrite_name')
	{
		/* для клиник
		'rewrite_spec_name',
				'spec_name'*/
		return CHtml::listData(
			SectorModel::model()
				->cache(3600)
				->active()
				->simple()
				->inCity($this->cityModel->id_city)
				->findAll(['order' => 't.name ASC']),
			$value,
			$text
		);
	}

	/**
	 * Получнеие списка станций метро
	 *
	 * @return array
	 */
	protected function getStationList()
	{
		return \CHtml::listData(
			StationModel::model()
				->cache(3600)
				->inCity($this->cityModel->id_city)
				->ordered()
				->findAll(),
			'rewrite_name',
			'name'
		);
	}

	/**
	 * Получнеие списка районов
	 *
	 * @return array
	 */
	protected function getDistrictList()
	{
		return \CHtml::listData(
			DistrictModel::model()
				->cache(3600)
				->inCity($this->cityModel->id_city)
				->ordered()
				->findAll(),
			'rewrite_name',
			'name'
		);
	}

	/**
	 * Url DocDoc'a
	 *
	 * @return string
	 */
	public function getDocDocUrl()
	{
		return "http://{$this->getHost()}/?pid={$this->partner->id}";
	}

	/**
	 * Url создания заявки
	 *
	 * @return string
	 */
	public function getCreateRequestUrl()
	{
		return "http://{$this->getHost()}/request?pid={$this->partner->id}";
	}

	/**
	 * Адрес точки входа для редиректа виджета на DocDoc
	 *
	 * @return string
	 */
	public function getRedirectFormUrl()
	{
		return "http://{$this->getHost()}/widget/redirectWidget";
	}

	/**
	 * Текущий хост виджета
	 *
	 * @return string
	 */
	public function getHost()
	{
		$host = \Yii::app()->params['hosts']['front'];

		if (!empty($this->cityModel->prefix) && $this->cityModel->prefix !== 'msk') {
			$host = $this->cityModel->prefix . $host;
		}

		return $host;
	}

	/**
	 * Определяет, использовать ли в качестве ГЕО районы вместо станций метро для текущего города
	 *
	 * @return bool
	 */
	public function isDistrict()
	{
		return $this->cityModel->search_type == CityModel::SEARCH_TYPE_DISTRICT;
	}

	/**
	 * Список станций
	 *
	 * @return string
	 */
	public function getStationListField()
	{
		return CHtml::dropDownList(
			'dd_clinic_station_list',
			!$this->stationModel ?: $this->stationModel->rewrite_name,
			$this->stationList,
			['empty' => 'Выберите метро', 'class' => 'dd_clinic_station_list', 'id' => false]
		);
	}

	/**
	 * Список районов
	 *
	 * @return string
	 */
	public function getDistrictListField()
	{
		return CHtml::dropDownList(
			'dd_clinic_district_list',
			!$this->districtModel ?: $this->districtModel->rewrite_name,
			$this->districtList,
			['empty' => 'Выберите район', 'class' => 'dd_clinic_district_list', 'id' => false]
		);
	}

	/**
	 * Получает название контейнера для CSS
	 *
	 * Если указана тема, делается из нее, или из шаблона
	 * Например,
	 * было : ClinicList/medinfa
	 * стало: cliniclist-medinfa
	 *
	 * @return string
	 */
	public function getContainerName()
	{
		return
			str_replace(["/", "_"], "-", strtolower($this->theme ? $this->theme : "{$this->name}-{$this->template}"));
	}
}