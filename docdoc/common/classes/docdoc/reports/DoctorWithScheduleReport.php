<?php
namespace dfs\docdoc\reports;

use dfs\docdoc\models\DoctorClinicModel;
use dfs\docdoc\objects\google\booking\ScheduleReport;

/**
 * Отчет о количестве врачей с расписанием на определенную дату
 *
 *
 * Class DoctorWithScheduleReport
 * @package dfs\docdoc\reports
 */
class DoctorWithScheduleReport extends BigQueryReport
{
	public function getBqModel()
	{
		return new ScheduleReport();
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
		$reportStartDate = date('Y-m-d 00:00', strtotime($date));
		$reportEndDate = date('Y-m-d 23:59', strtotime($date));

		$clinicsWithSlots = DoctorClinicModel::model()
			->with(
				[
					'slots' => [
						'joinType' => 'INNER JOIN',
						'scopes'   => [
							'inInterval' => [$reportStartDate, $reportEndDate]
						]
					],
					'clinic' => [
						'joinType' => 'INNER JOIN',
						'condition' => ' clinic.parent_clinic_id = 0 OR clinic.parent_clinic_id IS NULL'
					]
				]
			)
			->findAll(['group' => 't.clinic_id']);

		foreach ($clinicsWithSlots as $dc) {

			$withCriteria = [
				'doctor' => [
					'joinType' => 'INNER JOIN',
					'scopes'   => [
						'active' => []
					],
				],
				'clinic' => [
					'joinType' => 'INNER JOIN',
					'scopes'   => [
						'withBranches' => [$dc->clinic->id]
					],
				],
				'slots' => [
					'joinType' => 'INNER JOIN',
					'scopes'   => [
						'inInterval' => [$reportStartDate, $reportEndDate]
					],
					'together' => true
				],
			];

			$row = [];
			$row['clinic_id'] = $dc->clinic->id;
			$row['date'] = $reportStartDate;
			$row['clinic_name'] = $dc->clinic->name;
			$row['branches_bo'] = count($dc->clinic->branches);

			$row['doctors_bo'] = DoctorClinicModel::model()
				->with(['clinic' => $withCriteria['clinic']])
				->count(['group' => 't.doctor_id']);

			$row['active_doctors_bo'] = DoctorClinicModel::model()
				->with(
					[
						'doctor' => $withCriteria['doctor'], 'clinic' => $withCriteria['clinic']
					]
				)
				->count(['group' => 't.doctor_id']);

			$row['active_doctors_with_manual_slot'] = DoctorClinicModel::model()
				->with($withCriteria)
				->count(['condition' => 't.doc_external_id IS NULL', 'group' => 't.doctor_id']);

			$row['active_doctors_with_api_slot'] = DoctorClinicModel::model()
				->with($withCriteria)
				->count(['condition' => 't.doc_external_id IS NOT NULL', 'group' => 't.doctor_id']);

			$this->addData($row);
		}
	}
} 
