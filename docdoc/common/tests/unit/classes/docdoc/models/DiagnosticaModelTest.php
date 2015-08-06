<?php

namespace dfs\tests\docdoc\models;

use dfs\docdoc\models\DiagnosticaModel;
use CDbTestCase;
use Yii;

/**
 * Class DiagnosticaModelTest
 *
 * @package dfs\tests\docdoc\models
 */
class DiagnosticaModelTest extends CDbTestCase
{

	/**
	 * Проверяем поиск по алиасу
	 *
	 * @throws \CException
	 */
	public function testSearchByAlias()
	{
		$this->getFixtureManager()->checkIntegrity(false);
		$this->getFixtureManager()->truncateTable(DiagnosticaModel::model()->tableName());
		$this->getFixtureManager()->loadFixture(DiagnosticaModel::model()->tableName());

		// Проверяем что нашлась диагностика с поддиагностикой
		$diagnostic = DiagnosticaModel::model()->searchByAlias('uzi-pecheni')->with('parent')->find();
		$this->assertNotNull($diagnostic);
		$this->assertNotNull($diagnostic->parent);
		$this->assertEquals('uzi-pecheni', $diagnostic->getRewriteName());
	}

}