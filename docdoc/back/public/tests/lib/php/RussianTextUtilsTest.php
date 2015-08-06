<?php

require_once LIB_PATH .'/php/russianTextUtils.class.php';
require_once LIB_PATH . '/php/validate.php';

/**
 * Class ClinicTest
 *
 * Тестируем класс с утилитами для обработки русского текста
 */
class RussianTextUtilsTest extends PHPUnit_Framework_TestCase
{
	/**
	 * Тестируем функцию сохранения алиаса
	 *
	 * @dataProvider provideData
	 *
	 * @param string $title
	 * @param string $result
	 */
	public function testTranslit($title, $result)
	{
		$alias = RussianTextUtils::translit($title);
		$this->assertEquals($result, $alias);
	}

	/**
	 * Данные для тестов
	 *
	 * @return array
	 */
	public function provideData()
	{
		return array(
			array('Даная', 'danaya'),
			array('блабла:', 'blabla'),
			array(' 023banan', '023banan'),
			array(' вввв вввввв', 'vvvv_vvvvvv'),
		);
	}
}