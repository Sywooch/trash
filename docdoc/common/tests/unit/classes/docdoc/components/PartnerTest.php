<?php

namespace dfs\tests\docdoc\components;

use CDbTestCase;
use dfs\tests\mocks\CHttpSessionMock;

/**
 * Class PartnerTest
 *
 * @package dfs\tests\docdoc\components
 */
class PartnerTest extends CDbTestCase
{
	/**
	 * Тест инициализацию компонента Partner через GET-запрос
	 */
	public function testPartnerFromGet()
	{
		$config = $this->initComponent();

		$_GET[$config['components']['referral']['getParam']] = 1;

		$partner = \Yii::createComponent($config['components']['referral']);
		$partner->init();

		$this->assertEquals('74951234567', $partner->phone);
	}

	/**
	 * Тест инициализацию компонента Partner через сессию
	 *
	 */
	public function testPartnerFromSession()
	{
		$config = $this->initComponent();

		\Yii::app()->session[$config['components']['referral']['sessParam']] = 1;

		$partner = \Yii::createComponent($config['components']['referral']);
		$partner->init();

		$this->assertEquals('74951234567', $partner->phone);

	}

	/**
	 * Инициализация компонентов и получение конфига для тестов
	 * @return array
	 */
	private function initComponent()
	{
		//убираем проверку первичных ключей
		$this->getFixtureManager()->checkIntegrity(false);

		$this->getFixtureManager()->truncateTable('partner');
		$this->getFixtureManager()->truncateTable('phone');
		$this->getFixtureManager()->loadFixture('partner');
		$this->getFixtureManager()->loadFixture('phone');

		$config = require ROOT_PATH . "/front/config/overall/main.php";

		if (!isset($config['components']['referral'])) {
			$this->fail('Не найден конфиг для компонента');
		}

		\Yii::app()->setComponent('session', new CHttpSessionMock());

		return $config;
	}

}