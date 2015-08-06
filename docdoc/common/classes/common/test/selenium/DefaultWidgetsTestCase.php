<?php
namespace dfs\common\test\selenium;

use dfs\docdoc\objects\Phone;
/**
 * Class DefaultWidgetsTestCase
 *
 * @package dfs\front\tests\ui\widget
 */
class DefaultWidgetsTestCase extends DefaultTestCase
{
	/**
	 * Стандартный номер телефона для тестов
	 * и отформатированный номер телефона
	 *
	 * @var string
	 */
	public static $rndPhone = '+7 (999) 111-67-69';
	public static $prettyPhone = '+7 (999) 111-67-69';


	/**
	 * Установка начальных значений
	 */
	protected function setUp()
	{
		parent::setUp();
		self::$rndPhone = new Phone("9".rand(100000000, 999999999));
		self::$prettyPhone = self::$rndPhone->prettyFormat("+7 ");
	}
	/**
	 * Открытие страницы с ссылкой на виджет
	 *
	 * @param array $widgetParameters
	 */
	public function openWidgetPage(array $widgetParameters)
	{
		$this->url($this->getFrontUrl() . "widget/test?config=" . json_encode($widgetParameters));
	}

	/**
	 * Нажимаем на ссылку чтобы появился виджет
	 */
	public function clickOnWidgetLink()
	{
		$widgetLink = $this->byId('testWidget');
		$widgetLink->click();
	}

	/**
	 * Тест видимости виджета
	 *
	 * @param array $params
	 *
	 */
	public function widgetShows($params)
	{
		$this->openWidgetPage($params);
		$this->clickOnWidgetLink();
		$this->timeouts()->implicitWait(10000);
		$this->byClassName("dd-widget"); // проверка что обертка виджета появилась на странице
}

	/**
	 * Записываемся в отображаемый виджет
 	*/
	public function requestOnWidget()
	{
		$this->byClassName('dd-button')->click(); // жмем на кнопку записаться
		$this->byId('dd-name')->value('Test');
		$this->byId('dd-phone')->value(substr(self::$rndPhone->getNumber(),1));
		$this->byId('dd-submit')->click();
	}
}