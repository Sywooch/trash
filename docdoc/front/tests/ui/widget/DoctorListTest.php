<?php
namespace dfs\front\tests\ui\widget;

use dfs\common\test\selenium\DefaultWidgetsTestCase;
/**
 * Class DoctorListTest
 *
 * @package dfs\front\tests\ui\widget
 */
class DoctorListTest extends DefaultWidgetsTestCase
{
	/**
	 * Тест виджета DoctorList
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
		$this->checkRequest(['phone' => DefaultWidgetsTestCase::$prettyPhone, 'client_name' => 'Test', 'picture' => 'Запись к врачу']);
	}

	/**
	 * Дата-провайдер для списка врачей
	 *
	 * @return array
	 */
	public function getWidgetParams()
	{
		$baseParams = [
			'pid'            => '1',
			'container'      => 'widget',
			'city'           => 'msk',
			'widget'         => 'DoctorList',
			'action'         => 'LoadWidget',
			'template'       => 'DoctorList_700',
			'station'        => '',
			'sector'         => '',
			'limit'          => 5,
			'order'          => 'rating',
			'orderDirection' => 'DESC',
			'atHome'         => 1,
			'clinics'        => null
		];

		return [
			[
				//Виджет DoctorList_700
				$baseParams
			],
			[
				//Виджет DoctorList_700 + город: Санкт-Петербург
				array_merge($baseParams,
					[
						'city' => 'spb'
					]
				)
			],
			[
				//Виджет DoctorList_700 + специальность: акушер
				array_merge($baseParams,
					[
						'sector' => 'akusher'
					]
				)
			],
			[
				//Виджет DoctorList_700 + специальность: акушер + метро: медведково
				array_merge($baseParams,
					[
						'sector'  => 'akusher',
						'station' => 'medvedkovo'
					]
				)
			],
			[
				//Виджет DoctorList_700 + специальность: акушер + метро: медведково + сортировка: по цене
				array_merge($baseParams,
					[
						'sector'  => 'akusher',
						'station' => 'medvedkovo',
						'order'   => 'price'
					]
				)
			],
			[
				//Виджет DoctorList_700 + специальность: акушер + метро: медведково + сортировка: по цене
				// + выезд на дом
				array_merge($baseParams,
					[
						'sector'  => 'akusher',
						'station' => 'medvedkovo',
						'order'   => 'price',
						'atHome'  => '0'
					]
				)
			],
			[
				//Виджет DoctorList_700 + специальность: акушер + метро: медведково + сортировка: по цене
				// + выезд на дом + врачи работают в клиниках: 13, 2094
				array_merge($baseParams,
					[
						'sector'  => 'akusher',
						'station' => 'medvedkovo',
						'order'   => 'price',
						'atHome'  => '0',
						'clinics' => ['2094', '13']
					]
				)
			],
		];
	}

}