<?php
namespace dfs\tests\docdoc\components;

use CTestCase;
use dfs\docdoc\components\Version;

class VersionTest
	extends CTestCase
{
	/**
	 * Проверяем сортировку списка версий
	 */
	public function testGetVersionImageNameFromList()
	{
		$currentVersion = '2.34.1';
		$needVersion = '2.33.1';

		$images = ['2.33', '2.33.1', '2.4'];
		$v = new Version();

		$v->setImagesVersionList($images);
		$v->setVersion($currentVersion);

		$this->assertEquals(
			$v->getCurrentImageIndex(),
			$needVersion
		);
	}
} 