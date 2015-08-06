var pane = $('.scroll-pane').jScrollPane(
	{
		showArrows: true,
		maintainPosition: false,
		stickToBottom: false
	}
);

var paneAudio = $('.scroll-pane-audio').jScrollPane(
	{
		showArrows: true,
		maintainPosition: false,
		stickToBottom: false
	}
);

$('#panel').bind( 'click', function() {
	var api = pane.data('jsp');api.reinitialise();
	var api = paneAudio.data('jsp');api.reinitialise();
});

$(".callBtn").click(function(){
	var phone = $(this).prev().val();
	if (phone != "") {
		call2client($(this), phone);
	} else {
		alert("Укажите номер телефона");
	}
});

$(".duplicate-link").click(function(){
	$.ajax({
		type: "post",
		url: $(this).data('href'),
		success: function (requestResponse) {
			try {
				var dataObj = JSON.parse(requestResponse);
				if (dataObj.error) {
					alert(dataObj.error);
				}
				if (dataObj.redirectUrl) {
					location.href = dataObj.redirectUrl;
				}
			} catch(e) {
				alert(e);
			}
		}
	});
});

$(".sendMessage").click(function(){
	if ($(this).hasClass('act')) {
		$(this).removeClass('act');
		$.ajax({
			type: "post",
			url: "/2.0/request/sendSms",
			data: {
				request: $("#requestId").val(),
				action: $(this).data('action')
			},
			success: function (requestResponse) {
				$(obj).next().removeClass("load");
				$(obj).show();
				try {
					var dataObj = JSON.parse(requestResponse);
					if (dataObj.status == 'error') {
						alert(dataObj.message);
					} else {
						alert('Сообщение успешно отправлено');
					}
				} catch (e) {
					alert('Не удается распарсить ответ: ' + e);
				}
			}
		});
	}
});

/*	Звонок пациенту	*/
function call2client (obj, phone) {
	$(obj).next().addClass("load");
	$(obj).hide();
	$.ajax({
		type: "get",
		url: "/request/call.htm",
		data: "id="+$("#requestId").val()+"&phone="+phone,
		success: function(requestResponse){
			$(obj).next().removeClass("load");
			$(obj).show();
			try {
				var dataObj = JSON.parse(requestResponse);
				if (dataObj.status == 'error') {
					alert(dataObj.message);
				}
			} catch(e) {
				alert('Не удается распарсить ответ: ' + e);
			}
		}
	});
}

/*	Перевод звонка	*/
function requestTransfer (phone, line, pos, clinicId) {
	$('#ceilWin_'+line).hide();
	$("#transfetStatus_"+line).addClass("load");
	$("#btTransfer_"+line).hide();
	$.ajax({
		type: "get",
		url: "/request/transfer.htm",
		data: "id="+$("#requestId").val()+"&phone="+phone+"&phoneFrom="+$("#phoneFrom").val()+"&clinicId="+clinicId,
		success: function(requestResponse){
			$("#transfetStatus_"+line).removeClass("load");
			$("#btTransfer_"+line).show();
			try {
				var dataObj = JSON.parse(requestResponse);
				if (dataObj.status == 'error') {
					alert(dataObj.message);
				}
			} catch(e) {
				alert('Не удается распарсить ответ: ' + e);
			}
		}
	});
}

var doctorResultsetReady = true;
function getItemResultset() {
	var url = "/request/doctorList.htm";
	var data = $('#requestEditForm').serialize();

	if ($("#kind").val() != 0) {
		url = "/request/clinicList.htm";
	}

	$.ajax({
		type: "get",
		url: url,
		async: true,
		data: data,
		success: function(html){
			$("#searchItemResultsetPane").html(html);
			var api = pane.data('jsp');
			api.reinitialise();
			$.bindRefreshDoctorList();
		}
	});
}

function getHistory() {
	//alert("docback/request/doctorList.htm?"+$('#searchDoctorform').serialize());
	$.ajax({
		type: "get",
		url: "/request/historyList.htm",
		async: false,
		data: "id="+$("#requestId").val(),
		success: function(html){
			$("#historyPane").html(html);
		}
	});
}

$(".multiSel").click(function () {
	getItemResultset();
});
$('#shSectorId, #shHome, #shDoctor, #shClinicName, #shKidsReception, #shKidsAgeFrom, #shKidsAgeTo').bind('change', function() {
	getItemResultset();
});
$('#shDoctor').stop().delay(500).live( 'keyup', function() {
	getItemResultset();
});

$('#shDoctorExt').bind( 'change', function() {
	if ( $('#shDoctorExt').val() != '' ) {
		$('#shSectorId').val("");
		$('#shMetro').val("");
	}
	getItemResultset();
});

$('#shMetro').bind("change", function() {
	if ($.trim($('#shMetro').val()) == '') {
		getItemResultset();
	}
});

