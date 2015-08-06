<?php
/**
 * Created by PhpStorm.
 * User: a.tyutyunnikov
 * Date: 01.10.14
 * Time: 18:15
 */

namespace dfs\tests\docdoc\objects;


use dfs\docdoc\models\ClinicModel;
use dfs\docdoc\api\feed\Gis2;

class Gis2Test extends \CDbTestCase {

	/**
	 * Настройка окружения
	 *
	 * @throws \CException
	 */
	public function setUp()
	{
		$this->getFixtureManager()->checkIntegrity(false);
		$this->getFixtureManager()->truncateTable('clinic');
		$this->getFixtureManager()->loadFixture('clinic');
	}

	/**
	 * Генерация телефонов
	 */
	public function testGeneratePhonesXml()
	{
		$clinic = ClinicModel::model()->findByPk(1);
		$xmlGenerator = new Gis2();
		$document = $xmlGenerator->getDocument();
		$xml = $xmlGenerator->generatePhonesXml($clinic);
		$document->appendChild($xml);
		$actual = $document->C14N(true, false);
		$expected = '<phones><phone>+7 (495) 641-06-06</phone></phones>';
		$this->assertEquals($expected, $actual);
	}

	/**
	 * Герация координат
	 */
	public function testGenerateCoordinatesXml()
	{
		$clinic = ClinicModel::model()->findByPk(1);
		$xmlGenerator = new Gis2();
		$document = $xmlGenerator->getDocument();
		$xml = $xmlGenerator->generateCoordinatesXml($clinic);
		$document->appendChild($xml);
		$actual = $document->C14N(true, false);
		$expected = '<coordinates><lat>55.6757020000</lat><lon>37.7676990000</lon></coordinates>';
		$this->assertEquals($expected, $actual);
	}

	/**
	 * Генерация xml для Supplier
	 */
	public function testGenerateSupplierXml()
	{
		$clinic = ClinicModel::model()->findByPk(1);
		$xmlGenerator = new Gis2();
		$document = $xmlGenerator->getDocument();
		$xml = $xmlGenerator->generateSupplierXml($clinic);
		$document->appendChild($xml);
		$actual = $document->C14N(true, false);
		$expected = '<supplier><name>Клиника №1</name><address>Краснодарская улица, д. 52, корп. 2</address><coordinates><lat>55.6757020000</lat><lon>37.7676990000</lon></coordinates><url>http://www.clinicanomer1.ru</url><email></email><phones><phone>+7 (495) 641-06-06</phone></phones></supplier>';
		$this->assertEquals($expected, $actual);
	}

	/**
	 * Генерация xml для клиники
	 */
	public function testGenerateClinicXml()
	{
		$config = \Yii::app()->params['2gis'];
		$clinic = ClinicModel::model()->findByPk(1);
		$xmlGenerator = new Gis2();
		$document = $xmlGenerator->getDocument();
		$xml = $xmlGenerator->generateClinicXml($clinic, $config);
		$document->appendChild($xml);
		$actual = $document->C14N(true, false);
		$expected = '<offer><id>1</id><reward metric="roubles">400</reward><order_url>http://docdoc.ru/appointment?clinicId=1</order_url><country_code>ru</country_code><city>Москва</city><supplier><name>Клиника №1</name><address>Краснодарская улица, д. 52, корп. 2</address><coordinates><lat>55.6757020000</lat><lon>37.7676990000</lon></coordinates><url>http://www.clinicanomer1.ru</url><email></email><phones><phone>+7 (495) 641-06-06</phone></phones></supplier></offer>';
		$this->assertEquals($expected, $actual);
	}
}
