<?php
/**
 * @var string $widgetHost
 */
?>
//хранилище инициализированных виджетов
var DdWidgets = {};

var DD_CITY = 'msk';

var DdRequire = {
	Button: {
		widget: 'Modal',
		action: 'LoadWidget',
		template: 'Modal'
	},
	ClinicListMedportal: {
		widget: 'Modal',
		action: 'LoadWidget',
		template: 'Modal'
	},
	ClinicList: {
		widget: 'Modal',
		action: 'LoadWidget',
		template: 'Modal'
	},
	DoctorList: {
		widget: 'Modal',
		action: 'LoadWidget',
		template: 'Modal'
	}
};

DdAttachScript('http://api.sypexgeo.net/jsonp/<?=$ip?>&callback=DdUpdateCity');

/**
 * Виджет DocDoc
 */
function DdWidget(options)
{
	//если вызван new DdWidget(options)
	if  (this instanceof DdWidget) {
		this.init(options);
		void(0);
	} else {
		if ((!options.widget || !options.template || !options.action) && options.widget !== 'frame') {
			return null;
		}

		if (options instanceof Object && options.widget){ //создание нового виджета DdWidget({widget:"Search",...})
			var wgt = null;
			if (!options.id) {
				options.id = 'DD' + options.widget;
			}

			//виджет уже был загружен
			if (options.id && DdWidgets[options.id]) {
				DdWidgets[options.id].extend(options);
				wgt = DdWidgets[options.id];
				console.log("WidgetContentLoad_ " + options.id);
				DdTriggerEvent(document, 'WidgetContentLoad_' + options.id, wgt);
				return wgt;
			}

			//новый виджет
			wgt = new DdWidget(options);
			wgt.loadWidget();
			DdWidgets[wgt.options.id] = wgt;
			return DdWidgets[wgt.options.id];
		}
	}
};

/**
* инициализация объекта
* @param options
*/
DdWidget.prototype.createUrl = function () {

	this.options.feedUrl = this.getProtocol() + '//<?=$host?>/routing.php?r=widget/'+ this.options.action;

	var n;
	for (n in this.options) {
		if (n != 'feedUrl' && n != 'action' && n != 'container') {
			if (this.options[n] instanceof Array) {
				var a = this.options[n];
				for (var i = 0; i < a.length; i++) {
					this.options.feedUrl += "&" + n + "[]=" + a[i];
				}
			} else {
				this.options.feedUrl += "&" + n + "=" + this.options[n];
			}
		}
	}

	return this.options.feedUrl;
};

/**
 * инициализация объекта
 * @param options
 */
DdWidget.prototype.createFrameUrl = function (url) {

	url  = (url.indexOf("/") === 0) ? url.substr(1) : url;
	return this.getProtocol() + '//<?=$host?>/'+ url + '?pid=' + this.options.pid + '&utm_source=partner&utm_medium=cpa&utm_campaign=whitelabel';
};

/**
 * инициализация объекта
 * @param options
 */
DdWidget.prototype.init = function (options) {

	this.offset = null;
	this.minViewable = 0.7;
	this.viewable = 0;
	this.container = null;
	this.iframe = null;
	this.options = {
		id: null,
		widget: null,
		template: null,
		pid: null,
		container: null,
		url: null,
		srcPath: window.location.pathname,
		city: DD_CITY
	};

	this.extend(options);

	if (this.createContainer() === false) {
		return false;
	}

	this.offset = this.getOffset();

	var _this = this;

	if (options.widget === 'frame') {
		var iframe = document.createElement('iframe');
		this.iframe = iframe;
		iframe.setAttribute('src', this.createFrameUrl(options.url));
		iframe.setAttribute('width', options.width);
		iframe.setAttribute('frameborder', 0);
		iframe.setAttribute('scrolling', 'no');

		DdEvent.add(window, 'message', function (e) {
			if (e.data && e.data == parseInt(e.data, 10)) {
				iframe.height = e.data + "px";
			}
			_this.sendBq();
		});

		this.container.appendChild(this.iframe);
	} else {
		DdEvent.add(document, this.options.id + '_load', function (e) {
			_this.handleResponse(e.data);
		});
	}

};

/**
 * проверка видимости виджета
 */
