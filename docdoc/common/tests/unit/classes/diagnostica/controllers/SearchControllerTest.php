<?php

require ROOT_PATH . '/diagnostica/public/protected/components/Controller.php';
require ROOT_PATH . '/diagnostica/public/protected/components/FrontendController.php';
require ROOT_PATH . '/diagnostica/public/protected/controllers/diagnostics/SearchController.php';

use dfs\docdoc\components\seo\SeoFactory;
use dfs\tests\mocks\CHttpSessionMock;

/**
 * Class SearchControllerTest
 */
class SearchControllerTest extends \CDbTestCase
{

	/**
	 * Провекра поиска диагностических центров
	 *
	 * @dataProvider searchProvider
	 *
	 * @param array $params
	 * @param int $count
	 *
	 * @throws CHttpException
	 */
	public function testSearch($params = array(), $count)
	{
		$controller = new SearchController('default');
		$data = $controller->getSearchData($params);

		$this->assertEquals($count, $data['dataProvider']->getTotalItemCount());
	}

	/**
	 * Данные для поиска
	 *
	 * @return array
	 */
	public function searchProvider()
	{
		return array(
			// Поиск по выбранной диагностике
			array(
				array(
					'rewriteName' => 'uzi',
					'rewriteNameArea' => null,
					'rewriteNameDistrict' => null,
					'rewriteNameStation' => null,
					'rewriteNameCity' => null,
				),
				6,
			),
			// Поиск по выбранной диагностике и округу
			array(
				array(
					'rewriteName' => 'uzi',
					'rewriteNameArea' => 'cao',
					'rewriteNameDistrict' => null,
					'rewriteNameStation' => null,
					'rewriteNameCity' => null,
				),
				6,
			),
			// Поиск по городу Подмосковья
			array(
				array(
					'rewriteName' => null,
					'rewriteNameArea' => null,
					'rewriteNameDistrict' => null,
					'rewriteNameStation' => null,
					'rewriteNameCity' => 'dolgoprudnyi',
				),
				2,
			),
		);
	}

	public function setup()
	{
		$this->loadFixtures();

		$_SERVER['HTTP_HOST'] = 'diagnostica.docdoc.ru';
		$_SERVER['REQUEST_URI'] = '/';

		$config = $this->getConfig();

		$city = Yii::createComponent($config['components']['city']);
		$city->init();

		Yii::app()->setComponent('city', $city);
		Yii::app()->setComponent('session', new CHttpSessionMock());
		Yii::app()->setComponent('request', new CHttpRequest());

		Yii::app()->params->siteName = 'diagnostica';
		Yii::app()->params->siteId = 2;

		Yii::app()->setComponent(
			'seo',
			SeoFactory::getSeo(
				'searchcontroller',
				'index',
				'/uzi',
				Yii::app()->city->getCityId()
			)
		);

	}

	/**
	 * Получение конфига для тестов
	 * @return array
	 */
	private function getConfig()
	{

		$config = require ROOT_PATH . "/common/config/overall/common.php";

		if (!isset($config['components']['city'])) {
			$this->fail('Не найден конфиг для компонента');
		}

		return $config;
	}

	/**
	 * Загрузка фикстур
	 *
	 * @throws CException
	 */
	private function loadFixtures()
	{
		$this->getFixtureManager()->checkIntegrity(false);

		$this->getFixtureManager()->truncateTable('city');
		$this->getFixtureManager()->truncateTable('clinic');
		$this->getFixtureManager()->truncateTable('diagnostica4clinic');
		$this->getFixtureManager()->truncateTable('diagnostica');
		$this->getFixtureManager()->truncateTable('area_moscow');
		$this->getFixtureManager()->truncateTable('district');
		$this->getFixtureManager()->truncateTable('closest_district');
		$this->getFixtureManager()->truncateTable('closest_station');
		$this->getFixtureManager()->truncateTable('reg_city');
		$this->getFixtureManager()->truncateTable('underground_station');
		$this->getFixtureManager()->truncateTable('district_has_underground_station');
		$this->getFixtureManager()->truncateTable('underground_station_4_clinic');
		$this->getFixtureManager()->truncateTable('underground_station_4_reg_city');

		$this->getFixtureManager()->loadFixture('city');
		$this->getFixtureManager()->loadFixture('clinic');
		$this->getFixtureManager()->loadFixture('diagnostica4clinic');
		$this->getFixtureManager()->loadFixture('diagnostica');
		$this->getFixtureManager()->loadFixture('area_moscow');
		$this->getFixtureManager()->loadFixture('district');
		$this->getFixtureManager()->loadFixture('reg_city');
		$this->getFixtureManager()->loadFixture('underground_station');
		$this->getFixtureManager()->loadFixture('closest_station');
		$this->getFixtureManager()->loadFixture('district_has_underground_station');
		$this->getFixtureManager()->loadFixture('underground_station_4_clinic');
		$this->getFixtureManager()->loadFixture('underground_station_4_reg_city');

	}

}