/*	Приём состоялся	*/
$('#appointmentStatus').bind("change", function() {
	var isAppointment = (this.value == 'yes');

	if (isAppointment && $.trim($('#apointmentDate').val()) == '') {
		this.selectedIndex = 0;
		alert("Должна быть указана дата посещения");
		return false;
	}
	if (isAppointment && !$('#audioResultset .isVisit').is(':checked') ) {
		this.selectedIndex = 0;
		alert("Должна быть отмечена запись разговора с подтверждением приёма");
		return false;
	}

	if (isAppointment) {
		$('#isRejection').attr("checked", false);
		$('#recallDate').val("");
		$('#recallHour').val("");
		$('#recallMin').val("");
        $('#requestComment').val("Приём состоялся. ");
	}
});


$('#isRejection').bind("change", function() {
	if ($('#isRejection').attr("checked")) {
		$('#appointmentStatus').attr("checked", false);
		
		$('#recallDate').val("");
		$('#recallHour').val("");
		$('#recallMin').val("");

        $('#requestComment').val("Отказ. "+$('#requestComment').val() );
	}
});

$('#rejectReason').bind("change", function() {
   if($('#rejectReason').val() != 0) {
       $('#requestComment').val("Отказ. "+$('#rejectReason option:selected').text());
   }
});

$('#isTransfer').bind("change", function() {
	if ($('#isTransfer').attr("checked")) {
		$('#requestComment').val("Переведен. "+$('#requestComment').val() );
	}
});

$('#apointmentDate').bind("change", function() {
	if (this.value) {
		var val = $('#requestComment').val();
		$('#requestComment').val(val + (val ? '. ' : '') + "Запись на " + this.value);
	}
});

$('#apointmentMin').bind("change", function() {
	if (this.value) {
		$('#requestComment').val($('#requestComment').val() + " " + $('#apointmentHour').val() + ":" + this.value);
	}
});

$('.appointment-checkbox').bind("change", function () {
	if ($(this).attr("checked")) {
		var clinics = [];
		$("#clinicName option").each(function() {
			clinics.push(parseInt($(this).val()));
		});
		$('#recordDate').val($(this).data("date"));
		$('#recordHour').val($(this).attr("data-hour"));
		$('#recordMin').val($(this).attr("data-min"));

		var clinic = $(this).data("clinic");
		if ($.inArray(clinic, clinics) < 0 && clinic != 0) {
			$.ajax({
				type: "post",
				url: "/2.0/request/getBranches",
				async: false,
				data: {
					id: $(this).data('id')
				},
				success: function (html) {
					if (html.length > 0) {
						$(".branches").html(html);

						var message = clinics.length > 0
							? 'ВНИМАНИЕ!!! Выбранная клиника не соответствует клинике из аудиозаписи, поэтому будет изменена!'
							: 'ВНИМАНИЕ!!! Автоматически выбрана клиника из аудиозаписи!';

						$("#report div").attr("class", "error").html(message);
						$("#report").show().attr("class", "wb errorMessage");
						$("#report").delay(3000).fadeOut();
					}
				}
			});
		}
	} else {
		$('#recordDate').val("");
		$('#recordHour').val("");
		$('#recordMin').val("");
	}
});


function toggleCheckBox ( id  ) {
	if ( $(id).attr("checked") ) {
		$(id).attr("checked",false);
	} else {
		eraseRecallTime();
		$(id).attr("checked", true);
	}
	$(id).trigger('change');
}

function eraseRecallTime() {
	$('#recallDate').val('');
	$('#recallHour').val('');
	$('#recallMin').val('')
}

function metroMap() {
	$("#mapOverlay").fadeIn("slow");
	$('#mapPopupIn').css('top', '5px');
//	$('#mapPopupIn').css('top', ($(window).scrollTop() + $(window).height()/2)+'px');
//	$('#mapPopupIn').css('margin-top', (-1)*($('#mapPopupIn').height()/2)+'px');
	$('#mapPopupIn').css('margin-left', (-1)*($('#mapPopupIn').width()/2)+'px');
		
}

function closeMapPopup(){
	$("#mapOverlay").hide();
}



function requestAction ( action, paramString ) {
	var id = $("#requestId").val();
	
	switch (action) {
		case 'reject' : Popup('/request/requestReject.htm','','noClose'); break;
		case 'transfer' : Popup('/request/requestTransfer.htm?sector='+$("#shSectorId").val()+'&'+paramString,'','noClose'); break;
		case 'call_later' : callLater(); break;
		case 'apointment' : {
			var id = $("#requestId").val();
			$("#apointmentIndentificator").html("<div class='load'></div>");
			saveRequest("id="+id+"&status=apointment"+"&call_date="+$("#apointmentDate").val()+"&call_time="+$("#apointmentHour").val()+":"+$("#apointmentMin").val(), setOkLoad("#apointmentIndentificator") ); 
		} break;
		case 'chStatus' : {
			var id = $("#requestId").val();
			saveRequest("id="+id+"&status=chStatus"+"&ownerId="+$("#owner").val()+"&req_status="+$("#status").val() ); 
		} break;
		case 'anotheDoctor' : Popup('/request/anotherDoctor.htm?id='+$("#requestId").val(),'','noClose'); break;
		case 'chClient' : saveClient(); break;
	}
	getHistory();
}




