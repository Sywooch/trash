<?php

namespace dfs\docdoc\front\widgets;

use dfs\docdoc\extensions\ClinicServiceTrait;
use dfs\docdoc\models\DoctorClinicModel;
use dfs\docdoc\models\ClinicModel;
use dfs\docdoc\models\DoctorModel;
use dfs\docdoc\models\SectorModel;
use dfs\docdoc\objects\Phone;
use dfs\docdoc\models\UndergroundStationModel;
use Yii;


/**
 * Class RequestFormWidget
 */
class RequestFormWidget extends \CWidget
{
	use ClinicServiceTrait;

	/**
	 * @var DoctorClinicModel;
	 */
	public $doctorInClinic;

	/**
	 * @var ClinicModel
	 */
	public $clinic;

	/**
	 * Массив клиник, которые выводить в виджете
	 *
	 * @var array
	 */
	public $clinics = null;

	/**
	 * @var SectorModel
	 */
	public $sector;

	/**
	 * Дата для записи ко врачу
	 *
	 * @var int
	 */
	public $bookDate;

	/**
	 * Данные для вьюхи
	 *
	 * @var array
	 */
	private $widgetData = [];

	/**
	 * Файл для вьюхи
	 *
	 * @var string
	 */
	private $viewFile;

	/**
	 * Запуск виджета формы заявки
	 */
	public function run()
	{
		$this->initDoctorInClinic();
		$this->initSector();
		$this->initClinic();

		$this->initPhone();

		$this->widgetData['docdocUrl'] = Yii::app()->params['hosts']['front'];

		$this->render($this->viewFile, $this->widgetData);
	}

	/**
	 * инициализация параметров виджета для врача в клинике
	 *
	 */
	private function initDoctorInClinic()
	{
		if (!$this->doctorInClinic instanceof DoctorClinicModel) {
			$this->viewFile = "chooseDoctor";

			$doctors = DoctorModel::model()
				->inClinics([$this->clinic->id])
				->active();

			if ($this->sector instanceof SectorModel) {
				Yii::app()->session["specialityId"] = $this->sector->id;
				$doctors = $doctors->bySpeciality($this->sector->id);
			}

			$this->widgetData['doctors'] = $doctors->findAll();
			return;
		}

		$this->viewFile = "requestForm";

		$this->clinic = $this->doctorInClinic->clinic;

		$this->widgetData['doctorInClinic'] = $this->doctorInClinic;
		$this->widgetData['doctor'] = $this->doctorInClinic->doctor;
		$this->widgetData['slotDates'] = $this->doctorInClinic->getSlotDates(date('Y-m-d'), date('Y-m-d', strtotime("+1 month")), true);
		$this->widgetData['bookDate'] = !empty($this->bookDate) ? date('d-m-Y', strtotime($this->bookDate)) : date('d-m-Y');
	}

	/**
	 * инициализация параметров виджета для клиники
	 *
	 */
	private function initClinic()
	{
		if (!$this->clinic instanceof ClinicModel) {
			$this->viewFile = "chooseClinic";
			return;
		}

		$this->widgetData['closestStations'] = UndergroundStationModel::model()->getClosestStationsByCoordinates(
			$this->clinic->latitude,
			$this->clinic->longitude
		);

		$this->widgetData['clinic'] = $this->clinic;

	}

	/**
	 * инициализация параметров виджета для специальности
	 *
	 */
	private function initSector()
	{
		if (!$this->doctorInClinic instanceof DoctorClinicModel && !$this->sector instanceof SectorModel) {

			if ($this->clinic instanceof ClinicModel) {
				$this->widgetData['services'] = $this->getServices($this->clinic->id, false);
			}

			$this->viewFile = "chooseSector";
			return;
		}

		if ($this->doctorInClinic instanceof DoctorClinicModel && !$this->sector instanceof SectorModel) {
			$this->widgetData['sector'] = $this->doctorInClinic->doctor->getDefaultSector();
		}

		$this->widgetData['sector'] = $this->sector;
	}

	/**
	 * инициализация номера телефона для виджета
	 *
	 */
	private function initPhone()
	{
		$referralPhone = \Yii::app()->referral->phone;

		$phone = null;
		if ($referralPhone) {
			$phone = new Phone($referralPhone);
		} else {
			$detect = \Yii::app()->mobileDetect->getMobileDetect();
			if (($detect->isMobile() || $detect->isTablet()) && !\Yii::app()->trafficSource->isContext()) {
				$phone = new Phone(\Yii::app()->params['phoneForMobile']);
			} else {
				$phone = \Yii::app()->city->getSitePhone();
			}
		}

		$this->widgetData['phone'] = $phone;
	}
}
