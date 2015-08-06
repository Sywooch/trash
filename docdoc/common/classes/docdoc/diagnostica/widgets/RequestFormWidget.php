<?php

namespace dfs\docdoc\diagnostica\widgets;

use dfs\docdoc\models\DiagnosticaModel;
use dfs\docdoc\objects\Phone;
use dfs\docdoc\models\PartnerModel;


/**
 * Class RequestFormWidget
 */
class RequestFormWidget extends \CWidget
{
	/**
	 * @var DiagnosticaModel;
	 */
	public $diagnostic;

	/**
	 * @var DiagnosticaModel;
	 */
	public $parentDiagnostic;

	/**
	 * Флаг показывать информацию о сервисе или нет
	 *
	 * @var bool
	 */
	public $withServiceInfo = false;

	/**
	 * ID партнера
	 *
	 * @var PartnerModel
	 */
	public $partner = null;


	/**
	 * Запуск виджета формы заявки
	 */
	public function run()
	{
		$diagnostic = $this->diagnostic ?: $this->parentDiagnostic;

		$referralPhone = \Yii::app()->referral->phone;

		if ($referralPhone) {
			$phone = new Phone($referralPhone);
		} else {
			$detect = \Yii::app()->mobileDetect->getMobileDetect();

			if ($this->partner) {
				$phone = $this->partner->getPhoneNumber(\Yii::app()->city->getCityId());
			} elseif (($detect->isMobile() || $detect->isTablet()) && !\Yii::app()->trafficSource->isContext()) {
				$phone = new Phone(\Yii::app()->params['phoneForMobile']);
			} else {
				$phone = \Yii::app()->city->getSitePhone();
			}
		}



		$this->render('requestForm', [
			'selectedDiagnosticId'  => $diagnostic ? $diagnostic->id : 0,
			'hasDiagnosticParent'   => $diagnostic ? boolval($diagnostic->parent_id) : false,
			'phone'                 => $phone,
			'withServiceInfo'       => $this->withServiceInfo,
			'partner'               => $this->partner
		]);
	}
}