function setAnotherDoctor() {
	var id = $("#requestId").val();
	//alert("/request/service/saveNewDoctor.htm?"+"id="+id+"&doctorName="+$("#doctorName").val()+"&anotherSectorId="+$("#anotherSectorId").val()+"&addDoctorClinicId="+$("#addDoctorClinicId").val()+"&commentNewDoctor="+$("#commentNewDoctor").val());
	$.ajax({
  		url: "/request/service/saveNewDoctor.htm",
		type: "post",
		data: "id="+id+"&doctorName="+$("#doctorName").val()+"&anotherSectorId="+$("#anotherSectorId").val()+"&addDoctorClinicId="+$("#addDoctorClinicId").val()+"&commentNewDoctor="+$("#commentNewDoctor").val(),
  		async: false,
  		dataType: 'json',
		evalJSON: 	true,
		error: function(xml,text){
			alert(text);
		},
  		success: function(text){
  			if (text['status'] == 'success') {
  				clousePopup();
  				$("#searchItemResultsetPane").append('<input id="doctorList_'+text['id']+'" class="hd" type="checkbox" value="'+text['id']+'" name="doctorList['+text['id']+']" checked="true">');
  				getItemResultset();
  			} else {
				alert("Ошибка");
			}
  		}
	});	
}




function createOpinion() {
	var id = $("#requestId").val();
//	alert("/request/service/createOpinion.htm?"+"id="+id+"&doctorName="+$("#doctorName").val()+"&anotherSectorId="+$("#anotherSectorId").val()+"&clinicId="+$("#clinicId").val()+"&commentNewDoctor="+$("#commentNewDoctor").val());
	$.ajax({
  		url: "/request/service/createOpinion.htm",
		type: "post",
		data: "id="+id+"&doctorId="+$("#requestDoctorId").val()+"&opinionText="+$("#opinionText").val(),
  		async: false,
  		dataType: 'json',
		evalJSON: 	true,
		error: function(xml,text){
			alert(text);
		},
  		success: function(text){
  			if (text['status'] == 'success') {
  				clousePopup();
  				alert("ok");
  			} else {
				alert("Ошибка");
			}
  		}
	});	
}


function saveClient() {
	//alert('docback/request/service/saveClient.htm?'+"id="+reqId+"&client="+$("#clientName").val()+"&phone="+$('#clientPhone').val());
	var reqId = $("#requestId").val();
	$.ajax({
  		url: "/request/service/saveClient.htm",
		type: "post",
		data: "id="+reqId+"&client="+$("#clientName").val()+"&phone="+$('#clientPhone').val(),
  		async: true,
  		dataType: 'json',
		evalJSON: 	true,
		error: function(xml,text){
			alert(text);
		},
  		success: function(text){
  			if (text['status'] == 'success') {
  				$("#clientData").html("");
  				getClientData(reqId, true );
  			} else {
				alert("Ошибка");
			}
  		}
	});	
	
}


function getClientData( id, status ) {
	
	$.ajax({
  		url: "/request/getClientData.htm",
		type: "get",
		data: "id="+id,
  		async: true,
		error: function(xml,text){
			alert(text);
		},
  		success: function(text){
  			$("#clientData").html(text);
  			if ( status ) {
  				setOkStatusLine ("#clStatusLine");
  			}
  		}
	});	
	
}


function setOkStatusLine (eltId) {
	 $(eltId).html("<span class='green'>Сохранено!</span>").show().delay(3000).fadeOut(400);
}


function setLoad (eltId) {
	 $(eltId).html("<div class='load'></div>");
}
function setOkLoad (eltId) {
	 $(eltId).html("<div class='loadOk green'>Ok!</div>").show().delay(3000).fadeOut(400);
}


function showPhoneList() {
	
}
function hidePhoneList() {
	
}

function chStatusSelector() {
	$("#ownerInput").attr("class","hd");
	$("#ownerSelector").attr("class","vs");
	$("#chManual").val('1');
	//$("#ownerSelector").bind("change", function(e){
		//$("#status").val($("#statusSel").val());
	//})
}

function chCompanySelector() {
	$("#companyInput").attr("class","hd");
	$("#companySelector").attr("class","vs");
}


