<?php
/**
 * Created by PhpStorm.
 * User: a.tyutyunnikov
 * Date: 11.12.14
 * Time: 12:20
 */

namespace dfs\docdoc\reports;

use dfs\docdoc\models\ApiDoctorModel;
use dfs\docdoc\models\ClinicModel;
use CLogger;
use dfs\docdoc\models\RequestModel;
use dfs\docdoc\models\BookingModel;
use dfs\docdoc\models\SlotModel;
use dfs\docdoc\objects\google\booking\Stats;

/**
 * Class BookingReport
 * @package dfs\docdoc\reports
 */
class BookingReport extends BigQueryReport
{
	public function getBqModel()
	{
		return new Stats();
	}

	/**
	 * Генераци отчета
	 *
	 * @param string $date
	 *
	 * @return array
	 */
	public function generate($date)
	{
		$clinics = ClinicModel::model()->findAll(['condition' => 'external_id IS NOT NULL']);
		$inReport = [];

		foreach ($clinics as $c) {

			$clinicId = empty($c->parent_clinic_id) ? $c->id : $c->parent_clinic_id;
			if (isset($inReport[$clinicId])) {
				continue;
			}
			$inReport[$clinicId] = 1;

			$this->addData($this->_clinicReport($clinicId, $date));
		}
	}

	/**
	 * инфорамция о клинике
	 *
	 * @param $clinicId
	 * @param $date
	 *
	 * @return array
	 */
	private function _clinicReport($clinicId, $date) {
		$reportStartDate = date('Y-m-d 00:00', strtotime($date) - 86400);
		$reportEndDate = date('Y-m-d 23:59', strtotime($date) - 86400);

		$mainClinic = ClinicModel::model()->findByPk($clinicId);
		$branches = $mainClinic->branches;
		$branches[] = $mainClinic;

		$total = [
			'clinic_id'      => $mainClinic->id,
			'date'           => $reportStartDate,
			'clinic_name'    => $mainClinic->name,
			'branches_bo'    => count($branches),
			'branches_api'   => 0,
			'doctors_bo'     => 0,
			'doctors_api'    => 0,
			'doctors_merged' => 0,
			'active_slots'   => 0,
			'request_count'  => 0,
			'booking_count'  => 0,
		];

		foreach($branches as $c) {
			if($c->apiClinic && $c->apiClinic->enabled){
				$total['branches_api']++;
			}

			$total['doctors_bo'] += count($c->doctors);

			if(!$c->apiClinic || !$c->apiClinic->enabled){
				continue;
			}

			$total['doctors_api'] += ApiDoctorModel::model()->byClinic($c->apiClinic->id)->enabled()->count();
			$total['doctors_merged'] += ApiDoctorModel::model()->byClinic($c->apiClinic->id)->merged()->count();

			$apiDoctors = ApiDoctorModel::model()->byClinic($c->apiClinic->id)->enabled()->with('doctorClinic')->findAll();

			foreach ($apiDoctors as $apiDoctor) {
				$dc = $apiDoctor->doctorClinic;
				if ($dc) {
					$total['active_slots'] += SlotModel::model()
						->inInterval(date('Y-m-d H:i:s'))
						->forDoctorInClinic($dc->id)
						->count();
				}
			}

			$total['request_count'] += RequestModel::model()
				->inClinic($c->id)
				->createdInInterval(strtotime($reportStartDate), strtotime($reportEndDate))
				->count();

			$total['booking_count'] += BookingModel::model()
				->byClinic($c->id)
				->createdInInterval(strtotime($reportStartDate), strtotime($reportEndDate))
				->count();
		}

		return $total;
	}
} 
