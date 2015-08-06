<?php

namespace dfs\tests\docdoc\components;

use CDbTestCase;
use dfs\docdoc\components\MobileDetect;

/**
 * Class MobileDetectTest
 *
 * @package dfs\tests\docdoc\components
 */
class MobileDetectTest extends CDbTestCase
{
	/**
	 * Тест определения мобильной версии и формирования url для перенаправления
	 *
	 * @dataProvider provideMobileDetect
	 *
	 * @param array  $params
	 * @param bool   $isMobile
	 * @param string $mobileUrl
	 *
	 * @throws \CException
	 */
	public function testMobileDetect(array $params, $isMobile, $mobileUrl = null)
	{
		$_SERVER['SCRIPT_FILENAME'] = 'index.php';
		$_SERVER['SCRIPT_NAME'] =  '/index.php';
		$_SERVER['REQUEST_URI'] = isset($params['REQUEST_URI']) ? $params['REQUEST_URI'] : '/';
		$_SERVER['HTTP_HOST'] = isset($params['HTTP_HOST']) ? $params['HTTP_HOST'] : 'docdoc.ru';

		$_COOKIE = [];
		if (isset($params['COOKIE'])) {
			$_COOKIE['isMobile'] = $params['COOKIE'];
		}
		$_GET = [];
		if (isset($params['GET'])) {
			$_GET['switchToMobile'] = $params['GET'];
		}

		// создаем заново компонент request, чтобы он смог обновить cookie и прочие параметры
		$request = new \CHttpRequest();
		$request->init();
		\Yii::app()->setComponent('request', $request, false);

		// переопределение города
		\Yii::app()->city->detect();

		$mobileDetect = $this->initComponent();
		$this->assertEquals($isMobile, $mobileDetect->isMobile());
		if ($isMobile) {
			$this->assertEquals($mobileUrl, $mobileDetect->getMobileUrl());
		}
	}

	/**
	 * Инициализация компонентов и получение конфига для тестов
	 * @return MobileDetect
	 */
	private function initComponent()
	{
		$fixtureManager = $this->getFixtureManager();
		$fixtureManager->checkIntegrity(false);
		$fixtureManager->truncateTable('city');
		$fixtureManager->loadFixture('city');

		$config = require ROOT_PATH . "/front/config/overall/main.php";

		if (!isset($config['components']['mobileDetect'])) {
			$this->fail('Не найден конфиг для компонента');
		}

		$mobileDetect = \Yii::createComponent($config['components']['mobileDetect']);
		$mobileDetect->init();

		return $mobileDetect;
	}

	/**
	 * Данные для определения мобильной версии
	 *
	 * @return array
	 */
	public function provideMobileDetect()
	{
		return array(
			array(
				array(
					'HTTP_HOST' => 'docdoc.ru',
					'REQUEST_URI' => '/clinic/spec/hirurgiya',
					'COOKIE' => 1,
				),
				true,
				null,
			),
			array(
				array(
					'HTTP_HOST' => 'docdoc.ru',
					'REQUEST_URI' => '/about',
				),
				false,
			),
			array(
				array(
					'HTTP_HOST' => 'docdoc.ru',
					'REQUEST_URI' => '/about',
					'COOKIE' => 1,
				),
				true,
				null,
			),
			array(
				array(
					'HTTP_HOST' => 'spb.docdoc.ru',
					'REQUEST_URI' => '/about',
					'GET' => 1,
				),
				false,
			),
			array(
				array(
					'HTTP_HOST' => 'docdoc.ru',
					'REQUEST_URI' => '/',
					'COOKIE' => 1,
				),
				true,
				'http://m.docdoc.ru/',
			),
			array(
				array(
					'HTTP_HOST' => 'docdoc.ru',
					'REQUEST_URI' => '/doctor/house/',
					'COOKIE' => 1,
				),
				true,
				'http://m.docdoc.ru/doctor/house',
			),
			array(
				array(
					'HTTP_HOST' => 'docdoc.ru',
					'REQUEST_URI' => '/doctor/stomatolog-terapevt/city/krasnoznamensk',
					'GET' => 1,
				),
				true,
				null,
			),
			array(
				array(
					'HTTP_HOST' => 'docdoc.ru',
					'REQUEST_URI' => '/doctor/stomatolog-terapevt/aviamotornaya',
					'GET' => 1,
				),
				true,
				'http://m.docdoc.ru/doctor/stomatolog-terapevt/aviamotornaya',
			),
			array(
				array(
					'HTTP_HOST' => 'docdoc.ru',
					'REQUEST_URI' => '/request?doctor=123',
					'GET' => 1,
				),
				true,
				'http://m.docdoc.ru/request?doctor=123',
			),
		);
	}
}