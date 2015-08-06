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
use \Yii;
use \CPagination;
use \CHtml;
use dfs\docdoc\front\widgets\LinkPagerWidget;

/**
 * Class PartnerWidget
 *
 * @package dfs\docdoc\front\widgets\partner
 *
 * @property \dfs\docdoc\front\controllers\WidgetController $owner
 */
class ClinicList extends ItemList
{
	/**
	 * имя виджета
	 * @var string
	 */
	public $name = 'ClinicList';

	/**
	 * Имя направления клиники
	 *
	 * @var  string
	 */
	public $spec = null;


	/**
	 * инициализация виджета
	 */
	public function init()
	{
		//с дуру в списке клиник параметр sector назвали spec чтобы не таскать два названия
		//все приводим к sector
		$this->sector = $this->spec;

		parent::init();
	}

	/**
	 * Получение выборки списка клиник
	 *
	 * @param bool $withGeo использовать ли ГЕО (используется когда не нашлось врачей, даже с ближайшими)
	 *
	 * @return ClinicModel
	 */
	protected function getModel($withGeo = true)
	{
		return null;
	}

	/**
	 * Загрузка виджета со списком клиник/врачей
	 */
	public function loadWidget()
	{
		$clinicList = new \dfs\docdoc\listInterface\ClinicList();

		$params = [
			'city' => $this->cityModel,
			'speciality' => $this->sectorModel,
			'station' => $this->stationModel,
			'district' => $this->districtModel,
		];

		if ($this->clinics && is_array($this->clinics)) {
			$this->limit = count($this->clinics);
			$params['clinicId'] = $this->clinics;
		} else {
			$params['withNearest'] = true;
			$params['isClinic'] = 'yes';
		}

		$clinicList
			->setCache(3600)
			->setLimit($this->limit)
			->setPage($this->page)
			->setParams($params)
			->buildParams();

		if (!$clinicList->hasErrors()) {
			$clinicList->loadData();

			$this->limit = $clinicList->getLimit();
			$this->count = $clinicList->getCount();

			$this->setItemList($clinicList->getItems());

			if ($this->count > $this->limit) {
				$pages = new CPagination($this->count);
				$pages->pageSize = $this->limit;

				$this->pager = $this->owner->widget(
					LinkPagerWidget::class,
					[
						'pages'       => $pages,
						'cssFile'     => false,
						'htmlOptions' => [
							'id'    => $this->pagerId,
							'class' => 'dd-pagination'
						]
					],
					true
				);
			} else {
				$bestClinics = $clinicList->findBestClinics($this->limit - $this->count);

				if ($bestClinics) {
					$this->setItemList($bestClinics, true);
					$this->setMessageForBest();

					$this->count = count($this->itemList);
				}
			}
		}
	}

	/**
	 * Установка элементов для построения списка
	 *
	 * @param ClinicModel[] $models
	 * @param bool          $isBest параметр используется для вывода лучших клиник
	 */
	public function setItemList($models, $isBest = false)
	{
		foreach ($models as $c) {
			$this->itemList[] = $this->listItemClinic($c, $isBest);
		}
	}

	/**
	 * HTML код кнопки записи в клинику
	 *
	 * @param array $clinic
	 * @param string $label
	 *
	 * @return string
	 */
	public function getSignUpButton(array $clinic, $label)
	{
		$modal = [
			'widget'       => 'Modal',
			'template'     => 'Modal',
			'id'           => 'DDModal',
			'action'       => 'LoadWidget',
			'clinicId'     => $clinic['id'],
			'partnerPhone' => $clinic['phone'],
			'themeForCss'  => $this->getContainerName()
		];

		return '<button
						class="dd-button dd-list-card-button dd-sign-up-button  dd-call-widget"
						data-widget=\'' . json_encode($modal) . '\'>
						<span>' . $label . '</span>
					</button>';
	}

	/**
	 * Устанавливает сообщение для лучших клиник
	 *
	 * @return void
	 */
	protected function setMessageForBest()
	{
		$this->messageForBest =
			"По вашему запросу клиник и центров" .
			($this->sectorModel ? "-" . mb_strtolower($this->sectorModel->name_plural) : "") .
			" найдено мало клиник, поэтому мы рекомендуем обратиться в лучшие центры в других районах " .
			($this->cityModel ? $this->cityModel->title_genitive : "") .
			".";
	}
}