function initMetroMap ( idArray ) {
	for (var i = 0 ; i < idArray.length; i++ ) {
		$("#map_stations div:not(.title)").each(function(){
			if ( $(this).attr("data-idline") == idArray[i] ) {
				$(this).addClass("act");
			}
		});
	}
	
	$selectStation = $("#map_stations .act").length;
	$stationsSelectName = [];
	$stationsSelectCount = $('#map_stations div.act').length;
	$('#map_stations div.act').each(function() {
		$stationsSelectName.push($(this).attr('title'));
	});
	$("#select-stations div").html($stationsSelectName.join(', '));
	$("#stations-select-count").html($stationsSelectCount);
	
	if ($selectStation > 0)
		$("#metro-clear-act, #select-stations").css("display","block");
	else
		$("#metro-clear-act, #select-stations").css("display","none");
}



/*	контролирует нажатие только одного чекбокса в аудиозаписях	*/
function changeCheckBoxState(checkbox) {
	if ( $(checkbox).attr("checked") ) {
		$("#audioResultset input:checkbox").attr("checked", false);
		$(checkbox).attr("checked",true);
	} 
}

function saveRequestWithoutRedirect(data)
{
	var request_id = $("#requestId").val();
	var $report = $("#report");
	var type = $("#typeView").val();

	$.ajax({
		url: "/request/service/save.htm",
		type: "post",
		data: data,
		async: true,
		dataType: 'json',
		evalJSON: 	true,
		error: function(xml,text){
			alert(text);
		},
		beforeSend: function() {
			$report.append($('<div>', {class:'waiting'}).html("Оправка данных..."));
			$report.show().attr("class", "wb successMessage");
		},
		success: function (text) {
			if (!$('div.waiting', $report).length) {
				$report.html("");
			}

			if (text['status'] == 'success') {
				var id = parseInt(text['id']);
				var request_url = "/request/request.htm?type=" + type + "&id=" + id;
				var current = (request_id && request_id == id) ? " (текущая) " : '';
				$('div.waiting:first', $report).removeClass('waiting').html("Заявка <a href='" + request_url + "' target='_blank' >" + id + current +"</a> успешно сохранена.");

			} else if (text['error']) {
				$report.attr("class", "wb errorMessage");
				$('div.waiting', $report).attr("class", "error").html("ВНИМАНИЕ!!! " + text['error']);
			} else {
				alert("Ошибка сохранения");
			}
		}
	});
}

/**
 * Сохранение заявки
 *
 * @param {boolean} disableRedirect
 */
function saveRequest(disableRedirect) {
	var $form = $("#requestEditForm");

	$.ajax({
  		url: "/request/service/save.htm",
		type: "post",
		data: $form.serialize(),
  		async: true,
  		dataType: 'json',
		evalJSON: 	true,
		error: function(xml,text){
			alert(text);
		},
		beforeSend: function() {
			// Блокировка от повторного нажатия на "Сохранить"
			if ($form.data("submiting") != undefined) {
				return false;
			} else {
				$form.data("submiting", "1");
			}
		},
  		success: function(text){
  			$("#report div").html("");
  			if (text['status'] == 'success') {
  				if ( !disableRedirect && text['redirect'] == 'yes' ) {
  					window.location.href = text['url'];
  				} else {
  					$("#report").show().attr("class", "wb successMessage");
  	  				$("#report div").attr("class", "ok").html("Заявка "+text['id']+" успешно сохранена.");
  	  				$("#report").delay(500).fadeOut("", function() {
  	  					if (parseInt(text['id']) > 0 ) {
						    var type = $("#typeView").val();
  	  						window.location.href="/request/request.htm?type=" + type + "&id=" + text['id'];
  	  					} else {
  	  						window.location.reload();
  	  					}

  	  				});

  				}

  			} else if (text['error']) {
  				$("#report").attr("class", "wb errorMessage");
  				$("#report div").attr("class", "error").html("ВНИМАНИЕ!!! "+text['error']);
			} else {
				alert("Ошибка сохранения");
			}

			//снимается блокировка кнопки "Сохранить"
			setTimeout(function () {
				$form.data("submiting", null);
			},1000);
  		}
	});	
	
}

/**
 * Обновление аудиозаписей
 */
function reloadAudioList() {
	var reqId = $("#requestId").val();
	var type = $("#typeView").val();
	$("#loadAudioStatus").attr("class", "load");
	$.ajax({
		url: "/request/getAudioList.htm",
		type: "get",
		data: "id=" + reqId + "&type=" + type,
		async: true,
		error: function (xml, text) {
			alert("Ошибка: " + text);
		},
		success: function (text) {
			$("#loadAudioStatus").removeClass("load");
			$("#audioBlock").html(text);
		}
	});
}


