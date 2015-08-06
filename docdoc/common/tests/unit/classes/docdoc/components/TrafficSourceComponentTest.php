<?php

namespace dfs\tests\docdoc\components;

use CDbTestCase;
use dfs\docdoc\components\TrafficSourceComponent;
use dfs\tests\mocks\CHttpSessionMock;
use Yii;

/**
 * Class TrafficSourceComponentTest
 *
 * @package dfs\tests\docdoc\components
 */
class TrafficSourceComponentTest extends CDbTestCase
{
	/**
	 * Проверка установки параметров
	 *
	 * @param array $params
	 * @param array $firstParams
	 * @param array $currentParams
	 * @dataProvider getParamsProvider
	 */
	public function testTrafficSource(array $params, array $firstParams, array $currentParams)
	{
		$config = $this->initComponent();

		$_SERVER['QUERY_STRING'] = $params['query'];
		$_COOKIE['traffic_source'] = $params['cookie'];
		if (!is_null($params['session'])) {
			\Yii::app()->session['traffic_source'] = $params['session'];
		}

		$trafficSource = Yii::createComponent($config['components']['trafficSource']);
		$trafficSource->init();

		$this->assertEquals($firstParams, $trafficSource->getParams(TrafficSourceComponent::FIRST_VISIT));
		$this->assertEquals($currentParams, $trafficSource->getParams(TrafficSourceComponent::CURRENT_VISIT));
	}

	/**
	 * @return array
	 */
	public function getParamsProvider()
	{
		return [
			// Пользователь зашел в первый раз с GET параметрами
			[
				[
					'cookie'  => null,
					'session' => null,
					'query'   => 'pid=1&utm_source=test',
				],
				['pid' => '1', 'utm_source' => 'test'],
				['pid' => '1', 'utm_source' => 'test'],
			],
			// Пользователь зашел с GET параметрами и с кукой
			[
				[
					'cookie'  => serialize([
						['params' => 'pid=1&utm_source=test'],
						['params' => 'pid=1&utm_source=test'],
					]),
					'session' => null,
					'query'   => 'pid=2&utm_source=test2',
				],
				['pid' => '1', 'utm_source' => 'test'],
				['pid' => '2', 'utm_source' => 'test2'],
			],
			// Пользователь зашел без параметров, но с кукой и в новой сессии
			[
				[
					'cookie'  => serialize([
						['params' => 'pid=1&utm_source=test'],
						['params' => 'pid=2&utm_source=test2'],
					]),
					'session' => null,
					'query'   => null,
				],
				['pid' => '1', 'utm_source' => 'test'],
				['utm_source' => 'typein', 'utm_medium' => 'typein'],
			],
			// Пользователь зашел без параметров, но с кукой и в текущей сессии
			[
				[
					'cookie'  => serialize([
						['params' => 'pid=1&utm_source=test'],
						['params' => 'pid=2&utm_source=test2'],
					]),
					'session' => true,
					'query'   => null,
				],
				['pid' => '1', 'utm_source' => 'test'],
				['pid' => '2', 'utm_source' => 'test2'],
			],
			// Пользователь зашел без параметров
			[
				[
					'cookie'  => null,
					'session' => null,
					'query'   => null,
				],
				[],
				[],
			],

		];
	}

	/**
	 * Инициализация компонентов и получение конфига для тестов
	 * @return array
	 */
	private function initComponent()
	{
		//убираем проверку первичных ключей
		$this->getFixtureManager()->checkIntegrity(false);

		$this->getFixtureManager()->truncateTable('traffic_params');
		$this->getFixtureManager()->truncateTable('traffic_params_dict');
		$this->getFixtureManager()->truncateTable('request');
		$this->getFixtureManager()->loadFixture('traffic_params');
		$this->getFixtureManager()->loadFixture('traffic_params_dict');
		$this->getFixtureManager()->loadFixture('request');

		$config = require ROOT_PATH . "/common/config/overall/common.php";

		if (!isset($config['components']['trafficSource'])) {
			$this->fail('Не найден конфиг для компонента');
		}

		\Yii::app()->setComponent('session', new CHttpSessionMock());
		\Yii::app()->setComponent('request', new \CHttpRequest());

		return $config;
	}
}
