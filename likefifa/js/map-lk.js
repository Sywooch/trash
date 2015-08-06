function LkMap() {
	this.map = null;
	this.placemark = null;
	this.center = [0, 0];
	this.zoom = null;
	this.model = 'LfMaster';
}

LkMap.prototype.init = function () {
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

LkMap.prototype.render = function () {
	this.map = new ymaps.Map("ya-map", {
		center: this.center,
		zoom: this.zoom,
		behaviors: ['default', 'scrollZoom'],
		controls: []
	});
	this.map.controls.add('zoomControl', {
        'float': "none",
        'position': {
            'top': 5,
            'left': 5
        }
	});
	this.renderPoint();
	this.initListeners();
};

LkMap.prototype.renderPoint = function () {
	this.placemark = new ymaps.Placemark(this.center, {
		balloonContentBody: this.balloonContent
	}, {
		iconLayout: 'default#image',
		iconImageHref: '/i/icon-ya-map.png',
		iconImageSize: [29, 39],
		iconImageOffset: [-10, -35],
		draggable: true
	});

	this.map.geoObjects.add(this.placemark);
};

LkMap.prototype.initListeners = function () {
	var $this = this;
	this.placemark.events.add("dragend", function (e) {
		var coords = $this.placemark.geometry.getCoordinates();
		$this.map.setCenter(coords);
		$('#' + $this.model + '_map_lat').val(coords[0]);
		$('#' + $this.model + '_map_lng').val(coords[1]);
	});

	$("#" + this.model + "_add_street, #" + this.model + "_add_house, #" + this.model + "_add_korp").blur(function () {
		$this.search();
	});

	$("#cur-select-popup-underground_station_id, #cur-select-popup-city_id").change(function () {
		$this.search();
	});
};

/**
 * Собирает запрос для поиска точки по адресу
 */
LkMap.prototype.search = function () {
	var metro = "";
	var city = "";

	if ($("#cur-select-popup-city_id").text() != "")    city = $("#cur-select-popup-city_id").text() + " ";
	if ($("#cur-select-popup-underground_station_id").text() != "" && $("#" + this.model + "_add_street").val() == "")
		metro = 'м. ' + $("#cur-select-popup-underground_station_id").text() + " ";
	var query = city + metro + $("#" + this.model + "_add_street").val() + " " + $("#" + this.model + "_add_house").val();
	if ($.trim($("#" + this.model + "_add_korp").val()) != "")
		query += " корпус " + $.trim($("#" + this.model + "_add_korp").val());

	this.searchByQuery(query);
};

/**
 * Ищет точку по адресу
 * @param query
 */
LkMap.prototype.searchByQuery = function (query) {
	var $this = this;

	var metroSelect = $('#inp-select-popup-underground_station_id');

	// Ищем точку на карте
	ymaps.geocode(query, {results: 1}).then(function (res) {
		var firstGeoObject = res.geoObjects.get(0);
		var newCoords = firstGeoObject.geometry.getCoordinates();
		$this.placemark.geometry.setCoordinates(newCoords);
		$this.map.setCenter(newCoords);
		$this.map.setZoom(15);
		$('#' + $this.model + '_map_lat').val(newCoords[0]);
		$('#' + $this.model + '_map_lng').val(newCoords[1]);

		// Если не заполнено метро - определяем по адресу
		if (metroSelect.val() == '') {
			ymaps.geocode(newCoords, {kind: 'metro'}).then(function (data) {
				// Если что-то было найдено
				if (typeof(data.geoObjects.get(0)) != 'undefined') {
					var prop = data.geoObjects.get(0).properties.getAll();
					if (typeof(prop.text) != undefined && prop.text.split(',').length == 4) {
						var parts = prop.text.split(',');
						$.get(homeUrl + 'lk/findMetroByString', {line: parts[2], station: parts[3]}, function (data) {
							if (data == '') {
								return false;
							}

							// Выбираем метро
							var st = $('#select-popup-underground_station_id').find('.item[data-value=' + data + ']');
							if (st.length > 0) {
								st.trigger('click');
							}
						});
					}
				}
			});
		}
	});
};