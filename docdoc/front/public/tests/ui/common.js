// Основные элементы для создания тестов

// http://www.browserstack.com/automate/capabilities-and-timeouts
// http://www.slideshare.net/khroliz/webdriver

// пример:
// node d:/sites/betty/tests/ui/req-doctor-hexShort.js local chrome ANY pics 1.38
// запустит тест через туннель на локальном сервере в хроме в случайной ОС и отобразит картинки в логах, все тесты будут определены в билд 1.38

// assert
var assert = require('assert');


// Проверка на содержание текста
assertEquals(driver.getPageSource().contains("sometext"), true);
assertTrue(driver.getPageSource().contains("sometext"));


// выставляем время ожидания перед выбором любого элемента, т.к. он может не успеть отрендериться браузером
driver.manage().timeouts().implicitlyWait(500);


// поиск элемента каждые 50 миллисекунд
 var locator = { css: '.b-button' };
 client.isElementPresent(locator).then(function(found) {
	 if (found) {
	 	client.findElement(locator).click();
	 }
 });


// Ловим аргументы, распределяем их по индексу
var passedArgs = {}
process.argv.forEach(function(val, index, array) {
	passedArgs[index] = val;
});


// Создаем объект для текущего теста, который может содержать любые свойства и методы
var ddtest = {}

// Определяем по переданному аргументу, где запускать тест
if ( passedArgs[2] == 'local' ) {
	ddtest.url = 'http://192.168.1.68/doctor/flebolog';
}
else if ( passedArgs[2] == 'external' ) {
	ddtest.url = 'http://docdoc.ru/doctor/flebolog';
}
else {
	ddtest.url = 'http://192.168.1.68/doctor/flebolog';
}


// Определяем переданный браузер или выставляем браузер по умолчанию
ddtest.browser = passedArgs[3] || 'chrome'; // firefox, chrome, internet_explorer, safari, opera, iPad, iPhone, android


// Определяем переданную ОС или выставляем ОС по умолчанию
ddtest.os = passedArgs[4] || 'ANY'; // MAC, WIN8, XP, WINDOWS, ANY, ANDROID

// Определяем отображать ли картинки логов
ddtest.debug = 'false';
if ( passedArgs[5] == 'pics' ) {
	ddtest.debug = 'true';
}
else {
	ddtest.debug = 'false';
}

// Определяем передан ли билд
if ( passedArgs[6] != '' ) {
	ddtest.build = passedArgs[6];
}


// Проверяем тайтл страницы
driver.wait(function() {
	return driver.getTitle().then(function(title) {
		return title === 'BrowserStack - Google Search';
	});
}, 1000);


// Вызов js напрямую (нужно поправить, в таком виде не работает
executeScript("return $('[data-stat=btnCardShortDoctor]')[0]");