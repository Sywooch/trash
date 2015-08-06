<?php
/**
 * Created by PhpStorm.
 * User: a.tyutyunnikov
 * Date: 16.09.14
 * Time: 15:57
 */

namespace dfs\tests\docdoc\components;

use dfs\docdoc\components\RatingComponent;
use dfs\docdoc\models\RatingStrategyModel;

class RatingComponentTest extends \CDbTestCase
{
	/**
	 * Тест получения объекта компонента Rating
	 *
	 */
	public function testSetFromConfig()
	{
		$config = $this->initComponent();

		/** @var RatingComponent $rating */
		$rating = \Yii::createComponent($config);
		$rating->init();

		$config['api']['rest']['strategy'] = [
			RatingStrategyModel::FOR_DOCTOR => 2,
		];
		\Yii::app()->setParams($config);
		$rating->setFromConfig();

		$this->assertEquals(2, $rating->getId(RatingStrategyModel::FOR_DOCTOR));

	}

	/**
	 * Инициализация компонентов и получение конфига для тестов
	 * @return array
	 */
	private function initComponent()
	{
		//убираем проверку первичных ключей
		$this->getFixtureManager()->checkIntegrity(false);

		$this->getFixtureManager()->truncateTable('rating_strategy');
		$this->getFixtureManager()->loadFixture('rating_strategy');

		$config = require ROOT_PATH . "/common/config/overall/common.php";

		if (!isset($config['components']['rating'])) {
			$this->fail('Не найден конфиг для сортировщика');
		}

		return $config['components']['rating'];
	}
}
