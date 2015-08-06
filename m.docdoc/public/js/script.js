jQuery(document).ready(function ($) {

	$(document).bind('mobileinit', function () {
		$.mobile.pushStateEnabled = false;
		$.mobile.ajaxEnabled = true;
	});

	// Временный костыль на отключение AJAX внутри плагина
	$.mobile.ajaxEnabled = false;

	var hideHeader;
	var defaultCityId = 1;//москва
	var isSubmitRequestDoctor = false;
	$('#listSpec > li').on('click', function (e) {
		if ($(this).hasClass("ui-li-divider")) {
			return false;
		}
		var text;
		var id;
		e.preventDefault();
		$(this).addClass('selected').siblings().removeClass('selected');
		text = $(this).find('strong').text();
		id = $(this).attr('data-alias');
		$('.f-s > a').html(text + '<span/><i/>');
		$('.f-s > a').attr('data-alias', id);
		$(this).parents('#select-specialist').find('.back-link').click();
		return false;
	});


	$('#listMetro > li').on('click', function (e) {
		if ($(this).hasClass("ui-li-divider")) {
			return false;
		}
		var text;
		var id;
		e.preventDefault();
		$(this).addClass('selected').siblings().removeClass('selected');
		text = $(this).find('strong').text();
		$('.f-m > a').html(text + '<span/><i/>');
		$('.f-m > a').attr('data-alias', $(this).attr('data-alias'));
		$('.f-m > a').attr('data-id', $(this).attr('data-id'));
		$(this).parents('#select-metro').find('.back-link').click();
		return false;
	});

	$(document).on("click", "#select-specialist-btn", function(){
		$("#filterSpec-input").val("");
		return $("#listSpec").find("li").removeClass("ui-screen-hidden").show();
	});
	$(document).on("click", "#select-metro-btn", function(){
		$("#filterMetro-input").val("");
		return $("#listMetro").find("li").removeClass("ui-screen-hidden").show();
	});

	$(document).on("click", '.form-item.item-3 a.textarea-spoiler', function (e) {
		e.preventDefault();
		$(this).parents('.form-item.item-3').next().slideDown().find('textarea').focus();
		return $(this).parents('.form-item.item-3').hide();
	});
	$('.rightPanel').on('panelbeforeopen', function (event, ui) {
		return $(this).parents('div[data-role=\"page\"]').find('.mask-overlay').css('display', 'block');
	});
	$('.rightPanel').on('panelbeforeclose', function (event, ui) {
		return $(this).parents('div[data-role=\"page\"]').find('.mask-overlay').css('display', 'none');
	});
	$(document).on('click', '#send-review .send-review-btn-block', function (e) {
		var form, link;
		link = $(this).find('a');
		form = $(this).parents('form');
		return $('.send-review-form .form-item .required').each(function () {
			if ($(this).val() === '') {
				return link.attr('href', '#review-error').click();
			} else {
				link.attr('href', '#review-success').click();
				return form.submit();
			}
		});
	});
	$(document).on('click', '#submit-doctor-form', function (e) {
		var $form, link;
		link = $(this);
		submitRedirectLink = $('#submit-redirect-link');
		$form = $(this).parents('form');

		// Для блокировки от повторного нажатия на "Отправить"
		if ($form.data("submiting") != undefined) {
			return false;
		}
		$form.data("submiting", "1");

		// Снимаем блокировку от повторного нажатия на "Отправить" по таймауту
		setTimeout(function () {
			$form.data("submiting", null);
		}, 3000);

		var name = $('.doctor-order-form input[name=name]').val();
		var phone = trimPhone($('.doctor-order-form input[name=phone]').val());
		var doctor = $form.data('doctor-id');
		var clinic = $form.data('clinic-id');
		var url = link.attr('data-request-url');

		if (name.length < 1 || phone.length != 11) {
			submitRedirectLink.attr('href', '#order-error').click();
		} else {
			var data = {'name': name, 'phone': phone, 'doctor': doctor, 'clinic': clinic};

			$(document).trigger('requestSend', [$form[0], data]);

			$.post(url, data, function (result) {
				if (result.status == 'success') {
					$(document).trigger('requestCreated', [$form[0], data, result]);
					submitRedirectLink.attr('href', '#order-success').click()
				}
				else {
					submitRedirectLink.attr('href', '#order-error').click();
				}
			});
		}
		return false;
	});
	hideHeader = function () {
		if ($(window).height() <= 400) {
			return $(document).on('scrollstart', function () {
				$('.hide-scroll').css('display', 'none');
				return $('#find-doctor div[data-role=\"header\"]').toolbar('updatePagePadding');
			}).on('scrollstop', function () {
				$('.hide-scroll').css('display', 'block');
				return $('#find-doctor div[data-role=\"header\"]').toolbar('updatePagePadding');
			});
		}
	};
	hideHeader();
	$('input#filterMetro-input').on('keyup', function () {
		if ($(this).val() === "") {
			return $('#listMetro .ui-li-divider').show();
		} else {
			return $('#listMetro .ui-li-divider').hide();
		}
	});
	$('input#filterSpec-input').on('keyup', function () {
		if ($(this).val() === "") {
			return $('#listSpec .ui-li-divider').show();
		} else {
			return $('#listSpec .ui-li-divider').hide();
		}
	});

	/**
	 * Получает ссылку на список врачей
	 *
	 * @param {string} specialityAlias
	 * @param {string} locationId
	 * @param {string} locationAlias
	 * @param {string} locationType
	 *
	 * @return string
	 */
	var getSearchUrl = function(specialityAlias, locationId, locationAlias, locationType)
	{
		// Если поиск по метро
		if (!locationId) {
			return 'doctor' + (specialityAlias ? '/' + specialityAlias : '');
		}

		// поиск по району
		if (locationType === "district") {
			if (specialityAlias) {
				return 'doctor/' + specialityAlias + '/district/' + locationAlias;
			}

			return 'district/' + locationAlias;
		}

		if (specialityAlias) {
			return 'doctor/' + specialityAlias + '/' + locationAlias;
		}

		return 'search/stations/' + locationId;
	};

	/**
	 * Делает редирект на страницу, опираясь на выбранные специальность и метро
	 *
	 * @returns {boolean}
	 */
	function searchRedirect()
	{
		var specialityAlias = $('#select-specialist').children('option:selected').data('alias');

		var $locationSelectedElement = $('#select-location');
		var locationAlias = $locationSelectedElement.children('option:selected').data('alias');
		var locationId = $locationSelectedElement.children('option:selected').data('id');

		var $form = $locationSelectedElement.closest("form");

		window.location.href =
			$form.data('href')
			+ getSearchUrl(specialityAlias, locationId, locationAlias, $form.data('location-type'));

		return false;
	}

	$(".search-redirect-on-change").on("change", function() {
		searchRedirect();
		return false;
	});

	/**
	 * Кнопка поиска врача
	 */
	$("#find-doctor-search").click(function ()
	{
		searchRedirect();
		return false;
	});

	var $selectSort = $('#select-sort');
	$selectSort.children('option[data-name=""]').hide();
	$selectSort.change(function () {
		$(document).trigger('changeSearchSort', [this, this.value]);
		window.location.href = $(this).find('option:selected').attr('data-url');
	});

	$('input#filterMetro-input').on('focusout', function () {
		if ($(this).val() === 0) {
			$('#listMetro .ui-li-divider').show();
		}
		return false;
	});
	$('input#filterSpec-input').on('focusout', function () {
		if ($(this).val() === 0) {
			$('#listSpec .ui-li-divider').show();
		}
		return false;
	});

	$("#phoneinput").mask("+7 ?(999) 999 99 99", {placeholder: " "});

	var $requestName = $("#requestName");
	if ($requestName.length) {
		$requestName.focus();
	}

	$(".right-panel-close").click( function() {
		$(".rightPanel").panel("close");
	});

	$(document).on("change", ".city-dropdown-wrapper select", function() {
		return window.location = $(this).val();
	});

	$('#listDistrict > li').on('click', function (e) {
		if ($(this).hasClass("ui-li-divider")) {
			return false;
		}
		var text;
		var id;
		e.preventDefault();
		$(this).addClass('selected').siblings().removeClass('selected');
		text = $(this).find('strong').text();
		$('.f-m > a').html(text + '<span/><i/>');
		$('.f-m > a').attr('data-alias', $(this).attr('data-alias'));
		$('.f-m > a').attr('data-id', $(this).attr('data-id'));
		$(this).parents('#select-district').find('.back-link').click();
		return false;
	});
	$(document).on("click", "#select-district-btn", function(){
		$("#filterDistrict-input").val("");
		return $("#listDistrict").find("li").removeClass("ui-screen-hidden").show();
	});
	$('input#filterDistrict-input').on('keyup', function () {
		if ($(this).val() === "") {
			return $('#listDistrict .ui-li-divider').show();
		} else {
			return $('#listDistrict .ui-li-divider').hide();
		}
	});
	$('input#filterDistrict-input').on('focusout', function () {
		if ($(this).val() === 0) {
			$('#listDistrict .ui-li-divider').show();
		}
		return false;
	});
});