/*	Создать отзыв */
function editOpinion (id) {
	var reqId = $("#requestId").val();
	var doctorId = $("#requestDoctorId").val();
	
	if ( reqId > 0 && doctorId > 0 ) {
		$("#popUp").html("");
		$('#modalWin').css("top",(windowCenterY - winDeltaY +getBodyScrollTop())+"px");
		$('#modalWin').css("left",(windowCenterX - winDeltaX)+"px");
		$('#modalWin').show();
				  
		$.ajax({
		  type: "get",
		  url: "/request/createOpinion.htm",
		  async: false,
		  data: "id="+id+"&reqId="+reqId + "&doctorId="+doctorId,
		  success: function(html){
			$("#popUp").html(html);
		  }
		});
	} else {
		alert("Отзыв можно оставить, если в заявке указан врач.");
	}
}

function saveOpinion ( form , path) {
	var path = path || '/opinion/service/editData.htm';
	var form = form || '#editFormOpinion';
	
	//alert("docback"+path+"?"+$(form).serialize());
	$.ajax({
  		url: path,
		type: "post",
		data: $(form).serialize(),
  		async: true,
  		dataType: 'json',
		evalJSON: 	true,
		error: function(xml,text){
			alert("Ошибка: "+text);
		},
  		success: function(text){
  			if (text['status'] == 'success') {
  				chKey = false;
//  				$("#statusWin").html("<span class='green'>Данные успешно сохранены!</span>").show().delay(3000).fadeOut(400);
//  				modalWinKey = 'reload';
  				window.location.reload();
			} else {
				$("#statusWin").html("<span class='red'>Внимание! "+text['error']+"</span>").show().delay(3000).fadeOut(400);
			}
			modalWinKey = 'reload';
  		}
	});	
}

var chKey = false;
function closeThisWindow() {
	if (!chKey) {
		(modalWinKey === 'close') ? $('#modalWin').hide() : window.location.reload()
	} else {
		if (confirm("Данные отзыва будут потеряны. Вы уверены?")) {
			(modalWinKey === 'close') ? $('#modalWin').hide() : window.location.reload()
		}
	}
}

toggleRejectReasons();

// Причины отказа
function toggleRejectReasons() {
	if ($("#isRejection").attr("checked")) {
		$(".rejectReasons").css("display", "");
	} else {
		$("#rejectReason option:first").attr("selected", "selected");
		$(".rejectReasons").css("display", "none");
	}
}


// Сбросить фильтры
$(".clear-filters").click(function(){
	$(".multiCheckbox").removeAttr("checked");
	$(".multiSel").html("");
	$(".multiSelect span").removeClass("act");
	$('.district-filter .hidden').show("Выбрать все");
	$("#shSectorId").val("");
	$("#shMetro").val("");
	$("#shHome").val(0).removeAttr("checked");
	$('#shDoctor').val('');
	$('#shClinic').val('');
	$('.checkbox-diagnostic').removeAttr("checked");
	$('.diagnosticaText').html('выбрать из списка');
	$('#shClinicName').val('');
	$('#shClinicId').val('');
	$('#doctorWorkDate, #doctorWorkHour, #doctorWorkMin, #doctorWorkToHour, #doctorWorkToMin').val('');
	getItemResultset();
});

// Получение ближайших станций
function getClosestStations(clinicId) {
	var output = '';
	if (clinicId > 0) {
		$.ajax({
			url: "/request/service/getClosestStations.htm",
			type: "get",
			data: "id="+clinicId,
			dataType: 'json',
			evalJSON: true,
			success: function(data){
				for (obj in data) {
					output += "<li style='color:" + data[obj].lineColor + "'><span>" + data[obj].name + " - " + data[obj].time + " мин. (" + data[obj].dist + ")</span></li>";
				}

				$(".closest-stations").html(output);
			}
		});
	} else {
		$(".closest-stations").html('');
	}
}

function toogleMarker(elt, id) {
	if ($(elt).hasClass("markerPassive") ) {
		$(elt).removeClass("markerPassive");
		$(elt).addClass("markerActive");
		$("#doctorList_"+id).attr("checked",true);
	} else {
		$(elt).removeClass("markerActive");
		$(elt).addClass("markerPassive");
		$("#doctorList_"+id).attr("checked",false);
	}
}

function checkStatus(elt) {
	if ( $(elt).attr("checked") ) {
		$(".doctorList").attr("checked", false);
		$(".clinicList").attr("checked", false);
		$(elt).attr("checked", true);
		$("#shClinicId").val($(elt).attr("clinicId"));
	} else {
		$("#shClinicId").val("");
	}
}

