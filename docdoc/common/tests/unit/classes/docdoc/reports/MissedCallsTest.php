<?php

namespace dfs\tests\docdoc\reports;

use CDbTestCase;
use dfs\docdoc\models\ClinicModel;
use dfs\docdoc\reports\MissedCalls;
use dfs\docdoc\reports\MissedCallsReport;
use PHPExcel_IOFactory;
use Yii;

/**
 * Class MissedCallsReportTest
 *
 * @package dfs\tests\docdoc\reports
 */
class MissedCallsTest extends CDbTestCase
{

	/**
	 * Установка фикстур
	 *
	 * @throws \CException
	 */
	public function setUp()
	{
		$fm = $this->getFixtureManager();
		$fm->basePath = ROOT_PATH . '/common/tests/fixtures/reports/missed_calls';

		$fm->checkIntegrity(false);
		$fm->truncateTable('clinic_partner_phone');
		$fm->truncateTable('clinic');
		$fm->truncateTable('phone');

		$fm->loadFixture('clinic_partner_phone');
		$fm->loadFixture('clinic');
		$fm->loadFixture('phone');
	}

	/**
	 * Проверка парсинга подменных телефонов
	 *
	 * @dataProvider provide
	 *
	 * @param array $data
	 */
	public function testReplacedPhonesParsing(array $data)
	{
		$phones = MissedCallsReport::getReplacedPhones($data);
		foreach ($phones as $phone) {
			$this->assertNotEmpty($phone, 'Проверка на отсуствие пустих записей');
		}

		$this->assertCount(8, $phones, 'Проверка количества полученных записей');
	}

	/**
	 * Проверка парсинга списка
	 *
	 * @dataProvider provide
	 *
	 * @param array $data
	 */
	public function testParsing(array $data)
	{
		$report = new MissedCallsReport();

		$phones  = MissedCallsReport::getReplacedPhones($data);
		$clinics = ClinicModel::model()->byReplacedPhoneWithPartner($phones)->findAll();
		$report->setClinics($clinics);

		$rawData = $report->parse($data);
		$report->setCalls($rawData);
		$result = $report->getData();

		foreach ($result as $row) {
			$this->assertNotEmpty($row['clinic_name'], 'Проверка на отсуствие записей с пустыми именами клиник');
		}

		// Проверка количества полученных записей
		$this->assertCount(8, $result);

		// Проверка генерации отчета
		$excelReport = new MissedCalls;
		$xls  = $excelReport->excel($result);
		$file = Yii::app()->runtimePath . '/failed-calls-report-test-' . time() . '.xls';
		PHPExcel_IOFactory::createWriter($xls, 'Excel5')->save($file);
		$this->assertTrue(filesize($file) > 1000);
		unlink($file);
	}

