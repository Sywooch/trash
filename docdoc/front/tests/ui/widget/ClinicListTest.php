<?php
namespace dfs\front\tests\ui\widget;

use dfs\common\test\selenium\DefaultWidgetsTestCase;
/**
 * Class ClinicListTest
 *
 * @package dfs\front\tests\ui\widget
 */
class ClinicListTest extends DefaultWidgetsTestCase
{
	/**
	 * Тест виджета ClinicList
	 *
	 * @param array $params
	 *
	 * @dataProvider getWidgetParams
	 */
	public function testWidgets($params)
	{
		$this->widgetShows($params);
		$this->requestOnWidget();
		sleep(3);
		$this->assertEquals('Заявка отправлена', $this->byXpath('/html/body/div[2]/div/div[2]/div[1]')->text());
		$this->checkRequest(['phone' => DefaultWidgetsTestCase::$prettyPhone, 'client_name' => 'Test', 'picture' => 'Подбор врача']);
	}

	/**
	 * Дата-провайдер для списка клиник
	 *
	 * @return array
	 */
	public function getWidgetParams()
	{
		$baseParams = [
			'pid'            => '1',
			'container'      => 'widget',
			'city'           => 'msk',
			'widget'         => 'ClinicList',
			'action'         => 'LoadWidget',
			'template'       => 'ClinicList_700',
			'station'        => '',
			'spec'           => '',
			'page'           => 1,
			'limit'          => 5,
			'clinics'        => null
		];

		return [
			[
				//Виджет ClinicList_700
				$baseParams
			],
			[
				//Виджет ClinicList_700 + город: Санкт-Петербург
				array_merge($baseParams,
					[
						'city' => 'spb'
					]
				)
			],
			[
				//Виджет ClinicList_700 + специализация: акушерство
				array_merge($baseParams,
					[
						'spec' => 'akusherstvo'
					]
				)
			],
			[
				//Виджет ClinicList_700 + специализация: акушерство + метро: медведково
				array_merge($baseParams,
					[
						'spec'    => 'akusherstvo',
						'station' => 'medvedkovo'
					]
				)
			],
			[
				//Виджет ClinicList_700 + специализация: акушерство + метро: медведково + количество клиник на странице: 10
				array_merge($baseParams,
					[
						'spec'    => 'akusherstvo',
						'station' => 'medvedkovo',
						'limit'   => '10'
					]
				)
			],
			[
				//Виджет ClinicList_700 + специализация: акушерство + метро: медведково + количество клиник на странице: 10
				// + выбираются клиники: 13, 2094
				array_merge($baseParams,
					[
						'spec'    => 'akusherstvo',
						'station' => 'medvedkovo',
						'limit'   => '10',
						'clinics' => ['2094', '13']
					]
				)
			],
		];
	}

}