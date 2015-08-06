<?php
namespace dfs\front\tests\ui\widget;

use dfs\common\test\selenium\DefaultWidgetsTestCase;
/**
 * Class RequestTest
 *
 * @package dfs\front\tests\ui\widget
 */
class RequestTest extends DefaultWidgetsTestCase
{
	/**
	 * Тест виджета Request
	 *
	 * @param array $params
	 *
	 * @dataProvider getWidgetParams
	 */
	public function testWidgets($params)
	{
		$this->widgetShows($params);
		$this->byId('dd-name-request')->value('Test');
		$this->byId('dd-phone-request')->value(substr(DefaultWidgetsTestCase::$rndPhone->getNumber(),1));
		$this->byClassname('dd-button')->click();
		sleep(2);
		$this->assertEquals("Спасибо, ваша заявка отправлена.\nМы перезвоним вам в течение 15 минут и подберем врача", $this->byClassName('dd-success-message')->text());
		$this->checkRequest(['phone' => DefaultWidgetsTestCase::$prettyPhone, 'client_name' => 'Test', 'picture' => 'Подбор врача']);

	}

	/**
	 * Дата-провайдер для заявки на подбор врача
	 *
	 * @return array
	 */
	public function getWidgetParams()
	{
		$baseParams = [
			'pid'            => '1',
			'container'      => 'widget',
			'widget'         => 'Request',
			'action'         => 'LoadWidget',
			'template'       => 'Request_728x90'
		];

		return [
			[
				//Виджет Request_728x90
				$baseParams
			]
		];
	}

}