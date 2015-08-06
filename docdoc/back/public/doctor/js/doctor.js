/*	Редактировать или просматривать запись */
function editContent(id) {

	$("#popUp").html("");
	$('#modalWin').css("top", (windowCenterY - winDeltaY + getBodyScrollTop()) + "px");
	$('#modalWin').css("left", (windowCenterX - winDeltaX) + "px");
	$('#modalWin').show();

	$.ajax({
		type: "get",
		url: "/doctor/doctor.htm",
		async: false,
		data: "id=" + id,
		success: function (html) {
			$("#popUp").html(html);
			$("#textSpec").cleditor({
				width: 566,
				height: 150,
				controls: "bold italic underline " +
				"style | bullets numbering | " +
				" undo redo | " +
				" image link unlink | " +
				" source ",
				styles: [["Header 1", "<h1>"], ["Header 2", "<h2>"], ["Header 3", "<h3>"], ["Paragraph", "<div>"]],
				useCSS: false,
				bodyStyle: "font:10pt Arial,Verdana; cursor:text; "
			});
			// Huck
			$("#popUp iframe").css("height", "150px");
		}
	});
}


function saveContent(form, path) {
	var path = path || '/doctor/service/editData.htm';
	var form = form || '#editForm';

	//alert("docback"+path+"?"+$(form).serialize());
	$.ajax({
		url: path,
		type: "post",
		data: $(form).serialize(),
		async: true,
		dataType: 'json',
		evalJSON: true,
		error: function (xml, text) {
			alert(text);
		},
		success: function (text) {
			if (text['status'] == 'success') {
				$("#statusWin").html("<span class='green'>Данные успешно сохранены!</span>").show().delay(3000).fadeOut(400);
				editContent(text['id']);
			} else {
				$("#statusWin").html("<span class='red'>Внимание! " + text['error'] + "</span>").show().delay(3000).fadeOut(400);
			}
			modalWinKey = 'reload';
		}
	});
}


function deleteContent(id) {
	$.ajax({
		url: "/2.0/doctor/delete",
		type: "post",
		error: function (xml, text) {
			alert(text);
		},
		success: function (html) {
			bindDeleteDoctor(html);
		}
	});
}

function moderateContent(id, row) {
	var $modal = $('#modalWin');

	$("#popUp").html("");
	$modal.css("top", (windowCenterY - winDeltaY + getBodyScrollTop()) + "px");
	$modal.css("left", (windowCenterX - winDeltaX) + "px");
	$modal.width(850);
	$modal.show();

	$.ajax({
		type: "get",
		url: "/2.0/doctor/moderation",
		async: false,
		data: "id=" + id,
		success: function (html) {
			$("#popUp").html(html);
			$("#popUp iframe").css("height", "150px");
			$('h1', $modal).html($('td.fio a', row).html());

			$(".js-save", $modal).click(function() {
				if (!$('#DoctorModerationForm input:checked', $modal).length) {
					$('.modWinClose', $modal).trigger('click');
					return;
				}

				$.ajax({
					url: "/2.0/doctor/moderationApply",
					type: "post",
					data: $('#DoctorModerationForm', $modal).serialize(),
					success: function (json) {
						if (json.success) {
							window.location.reload();
						} else {
							alert(json.errorMsg);
						}
					}
				});
			});

			$(".js-close", $modal).click(function() {
				$('.modWinClose', $modal).trigger('click');
			});

			$('input.apply', $modal).change(function() {
				if (this.checked) $('input.reset', $(this).closest('tr')).removeAttr('checked');
			});

			$('input.reset', $modal).change(function() {
				if (this.checked) $('input.apply', $(this).closest('tr')).removeAttr('checked');
			});

			$('a.apply_all', $modal).click(function() {
				$('input.apply', $modal).attr('checked','checked');
				$('input.reset', $modal).removeAttr('checked');
			});

			$('a.reset_all', $modal).click(function() {
				$('input.apply', $modal).removeAttr('checked');
				$('input.reset', $modal).attr('checked','checked');
			});
		}
	});
}

function bindDeleteDoctor(html)
{
	$('#deleteDoctorWin').css("top", (windowCenterY + getBodyScrollTop()) + "px");
	$('#deleteDoctorWin').css("left", (windowCenterX - 380) + "px");
	$('#deleteDoctorWin').show();
	$("#deleteDoctorWin #popUp").html(html);
	$("#deleteDoctorWin").show();

	$("#doctor").autocomplete("/2.0/doctor/getItems",{
		delay:10,
		minChars:1,
		max:1,
		autoFill:false,
		selectOnly:true,
		formatResult: function(row) {
			console.log(row);
			$("#doctor").val(row[1]);
			$("#doctorName").html(row[0]);
			return row[1];
		},
		formatItem: function(row, i, max) {
			$("#doctor").val(row[1]);
			$("#doctorName").html(row[0]);
			return row[1];
		}
	});

	$(".js-close").click(function(){
		$("#deleteDoctorWin .modWinClose").trigger('click');
	});

	$(".js-save").click(function(){
		var id = $("#doctor").val();
		var dublId = $("#doctorId").val();
		if (confirm("Врач будет удален!!! Вы подтверждаете удаление?")) {
			$.ajax({
				url: "/2.0/doctor/delete",
				type: "post",
				data: {
					"id": id,
					"dublId": dublId
				},
				success: function () {
					window.location.reload();
				}
			});
		}
	});

}



