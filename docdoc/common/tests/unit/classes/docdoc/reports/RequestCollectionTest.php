<?php

namespace dfs\tests\docdoc\reports;

use CDbTestCase;
use dfs\docdoc\reports\RequestCollection;

/**
 * Class RequestCollectionTest
 *
 * @package dfs\tests\docdoc\reports
 */
class RequestCollectionTest extends CDbTestCase
{
	/**
	 * Установка фикстур
	 *
	 * @throws \CException
	 */
	public function setUp()
	{
		$fm = $this->getFixtureManager();
		$fm->basePath = ROOT_PATH . '/common/tests/fixtures/reports';

		$fm->checkIntegrity(false);
		$fm->truncateTable('clinic');
		$fm->truncateTable('request');
		$fm->loadFixture('clinic');
		$fm->loadFixture('request');
	}

	/**
	 * Проверка формирования данных отчета
	 *
	 * @dataProvider provideReportData
	 *
	 * @param array $params
	 * @param array $result
	 *
	 * @throws \CException
	 */
	public function testReportData($params, $result)
	{
		$report = new RequestCollection();

		$report->
			setReportType($params['ReportType'])->
			setRequestType($params['RequestType'])->
			setCityId($params['CityId'])->
			setPeriod($params['DateBegin'], $params['DateEnd']);

		if (isset($params['DateAdmissionBegin'], $params['DateAdmissionEnd'])) {
			$report->setAdmissionPeriod($params['DateAdmissionBegin'], $params['DateAdmissionEnd']);
		}

		foreach ($report->getReportData() as $row) {
			$clinicId = $row['clinic_id'];
			$type = $row['type'];
			if (isset($result[$clinicId][$type])) {
				foreach ($result[$clinicId][$type] as $field => $value) {
					$this->assertEquals($row[$field], $value);
				}
				unset($result[$clinicId][$type]);
			} else {
				$this->fail('Неверная строка в отчёте clinic_id = ' . $clinicId . ', ' . $type);
			}
		}

		foreach ($result as $clinicId => $records) {
			foreach ($records as $type => $values) {
				$this->fail('Не хватает строки в отчёте clinic_id = ' . $clinicId . ', ' . $type);
			}
		}
	}

	/**
	 * Данные для проверки отчета
	 *
	 * @return array
	 */
	public function provideReportData()
	{
		return [
			[
				[
					'ReportType' => 'clinics',
					'RequestType' => null,
					'CityId' => 1,
					'DateBegin' => '01.06.2014',
					'DateEnd' => '30.06.2014',
				],
				[
					1 => [
						'record' => [
							'clinic_name' => 'Клиника №1',
							'count' => 3,
						],
					]
				],
			],

			[
				[
					'ReportType' => 'diagnostics',
					'RequestType' => 'record',
					'CityId' => 1,
					'DateBegin' => '01.06.2014',
					'DateEnd' => '30.06.2014',
				],
				[
					1 => [
						'record' => [
							'clinic_name' => 'Клиника №1',
							'count_kt' => 0,
							'count_mrt' => 0,
							'count_other' => 2,
						],
					],
				],
			],

			[
				[
					'ReportType' => 'diagnostics',
					'RequestType' => null,
					'CityId' => 1,
					'DateBegin' => '01.06.2014',
					'DateEnd' => '30.06.2014',
					'DateAdmissionBegin' => '01.06.2014',
					'DateAdmissionEnd' => '30.06.2014',
				],
				[
					1 => [
						'record' => [
							'clinic_name' => 'Клиника №1',
							'count_kt' => 0,
							'count_mrt' => 0,
							'count_other' => 2,
						],
						'come' => [
							'clinic_name' => 'Клиника №1',
							'count_kt' => 0,
							'count_mrt' => 0,
							'count_other' => 1,
						]
					],
				],
			],
		];
	}
}
