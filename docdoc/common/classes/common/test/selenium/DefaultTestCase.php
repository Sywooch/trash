<?php
namespace dfs\common\test\selenium;

use dfs\common\config\Environment;
use PHPUnit_Extensions_Selenium2TestCase;
/**
 * Стандартный тесткейс для запуска тестов в селениуме
 *
 * Class DefaultTestCase
 *
 * @package dfs\common\test\selenium
 */
class DefaultTestCase extends PHPUnit_Extensions_Selenium2TestCase
{
	/**
	 * Логин + пароль для входа в БО
	 */
	const OPERATOR_LOGIN = 'test_operator';
	const OPERATOR_PASSWORD = 'docdoc_test_operator';

	/**
	 * проверка в БО что заявка создалась
	 *
	 * @param array $check_parameters
	 */
	public function checkRequest(array $check_parameters)
	{
		$this->loginInBO(self::OPERATOR_LOGIN, self::OPERATOR_PASSWORD);
		$this->openPageBO('Список заявок');
		$this->assertTrue($this->findRequestBO($check_parameters));
	}

	/**
	 * Установка начальных значений
	 */
	protected function setUp()
	{
		parent::setUp();
		$this->setBrowserUrl("http://{$this->getFrontHost()}/");
	}

	/**
	 * логин в БО
	 *
	 * @param $login
	 * @param $password
	 */
	public function loginInBO($login, $password)
	{
		$this->url($this->getBackUrl());
		$this->timeouts()->implicitWait(5000);
		$this->byName('login')->value(self::OPERATOR_LOGIN);
		$this->byName('passwd')->value(self::OPERATOR_PASSWORD);
		$this->byClassName('submit')->click();
		$this->timeouts()->implicitWait(5000);
	}

	/**
	 * Открытие нужной страницы в БО
	 *
	 * @param $pageTitle
	 */
	public function openPageBO($pageTitle)
	{
		$link = $this->byLinkText($pageTitle);
		$link->click();
	}

	/**
	 * Проверим есть ли запись в БО
	 *
	 * @param array $attr
	 *
	 * @return bool
	 */
	public function findRequestBO(array $attr)
	{
		$findOrNot = false;
		$table_with_records = $this->byClassName('resultSet');

		if(isset($attr["phone"])) {
			$phone_number = $table_with_records->element($this->using('css selector')->value('td:nth-child(9)'));
			$this->assertEquals($attr["phone"], $phone_number->text());
			$findOrNot = true;
		}
		if(isset($attr["client_name"])) {
				$call_back = $table_with_records->element($this->using('css selector')->value('td:nth-child(8)'));
				$this->assertEquals($attr["client_name"], $call_back->text());
				$findOrNot = true;
		}
		if(isset($attr["picture"])) {
				$picture = $table_with_records->element($this->using('css selector')->value('td:nth-child(7) div.null'));
				$this->assertEquals($attr["picture"], $picture->attribute('title'));
				$findOrNot = true;
			}
		return $findOrNot;
	}

	/**
	 * Нажимаем на первый элемент списка используя XPath и css selector
	 *
	 * @param array $attributes
	 */
	public function firstElementOfList(array $attributes)
	{
		$list = $this->byXPath($attributes["list"]);
		$first_of_list =  $list->element($this->using('css selector')->value($attributes["element"]));
		$first_of_list->click();
	}

	/**
	 * Главный домен
	 *
	 * @return string
	 */
	public function getFrontHost()
	{
		return \Yii::app()->getParams()['hosts']['front'];
	}

	/**
	 * Бекофис
	 *
	 * @return string
	 */
	public function getBackHost()
	{
		return \Yii::app()->getParams()['hosts']['back'];
	}

	/**
	 * Ссылка
	 *
	 * @return string
	 */
	public function getFrontUrl()
	{
		$auth = Environment::isDebug() ? 'test:docdocdoc@' : '';
		return "http://{$auth}{$this->getFrontHost()}/";
	}

	/**
	 * Ссылка на бекофис
	 *
	 * @return string
	 */
	public function getBackUrl()
	{
		$auth = Environment::isDebug() ? 'test:docdocdoc@' : '';
		return "https://{$auth}{$this->getBackHost()}/";
	}

	/**
	 * Помогает сгенерировать параметры запуска для селениума
	 *
	 * @param array $browser
	 *
	 * @return array
	 */
	public static function buildBrowser(array $browser)
	{
		return \CMap::mergeArray($browser, [
			'host'                => 'hub.browserstack.com',
			'port'                => 80,
			'desiredCapabilities' => [
				'browserstack.user'  => \Yii::app()->getParams()['browserstack']['user'],
				'browserstack.key'   => \Yii::app()->getParams()['browserstack']['key'],
				'browserstack.debug' => \Yii::app()->getParams()['browserstack']['debug'],
			],
		]);
	}

	/**
	 * Тест на мобильном устройстве или нет
	 *
	 * @return bool
	 */
	public function isMobile()
	{
		return $this->getBrowser() === 'iPhone';
	}

	/**
	 * Установка начальный значений
	 *
	 * @return array
	 */
	public static function browsers()
	{
		return [
			/**
			 * Десктопный браузер
			 */
			static::buildBrowser([
				"browserName"         => "chrome",
				'desiredCapabilities' => [
					'version'           => '38',
					'os'                => 'Windows',
					'os_version'        => '8.1',
					"browser"           => "Chrome",
					"browser_version"   => "38.0",
				]
			]),
			/**
			 * Мобильный браузер
			 *
			 * @todo Поправить
			 *       Не проходит, потому что iOS не нравиться пароль для базовой авторизации который я передаю для входа на сайт
			 */
//			static::buildBrowser([
//				"browserName"         => "iPhone",
//				'desiredCapabilities' => [
//					"browserName" => "iPhone",
//					"device"      => "iPhone 5",
//				],
//			]),
		];
	}
} 