$(document).on('pageinit', '#map-page', function () {
	mapInit($(this).data('address'));
});

function mapInit(address) {
	var geocoder = new google.maps.Geocoder();
	var mapOptions = {
		zoom: 14,
		scrollwheel: false,
		navigationControl: false,
		mapTypeControl: false,
		scaleControl: false,
		zoomControl: false,
		streetViewControl: false,
		mapTypeId: google.maps.MapTypeId.ROADMAP
	};
	var map = new google.maps.Map(document.getElementById('map_canvas'), mapOptions);

	geocoder.geocode(
		{'address': cityName + ', ' + address},
		function (results, status) {
			if (status == google.maps.GeocoderStatus.OK) {
				var $mapCanvas = $("#map_canvas");
				var latitude = $mapCanvas.data("latitude");
				if (latitude == "") {
					latitude = results[0].geometry.location.lng();
				}
				var longitude = $mapCanvas.data("longitude");
				if (longitude == "") {
					longitude = results[0].geometry.location.lat();
				}
				var mapLatLng = new google.maps.LatLng(latitude, longitude),
					marker = new google.maps.Marker({
						position: mapLatLng,
						visible: false
					});
				map.setCenter(mapLatLng, mapOptions.zoom);
				marker.setMap(map);
				google.maps.event.trigger(map, 'resize');
				map.setCenter(marker.getPosition());
			}
			var myOptions = {
				content: document.getElementById('infobox'),
				disableAutoPan: true,
				pixelOffset: new google.maps.Size(-15, 0),
				position: mapLatLng,
				closeBoxURL: '',
				isHidden: false,
				alignBottom: true,
				pane: 'mapPane',
				enableEventPropagation: true,
				infoBoxClearance: new google.maps.Size(1, 1)
			};
			var ibLabel = new InfoBox(myOptions);
			ibLabel.open(map);
		}
	);
}

function trimPhone(phone) {
	phone = phone.replace(/[^\d]/g, '');
	return phone.length == 10 ? '7' + phone : phone;
}
