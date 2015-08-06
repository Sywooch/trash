function CardMap() {
	this.map = null;
	this.placemark = null;

	this.center = [0, 0];
	this.defaultCenter = [0, 0];
	this.zoom = null;

	this.balloonContent = null;
	this.metro = null;

	this.completeCallback = function() {};
}

CardMap.prototype.init = function() {
	var $this = this;

	$.getScript('//api-maps.yandex.ru/2.1/?lang=ru_RU', function(data, textStatus, jqxhr){
		if (jqxhr.status === 200){
			ymaps.ready(function() {
				$this.render();
			});
		} else {
			console.log('Не удалось подключить Яндекс.Карты');
		}
	});
};

CardMap.prototype.render = function() {
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
};

CardMap.prototype.renderPoint = function() {
	this.placemark = new ymaps.Placemark(this.center, {
		balloonContentBody: this.balloonContent
	}, {
		iconLayout: 'default#image',
		iconImageHref: '/i/icon-ya-map.png',
		iconImageSize: [29, 39],
		iconImageOffset: [-10, -35]
	});

	this.map.geoObjects.add(this.placemark);

	if (this.metro != null && this.center[0] == this.defaultCenter[0] && this.center[1] == this.defaultCenter[1]) {
		this.searchByMetro(this.metro)
	}

	this.completeCallback();
};

CardMap.prototype.searchByMetro = function(query) {
	var $this = this;
	ymaps.geocode(query, {results: 1}).then(function (res) {
		var firstGeoObject = res.geoObjects.get(0);
		newCoords = firstGeoObject.geometry.getCoordinates();
		$this.placemark.geometry.setCoordinates(newCoords);
		$this.map.setCenter(newCoords);
		$this.map.setZoom(15);
	});
};