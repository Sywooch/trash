$(document).ready(function () {
	$(function () {
		$.datepicker.setDefaults($.extend($.datepicker.regional["ru"]));
		$(".datePicker").datepicker({
			changeMonth: true,
			changeYear: true,
			duration: "fast",
			maxDate: "+1y",
			showButtonPanel: true
		});
	});

	$("#shDoctor").autocomplete("/opinion/service/getDoctorList.htm", {
		delay: 10,
		minChars: 1,
		max: 20,
		autoFill: true,
		multiple: false,
		selectOnly: true,
		formatResult: function (row) {
			return row[0];
		},
		formatItem: function (row, i, max) {
			return row[0];
		}
	}).result(function (event, item) {
		$("#shDoctorId").val(item[1]);
	});

	if ($("#typeView").val() == 'call_center') {
		reminder();
	}

	/*	Выбор диагностического центра	*/
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
		$("#withBranches").removeAttr("disabled");
		$("#clinicNotFound").removeAttr("checked");
		$('#startPage').val("1");
	});


	$("#shClinicName").bind("change", function (e) {
		if (jQuery.trim($("#shClinicName").val()) == '') {
			$("#shClinicId").val("");
			$("#withBranches").attr("disabled", "true").removeAttr("checked");
		}
		$('#startPage').val("1");
	});

	$("#clinicNotFound").change(function () {
		$("#shClinicName").val("");
		$("#shClinicId").val("");
		$("#withBranches").attr("disabled", "true").removeAttr("checked");
		$("#shType").val("3");
	});

	$("#shClinicId").bind("change", function (e) {
		$("#clinicNotFound").removeAttr("checked");
		if ($(this).val() == '') {
			$("#withBranches").attr("disabled", "true").removeAttr("checked");
		} else {
			$("#withBranches").removeAttr("disabled");
		}
	});

	$('#formSelectAll').change(function() {
		var value = this.checked;
		$('#formRequestList input.selectRow').each(function() {
			this.checked = value;
			this.onchange();
		});
	});

	$("#stepList").change(function() {
		var form = $("#filterForm");
		form.find("input[name='step']").val($(this).val());
		form.submit();
	});

	$("#setStatus").live('change', function(){
		if($(this).val() == 4){//удалена
			$("#rejectionDiv").show();
		} else {
			$("#rejectionDiv").hide();
		}
	});

	$("#rejectionDiv input[type=checkbox]").live('change', function(){
		$("#rejectionDiv select").attr('disabled', !$(this).attr('checked'));
	});
});

/**
 * Очищает фильтр
 */
function clearFilterForm() {
	$(".multi-checkbox").removeAttr("checked");
	$("#filter .hidden").show();
	$(".multiSel").html("");
	$(".multiSelect span").removeClass("act");

	$("#crDateShFrom").val("");
	$("#crDateShTill").val("");

	$("#recDateShFrom").val("");
	$("#recDateShTill").val("");

	$("#crDateReciveFrom").val("");
	$("#crDateReciveTill").val("");
	$("#phone").val("");
	$("#client").val("");
	$("#id").val("");
	$("#destinationPhoneId").val("");

	$("#shClinicName").val("");
	$("#shClinicId").val("");
	$("#clinicNotFound").val("");

	$("#sortBy").val("");
	$("#sortType").val("");

	$('#filter :input').attr('checked', false);
	$("#diagnostica_0").attr('checked', true);
	setFilterLine();
}

/**
 * Строка фильтра
 *
 * @return {string}
 */
function setFilterLine() {
	var str = "";
	$("#ceilWin_multy :checkbox[checked]").each(function (i) {
		if (str != "") {
			str = str + ", " + $(this).attr("txt");
		} else {
			str = $(this).attr("txt");
		}
	});
	$("#statusFilter").text(str);
}

/**
 * Очищает фильтр по диагностике
 */
function setFilterListToNull() {
	$('#diagnostica_0').attr('checked', false);
	if ($("#ceilWin_multy :checkbox[checked]").length == 0) {
		$('#diagnostica_0').attr('checked', true);
	}
}
function setFilterSubList(checkbox) {

	$('input.checkBox4Text', $('#subList_'+checkbox.value)).attr('checked', checkbox.checked);
	setFilterListToNull();
}

$.bindSelectClinic = function ($input, $clinic, $doctor) {

	/*	Выбор клиники	*/
	$input.autocomplete("/service/getClinicList.htm", {
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
		$clinic.val(item[1]);
		$doctor.val(0);
		reloadDoctorList($doctor, $clinic.val());
	});


	$input.bind("change", function (e) {
		if (jQuery.trim($input.val()) == '') {
			$clinic.val("0");
		}
	});
}

