<?php
namespace dfs\front\tests\ui\widget;

use dfs\common\test\selenium\DefaultWidgetsTestCase;
/**
 * Class RequestButtonTest
 *
 * @package dfs\front\tests\ui\widget
 */
class RequestButtonTest extends DefaultWidgetsTestCase
{
	/**
	 * Тест виджета Button_common
	 *
	 * @param array $params
	 *
	 * @dataProvider getWidgetParams
	 */
	public function testWidgets($params)
	{
		$this->widgetShows($params);
		if($params['doctorId'] != '' && $params['allowOnline'] == '0') {
			$this->requestOnWidget();
			sleep(3);
			$this->assertEquals('Заявка отправлена', $this->byXpath('/html/body/div[2]/div/div[2]/div[1]')->text());
			$this->checkRequest(
				['phone' => DefaultWidgetsTestCase::$prettyPhone, 'client_name' => 'Test', 'picture' => 'Запись к врачу']
			);
		}
		if($params['doctorId'] == '' && $params['allowOnline'] == '0') {
			$this->requestOnWidget();
			sleep(3);
			$this->assertEquals('Заявка отправлена', $this->byXpath('/html/body/div[2]/div/div[2]/div[1]')->text());
			$this->checkRequest(
				['phone' => DefaultWidgetsTestCase::$prettyPhone, 'client_name' => 'Test', 'picture' => 'Подбор врача']
			);
		}
		if($params['doctorId'] != '' && $params['allowOnline'] == '1') {
			//ничего не делаем
		}

	}

	/**
	 * Дата-провайдер для кнопки записи к врачу
	 *
	 * @return array
	 */
	public function getWidgetParams()
	{
		$baseParams = [
			'pid'            => '1',
			'container'      => 'widget',
			'widget'         => 'Button',
			'action'         => 'LoadWidget',
			'template'       => 'Button_common',
			'type'           => '',
			'clinicId'       => '',
			'doctorId'       => '',
			'diagnosticId'   => '',
			'allowOnline'    => '0',
			'specialities'   => ''
		];

		return [
			[
				//Виджет Button_common
				$baseParams
			],
			[
				//Виджет Button_common + тип: Doctor + Клиника: 525 + Врач: 7168
				array_merge($baseParams,
					[
						'type'     => 'Doctor',
						'clinicId' => '525',
						'doctorId' => '7168'
					]
				)
			],
			[
				//Виджет Button_common + тип: Diagnostic + Клиника: 525 + Врач: 7168 + выбранная диагностика: 1
				// + запись онлайн: 1 + Отображаемые диагностики: ['1', '21']
				array_merge($baseParams,
					[
						'type'         => 'Diagnostic',
						'clinicId'     => '525',
						'doctorId'     => '7168',
						'diagnosticId' => '1',
						'allowOnline'  => '1',
						'specialities' => ['1', '21']
					]
				)
			],
		];
	}

}