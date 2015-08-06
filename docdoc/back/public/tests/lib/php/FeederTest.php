<?php

require_once LIB_PATH . '/php/feeder.class.php';

/**
 * Class FeederTest
 *
 * Тестируем класс создания фида для Яндекса
 */
class FeederTest extends PHPUnit_Framework_TestCase
{
	/**
	 * Тестируем функцию форматирования ссылки
	 *
	 * @dataProvider provideUrls
	 *
	 * @param string $srcUrl
	 * @param string $resultUrl
	 */
	public function testFormatUtl($srcUrl, $resultUrl)
	{
		$f = new Feeder();
		$this->assertEquals($resultUrl, $f->formatClinicUrl($srcUrl));
	}

	/**
	 * Ссылки для тестов
	 *
	 * @return array
	 */
	public function provideUrls()
	{
		return array(
			array('http://ya.ru', 'http://ya.ru'),
			array('блабла', 'http://блабла'),
			array('https://ya.ru', 'https://ya.ru'),
			array('ya.ru/', 'http://ya.ru/'),
		);
	}
}