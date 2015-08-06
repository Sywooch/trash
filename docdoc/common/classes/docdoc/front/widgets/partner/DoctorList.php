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
use dfs\docdoc\models\DoctorModel;


/**
 * Class PartnerWidget
 *
 * @package dfs\docdoc\front\widgets\partner
 *
 * @property \dfs\docdoc\front\controllers\WidgetController $owner
 */
class DoctorList extends ItemList
{
	/**
	 * имя виджета
	 * @var string
	 */
	public $name = 'DoctorList';


	/**
	 * #############################################
	 * параметры, которые берутся из адресной строки
	 * #############################################
	 */

	/**
	 * @var int | null
	 */
	public $atHome = null;

	/**
	 * @var string
	 */
	public $order = null;

	/**
	 * @var string
	 */
	public $orderDirection = 'DESC';

	/**
	 * ############################
	 * внутренние свойств виджета
	 * ###########################
	 */

	/**
	 * Имя Scope для выборки сектора
	 *
	 * @var string
	 */
	protected $sectorScope = 'byRewriteName';

	/**
	 * Имя совйства со специальностью
	 *
	 * @var string
	 */
	protected $sectorSpecialityAttribute = 'name';

	/**
	 * По какому rewrite_name искать сектор
	 *
	 * @var string
	 */
	protected $sectorRewriteNameAttribute = 'rewrite_name';

	/**
	 * Имя выпадающего списка со специальностями
	 *
	 * @var string
	 */
	protected $sectorListFieldName = 'dd_sector_list';

	/**
	 * имя списка со станциями метро
	 *
	 * @var string
	 */
	protected $stationListFieldName = 'dd_station_list';

	/**
	 * Идентификатор пейджера
	 *
	 * @var string
	 */
	protected $pagerId = 'dd_doctor_pager';

	/**
	 * Получение выборки списка докторов
	 *
	 * @param bool $withGeo использовать ли ГЕО (используется когда не нашлось врачей, даже с ближайшими)
	 *
	 * @return DoctorModel
	 */
	protected function getModel($withGeo = true)
	{
		$model = DoctorModel::model()
			->cache(3600)
			->inCity($this->cityModel->id_city)
			->active()
			->groupByDoctor()
			->withRating()
			->with([
					'clinics' => [
						'joinType' => "INNER JOIN",
						"together" => true
					]
				]);

		if ($this->sectorModel !== null) {
			$model->inSector($this->sectorModel->id);
		}

		if (count($this->clinics) && is_array($this->clinics)) {
			$model->inClinics($this->clinics);
		}

		if ($withGeo && !empty($this->stationModel)) {
			$model->atStations([$this->stationModel->id]);
		}

		if ($withGeo && !empty($this->districtModel)) {
			$model->inDistricts([$this->districtModel->id]);
		}

		if ($this->order === 'experience') {
			$model->withExperience();
		}

		if (!empty($this->atHome)) {
			$model->withDeparture();
		}

		return $model;
	}

	/**
	 * Порядок сортировки в списке
	 *
	 * @param DoctorModel $model
	 *
	 * @return mixed
	 */
	protected function applyOrder($model)
	{
		$variants = ['DESC' => 'DESC', 'ASC' => 'ASC'];
		if (isset($variants[$this->orderDirection])) {
			$direction = $this->orderDirection = $variants[$this->orderDirection];
		} else {
			$direction = $this->orderDirection = 'DESC';
		}

		$variants = ['doctorRating' => 't.rating_internal', 'experience' => 't.experience_year', 'price' => 't.price'];

		//если сортировка по опыту делаем ее наоборот
		if ($this->order === 'experience') {
			$direction = $this->orderDirection === 'ASC' ? 'DESC' : 'ASC';
		}

		$criteria = new \CDbCriteria();
		if ($this->order && isset($variants[$this->order])) {
			$criteria->order = $variants[$this->order] . " " . $direction;
		} else {
			$this->order = null;
			$criteria->order = 'r.rating_value ' . $direction;
		}
		$model->getDbCriteria()->mergeWith($criteria);
		return $model;
	}


