/**
 * Открывает окно редактирования персоональных данных
 *
 * @param self
 */
function getPersonalData(self) {
	$.ajax({
		url: "/user/personal.htm",
		async: true,
		error: function(xml,text){
			alert(text);
		},
		success: function(text){
			containerShow(self, 'PersonalData', text);
		}
	});
}

/**
 * Открывает окно смены города
 *
 * @param self
 */
function changeCity(self) {
	containerShow(self, 'City');
}

/**
 * Изменияет идентификатор города
 *
 * @param cityId
 */
function setCity(cityId) {
	$.ajax({
		type: "get",
		url: "/include/setCity.htm",
		data: "city="+cityId,
		success: function(html) {
			window.location.reload();
		}
	});
}

/**
 * Открывает окно ообщить о проблеме
 *
 * @param self
 */
function getSupportReportScreen(self) {
	console.log(this);
	var str = document.location.href;
	var nStr = str.replace(/&amp;/g,"---ak---");
	$.ajax({
		url: "/support/report.htm",
		type: "post",
		data: "URL="+nStr,
		async: true,
		error: function(xml,text){
			alert(text);
		},
		success: function(text){
			containerShow(self, 'SupportReportPanel', text);
		}
	});
}

/**
 * Показывает всплывающее окно в верхнем меню
 *
 * @param self Кнопка активации окна
 * @param name Название контейнера
 * @param text Содержимое контейнера
 */
function containerShow(self, name, text) {
	if (text) {
		$('#ceilWin_' + name + '_container').html(text);
	}
	var popup = $('#ceilWin_' + name );

	var isPOpupVisible = popup.is(":visible");
	$('.infoElt').hide();
	if (!isPOpupVisible) {
		popup.show();

		popup.css({
			'left': $(self).offset().left + $(self).outerWidth() - popup.outerWidth(),
			'top': $(self).position().top + $(self).outerHeight()
		});
	}
}

/**
 * Выводит на экран список округов по городу
 *
 * @param {int} cityId идентификатор города
 * @param {int} areaId идентификатор округа
 */
function loadAreaList(cityId, areaId) {
	$.get("/2.0/District/areaList/id/" + cityId + "/areaId/" + areaId, function (data) {
		$("#areas").html(data);
	});
}

/**
 * Выводит на экран ближайшие районы для выбора
 *
 * @param {int} cityId идентификатор города
 * @param {int} districtId идентификатор района
 */
function loadClosestDistricts(cityId, districtId) {
	$.get("/2.0/District/closestDistricts/cityId/" + cityId + "/districtId/" + districtId, function (data) {
		$("#closestDistricts").html(data);
	});
}

	$(document).ready(function() {
	var $city = $("#dfs_docdoc_models_DistrictModel_id_city");
	if ($city.length) {
		var areaId = $("#areas").data("id");
		var districtId = $("#closestDistricts").data("id");
		loadAreaList($city.val(), areaId);
		loadClosestDistricts($city.val(), districtId);
		$city.change(function(){
			loadAreaList($city.val(), areaId);
			loadClosestDistricts($city.val(), districtId);
		});
	}
});