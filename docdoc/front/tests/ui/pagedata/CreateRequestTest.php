<?php
namespace dfs\front\tests\ui\pagedata;

use dfs\common\test\selenium\DefaultTestCase;
use dfs\docdoc\objects\Phone;
/**
 * Class CreateRequestTest
 *
 * @package dfs\front\tests\ui\pagedata
 */
class CreateRequestTest extends DefaultTestCase
{
	/**
	 * Стандартный номер телефона для тестов
	 * и отформатированный номер телефона
	 *
	 * @var string
	 */
	public static $rndPhone = '+7 (999) 111-67-69';
	public static $prettyPhone = '+7 (999) 111-67-69';


	/**
	 * Установка начальных значений
	 */
	protected function setUp()
	{
		parent::setUp();
		self::$rndPhone = new Phone("9".rand(100000000, 999999999));
		self::$prettyPhone = self::$rndPhone->prettyFormat("+7 ");
	}

	/**
	 * проверка возможности отправки заявки из формы - перезвонить мне
	 */
	public function testCreateCallBackRequest()
	{
		$this->url($this->getFrontUrl());
		$this->byClassName('js-callmeback-tr')->click();
		$this->byClassName('callback_input')->value(substr(CreateRequestTest::$rndPhone->getNumber(),1));
		$this->byClassName('callback_submit')->click();
		$this->timeouts()->implicitWait(5000);
		$this->assertEquals('Спасибо, мы вам перезвоним!', $this->byClassName('callback_thanks')->text());
		$this->checkRequest(['phone' => CreateRequestTest::$prettyPhone, 'client_name' => 'Перезвонить Мне', 'picture' => 'Подбор врача']);
	}

	/**
	 * проверка возможности отправки заявки из краткой анкеты
	 */
	public function testCreateShortCardRequest()
	{
		$this->url($this->getFrontUrl());
		$this->firstElementOfList(['list' => '/html/body/main/ul', 'element' => 'a.spec_list_link']);
		$this->timeouts()->implicitWait(5000);
		$this->firstElementOfList(['list' => '/html/body/main/div/section', 'element' => 'input.ui-btn.ui-btn_green.js-request-popup.js-popup-tr.request-button']);
		$this->byName('requestName')->value('Тестовая Запись');
		$this->byXPath('/html/body/div[2]/div[3]/form/p[3]/label/input')->click();// кликаем в поле для ввода телефона
		$this->byXPath('/html/body/div[2]/div[3]/form/p[3]/label/input')->value(substr(CreateRequestTest::$rndPhone->getNumber(),1));
		$this->byXPath('/html/body/div[2]/div[3]/form/p[6]/input')->click();
		$this->timeouts()->implicitWait(5000);
		$this->assertEquals('Ваша заявка о записи на прием к врачу отправлена. Наши консультанты свяжутся с вами в течение 15 минут ежедневно с 9:00 до 21:00 и запишут Вас на прием.', $this->byClassName('js-request-success')->text());
		$this->checkRequest(['phone' => CreateRequestTest::$prettyPhone,'client_name' => 'Тестовая Запись', 'picture' => 'Запись к врачу']);
	}

	/**
	 * проверка возможности отправки заявки из полной анкеты
	 */
	public function testCreateFullCardRequest()
	{
		$this->url($this->getFrontUrl());
		$this->firstElementOfList(['list' => '/html/body/main/ul', 'element' => 'a.spec_list_link']);
		$this->timeouts()->implicitWait(5000);
		$this->firstElementOfList(['list' => '/html/body/main/div/section/article[1]', 'element' => 'h2.doctor_name a']);
		$this->byXPath('/html/body/main/article/div/div[2]/form/input[3]')->click();
		$this->byName('requestName')->value('Тестовая Запись');
		$this->byXPath('/html/body/div[1]/div[3]/form/p[3]/label/input')->click();// кликаем в поле для ввода телефона
		$this->byXPath('/html/body/div[1]/div[3]/form/p[3]/label/input')->value(substr(CreateRequestTest::$rndPhone->getNumber(),1));
		$this->byXPath('/html/body/div[1]/div[3]/form/p[6]/input')->click();
		$this->timeouts()->implicitWait(5000);
		$this->assertEquals('Ваша заявка о записи на прием к врачу отправлена. Наши консультанты свяжутся с вами в течение 15 минут ежедневно с 9:00 до 21:00 и запишут Вас на прием.', $this->byClassName('js-request-success')->text());
		$this->checkRequest(['phone' => CreateRequestTest::$prettyPhone,'client_name' => 'Тестовая Запись', 'picture' => 'Запись к врачу']);
	}


	/**
	 * проверка возможности отправки заявки со страницы /request
	 */
	public function testOnRequestPage()
	{
		$this->url($this->getFrontUrl()."request");
		$titleOnRequestPage = "DocDoc - поиск врачей";
		$this->assertEquals($titleOnRequestPage, $this->title());
		$this->byXPath('/html/body/main/form/p[1]/label/input')->value('Тестовая Запись');
		$this->byXPath('/html/body/main/form/p[2]/label/input')->click();// кликаем в поле для ввода телефона
		$this->byXPath('/html/body/main/form/p[2]/label/input')->value(substr(CreateRequestTest::$rndPhone->getNumber(),1));
		$this->byClassName('search_list_spec')->click();
		$this->timeouts()->implicitWait(5000);
		$this->firstElementOfList(['list' => '/html/body/div[1]/div[1]/ul', 'element' => 'a.spec_list_link']);
		$this->byClassName('search_list_metro')->click();
		sleep(3);
		$this->byXPath('/html/body/div[1]/div[2]/div[1]/div[1]/form/div/div/div[2]/div[2]/div/div[11]/div[1]')->click();//станция Алтуфьево
		$this->byXPath('/html/body/div[1]/div[2]/div[2]/div[2]/div')->click();//кнопка Найти на карте
		$this->timeouts()->implicitWait(5000);
		$this->byClassName('req_submit')->click(); //кнопка Записаться к врачу
		/**
		 * @todo Найти возможный вариант решения
		 *       Если убрать данный sleep то страница не успеет отобразится
		 *       implicitWait не срабатывает
		 */
		sleep(3);
		$this->assertEquals("Благодарим вас за обращение!", $this->byXPath('/html/body/main/h1')->text());
		$this->checkRequest(['phone' => CreateRequestTest::$prettyPhone, 'client_name' => 'Тестовая Запись', 'picture' => 'Подбор врача']);
	}
}