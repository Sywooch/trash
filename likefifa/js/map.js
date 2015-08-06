/**
 * Объект для работы с основной картой каталога
 * @constructor
 */
var CatalogMap = function () {
	this.map = null;
	this.cluster = null;
	this.geoObjects = null;
	this.balloonLayout = null;
	this.isClustered = true;

	this.pane = null;
	this.api = null;
};

/**
 * Загружает скрипты карты
 */
CatalogMap.prototype.init = function () {
	var $this = this;

	$.getScript('//api-maps.yandex.ru/2.1/?lang=ru_RU', function (data, textStatus, jqxhr) {
		if (jqxhr.status === 200) {
			ymaps.ready(function () {
				$this.render();
			});
		} else {
			console.log('Не удалось подключить Яндекс.Карты');
		}
	});
};

/**
 * Инициализирует отрисовку карты
 */
CatalogMap.prototype.render = function () {
	this.balloonLayout = ymaps.templateLayoutFactory.createClass("<div class=\"b-simple-balloon-layout\">\<div class=\"content\" id=\"mapBalloonContent\">{{properties.body|raw}}</div>\</div>");

	var customBalloonContentLayout = ymaps.templateLayoutFactory.createClass([
		'<div class="b-simple-balloon-layout b-custom-balloon-layout">' +
			'<div class="baloon-menu"><ul class=list>',
		// Выводим в цикле список всех геообъектов.
		'{% for geoObject in properties.geoObjects %}',
		'<li><a href=# data-id="{{ geoObject.properties.id }}" class="list_item">{{ geoObject.properties.balloonContentHeader|raw }}</a></li>',
		'{% endfor %}',
		'</ul></div><div class="content" id="mapBalloonContent">Идет загрузка данных ...</div>' +
			'</div>'
	].join(''));

	this.aside();

	this.map = new ymaps.Map("YMapsID", {
		center: [55.743502, 37.633598],
		zoom: 11,
		behaviors: ["default", "scrollZoom"],
		controls: []
	});
	this.map.controls.add('zoomControl');

	this.cluster = new ymaps.Clusterer({
		preset: 'islands#violetClusterIcons',
		clusterBalloonLayout: customBalloonContentLayout
	});

	var $this = this;

	// Событие открытие балуна кластера
	/*this.cluster.events.add('balloonopen', function () {
		var id = $('.baloon-menu li:first-child a').data('id');
		$this.sendCardQuery(id, false, $('#mapBalloonContent'))
	});*/

	// Событие открытие балуна метки
	this.map.events.add('balloonopen', function (e) {
		var balloon = e.get('balloon');
		$this.events.add('click', function (e) {
			if (e.get('target') === $this.map) {
				balloon.close();
			}
		});
	});

	this.getItems();
};

/**
 * Загружает метки
 */
CatalogMap.prototype.getItems = function () {
	var $this = this;
	var path = document.location.pathname;
	$.get(path + (path[path.length - 1] == '/' ? '' : '/') + 'points', function (data) {
		$this.renderItems(data);
	}, 'json');
};

/**
 * Отрисовывает метки на карте
 * @param points
 */
CatalogMap.prototype.renderItems = function (points) {
	var $this = this;
	if (points.length < 20)
		this.isClustered = false;

	for (var i in points) {
		var coordinates = points[i].coords,
			properties = {
				id: points[i].id,
				balloonContentBody: '<p style="margin-bottom:5px; white-space:nowrap;">Идет загрузка данных ...</p>',
				balloonContentHeader: points[i].name

			},
			options = {
				iconLayout: 'default#image',
				iconImageHref: homeUrl + 'i/icon-ya-map-sm.png', // картинка иконки
				iconImageSize: [14, 14],
				iconImageOffset: [-12, -12],
				maxWidth: 350,
				balloonCloseButton: false,
				balloonShadow: false,
				balloonLayout: this.balloonLayout,
				balloonOffset: [0, -60]
			};

		var placemark = new ymaps.Placemark(coordinates, properties, options);
		if (this.isClustered)
			this.cluster.add(placemark);
		else
			this.map.geoObjects.add(placemark);
		placemark.events.add('click', function (e) {
			$this.clickHandler(e);
		});
	}

	if (this.isClustered) {
		this.map.geoObjects.add(this.cluster);
		$(document).on('click', '.baloon-menu li a', function () {
			var id = $(this).data('id');
			$this.sendCardQuery(id, false, $('#mapBalloonContent'))
		});
	}
};