function initAudioPayer()
{
	$(".audio-rate-button").click(
		function() {
			var rcrd_id = $(this).data('record');
			var rcrd = $("#"+rcrd_id);
			var audioRecord = rcrd[0];
			if (audioRecord.playbackRate == 1)	{
				audioRecord.playbackRate = 2;
				$(this).html("x2");
			} else {
				audioRecord.playbackRate = 1;
				$(this).html("x1");
			}
		}
	);
}

$("div.helper, span.helper").mouseover( function() {
	$(this).stop(true).delay(300).children().show();
});
$("div.helper, span.helper").mouseleave( function() {
	$(".helpEltR").hide();
});

$(document).ready(function() {

    var $diagnosticsList = $('#ceilWin_diagnosticaList');

	function closeDiagnosticListWindow()
	{
		var $checked = $diagnosticsList.find('input[type=checkbox]:checked');

		if($checked.val() == undefined || $checked.val() < 0){
			//ничего не выбрано
		} else {
			var val = $checked.val();
			var other_val = '';
			var name = 'unknown';

			if($checked.val() == 0){
				other_val = name = $('#diagnosticaName').val();
			} else {
                var $label = $checked.closest('label');

				name = $label.text();

                if($label.data('parent')){
                    name = $label.data('parent') + ' ' + name;
                }
			}

			$('span', $diagnosticsList._span).text(name);
			$('input.diagnostica', $diagnosticsList._span).val(val);
			$('input.hidden-diagnostica', $diagnosticsList._span).val(other_val);

		}

		$diagnosticsList.hide();

		if($diagnosticsList._span.closest('.tr-diagnostics').hasClass('main-diagnostics')){
			getItemResultset();
		}
	}


	var uncheckDiagnosticsCheckboxes = function(){
		$('input[type=checkbox]', $diagnosticsList).attr("checked", false);
		$('input[type=text]', $diagnosticsList).val('');
	};

    $(".diagnosticaText").live('click', function(){
        $diagnosticsList._span = $(this);

	    var $input = $('input[type=hidden].diagnostica', this);

	    uncheckDiagnosticsCheckboxes();

	    if($input.val() >= 0){
		    $diagnosticsList.find('input[type=checkbox][value=' + $input.val() + ']').attr('checked', true);
		    $("#diagnosticaName").val($('input[type=hidden].hidden-diagnostica', this).val());
	    }

	    if(parseInt($input.val()) == $input.val()){

	    } else {
		    $diagnosticsList.find('input[type=checkbox][value=' + 0 + ']').attr('checked', true);
	    }

        $diagnosticsList.show();
    });

	//нажатие любоко чекбокса с диагностикой кроме галки "другое"
    $('#ceilWin_diagnosticaList input[type=checkbox]').change(function(){
	    $("#diagnosticaName").val("");
        uncheckDiagnosticsCheckboxes();
        $(this).attr("checked", true);
    });

	//добавить еще одну диагностику
	$(".icon.add").live('click', function () {
		var $thisRow = $(this).closest('tr.tr-diagnostics');
		var $newRow = $("#tr-diagnostics-donor tr.tr-diagnostics").clone();

		$newRow.insertAfter($thisRow);
		$('input[name=apDate]', $newRow).addClass('apointmentDate');

		//reinit datepicker
		$('.apointmentDate').datepicker( {
			changeMonth : true,
			changeYear: true,
			duration : "fast",
			maxDate : "+1y",
			showButtonPanel: true
		});

		$newRow.show();
	});

	$(".icon.minus").live('click', function () {
		if($('#table-filter .tr-diagnostics').length > 1){
			//последний не удаляю
			$(this).closest('tr.tr-diagnostics').remove();
		}
	});

	var kind = $("#kind").val();

	if (kind == 0) {
		$(".doctor-filter").show();
	} else {
		$(".clinic-filter").show();
	}

	getItemResultset();

	$("#diagnosticaName").click(function(){
		if(!$("#subdiagnostica_0").attr('checked')){
			$("#subdiagnostica_0").trigger("change");
		}
	});

	$(".change-field").click(function(){
		$(this).parent().hide();
		$(this).parent().next().show();
	});

	$("#kind-selector").change(function(){
		var kind = $(this).find("select").val();
		$("#kind").val(kind);
		getItemResultset();
		if (kind != 0) {
			$("#searchItem h1").html("Подбор клиники");
			$(".kind-doctor").hide();
			$(".kind-diag").show();
			$(".clinic-filter").show();
			$(".doctor-filter").hide();
		} else {
			$("#searchItem h1").html("Подбор специалиста");
			$(".kind-diag").hide();
			$(".kind-doctor").show();
			$(".clinic-filter").hide();
			$(".doctor-filter").show();
		}
	});

	/*	Выбор клиники	*/
	$("#shClinicName").autocomplete("/service/getClinicList.htm", {
		delay: 10,
		minChars: 2,
		max: 20,
		autoFill: true,
		selectOnly: true,
        matchContains: true,
		formatResult: function (row) {
			return row[0];
		},
		formatItem: function (row, i, max) {
			return row[0];
		}
	}).result(function (event, item) {
		$("#shClinicId").val(item[1]);
		getItemResultset();
	});


	$(".clear-doctor").click(function(){
		$('#shClinicName').val('');
		$('#shClinicId').val('');
		getItemResultset();
	});

	$("#shClinicName").bind("change", function (e) {
		if (jQuery.trim($("#shClinicName").val()) == '') {
			$("#shClinicId").val("0");
		}
	});

	$(".closeButton4Window").click(function() {
		closeDiagnosticListWindow();
	});

	$(".addNewDiagnostic").click(function() {
		closeDiagnosticListWindow();
	});

	$("#doctorWorkHour, #doctorWorkMin, #doctorWorkToHour, #doctorWorkToMin").blur(
		function() {
			getItemResultset();
		}
	);

	$("input[name=apHour]").live('keyup', function(){
		if ($(this).val().length == 2) {
			$(this).next('input[name=apMin]').focus();
		}
	});


	$("#topButton").click(function(){
		//сохранение заявки
		var $form = $("#requestEditForm");

		if($('#kind').val() == 1){ //если выбрана диагностика
			var $diagnosticsTrs = $('.tr-diagnostics').filter(function () {
				return $('input[name="multiple_diagnostics[]"]', this).length &&  $('input[name="multiple_diagnostics[]"]', this).val() != -1;
			});


			if($diagnosticsTrs.length > 0){
				//создается несколько заявок
				if ($form.data("submiting") == undefined) {
					$form.data("submiting", "1");

					var original_data = {};

					$.each($form.serializeArray(), function(_, kv) {
						if (original_data.hasOwnProperty(kv.name)) {
							original_data[kv.name] = $.makeArray(original_data[kv.name]);
							original_data[kv.name].push(kv.value);
						}
						else {
							original_data[kv.name] = kv.value;
						}
					});

					saveRequestWithoutRedirect(original_data);

					$diagnosticsTrs.each(function(k, tr){
						var data = jQuery.extend({}, original_data);

						data['diagnosticaName'] =  $('input.hidden-diagnostica', tr).val();
						data['subdiagnostica[]'] = $('input.diagnostica', tr).val();
						data['apointmentDate'] = $('input[name=apDate]', tr).val();
						data['apointmentHour'] = $('input[name=apHour]', tr).val();
						data['apointmentMin'] = $('input[name=apMin]', tr).val();
						data['chManual'] = 1; //ручная установка статуса
						data['statusSel'] = 2; //принудительно статус "обработана"
						data['multiply_create'] = 1;
						data['requestId'] = null;

						saveRequestWithoutRedirect(data);
					});
				}

				return false;
			}
		}

		saveRequest();
		return false;
	})
});

