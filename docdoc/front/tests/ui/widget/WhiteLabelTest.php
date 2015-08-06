<?php
namespace dfs\front\tests\ui\widget;

use dfs\common\test\selenium\DefaultWidgetsTestCase;
/**
 * Class WhiteLabelTest
 *
 * @package dfs\front\tests\ui\widget
 */
class WhiteLabelTest extends DefaultWidgetsTestCase
{
	/**
	 * Тест виджета WhiteLabel
	 *
	 * @param array $params
	 *
	 * @dataProvider getWidgetParams
	 */
	public function testWidgets($params)
	{
		$this->openWidgetPage($params);
		$this->clickOnWidgetLink();
		$this->timeouts()->implicitWait(10000);
		$this->byId("widget"); // проверка что обертка виджета появилась на странице
	}

	/**
	 * Дата-провайдер для WhiteLabel
	 *
	 * @return array
	 */
	public function getWidgetParams()
	{
		$baseParams = [
			'pid'            => '1',
			'container'      => 'widget',
			'widget'         => 'frame',
			'width'          => '1000',
			'url'            => '/'
		];

		return [
			[
				//Виджет Whitelabel
				$baseParams
			]
		];
	}

}