/**
 * Обработчик клика по метке на карте
 * @param e
 */
CatalogMap.prototype.clickHandler = function (e) {
	e.preventDefault();
	var placemark = e.get('target');
	var id = placemark.properties.get('id');

	this.getCardInfo(id);
};

CatalogMap.prototype.checkPlacemarkIsVisible = function (geoObject, move) {
	move = move || false;
	var visible = geoObject.getMap() != null;
	if (move == false)
		return visible;
	this.map.setCenter(geoObject.geometry.getCoordinates());
	while (geoObject.getMap() == null) {
		this.map.setZoom(this.map.getZoom() + 1);
	}
};

/**
 * Получает данные карточки мастера или агентства
 * @param id
 */
CatalogMap.prototype.getCardInfo = function (id) {
	if (this.map.balloon.isOpen())
		this.map.balloon.close();

	var $this = this;

	if (this.isClustered) {
		$.each($this.cluster.getGeoObjects(), function (index, geoObject) {
			if (geoObject.properties.get('id') == id) {
				// Если геообъект скрыт в кластере
				$this.checkPlacemarkIsVisible(geoObject, true);
				$this.sendCardQuery(id, geoObject);
			}
		});
	} else {
		this.map.geoObjects.each(function (placemark) {
			if (placemark.properties.get('id') == id) {
				$this.sendCardQuery(id, placemark);
			}
		});
	}
};

CatalogMap.prototype.sendCardQuery = function (id, geoObject, target) {
	var $this = this;
	$.ajax({
		url: homeUrl + searchEntity + '/card/' + id + '/',
		type: "get",
		success: function (html) {
			if(geoObject == false && typeof(target) != 'undefined') {
				target.html(html);
			} else {
				$this.showCardInfo(geoObject, html);
			}
		}
	});
};

/**
 * Открывает карточку после клика на балун или элемент каталога
 * @param geoObject
 * @param html
 */
CatalogMap.prototype.showCardInfo = function (geoObject, html) {
	var projection = this.map.options.get('projection'),
		position = geoObject.geometry.getCoordinates(),
		positionpx = projection.toGlobalPixels(position, this.map.getZoom());

	var newPost = projection.fromGlobalPixels([positionpx[0] + 150, positionpx[1] - 50 ], this.map.getZoom());

	geoObject.properties.set('body', html);
	geoObject.balloon.open(newPost);

	if (!this.isClustered)
		this.map.panTo(newPost, {delay: 0});
};

/**
 * Закрывает балун
 */
CatalogMap.prototype.closeBalloon = function () {
	this.map.balloon.close();
	$('.left-item.act').removeClass("act");
};

/**
 * Инициализирует события левого каталога
 */
CatalogMap.prototype.aside = function () {
	this.pane = $('.left-col-cont');
	var $this = this;
	this.pane.jscroll({
		contentSelector: '.left-item',
		nextSelector: 'a.lazy-load-next',
		loadingHtml: '<small>Загрузка...</small>',
		callback: function () {
			$this.pane.jScrollPane({maintainPosition: true, animateScroll: true, autoReinitialise: true});
			$this.api = $this.pane.data('jsp');
		}
	});

	$(document).on('click', '.left-col-cont .left-item', function (e) {
		e.preventDefault();

		$(".left-item").removeClass("act");
		$(this).addClass("act");
		var id = $(this).data("id");

		$this.getCardInfo(id);
	});

	$(window).on("resize",function () {
		if ($(window).width() < 1250)
			$("body").removeClass("window-1400").addClass("window-1250");
		else
			$("body").removeClass("window-1250");
		if ($(window).width() < 1400 && $(window).width() >= 1250)
			$("body").addClass("window-1400");
		if ($(window).width() >= 1400)
			$("body").removeClass("window-1400");
		$("#col-right-map").css({height: $(window).height() - $("#header").height()});
		$this.pane.css({height: $(window).height() - $("#header").height() - 10 - $this.pane.position().top});
		if ($this.api != null) {
			$this.api.reinitialise();
		}
	}).trigger("resize");
};