	/**
	 * @param DoctorModel[] $models
	 * @param bool          $isBest параметр используется для вывода лучших врачей
	 */
	public function setItemList($models, $isBest = false)
	{
		foreach ($models as $d) {
			$doctor = [];
			$c = $d->getDefaultClinic();
			$doctor['clinic'] = $c ? $this->listItemClinic($c) : null;
			$doctor['id'] = $d->id;
			$doctor['name'] = $d->name;
			$doctor['url'] = "http://{$this->getHost()}/doctor/{$d->rewrite_name}?pid={$this->partner->id}";
			$doctor['logo'] = $d->getImg();
			//@todo переделать
			$doctor['countReviews'] = count($d->doctorReviews);
			$doctor['reviewText']   = TextUtils::caseForNumber($doctor['countReviews'], ['отзыв', 'отзыва', 'отзывов']);
			$doctor['rating'] = $d->getDoctorRating();

			$doctor['sectors'] = [];
			foreach ($d->sectors as $s) {
				$doctor['sectors'][] = $s->name;
			}

			$doctor['experience'] = $d->getExperience();
			$doctor['experienceText'] = $doctor['experience'] . ' ' .  TextUtils::caseForNumber($doctor['experience'], ['год', 'года', 'лет']) ;

			$doctor['text'] = $d->text;
			$doctor['price'] = $d->price;

			$doctor['address'] = $c ? $c->getAddress() : '';
			$doctor['text'] = $d->text;
			$doctor['isBest'] = $isBest;

			$this->itemList[] = $doctor;
		}
	}

	/**
	 * Чекбокс выезда на дом
	 *
	 * @return string
	 */
	public function getAtHomeField()
	{
		return
			'<input type="checkbox" class="dd_atHome" ' .
			(($this->atHome == 0) ? '' : 'checked="checked"') .
			' />';
	}

	/**
	 * Ссылка фильтрации по критерию
	 *
	 * @param $sortType
	 * @param $sortTitle
	 *
	 * @return string
	 */
	public function getFilterLink($sortType, $sortTitle)
	{
		$rating_style = '';
		$rating_direction = '';
		if ($this->order === $sortType) {
			$rating_style = 'dd-list-header-filter-active';
			$rating_direction = ($this->orderDirection === 'DESC') ? '&#x2193; ' : '&#x2191; ';
		}

		return
			'<a href="#" class="dd_order_' .
			$sortType .
			' ' .
			$rating_style .
			' ' .
			$this->orderDirection .
			'">
			<span id="dd_order_' .
			$sortType .
			'_d">' .
			$rating_direction .
			'</span>' .
			$sortTitle .
			'</a>';
	}

	/**
	 * HTML код кнопки записи в клинику
	 *
	 * @param array $doctor
	 * @param string $label
	 *
	 * @return string
	 */
	public function getSignUpButton(array $doctor, $label)
	{
		$template = 'Modal';
		$modal = [
			'widget' => 'Modal',
			'template' => $template,
			'id' => 'DD' . $template . $this->type,
			'action' => 'LoadWidget',
			'clinicId' => $doctor['clinic']['id'],
			'doctorId' => $doctor['id'],
			'partnerPhone' =>  $doctor['clinic']['phone'],
			'allowOnline' => (int)$this->allowOnline,
			'themeForCss'  => $this->getContainerName()
		];

		return '<button data-partner-phone="' . $doctor['clinic']['phone'] . '"
				class="dd-button dd-list-card-button dd-sign-up-button  dd-call-widget"
				data-widget=\'' . json_encode($modal) . '\'>
				<span>' . $label . '</span></button>';
	}

	/**
	 * урл на страницу со списков врачей в зависимости от сектора
	 *
	 * @return string
	 */
	public function getDoctorInSectorUrl()
	{
		$sectorName = ($this->sectorModel) ? $this->sectorModel->rewrite_name : "";
		return "http://{$this->getHost()}/doctor/{$sectorName}?pid={$this->partner->id}";
	}

	/**
	 * Заголовок виджета в зависимости от сектора
	 *
	 * @return string
	 */
	public function getDoctorInSectorTitle()
	{
		return ($this->sectorModel) ? $this->sectorModel->name_plural : "Лучшие врачи";
	}

	/**
	 * Устанавливает сообщение для лучших клиник
	 *
	 * @return void
	 */
	protected function setMessageForBest()
	{
		$this->messageForBest =
			"По вашему запросу врачей" .
			($this->sectorModel ? "-" . mb_strtolower($this->sectorModel->name_plural_genitive) : "") .
			" мало, поэтому мы предлагаем вам ознакомиться и выбрать врача" .
			($this->sectorModel ? "-" . mb_strtolower($this->sectorModel->name_genitive) : "") .
			" из лучших клиник " .
			($this->cityModel ? $this->cityModel->title_genitive : "") .
			" и записаться к нему на приём.";
	}
}