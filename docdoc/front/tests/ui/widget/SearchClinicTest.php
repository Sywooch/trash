<?php
namespace dfs\front\tests\ui\widget;

use dfs\common\test\selenium\DefaultWidgetsTestCase;
/**
 * Class SearchClinicTest
 *
 * @package dfs\front\tests\ui\widget
 */
class SearchClinicTest extends DefaultWidgetsTestCase
{
	/**
	 * Тест виджета ClinicSearch
	 *
	 * @param array $params
	 *
	 * @dataProvider getWidgetParams
	 */
	public function testWidgets($params)
	{
		$this->widgetShows($params);
	}

	/**
	 * Дата-провайдер для поиска клиник
	 *
	 * @return array
	 */
	public function getWidgetParams()
	{
		$baseParams = [
			'pid'            => '1',
			'container'      => 'widget',
			'city'           => 'msk',
			'widget'         => 'Search',
			'action'         => 'LoadWidget',
			'template'       => 'SearchClinic_240x400',
			'sector'         => '',
			'station'        => ''
		];

		return [
			[
				//Виджет SearchClinic_240x400
				$baseParams
			],
			[
				//Виджет SearchClinic_240x400 + город: Санкт-Петербург
				array_merge($baseParams,
					[
						'city' => 'spb'
					]
				)
			],
			[
				//Виджет SearchClinic_240x400 + специализация: акушерство
				array_merge($baseParams,
					[
						'sector' => 'akusher'
					]
				)
			],
			[
				//Виджет SearchClinic_240x400 + специализация: акушерство + метро: медведково
				array_merge($baseParams,
					[
						'sector'  => 'akusher',
						'station' => 'medvedkovo'
					]
				)
			]
		];
	}

}