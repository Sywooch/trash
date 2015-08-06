<?php

namespace dfs\tests\docdoc\validators;
use dfs\docdoc\models\RequestModel;
use dfs\docdoc\validators\PhoneValidator;

/**
 * Class StringValidatorTest
 *
 * @package dfs\tests\docdoc\validators
 */
class PhoneValidatorTest extends \CTestCase
{
	/**
	 * Тест валидатора
	 */
	public function testValidator()
	{
		$model = new RequestModel();
		$validator = new PhoneValidator();
		$validator->attributes =  array('client_phone');

		//allowEmpty
		$model->client_phone = '';

		$validator->validate($model);
		$this->assertEquals(0, count($model->getErrors()));
		$model->clearErrors();

		$validator->allowEmpty = false;
		$validator->validate($model);
		$this->assertEquals(1, count($model->getErrors()));
		$model->clearErrors();


		$model->client_phone = '8 (916) 123-45-66';
		$validator->validate($model);
		$this->assertEquals(0, count($model->getErrors()));
		$this->assertEquals($model->client_phone, '79161234566');
		$model->clearErrors();

		$model->client_phone = '8 916 1234566';
		$validator->validate($model);
		$this->assertEquals(0, count($model->getErrors()));
		$model->clearErrors();

		$model->client_phone = '1 (213) 1-45-66';
		$validator->validate($model);
		$this->assertEquals(1, count($model->getErrors()));
		$model->clearErrors();
	}

}