function editRequest(id) {
	$.ajax({
		type: 'json',
		url: '/2.0/request/switchToRequest?id=' + id,
		async: false,
		data: {
			nameSession: 'requestFilter',
			filters: $("#filterForm").serializeArray()
		},
		success: function (json) {
			var $tr = $('#tr_' + id);
			var oldOwner = parseInt($tr.data('owner'), 10);
			if (json && !json.isAssignToMe && json.owner && oldOwner != json.owner) {
				var html = '<p>У заявки изменился оператор (' + json.ownerName + '). Перейти к заявке ?</p>';
				$(html).dialog({
					modal: true,
					buttons: {
						'Обновить страницу': function() {
							window.location.reload();
						},
						'Перейти': function() {
							window.location.href = "/request/request.htm?type=" + $("#typeView").val() + "&id=" + id;
						}
					}
				});
			} else {
				window.location.href = "/request/request.htm?type=" + $("#typeView").val() + "&id=" + id;
			}
		}
	});
}


/*	Очередь	*/

function setQueue() {
	Popup('/request/queue.htm', '', 'noClose');
}


function unsetQueue() {
	Popup('/request/unsetqueue.htm', '', 'noClose');

	$.ajax({
		url: "/request/service/unsetQueue.htm",
		type: "get",
		async: true,
		dataType: 'json',
		evalJSON: true,
		error: function (xml, text) {
			alert(text);
		},
		success: function (text) {
			if (text['status'] == 'success') {
				$('#queueButton').text("Зарегистрироваться в очереди");
				$('#queueButton').unbind("click");
				$('#queueButton').bind("click", function (e) {
					setQueue()
				});
				$("#userSip").html("SIP: <i>нет</i> [<span class='red'>не в очереди</span>]");
				alert("Вы успешно вышли из очереди");
			} else {
				alert(text['message']);
			}
			clousePopup();
		}
	});
}


function setQueueNum(queue, num) {
	var buffer = $("#PopupContent").html();
	$("#PopupContent").html("<div style='width: 200px; padding: 20px; text-align: center'><div class='loader32'/></div>");

	$.ajax({
		url: "/request/service/setQueue.htm",
		type: "get",
		data: "number=" + num + "&queue=" + queue,
		async: true,
		dataType: 'json',
		evalJSON: true,
		error: function (xml, text) {
			$("#PopupContent").html(buffer);
			alert(text);
		},
		success: function (text) {
			if (text['status'] == 'success') {
				$('#queueButton').text("Выйти из очереди");
				$('#queueButton').unbind("click");
				$('#queueButton').bind("click", function (e) {
					unsetQueue()
				});
				clousePopup();
				$("#userSip").html("SIP: <b>" + text['sip'] + "</b> [<span class='green'>" + text['queueName'] + "</span>]");
				alert("Вы зарегистрированы в очереди " + text['queryName']);
			} else if (text['status'] == 'error') {
				alert(text['message']);
				clousePopup();
			} else {
				$("#PopupContent").html(buffer);
				alert("Произошла ошибка");
			}
		}
	});

}

function setStatus(status, typeView) {
	var $popup = $("#PopupContent");

	var additionalParams = '';

	if($('input[name=isReject]', $popup).attr('checked')){
		additionalParams += '&rejectReasonId=' + $('select[name=rejectReasonId]', $popup).val();
	}

	var buffer = $popup.html();

	$popup.html("<div style='width: 200px; padding: 20px; text-align: center'><div class='loader32'/></div>");

	$.ajax({
		url: "/request/service/setGroupAction.htm",
		type: "post",
		data: $("#formRequestList").serialize() + "&status=" + status + "&typeView=" + typeView + additionalParams,
		async: true,
		dataType: 'json',
		evalJSON: true,
		error: function (xml, text) {
			$("#PopupContent").html(buffer);
			alert(text);
		},
		success: function (text) {
			if (text['status'] == 'success') {
				clousePopup();
				window.location.reload();
			} else if (text['error'] != null) {
				alert(text['error']);
				clousePopup();
			} else {
				$("#PopupContent").html(buffer);
				alert("Произошла ошибка");
			}
		}
	});

}


function setGroupAction(typeView) {
	Popup('/request/setGroupAction.htm?typeView=' + typeView, '', 'noClose');
}


function reminder() {
	var i = 0;
	var type = $("#typeView").val();
	var interval = setInterval(function () {

		if (!$("#reminderBlock").is(":visible")) {
			$.ajax({
				url: "/request/callLaterReminder.htm?type=" + type,
				async: true,

				error: function (xml, text) {

				},
				success: function (html) {
					if (!isBlank(html))
						$("#reminderBlock").html(html).fadeIn('slow');
				}
			});
		}
		i++;
		if (i > 100) clearInterval(interval);

	}, 10000);
}

function isBlank(str) {
	return (!str || /^\s*$/.test(str));
}


$("div.helper").mouseover(function () {
	$(this).stop(true).delay(300).children().show();
});
$("div.helper").mouseleave(function () {
	$(".helpEltR").hide();
});

var editOrderKey = true;
$("div.trNoClick").mouseover(function () {
	editOrderKey = false;
});
$("div.trNoClick").mouseleave(function () {
	editOrderKey = true;
});

$("td.trNoClick").mouseover(function () {
	editOrderKey = false;
});
$("td.trNoClick").mouseleave(function () {
	editOrderKey = true;
});
