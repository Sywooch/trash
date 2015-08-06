<?php

namespace dfs\tests\docdoc\models;

use dfs\docdoc\listInterface\ClinicList;
use CDbTestCase;
use Yii;


/**
 * Class ClinicModelTest
 *
 * @package dfs\tests\docdoc\listInterface
 */
class ClinicListTest extends CDbTestCase
{
	/**
	 * Подготовка данных
	 */
	public function setUp()
	{
		$this->loadFixtures();
	}

	/**
	 * Подготовка данных
	 */
	public function loadFixtures()
	{
		$fm = $this->getFixtureManager();

		$basePath = $fm->basePath;
		$fm->basePath = ROOT_PATH . '/common/tests/fixtures/search';

		$fm->checkIntegrity(false);

		$fm->truncateTable('clinic');
		$fm->truncateTable('city');
		$fm->truncateTable('sector');
		$fm->truncateTable('diagnostica');
		$fm->truncateTable('diagnostica4clinic');
		$fm->truncateTable('underground_line');
		$fm->truncateTable('underground_station');
		$fm->truncateTable('underground_station_4_clinic');
		$fm->truncateTable('underground_station_4_reg_city');
		$fm->truncateTable('closest_station');
		$fm->truncateTable('street_dict');
		$fm->truncateTable('reg_city');
		$fm->truncateTable('district');
		$fm->truncateTable('district_has_underground_station');
		$fm->truncateTable('area_moscow');
		$fm->truncateTable('area_underground_station');
		$fm->truncateTable('rating_strategy');
		$fm->truncateTable('rating');
		$fm->truncateTable('doctor');
		$fm->truncateTable('doctor_4_clinic');
		$fm->truncateTable('doctor_sector');

		$fm->loadFixture('clinic');
		$fm->loadFixture('city');
		$fm->loadFixture('sector');
		$fm->loadFixture('diagnostica');
		$fm->loadFixture('diagnostica4clinic');
		$fm->loadFixture('underground_line');
		$fm->loadFixture('underground_station');
		$fm->loadFixture('underground_station_4_clinic');
		$fm->loadFixture('underground_station_4_reg_city');
		$fm->loadFixture('closest_station');
		$fm->loadFixture('street_dict');
		$fm->loadFixture('reg_city');
		$fm->loadFixture('district');
		$fm->loadFixture('district_has_underground_station');
		$fm->loadFixture('area_moscow');
		$fm->loadFixture('area_underground_station');
		$fm->loadFixture('rating_strategy');
		$fm->loadFixture('rating');
		$fm->loadFixture('doctor');
		$fm->loadFixture('doctor_4_clinic');
		$fm->loadFixture('doctor_sector');

		$fm->basePath = $basePath;
	}


	/**
	 * Проверка выборки клиник
	 *
	 * @dataProvider provideSearchParams
	 *
	 * @param array $params
	 * @param int $count
	 * @param int[] $selectIds
	 */
	public function testSearch($params, $count, $selectIds)
	{
		$clinicList = new ClinicList();

		$clinicList
			->setParams($params)
			->buildParams();

		// var_dump($clinicList->getErrors());

		$this->assertEquals(false, $clinicList->hasErrors());

		$clinicList->loadData();

		$this->assertEquals($selectIds, $clinicList->getItemIds());
		$this->assertEquals($count, $clinicList->getCount());
	}

