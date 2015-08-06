<?php

namespace dfs\tests\docdoc\validators;
use dfs\docdoc\models\RequestModel;
use dfs\docdoc\validators\StringValidator;

/**
 * Class StringValidatorTest
 *
 * @package dfs\tests\docdoc\validators
 */
class StringValidatorTest extends \CTestCase
{
	/**
	 * Тест валидатора
	 */
	public function testValidator()
	{
		$model = new RequestModel();
		$validator = new StringValidator();
		$validator->attributes =  array('client_name');

		//allowEmpty
		$model->client_name = '';

		$validator->validate($model);
		$this->assertEquals(0, count($model->getErrors()));
		$model->clearErrors();

		$validator->allowEmpty = false;
		$validator->validate($model);
		$this->assertEquals(1, count($model->getErrors()));
		$model->clearErrors();


		//russian_fio
		$model->client_name = 'ЪфбвёЁА Ёйшцйцяцчщшъьюя Йццйщцйц-фдылфывфв';
		$validator->type = "russian_fio";
		$validator->validate($model);
		$this->assertEquals(0, count($model->getErrors()));
		$model->clearErrors();

		//латинская а и 0
		$model->client_name = 'Ивaн0в';
		$validator->type = "russian_fio";
		$validator->validate($model);
		$this->assertEquals(1, count($model->getErrors()));
		$model->clearErrors();


		//word
		$model->client_name = 'ЪфбвёЁАйцщичнцwiyq_qxikxaa';
		$validator->type = "word";
		$validator->validate($model);
		$this->assertEquals(0, count($model->getErrors()));
		$model->clearErrors();

		//пробел
		$model->client_name = 'Ивaнв ww';
		$validator->type = "word";
		$validator->validate($model);
		$this->assertEquals(1, count($model->getErrors()));
		$model->clearErrors();

		//russian_word
		$model->client_name = 'ЪфбвёЁАйцщи-чнц_';
		$validator->type = "russian_word";
		$validator->validate($model);
		$this->assertEquals(0, count($model->getErrors()));
		$model->clearErrors();

		//латинское слово
		$model->client_name = 'Ивaнвww';
		$validator->type = "russian_word";
		$validator->validate($model);
		$this->assertEquals(1, count($model->getErrors()));
		$model->clearErrors();

		// проверка на латинские символы
		$model->client_name = 'abcdeЖ';
		$validator->type = "latinCharacters";
		$validator->validate($model);
		$this->assertEquals(1, count($model->getErrors()));
		$model->clearErrors();

		// проверка префикса для города
		$model->client_name = 'msk';
		$validator->type = "prefix";
		$validator->validate($model);
		$this->assertEquals(1, count($model->getErrors()));
		$model->clearErrors();

		//идентификатор
		$model->client_name = 'abc12345-123123_13123-qweasdasd';
		$validator->type = "uid";
		$validator->validate($model);
		$this->assertEquals(0, count($model->getErrors()));
		$model->clearErrors();

		//идентификатор
		$model->client_name = 'йцуйцу ';
		$validator->type = "uid";
		$validator->validate($model);
		$this->assertEquals(1, count($model->getErrors()));
	}

}