<?php

namespace dfs\tests\docdoc\validators;
use dfs\docdoc\models\PartnerWidgetModel;
use dfs\docdoc\validators\JsonValidator;

/**
 * Class StringValidatorTest
 *
 * @package dfs\tests\docdoc\validators
 */
class JsonValidatorTest extends \CTestCase
{
	/**
	 * Тест валидатора
	 */
	public function testValidator()
	{
		$model = new PartnerWidgetModel();
		$validator = new JsonValidator();
		$validator->attributes =  array('json_config');

		//allowEmpty
		$model->json_config = '';
		$validator->validate($model);
		$this->assertEquals(0, count($model->getErrors()));
		$model->clearErrors();

		$validator->allowEmpty = false;
		$validator->validate($model);
		$this->assertEquals(1, count($model->getErrors()));
		$model->clearErrors();

		$model->json_config = '{qweqwe:qweqwew}';
		$validator->validate($model);
		$this->assertEquals(1, count($model->getErrors()));
		$model->clearErrors();


		$model->json_config = '{qweqwe:"qweqwew"}';
		$validator->validate($model);
		$this->assertEquals(1, count($model->getErrors()));
		$model->clearErrors();

		$model->json_config = '{"qweqwe":"qweqwew", "deee":[]}';
		$validator->validate($model);
		$this->assertEquals(0, count($model->getErrors()));
		$model->clearErrors();

		$model->json_config = '{}';
		$validator->validate($model);
		$this->assertEquals(0, count($model->getErrors()));
		$model->clearErrors();
	}

}