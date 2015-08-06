// Test
// Пробуем записаться к врачу из краткой анкеты

var webdriver = require('browserstack-webdriver');
var assert = require('assert');
// ***************************************************************** //
// ************************ Настройка теста ************************ //
// ***************************************************************** //

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
ddtest.browser = passedArgs[3] || 'chrome';

// Определяем переданную ОС или выставляем ОС по умолчанию
ddtest.os = passedArgs[4] || 'ANY';

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

var capabilities = {
	'browserName' : ddtest.browser,
	'platform' : ddtest.os,
	'build' : ddtest.build,
	'browserstack.user' : 'docdoc',
	'browserstack.key' : 'G88YCshCppT5GSsGD49B',
	'browserstack.debug' : ddtest.debug,
	'browserstack.tunnel' : 'true'
}
var driver = new webdriver.Builder().
	usingServer('http://hub.browserstack.com/wd/hub').
	withCapabilities(capabilities).
	build();

// выставляем время ожидания перед выбором любого элемента, т.к. он может не успеть отрендериться браузером
driver.manage().timeouts().implicitlyWait(500);




// **************************************************************** //
// ************************ Сценарий теста ************************ //
// **************************************************************** //

driver.get( ddtest.url ).then(function() {

	log(driver.By);
	assert.ok(driver.isElementPresent(driver.By.className(".uidfgdfg")), "no element such this");

	// кликаем на кнопку Записаться и вызываем попап для записи
	driver.findElement({ css: '.ui-btn_green.js-request-popup' }).click();

	// ищем поле для ввода имени и пишем туда test
	driver.findElement({ css: '.popup [name="requestName"]' }).sendKeys('test');

	// кликаем в инпут для номера телефона, без клика ввод номера ведёт себя непредсказуемо - иногда работает, иногда не вводит ничего, иногда путает цифры местами, скорее всего это связано с маской, применяемой к инпуту
	driver.findElement({ css: '.popup [name="requestPhone"]' }).click();

	// отправляем в инпут для номера телефона, собственно, сам номер
	driver.findElement({ css: '.popup [name="requestPhone"]' }).sendKeys('9154736453');

	// кликаем на кнопку Записаться внутри попапа и завершаем запись к врачу
	driver.findElement({ css: '.popup .req_submit' }).click();

	// ждем полсекунды, чтобы произошла реакция сервера на заявку
	driver.sleep(500);

	// кликаем на попап, чтобы сохранился скриншот экрана после отправки заявки, если скриншоты включены
	driver.findElement({ css: '.js-popup.request' }).click();

	// ждем полсекунды на всякий случай
	driver.sleep(500);

	driver.wait(function() {
		return driver.getTitle().then(function(title) {
			// return title === 'BrowserStack - Google Search';
			log('everyithing is cool!');
			return false;
		});
	}, 1000);

});

// завершаем тест
driver.quit();