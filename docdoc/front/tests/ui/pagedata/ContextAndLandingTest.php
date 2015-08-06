<?php
namespace dfs\front\tests\ui\pagedata;

use dfs\common\test\selenium\DefaultTestCase;

/**
 * Class ContextAndLandingTest
 *
 * @package dfs\front\tests\ui\pagedata
 */
class ContextAndLandingTest extends DefaultTestCase
{
	/**
	 * Проверка страницы /landing/akusher
	 */
	public function testLandingPage()
	{
		$this->url($this->getFrontUrl()."landing/akusher");
		$titleOnLandingPage = "Акушеры Москвы, запись на прием, рейтинги и отзывы на DocDoc.ru";
		$this->assertEquals($titleOnLandingPage, $this->title());
		$this->assertEquals("бесплатно", $this->byClassName('freelabel_item')->text());
		$this->assertEquals("Запишитесь на прием к врачу, сделав всего один звонок", $this->byCssSelector('h1')->text());
		$this->assertEquals("Самые востребованные врачи-акушеры портала DocDoc", $this->byClassName('mvm')->text());
		$seo_H2 = $this->byClassName('context_list');
		$count_seo_H2 = $seo_H2->elements($this->using('class name')->value('context_title'));
		$this->assertCount(3, $count_seo_H2);// будет ошибка если seo заголовков H2 будет не  равно 3
		$this->assertEquals("посмотреть всех акушеров на DocDoc.ru", $this->byClassName('link_all_doctors')->text());
		$this->assertEquals("Все специальности", $this->byCssSelector('h3')->text());
		$doctor_cards = $this->byClassName('doctor_list');
		$count_doctor_cards = $doctor_cards->elements($this->using('class name')->value('doctor_card'));
		$this->assertLessThan(11, count($count_doctor_cards)); //будет ошибка, если на странице 11 или больше врачей.
	}

	/**
	 * Проверка страницы /context/akusher
	 */
	public function testContextPage()
	{
		$this->url($this->getFrontUrl()."context/akusher");
		$titleOnContextPage = "Акушеры Москвы, запись на прием, рейтинги и отзывы на DocDoc.ru";
		$this->assertEquals($titleOnContextPage, $this->title());
		$this->assertEquals("Москва", $this->byId('CurrentCityName')->text());
		$this->assertEquals("Вас приветствует docdoc – online-сервис по поиску врачей", $this->byCssSelector('h1')->text());
		$this->assertEquals("Все специальности", $this->byCssSelector('h3')->text());
		$seo_H2 = $this->byClassName('context_list');
		$count_seo_H2 = $seo_H2->elements($this->using('class name')->value('context_title'));
		$this->assertCount(3, $count_seo_H2);// будет ошибка если seo заголовков H2 будет не  равно 3
		$doctor_cards = $this->byClassName('doctor_list');
		$count_doctor_cards = $doctor_cards->elements($this->using('class name')->value('doctor_card'));
		$this->assertLessThan(11, count($count_doctor_cards)); //будет ошибка, если на странице 11 или больше врачей.
	}
}