DdWidget.prototype.checkViewable = function()
{
	if (this.viewable > this.minViewable) {
		return;
	}

	this.calcViewable();

	if (this.viewable > this.minViewable) {
		this.sendBq({action:'viewable'});
		return;
	}


	var _this = this;
    DdEvent.add(window, 'scroll', function (e) {
		if (_this.viewable > _this.minViewable) {
			return;
		}

		_this.calcViewable();
		if (_this.viewable > _this.minViewable) {
			_this.sendBq({action:'viewable'});
		}
	});
};

/**
 * Расчет процента видимости
 * @returns {number}
 */
DdWidget.prototype.calcViewable = function()
{
	var scrollTop = window.pageYOffset || document.documentElement.scrollTop;

	//верх виджета ушел под скролл
	if (this.offset.top < scrollTop) {
		return this.viewable = 1;
	}
	//низ виджета выше низа окна
	if (scrollTop + document.documentElement.clientHeight > this.container.clientHeight + this.offset.top) {
		return this.viewable = 1;
	}
	//% видимости
	this.viewable = (scrollTop + document.documentElement.clientHeight - this.offset.top) / (this.container.clientHeight);
	return this.viewable;
};

/**
* логирование в bq
*/
DdWidget.prototype.sendBq = function(params) {
	// Логгируем событие загрузки виджета
	var eventData = {
		partner: this.options.pid,
		action: this.options.action,
		widget: this.options.widget,
		template: this.options.template,
		theme: this.options.theme,
		domain: this.options.srcDomain,
		srcPath: window.location.pathname,
		srcDomain: window.location.host
	};

	if (params) {
		var each;
		for (each in params) {
			eventData[each] = params[each];
		}
	}

	var json = JSON.stringify(eventData);
	var url = '<?=$widgetHost?>/bQ/event/?category=widget&data=' + json;
	var _this = this;
	setTimeout(function() {_this.callDd(url);}, 1000);
};

/**
 * Переопределение свойств объекта
 * @param options
 */
DdWidget.prototype.extend = function (options) {
	var n;
	for (n in options) {
		options.hasOwnProperty(n) && (this.options[n] = options[n]);
	}

	if (this.options.id == null) {
		this.options.id = this.widget + "_" + Math.round(Math.random()*10000);
	}
};

/**
 * создание контейнера
 *
 * @return {boolean}
 */
DdWidget.prototype.createContainer = function () {
	if (!this.options.container) {
		this.options.container = 'dd-feed-container' + this.options.id;
	}

	this.container = document.getElementById(this.options.container);
	if (!this.container) {
		if (this.options.widget !== "Modal") {
			return false;
		}

		this.container = document.createElement("div");
		this.container.id = this.options.container;
		document.body.appendChild(this.container);
		this.container.innerHTML = "";
	}

	return true;
};

/**
 * пустышка для совместимости со старой спецификацией
 */
DdWidget.prototype.load = function () {};

/**
 * загрузка контента
 */
DdWidget.prototype.loadWidget = function (url) {

	if (this.options.widget === 'frame') {
		if (url) {
			this.iframe.src = this.createFrameUrl(url);
		}
	} else {
		this.options.feedUrl = (url != undefined) ? url : this.createUrl();
		this.callDd(this.options.feedUrl);
	}
};

DdWidget.prototype.callDd = function(url) {
	var js, ref = window.document.getElementsByTagName('script')[0];
	js = window.document.createElement('script');
	js.async = true;
	js.src = url;
	ref.parentNode.insertBefore(js, ref);
};

/**
 * обработка загруженного контента
 * @param data
 */
DdWidget.prototype.handleResponse = function(data)
{
	if (data.css) {
		for (var i=0; i < data.css.length; i++) {
			var id = "dd-" + data.css[i].replace("/","-");
			if (document.getElementById(id)) {
				continue;
			}
			var sheet = document.createElement('style')
			sheet.innerHTML = data.styles[data.css[i]];
			sheet.id = id;
			document.body.appendChild(sheet);
		}
	}

	if (data.content) {
		this.container.innerHTML = data.content;
	}

	if (data.onload) {
		var _this = this;
		setTimeout(function() {
			data.onload(_this);
			DdTriggerEvent(document, 'WidgetContentLoad_' + _this.options.id, _this);
			_this.initCallWidgets();
		}, 0);
	} else {
		DdTriggerEvent(document, 'WidgetContentLoad' + this.options.id, this);
		this.initCallWidgets();
	}

	if (data.content) {
		this.checkViewable();
		this.sendBq();
	}
};

