<?php

namespace dfs\tests\docdoc\extensions;

use dfs\docdoc\extensions\TextUtils;
use CTestCase;

/**
 * Class TextUtilsTest
 * @package dfs\tests\docdoc\extensions
 */
class TextUtilsTest extends CTestCase
{

	/**
	 * Тест склонения слов
	 *
	 * @dataProvider wordInCaseProvider
	 *
	 * @param string $function
	 * @param array  $params
	 * @param string $result
	 */
	public function testWordInCase($function, $params, $result)
	{
		$this->assertEquals($result, TextUtils::parseWords($params['name'], $params['many'], $function));
	}

	/**
	 * Данные для теста
	 *
	 * @return array
	 */
	public function wordInCaseProvider()
	{
		return array(
			array(
				'wordPrepositional',
				array(
					'name' => 'Центральный округ',
					'many' => false,
				),
				'Центральном округе'
			),
			array(
				'wordPrepositional',
				array(
					'name' => 'Северо-Восточный округ',
					'many' => false,
				),
				'Северо-Восточном округе'
			),
		);
	}

}