function addCollegue() {
	var title = $("#study").val();
	var type = $("#studyType").val();
	var year = $("#studyYear").val();

	//alert("docback"+"/doctor/service/addCollegue.htm?"+"title="+title+"&type="+type+"&year="+year);

	$.ajax({
		url: "/doctor/service/addCollegue.htm",
		type: "post",
		data: "title=" + title + "&type=" + type,
		async: true,
		dataType: 'json',
		evalJSON: true,
		error: function (xml, text) {
			alert(text);
		},
		success: function (text) {
			if (text['status'] == 'success') {
				var str_type = "";
				var str_type_name = "";
				pos = pos + 1;
				str_type = getUniversityType(text['type']);
				var formLine = "<input type='hidden' name='educationId[" + pos + "]' value='" + text['id'] + "'/>" +
					"<input type='hidden' name='educationYear[" + pos + "]' value='" + year + "'/>";
				$('#eduList').append("<tr id='eduLine_" + pos + "'><td>" + formLine + "<strong title='" + str_type_name + "'>" + str_type + "</strong></td><td>" + text['title'] + "</td><td>" + year + "</td><td><img style='cursor:pointer' onclick='$(\"#eduLine_" + pos + "\").remove()' src='/img/icon/delete.png'/></td></tr>");
				$("#study").val("");
				$("#studyType").val("");
				$("#studyYear").val("");
			} else {
				$("#statusWin").html("Внимание! Ошибки: " + text['error']).show().delay(3000).fadeOut(400);
			}
			modalWinKey = 'reload';
		}
	});

}


function getUniversityType(typeId) {
	var str_type = "";
	var str_type_name = "";

	switch (typeId) {
		case 'university' :
		{
			str_type = "В";
			str_type_name = 'ВУЗ';
		}
			break;
		case 'college' :
		{
			str_type = "К";
			str_type_name = 'Колледж';
		}
			break;
		case 'traineeship' :
		{
			str_type = "О";
			str_type_name = 'Ординатура';
		}
			break;
		case 'graduate' :
		{
			str_type = "А";
			str_type_name = 'Аспирантура';
		}
			break;
		case 'internship' :
		{
			str_type = "И";
			str_type_name = 'Интернатура';
		}
			break;
		default :
			str_type = "-";
	}
	return str_type;
}


function checkAlias(eltVal, id) {
	$.ajax({
		url: "/doctor/service/checkAlias.htm",
		type: "get",
		data: "q=" + eltVal + "&id=" + id,
		async: true,
		success: function (text) {
			if (text == '0') {
				$("#aliasCheck").html("<span class='green'>Ок!</span>");
			} else if (text != '-1') {
				$("#aliasCheck").html("<span class='red'>Занято</span>");
			}
		}
	});
}


function checkFIO(eltVal) {
	$.ajax({
		url: "/doctor/service/checkFIO.htm",
		type: "get",
		data: "q=" + eltVal,
		dataType: 'json',
		evalJSON: true,
		async: true,
		success: function (text) {
			if (text['count'] == '0') {
				$("#fioCheck").html("<span class='green'>Ок. Врач уникален!</span>");
			} else {
				var idList = text['list'];
				$("#fioCheck").html("<span class='red'>Внимание возможный повтор: </span>");
				for (var i = 0; i < idList.length; i++) {
					$("#fioCheck span.red").append("<span class='link' onclick='editContent(\"" + idList[i]['id'] + "\")'>" + idList[i]['name'] + "</span>, ");
				}
			}
		}
	});
}


function checkAddPhone(eltVal, id) {
	$.ajax({
		url: "/doctor/service/checkAddPhone.htm",
		type: "get",
		data: "q=" + eltVal + "&id=" + id,
		async: true,
		success: function (text) {
			if (text == '0') {
				$("#addPhoneCheck").html("<span class='green'>Ок!</span>");
			} else if (text != '-1') {
				$("#addPhoneCheck").html("<span class='red'>Занято</span>");
			}
		}
	});
}


function loadImg(id) {
	$("#imgWin div.modWinContent").html("");
	$('#imgWin').css("top", (windowCenterY - 100 + getBodyScrollTop()) + "px");
	$('#imgWin').css("left", (windowCenterX - 600) + "px");
	$('#imgWin').show();

	$.ajax({
		type: "get",
		data: {
			id: id,
			headers: 0
		},
		url: "/doctor/chImage.htm",
		async: false,
		success: function (html) {
			$("#imgWin div.modWinContent").html(html);
		}
	});
}

function deleteImg(id) {
	$.ajax({
		url: "/doctor/service/saveImages.htm",
		type: "get",
		data: "id=" + id + "&delete=1",
		dataType: 'json',
		evalJSON: true,
		async: true,
		error: function (xml, text) {
			alert(text);
		},
		success: function (text) {
			if (text['status'] == 'success') {
				editContent(id);
			} else {
				alert(text['error']);
			}
		}
	});
}

function saveShablon(id) {
	//alert("/doctor/service/saveImages.htm?"+$("#cropForm").serialize());
	$.ajax({
		url: "/doctor/service/saveImages.htm",
		type: "get",
		data: $("#cropForm").serialize(),
		dataType: 'json',
		evalJSON: true,
		async: true,
		error: function (xml, text) {
			alert(text);
		},
		success: function (text) {
			if (text['status'] == 'success') {
				loadImg(id);
			} else {
				alert(text['error']);
			}
		}
	});
}


function setMetroList(clinicId) {
	$.ajax({
		url: "/doctor/service/getMetroList4Clinic.htm",
		type: "get",
		data: "id=" + clinicId,
		async: true,
		error: function (xml, text) {
			alert(text);
		},
		success: function (html) {
			$("#metro").val(html);
		}
	});
}