DdWidget.prototype.getProtocol = function()
{
	return document.location.protocol === "https:" ? "https:" : "http:";
};

DdWidget.prototype.getOffset = function ()
{
	var offsetTop = 0, offsetLeft = 0, el = this.container;
	do {
		if ( !isNaN( el.offsetTop ) ) {
			offsetTop += el.offsetTop;
		}
		if ( !isNaN( el.offsetLeft ) ) {
			offsetLeft += el.offsetLeft;
		}
	} while( el = el.offsetParent );

	return {
		top : offsetTop,
		left : offsetLeft
	}
};

/**
 * инициализация вызова других виджетов из HTML шаблона
 */
DdWidget.prototype.initCallWidgets = function()
{
	var _this = this;
	var el = this.allByClass("dd-call-widget");
	for (var i=0; i < el.length; i++) {
		DdEvent.add(el[i], 'click', function(e) {
			var sParams = this.getAttribute('data-widget');
			var oParams = eval('('+sParams+")");
			oParams.pid = _this.options.pid;
			oParams.id = "DDModal";
			DdWidget(oParams);
			return false;
		});
	}
};

/**
 * поиск по имен класса
 *
 * @param className
 */
DdWidget.prototype.byClass = function(className) {
	var e = this.container.getElementsByClassName(className);
	return e[0];
}

/**
 * поиск по имен класса
 *
 * @param className
 */
DdWidget.prototype.allByClass = function(className) {
	return this.container.getElementsByClassName(className);
}

/**
 * удаление объекта
 */
DdWidget.prototype.destroyWidget = function () {
	this.container.innerHTML = '';
	DdWidgets[this.options.id] = null;
};

/**
 * подписка на события
 */
var DdEvent = (function() {

	var guid = 0;

	function fixEvent(event) {
		event = event || window.event;

		if ( event.isFixed ) {
			return event
		}
		event.isFixed = true;

		event.preventDefault = event.preventDefault || function(){this.returnValue = false};
		event.stopPropagation = event.stopPropagaton || function(){this.cancelBubble = true};

		if (!event.target) {
			event.target = event.srcElement
		}

		if (!event.relatedTarget && event.fromElement) {
			event.relatedTarget = event.fromElement == event.target ? event.toElement : event.fromElement;
		}

		if ( event.pageX == null && event.clientX != null ) {
			var html = document.documentElement, body = document.body;
			event.pageX = event.clientX + (html && html.scrollLeft || body && body.scrollLeft || 0) - (html.clientLeft || 0);
			event.pageY = event.clientY + (html && html.scrollTop || body && body.scrollTop || 0) - (html.clientTop || 0);
		}

		if ( !event.which && event.button ) {
			event.which = (event.button & 1 ? 1 : ( event.button & 2 ? 3 : ( event.button & 4 ? 2 : 0 ) ));
		}

		return event
	}

	/* Вызывается в контексте элемента всегда this = element */
	function commonHandle(event) {
		event = fixEvent(event);

		var handlers = this.events[event.type];

		for ( var g in handlers ) {
			var handler = handlers[g];

			var ret = handler.call(this, event);
			if ( ret === false ) {
				event.preventDefault();
				event.stopPropagation();
			}
		}
	}

	return {
		add: function(elem, type, handler) {
			if (elem.setInterval && ( elem != window && !elem.frameElement ) ) {
				elem = window;
			}

			if (!handler.guid) {
				handler.guid = ++guid
			}

			if (!elem.events) {
				elem.events = {};
				elem.handle = function(event) {
					if (typeof Event !== "undefined") {
						return commonHandle.call(elem, event)
					}
				}
			}

			if (!elem.events[type]) {
				elem.events[type] = {};

				if (elem.addEventListener)
					elem.addEventListener(type, elem.handle, false);
				else if (elem.attachEvent)
					elem.attachEvent("on" + type, elem.handle)
			}

			elem.events[type][handler.guid] = handler
		}
	}
}());