$.bindRefreshDoctorList = function(){
	getClosestStations($("#shClinicId").val());

	hideSchedule();

    $(".icon-schedule-period").click(
        function(){
            showSchedule($(this));
        }
    )

	$(".doctorList").click(function(){
		checkStatus(this);
		getClosestStations($("#shClinicId").val());
	});

	$(".clinicList").click(function(){
		getClosestStations($("#shClinicId").val());
	});
}

function showSchedule(elem)
{
    var doctorId = $(elem).data('doctor');
    var clinicId = $(elem).data('clinic');

	if ($(elem).hasClass("with-schedule-period")) {
		showSchedulePeriods(doctorId, clinicId);
	} else if ($(elem).hasClass("with-schedule-slots")) {
		showScheduleSlots(doctorId, clinicId);
	} else {
		hideSchedule();
	}
}


/**
 *   Показать расписание работы врача
 */
function showSchedulePeriods(doctorId, clinicId)
{
    var data = $('#requestEditForm').serialize();
    data += '&doctorId=' + doctorId;
    data += '&clinicId=' + clinicId;

	$.ajax({
		type: "get",
		url: '/2.0/schedule/period/',
		async: true,
		data: data,
		success: function(html){
			$("#middleLineBlock").addClass('thin-mode');
			$("#scheduleSlots").hide();
			$("#schedulePeriod").html(html).show();
			updateScrollPane();
		}
	});

}

/**
 *   Показать слоты врача
 */