	/**
	 * Данные для testSearch
	 *
	 * @return array
	 */
	public function provideSearchParams()
	{
		return [
			// Поиск всех активных клиник
			'clinics_all' => [
				'params' => [
					'isClinic' => 'yes',
				],
				'count' => 12,
				'clinicIds' => [1, 2, 5, 13, 46, 65, 83, 86, 105, 116],
			],
			// Поиск всех активных клиник, 2 страница выдачи
			'clinics_page' => [
				'params' => [
					'isClinic' => 'yes',
					'page' => 2,
				],
				'count' => 12,
				'clinicIds' => [154, 155],
			],
			// Поиск всех активных клиник с обратной сортировкой по рейтингу, 2 страница выдачи
			'clinics_sort_page' => [
				'params' => [
					'isClinic' => 'yes',
					'sort' => 'rating',
					'sortDirection' => 'asc',
					'page' => 2,
				],
				'count' => 12,
				'clinicIds' => [86, 5],
			],
			// Поиск всех активных клиник с сортировкой по названию
			'clinics_sortName' => [
				'params' => [
					'isClinic' => 'yes',
					'sort' => 'name',
				],
				'count' => 12,
				'clinicIds' => [116, 154, 83, 65, 1, 2, 5, 155, 105, 13],
			],
			// Поиск клиник с докторами
			'clinics_withDoctors' => [
				'params' => [
					'isClinic' => 'yes',
					'withDoctors' => true,
				],
				'count' => 9,
				'clinicIds' => [1, 2, 5, 13, 46, 86, 105, 154, 155],
			],
			// Поиск пс выборкой макс. и мин. цен
			'clinics_selectPrice' => [
				'params' => [
					'speciality' => 'akusherstvo',
					'selectPrice' => true,
					'checkUseSpecialPriceForPartner' => true,
				],
				'count' => 10,
				'clinicIds' => [1, 2, 3, 4, 13, 46, 86, 105, 154, 155],
			],
			// Поиск по специальности
			'clinics_speciality' => [
				'params' => [
					'isClinic' => 'yes',
					'speciality' => 'akusherstvo',
				],
				'count' => 8,
				'clinicIds' => [1, 2, 13, 46, 86, 105, 154, 155],
			],
			// Поиск по специальности и городу Подмосковья
			'clinics_speciality_regCity' => [
				'params' => [
					'isClinic' => 'yes',
					'speciality' => 'akusherstvo',
					'regCity' => 'dolgoprudnyi',
				],
				'count' => 2,
				'clinicIds' => [1, 2],
			],
			// Поиск по специальности и району
			'clinics_speciality_district' => [
				'params' => [
					'isClinic' => 'yes',
					'speciality' => 'akusherstvo',
					'district' => 'arbat',
				],
				'count' => 6,
				'clinicIds' => [1, 46, 86, 105, 154, 155],
			],
			// Поиск по специальности
			'clinics_diagnostic' => [
				'params' => [
					'diagnostic' => 1,
				],
				'count' => 6,
				'clinicIds' => [1, 13, 46, 65, 83, 86],
			],
			// Поиск по округу
			'clinics_area' => [
				'params' => [
					'isClinic' => 'yes',
					'area' => 'cao',
				],
				'count' => 10,
				'clinicIds' => [1, 2, 5, 13, 46, 65, 83, 86, 105, 116],
			],
			// Поиск по округу, на котором нет клиник
			'clinics_area_empty' => [
				'params' => [
					'isClinic' => 'yes',
					'area' => 'sao',
				],
				'count' => 0,
				'clinicIds' => [],
			],
			// Поиск по специальности и округу
			'clinics_speciality_area' => [
				'params' => [
					'isClinic' => 'yes',
					'speciality' => 'akusherstvo',
					'area' => 'cao',
				],
				'count' => 6,
				'clinicIds' => [1, 2, 13, 46, 86, 105],
			],
			// Поиск по специальности, округу и району
			'clinics_speciality_area_district' => [
				'params' => [
					'isClinic' => 'yes',
					'speciality' => 'akusherstvo',
					'area' => 'cao',
					'district' => 'arbat',
				],
				'count' => 4,
				'clinicIds' => [1, 46, 86, 105],
			],
			// Поиск по специальности и станции метро
			'clinics_speciality_station' => [
				'params' => [
					'isClinic' => 'yes',
					'speciality' => 'akusherstvo',
					'station' => 'avtozavodskaya',
				],
				'count' => 2,
				'clinicIds' => [1, 2],
			],
			// Поиск по специальности и станции метро с добивкой ближайших
			'clinics_withNearest_speciality_station' => [
				'params' => [
					'isClinic' => 'yes',
					'withNearest' => true,
					'speciality' => 'akusherstvo',
					'station' => 'aviamotornaya',
				],
				'count' => 5,
				'clinicIds' => [1, 13, 46, 86, 105],
			],
			// Поиск по станции метро
			'clinics_station' => [
				'params' => [
					'isClinic' => 'yes',
					'station' => 'avtozavodskaya',
				],
				'count' => 3,
				'clinicIds' => [1, 2, 5],
			],
			// Поиск по станциям метро (строгое совпадение)
			'clinics_stations_strict' => [
				'params' => [
					'isClinic' => 'yes',
					'stations' => '1,2',
				],
				'count' => 10,
				'clinicIds' => [1, 2, 5, 13, 46, 65, 83, 86, 105, 116],
			],
			// Поиск по станциям метро (строгое совпадение по станциям, а потом поиск по ближайшим)
			'clinics_stations_mixed' => [
				'params' => [
					'isClinic' => 'yes',
					'stations' => '2,3',
					'near' => 'mixed',
				],
				'count' => 10,
				'clinicIds' => [1, 2, 5, 13, 46, 65, 83, 86, 105, 116],
			],
			// Поиск по станциям метро (поиск по ближайшим)
			'clinics_stations_closest' => [
				'params' => [
					'isClinic' => 'yes',
					'stations' => '1,2',
					'near' => 'closest',
				],
				'count' => 8,
				'clinicIds' => [1, 13, 46, 65, 83, 86, 105, 116],
			],
			// Поиск по улице
			'clinics_street' => [
				'params' => [
					'isClinic' => 'yes',
					'street' => 'abelmanovskaya',
				],
				'count' => 3,
				'clinicIds' => [1, 13, 46],
			],
		];
	}
}