/**
 * зажигание события
 * @param el
 * @param eventName
 * @param data
 */
function DdTriggerEvent(el,eventName, data){
	var event;
	try {
		if(document.createEvent){
			event = document.createEvent('HTMLEvents');
			event.initEvent(eventName,true,true);
		}else if(document.createEventObject){// IE < 9
			event = document.createEventObject();
			event.eventType = eventName;
		}
		event.eventName = eventName;
		event.data = data;
		if(document.createEvent){
			el.dispatchEvent(event);
		}else if(document.createEventObject) {// IE < 9
			el.fireEvent('on'+event.eventType,event);// can trigger only real event (e.g. 'click')
		}else if(el[eventName]){
			el[eventName]();
		}else if(el['on'+eventName]){
			el['on'+eventName]();
		}
	} catch (e) {}
}

/**
* Проверяет, является ли браузер IE. Возвращает версию
*
* @return {integer}
*/
function isIE () {
	var myNav = navigator.userAgent.toLowerCase();
	return (myNav.indexOf('msie') != -1) ? parseInt(myNav.split('msie')[1]) : 0;
}

/**
 * Устанавливает placeholder для IE
 *
 * @var {object} t
 *
 * @return {bool|void}
 */
function setPlaceholder(t) {
	if (!isIE ()) {
		return false;
	}

	var cls = "dd-placeholder";
	var txt = t.getAttribute("placeholder");

	if (txt.length == 0) {
		return false;
	}

	t.className = t.value.length == 0 ? t.className + " " + cls : t.className;
	t.value = t.value.length > 0 ? t.value : txt;

	DdEvent.add(t, 'focus', function() {
		this.className = this.className.replace(cls);
		this.value = this.value == this.getAttribute("placeholder") ? "" : this.value;
	});

	DdEvent.add(t, 'blur', function() {
		if (this.value.length == 0) {
			this.value = this.getAttribute("placeholder");
			this.className = this.className + " " + cls;
		}
	});
}

// Маска для телефона. Начало кода...
var ddFilterStep;

/**
 * Возвращает введенные цифры (без скобок и тире)
 *
 * @param {string} ddFilterTemp значение, введенное в поле телефона
 * @param {string} ddFilterMask маска
 *
 * @return {string}
 */
function DDFilterStrip(ddFilterTemp, ddFilterMask) {
	ddFilterMask = DDReplace(ddFilterMask, '#', '');
	for (ddFilterStep = 0; ddFilterStep < ddFilterMask.length++; ddFilterStep++) {
		ddFilterTemp = DDReplace(ddFilterTemp, ddFilterMask.substring(ddFilterStep, ddFilterStep + 1), '');
	}
	return ddFilterTemp;
}

/**
 * Получает максимальнок количество символов по маске

 *
 * @param {string} ddFilterMask маска
 *
 * @return {integer}
 */
function DDFilterMax(ddFilterMask) {
	var ddFilterTemp = ddFilterMask;
	for (ddFilterStep = 0; ddFilterStep < (ddFilterMask.length + 1); ddFilterStep++) {
		if (ddFilterMask.charAt(ddFilterStep) != '#') {
			ddFilterTemp = DDReplace(ddFilterTemp, ddFilterMask.charAt(ddFilterStep), '');
		}
	}
	return ddFilterTemp.length;
}

/**
 * Маска для ввода телефона
 *
 * @param {integer} key     код клавиши
 * @param {object}  textbox объект input, для которго делается маска
 *
 * @return {bool}
 */