function showScheduleSlots(doctorId, clinicId) {
    $("#middleLineBlock").addClass('thin-mode');
    $("#schedulePeriod").hide();
    $("#scheduleSlots").show(); //todo крутилку какуюнить типо идет загрузка

    var data = $('#requestEditForm').serialize();
    data += '&doctorId=' + doctorId;
    data += '&clinicId=' + clinicId;

    $.ajax({
        type: "get",
        url: '/2.0/schedule/slots',
        async: true,
        data: data,
        success: function (resp) {
            var div = $("#scheduleSlots");
            div.empty()

            var requestId = $("#requestId").val();
            var li_list = '';

            $.each(resp.slots, function (date, slots) {
                li_list = li_list + '<li><div class="sc-day">' + date + '</div><div class="sc-time"><p>';

                $.each(slots, function (k, slot) {
                    li_list = li_list + '<a class="book_href" data-request-id="' + requestId +'" data-slot-id="' + slot.external_id + '" href="#">' + slot.start_time + '-' + slot.finish_time + '</a><br>';
                })

                li_list = li_list + '</p></div>';
            })


            div.append($("<ul/>", {class: "schedule schedule-slots open"}).append(li_list));

            $(".book_href").click(function (e) {
                e.preventDefault();

                var data = $("#requestEditForm").serialize();
                data = data + '&slotId=' + $(this).data('slot-id');

                $.ajax({
                    type: 'post',
                    url: "/request/service/save.htm",
                    data: data,
                    evalJSON: true,
                    dataType: 'json',
                    success: function (resp) {
                        var errors = '';

                        if (resp.status !== undefined && resp.status == 'success' && resp.id !== undefined) {
                            var msg = 'Заявка успешно сохранена!';

                            if (resp.errors !== undefined && resp.errors.length > 0) {

                                $.each(resp.errors, function (k, error) {
                                    errors = errors + error + "\n";
                                });

                                msg += "\n\nНо есть следующие ошибки: \n" + errors
                            }

                            msg += "\n\n После закрытия этого уведомления, страница будет перезагружена";

                            alert(msg);

                            window.location.href = '/request/request.htm?id=' + resp.id;
                        } else if (resp.errors !== undefined && resp.errors.length > 0) {

                            $.each(resp.errors, function (k, error) {
                                errors = errors + error + "\n";
                            });
                            alert(errors);
                        } else if (resp.error !== undefined) {
                            alert(resp.error);
                        } else {
                            console.log(resp);
                            alert('Сервер вернул неожиданный ответ.');
                        }
                    },
                    error: function () {
                        alert('Запрос вызвал ошибку на сервере')
                    }
                })
            })

            updateScrollPane();
        }
    });

    updateScrollPane();
}

$(document).ready(function(){
    $(".booking-cancel").click(function(e){
        e.preventDefault();

        var data = $("#requestEditForm").serialize();
        data = data + '&slotId=0'; //пусто значит unbook

        $.ajax({
            type: 'post',
            url: "/request/service/save.htm",
            data: data,
            evalJSON: true,
            dataType: 'json',
            success: function(resp){

                var errors = '';

                if (resp.status !== undefined && resp.status == 'success' && resp.id !== undefined) {
                    var msg = 'Заявка успешно сохранена!';

                    if (resp.errors !== undefined && resp.errors.length > 0) {

                        $.each(resp.errors, function (k, error) {
                            errors = errors + error + "\n";
                        });

                        msg += "\n\nНо есть следующие ошибки: \n" + errors
                    }

                    msg += "\n\n После закрытия этого уведомления, страница будет перезагружена";

                    alert(msg);

                    window.location.href = '/request/request.htm?id=' + resp.id;
                } else if (resp.errors !== undefined && resp.errors.length > 0) {

                    $.each(resp.errors, function (k, error) {
                        errors = errors + error + "\n";
                    });
                    alert(errors);
                } else if (resp.error !== undefined) {
                    alert(resp.error);
                } else {
                    console.log(resp);
                    alert('Сервер вернул неожиданный ответ.');
                }
            },
            error: function(){
                alert('Запрос вызвал ошибку на сервере')
            }
        })
    });

	$(".booking-confirm").click(function () {
		var bookingId = $(this).data('booking');

		$.ajax({
			type: 'post',
			url: '/2.0/booking/confirm/' + bookingId,
			success: function (resp) {
				if (resp.success !== undefined && resp.success) {
					var msg = "Резерв подтвержден";
					msg += "\n\n После закрытия этого уведомления, страница будет перезагружена";
					alert(msg);
					location.reload();
				} else if (resp.errors !== undefined && resp.errors.length > 0) {
					var errors = '';
					$.each(resp.errors, function (k, error) {
						errors = errors + error + '\n';
					});
					alert(errors);
				} else {
					console.log(resp);
					alert('Сервер вернул неожиданный ответ.');
				}
			},
			error: function () {
				alert('Запрос вызвал ошибку на сервере')
			}
		})
	});
});

/**
 *   Скрыть расписание
 */
function hideSchedule()
{
	$("#middleLineBlock").removeClass('thin-mode');
	updateScrollPane();
}

/**
 *  Обновить полосы прокрутки
 */
function updateScrollPane()
{
	var pane = $('#searchItemResultset').data('jsp');
	pane.reinitialise();
}
