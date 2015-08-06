$.getScript(document.location.protocol + '//api-maps.yandex.ru/2.1/?lang=ru_RU', function (data, textStatus, jqxhr) {
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
var yaMapObj = {};
function showMap (mapId) {
	ymaps.ready(function () {
		var $mapContainer = $("#" + mapId);
		var $mapData = $mapContainer.find(".js-map-data");
		var mapLatitude = $mapData.eq(0).data("latitude");
		var mapLongitude = $mapData.eq(0).data("longitude");
		var draggable = $mapData.eq(0).data("draggable");
		var zoomControl = $mapData.eq(0).data("zoom-control");
		var title = $mapData.eq(0).data("title");
		var moreAddress = false;
		var disabledSettings = ['default', 'scrollZoom'];

		if (title) {
			$('.js-popup.popup-address .popup-address-right h4.t-fs-l').html(title);
		}

		yaMapObj[mapId] = new ymaps.Map(mapId, {
				center: [mapLatitude, mapLongitude],
				zoom: 15,
				behaviors: ["default", "scrollZoom"],
				controls: []
			}
		);
		if ($mapContainer.find(".js-map-data").length > 1) {
			moreAddress = true;
		}

		$mapContainer.find(".js-map-data").each(function(){
			var mapLatitude = $(this).data("latitude");
			var mapLongitude = $(this).data("longitude");
			var markerNum = moreAddress ? '<div class="js-ymap-marker-index">'+$(this).data("number")+'</div>' : '';
			addPlacemark(yaMapObj[mapId], mapLatitude, mapLongitude, markerNum);
		});

		if (moreAddress) {
			yaMapObj[mapId].setBounds(yaMapObj[mapId].geoObjects.getBounds());
			yaMapObj[mapId].setZoom(yaMapObj[mapId].getZoom() - 1);
		}

		if ($mapContainer.data("mobile") == 1 || draggable == 0) {
			disabledSettings.push('drag');
			yaMapObj[mapId].cursors.push('pointer');
		}
		yaMapObj[mapId].behaviors.disable(disabledSettings);

		if (zoomControl) {
			yaMapObj[mapId].controls.add(
				new ymaps.control.ZoomControl({
					options: {
						position: {top: 40, left: 10}
					}
				})
			);
		}

		$mapContainer.addClass("s-open");
		yaMapObj[mapId].container.fitToViewport();
	});

}

/**
 * Добавление метки на карту
 *
 * @param myMap
 * @param mapLongitude
 * @param mapLatitude
 * @param markerNum
 */
function addPlacemark(myMap, mapLatitude, mapLongitude, markerNum)
{
	/* Map Placemark */
	myPlacemark = new ymaps.Placemark([mapLatitude, mapLongitude],
		{
			iconContent: markerNum
		}, {
			iconLayout: 'default#imageWithContent',
			iconImageHref: '/img/icons/i-map_flag.png',
			iconImageSize: [34, 51],
			iconImageOffset: [-13, -47]
		});
	myMap.geoObjects.add(myPlacemark);
}

/* yandex maps end */