function DDPhoneFilter(key, textbox) {
	var ddFilterMask = '(###)###-##-##';
	var ddFilterNum = DDFilterStrip(textbox.value, ddFilterMask);

	// Преобразование (NumPad 0 в 0, NumPad 1 в 1...)
	if (key > 95 && key < 106) {
		key = key - 48
	}

	// Для табуляции и пробела
	if ((key == 9) || (key == 13)) {
		return true;
	}

	// Delete
	if (key == 46) {
		ddFilterNum = DDFilterStrip('', ddFilterMask);
	}

	// Backspace
	if (key == 8 && ddFilterNum.length != 0) {
		ddFilterNum = ddFilterNum.substring(0, ddFilterNum.length - 1);
	}

	// Если еще можно вписывать, прибавляется новый символ
	else if (
		(key > 47 && key < 58)
		&& ddFilterNum.length < DDFilterMax(ddFilterMask)
	) {
		ddFilterNum = ddFilterNum + String.fromCharCode(key);
	}

	// Формируется окончательная строка для вывода в input
	var ddFilterFinal = '';
	for (ddFilterStep = 0; ddFilterStep < ddFilterMask.length; ddFilterStep++) {
		if (ddFilterMask.charAt(ddFilterStep) == '#') {
			if (ddFilterNum.length != 0) {
				ddFilterFinal = ddFilterFinal + ddFilterNum.charAt(0);
				ddFilterNum = ddFilterNum.substring(1, ddFilterNum.length);
			}
		} else if (ddFilterMask.charAt(ddFilterStep) != '#') {
			ddFilterFinal = ddFilterFinal + ddFilterMask.charAt(ddFilterStep);
		}
	}

	// Своевременное подставление скобок и тире
	if (ddFilterFinal.length < 12) {
		ddFilterFinal = ddFilterFinal.substring(0, ddFilterFinal.length - 1);
	}
	if (ddFilterFinal.length < 9) {
		ddFilterFinal = ddFilterFinal.substring(0, ddFilterFinal.length - 1);
	}
	if (ddFilterFinal.length < 5) {
		ddFilterFinal = ddFilterFinal.substring(0, ddFilterFinal.length - 1);
	}
	if (ddFilterFinal.length < 2) {
		ddFilterFinal = ddFilterFinal.substring(0, ddFilterFinal.length - 1);
	}

	textbox.value = ddFilterFinal;

	return false;
}

/**
 * Заменяет в строке подстроку
 *
 * @param {string} fullString строка, в которой менять
 * @param {string} text       что заменить
 * @param {string} by         на что заменить
 *
 * @return {string}
 */
function DDReplace(fullString, text, by) {
	var strLength = fullString.length;
	var txtLength = text.length;

	if ((strLength == 0) || (txtLength == 0)) {
		return fullString;
	}

	var i = fullString.indexOf(text);
	if ((!i) && (text != fullString.substring(0, txtLength))) {
		return fullString;
	}

	if (i == -1) {
		return fullString;
	}

	var newstr = fullString.substring(0, i) + by;

	if (i + txtLength < strLength) {
		newstr += DDReplace(fullString.substring(i + txtLength, strLength), text, by);
	}

	return newstr;
}

/**
 * Устанавливает маску телефона для заданного объекта
 *
 * @param {object} obj объект input
 *
 * @return {void}
 */
function setPhoneMask(obj) {
	DdEvent.add(obj, 'keydown', function(event) {
		return DDPhoneFilter(event.keyCode, obj);
	});
}
// Маска для телефона. Конец кода.

/**
 * Подключение скрипта
 *
 * @param {string} src
 */
function DdAttachScript(src) {
	var elem = document.createElement("script");
	elem.src = src;
	document.getElementsByTagName('head')[0].appendChild(elem);
}

/**
 * Обновление глобальной переменной DD_CITY
 *
 * @param {string} src
*/
function DdUpdateCity(data) {
	var prefix = DdGetPrefixByRegion(data.region.name_en);
	if (prefix) {
		//DD_CITY = prefix;
	}
}

/**
 * Получение прификса города по региону
 *
 * @param {string} region
*/
function DdGetPrefixByRegion(region) {
	var cityPrefixes = {
		"Sankt-Peterburg": "spb",
		"Leningradskaya Oblast'": "spb",
		"Sverdlovskaya Oblast'": "ekb",
		"Tatarstan": "kazan",
		"Nizhegorodskaya Oblast'": "nn",
		"Novosibirskaya Oblast'": "nsk",
		"Perm Krai": "perm",
		"Samarskaya Oblast'": "samara"
	};

	if (cityPrefixes[region]) {
		return cityPrefixes[region];
	} else {
		return false;
	}
}

window.DdFeedAsyncInit && setTimeout(window.DdFeedAsyncInit, 0);
