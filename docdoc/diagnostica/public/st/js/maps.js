$.getScript('http://api-maps.yandex.ru/2.0-stable/?load=package.standard&lang=ru-RU&loadByRequire=1', function(data, textStatus, jqxhr){
	if (jqxhr.status === 200){
		//ymaps.ready(createMap);
		createMap();
	} else {
		console.log('Не удалось подключить Яндекс.Карты');
	}
});

var createMap = function(){
	if ($(".js-ymap-ct.s-open").length > 0) {
		var mapId = $(".js-ymap").attr("id");
		showMap(mapId);
	}

	if ($(".js-ymap-tr").length > 0) {
		$(".js-ymap-tr").click(function(){
			var mapId = $(this).parent().children(".js-ymap").attr("id");
			if(!$(this).hasClass("s-open")) {
				$(".js-ymap-tr").removeClass("s-open");
				$(".js-ymap").not('.js-ymap-main').removeClass("s-open");
				$(this).addClass("s-open");
				showMap(mapId);
			}
			else {
				$("#" + mapId).removeClass("s-open");
				$(this).removeClass("s-open");
			}
		});
	}
}

/* yandex maps */

function showMap (mapId) {
	ymaps.ready(function () {
		var $mapContainer = $("#" + mapId);
		var $mapData = $mapContainer;
		var mapLatitude = $mapData.data("latitude");
		var mapLongitude = $mapData.data("longitude");
		var mapAdress = $mapData.data("adress");
		myMap = new ymaps.Map(mapId, {
				center: [mapLatitude, mapLongitude],
				zoom: 15,
				behaviors: ["default", "scrollZoom"]
			}
		);

		/* Map Placemark */
		myPlacemark = new ymaps.Placemark([mapLatitude, mapLongitude], {
			// Чтобы балун и хинт открывались на метке, необходимо задать ей определенные свойства.
			//balloonContentHeader: "Балун метки",
			//balloonContentBody: "Содержимое <em>балуна</em> метки",
			//balloonContentFooter: "Подвал",
			//hintContent: mapAdress
		}, {
			// Своё изображение иконки метки.
			iconImageHref: '/st/i/icons/i-map_flag.png',
			// Размеры метки.
			iconImageSize: [25, 40]
		});
		myMap.geoObjects.add(myPlacemark);

		if ($mapContainer.data("mobile") == 1) {
			myMap.behaviors.disable(['default', 'scrollZoom', 'drag'])
		}
		else {
			myMap.behaviors.disable(['default', 'scrollZoom'])
		}
		myMap.controls
			// Кнопка изменения масштаба.
			.add('zoomControl', { left: 5, top: 5 })
		/*
		 // Открываем балун на карте (без привязки к геообъекту).
		 myMap.balloon.open([mapLongitude, mapLatitude], "Содержимое балуна", {
		 // Опция: не показываем кнопку закрытия.
		 closeButton: false
		 });
		 // Показываем хинт на карте (без привязки к геообъекту).
		 myMap.hint.show(myMap.getCenter(), "Содержимое хинта", {
		 // Опция: задержка перед открытием.
		 showTimeout: 1500
		 });*/
		/* Map Placemark End */

		$mapContainer.addClass("s-open");
		myMap.container.fitToViewport();


	});

}

/* yandex maps end */