	/**
	 * Данные для проверки отчета
	 *
	 * @return array
	 */
	public function provide()
	{
		return [
			[
				'data' => [
					[
						'id' => '523113558',
						'start_time' => '2014-10-09 16:37:17',
						'duration' => '0:00:03',
						'ani' => '74995799342',
						'did' => '74952553641',
						'tariff_duration' => '0:00:03',
						'tariff' => '1.1000',
						'cost' => '0.06',
						'tag_id' => '',
						'contact_name' => '',
						'contact_category_name' => '',
						'forwarding_duration' => '',
						'is_lost' => 'True',
						'is_transfer' => 'False',
						'type_id' => '1',
						'files' => '[]',
						'scenario_name' => 'МРТ-Эксперт в Зеленограде  (mrt-rus)',
						'sort' => '1',
						'clinic_id' => 1,
					], [
						'id' => '523113725',
						'start_time' => '2014-10-09 16:37:27',
						'duration' => '0:00:02',
						'ani' => '74995799342',
						'did' => '74952553641',
						'tariff_duration' => '0:00:02',
						'tariff' => '1.1000',
						'cost' => '0.04',
						'tag_id' => '',
						'contact_name' => '',
						'contact_category_name' => '',
						'forwarding_duration' => '',
						'is_lost' => 'True',
						'is_transfer' => 'False',
						'type_id' => '1',
						'files' => '[]',
						'scenario_name' => 'МРТ-Эксперт в Зеленограде  (mrt-rus)',
						'sort' => '1',
					], [
						'id' => '523113920',
						'start_time' => '2014-10-09 16:37:36',
						'duration' => '0:00:03',
						'ani' => '74994263523',
						'did' => '74952553641',
						'tariff_duration' => '0:00:03',
						'tariff' => '1.1000',
						'cost' => '0.06',
						'tag_id' => '',
						'contact_name' => '',
						'contact_category_name' => '',
						'forwarding_duration' => '',
						'is_lost' => 'True',
						'is_transfer' => 'False',
						'type_id' => '1',
						'files' => '[]',
						'scenario_name' => 'МРТ-Эксперт в Зеленограде  (mrt-rus)',
						'sort' => '1',
					], [
						'id' => '523114120',
						'start_time' => '2014-10-09 16:37:46',
						'duration' => '0:01:08',
						'ani' => '74994263523',
						'did' => '74952553641',
						'tariff_duration' => '0:01:08',
						'tariff' => '1.1000',
						'cost' => '1.25',
						'tag_id' => '',
						'contact_name' => 'МРТ-Эксперт в Зеленограде  (mrt-rus)',
						'contact_category_name' => '',
						'forwarding_duration' => '0:00:08',
						'is_lost' => 'False',
						'is_transfer' => 'False',
						'type_id' => '1',
						'files' => '[&#39;ftp://svbmLV4dym:YNSyIfFKPolhsIX8Uo6N@ftp.universe.uiscom.ru/centrex/global_conversation/2014-10-09_16.37.54,545_from_74994263523_to_74997310009_conv.wav&#39;]',
						'scenario_name' => 'МРТ-Эксперт в Зеленограде  (mrt-rus)',
						'sort' => '1',
					], [
						'id' => '523117571',
						'start_time' => '2014-10-09 16:40:22',
						'duration' => '0:01:34',
						'ani' => '79168325687',
						'did' => '74959840943',
						'tariff_duration' => '0:01:34',
						'tariff' => '2.9000',
						'cost' => '4.54',
						'tag_id' => '',
						'contact_name' => 'мрт24',
						'contact_category_name' => '',
						'forwarding_duration' => '0:00:01',
						'is_lost' => 'True',
						'is_transfer' => 'False',
						'type_id' => '1',
						'files' => '[&#39;ftp://svbmLV4dym:YNSyIfFKPolhsIX8Uo6N@ftp.universe.uiscom.ru/centrex/global_conversation/2014-10-09_16.40.23,770_from_79168325687_to_74996382613_conv.wav&#39;]',
						'scenario_name' => 'МРТ 24',
						'sort' => '1',
					], [
						'id' => '523119696',
						'start_time' => '2014-10-09 16:42:06',
						'duration' => '0:00:40',
						'ani' => '74957076947',
						'did' => '74952551023',
						'tariff_duration' => '0:00:40',
						'tariff' => '1.1000',
						'cost' => '0.73',
						'tag_id' => '',
						'contact_name' => 'trunk to main',
						'contact_category_name' => 'trunks',
						'forwarding_duration' => '0:00:00',
						'is_lost' => 'False',
						'is_transfer' => 'False',
						'type_id' => '1',
						'files' => '[]',
						'scenario_name' => 'trunks',
						'sort' => '1',
					], [
						'id' => '523121946',
						'start_time' => '2014-10-09 16:44:07',
						'duration' => '0:00:35',
						'ani' => '74952284738',
						'did' => '74959894339',
						'tariff_duration' => '0:00:35',
						'tariff' => '1.1000',
						'cost' => '0.64',
						'tag_id' => '',
						'contact_name' => 'Медэксперт',
						'contact_category_name' => '',
						'forwarding_duration' => '0:00:02',
						'is_lost' => 'False',
						'is_transfer' => 'False',
						'type_id' => '1',
						'files' => '[&#39;ftp://svbmLV4dym:YNSyIfFKPolhsIX8Uo6N@ftp.universe.uiscom.ru/centrex/global_conversation/2014-10-09_16.44.09,142_from_74952284738_to_74955653325_conv.wav&#39;]',
						'scenario_name' => 'Медэксперт',
						'sort' => '1',
					], [
						'id' => '523122924',
						'start_time' => '2014-10-09 16:44:53',
						'duration' => '0:03:31',
						'ani' => '74986841470',
						'did' => '74952551028',
						'tariff_duration' => '0:03:31',
						'tariff' => '1.1000',
						'cost' => '3.87',
						'tag_id' => '',
						'contact_name' => 'МК Вента-Мед',
						'contact_category_name' => '',
						'forwarding_duration' => '0:00:08',
						'is_lost' => 'True',
						'is_transfer' => 'False',
						'type_id' => '1',
						'files' => '[]',
						'scenario_name' => 'ВЕНТА-МЕД',
						'sort' => '1',
					], [
						'id' => '523124152',
						'start_time' => '2014-10-09 16:46:03',
						'duration' => '0:00:31',
						'ani' => '74996850035',
						'did' => '74957218946',
						'tariff_duration' => '0:00:31',
						'tariff' => '1.1000',
						'cost' => '0.57',
						'tag_id' => '',
						'contact_name' => 'МРТ-Сити (mrtportal)',
						'contact_category_name' => '',
						'forwarding_duration' => '0:00:00',
						'is_lost' => 'False',
						'is_transfer' => 'False',
						'type_id' => '1',
						'files' => '[&#39;ftp://svbmLV4dym:YNSyIfFKPolhsIX8Uo6N@ftp.universe.uiscom.ru/centrex/global_conversation/2014-10-09_16.46.04,132_from_74996850035_to_74952210418_conv.wav&#39;]',
						'scenario_name' => 'МРТ-Сити (mrtportal)',
						'sort' => '1',
					], [
						'id' => '523124788',
						'start_time' => '2014-10-09 16:46:37',
						'duration' => '0:03:41',
						'ani' => '74953363491',
						'did' => '74952552963',
						'tariff_duration' => '0:03:41',
						'tariff' => '1.1000',
						'cost' => '4.05',
						'tag_id' => '',
						'contact_name' => 'Ниармедик полеж',
						'contact_category_name' => '',
						'forwarding_duration' => '0:00:09',
						'is_lost' => 'False',
						'is_transfer' => 'False',
						'type_id' => '1',
						'files' => '[&#39;ftp://svbmLV4dym:YNSyIfFKPolhsIX8Uo6N@ftp.universe.uiscom.ru/centrex/global_conversation/2014-10-09_16.46.46,867_from_74953363491_to_74956171171_conv.wav&#39;]',
						'scenario_name' => 'Ниармедик полеж',
						'sort' => '1',
					], [
						'id' => '523126787',
						'start_time' => '2014-10-09 16:48:28',
						'duration' => '0:01:47',
						'ani' => '74996535129',
						'did' => '74952553734',
						'tariff_duration' => '0:01:47',
						'tariff' => '1.1000',
						'cost' => '1.96',
						'tag_id' => '',
						'contact_name' => 'Добромед (zoon)',
						'contact_category_name' => '',
						'forwarding_duration' => '0:00:01',
						'is_lost' => 'False',
						'is_transfer' => 'False',
						'type_id' => '1',
						'files' => '[&#39;ftp://svbmLV4dym:YNSyIfFKPolhsIX8Uo6N@ftp.universe.uiscom.ru/centrex/global_conversation/2014-10-09_16.48.29,68_from_74996535129_to_74952280343_conv.wav&#39;]',
						'scenario_name' => 'Добромед (zoon)',
						'sort' => '1',
					], [
						'id' => '523126957',
						'start_time' => '2014-10-09 16:48:37',
						'duration' => '0:00:05',
						'ani' => '79166585200',
						'did' => '74959840943',
						'tariff_duration' => '0:00:05',
						'tariff' => '2.9000',
						'cost' => '0.24',
						'tag_id' => '',
						'contact_name' => 'мрт24',
						'contact_category_name' => '',
						'forwarding_duration' => '0:00:02',
						'is_lost' => 'False',
						'is_transfer' => 'True',
						'type_id' => '1',
						'files' => '[&#39;ftp://svbmLV4dym:YNSyIfFKPolhsIX8Uo6N@ftp.universe.uiscom.ru/centrex/global_conversation/2014-10-09_16.48.39,307_from_79166585200_to_74996382613_conv.wav&#39;]',
						'scenario_name' => 'МРТ 24',
						'sort' => '1',
					]
				]
			]
		];
	}
}
