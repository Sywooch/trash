/*	Редактировать или просматривать запись */
function editContent (id, parentId, slide) {
	var parentId = parentId || 0; 
	var slide = slide || '1';
		
	$("#popUp").html("");
	$('#modalWin').css("top",(windowCenterY - winDeltaY +getBodyScrollTop())+"px");
	$('#modalWin').css("left",(windowCenterX - winDeltaX)+"px");
	$('#modalWin').show();
			  
	$.ajax({
	  type: "get",
	  url: "/clinic/clinic.htm",
	  async: false,
	  data: "id="+id+"&parentId="+parentId+"&slide="+slide,
	  success: function(html){
		$("#popUp").html(html);
	  }
	});
}	 




function saveContent ( form , path, slide) {
	var slide = slide || '1';
	
	//alert("docback"+path+"?"+$(form).serialize());
	$.ajax({
  		url: path,
		type: "post",
		data: $(form).serialize(),
  		async: true,
  		dataType: 'json',
		evalJSON: 	true,
		error: function(xml,text){
			alert(text);
		},
  		success: function(text){
  			if (text['status'] == 'success') {
  				$("#statusWin").html("<span class='green'>Данные успешно сохранены!</span>").show().delay(3000).fadeOut(400);
  				editContent (text['id'],text['parentId'], slide);
			} else {
				$("#statusWin").html("<span class='red'>Внимание! "+text['error']+"</span>").show().delay(3000).fadeOut(400);
			}
			modalWinKey = 'reload';
  		}
	});	
}	




function deleteContent ( id ) {
	if ( confirm("Клиника будет удалена!!! Вы подтверждаете удаление?") ) {
		//alert("docback"+path+"?"+$(form).serialize());
		$.ajax({
	  		url: "/clinic/service/deleteData.htm",
			type: "post",
			data: "id="+id,
	  		async: true,
	  		dataType: 'json',
			evalJSON: 	true,
			error: function(xml,text){
				alert(text);
			},
	  		success: function(text){
	  			if (text['status'] == 'success') {
	  				window.location.reload();
				} else {
					$("#statusWin").html("Внимание! Ошибки: "+text['error']).show().delay(3000).fadeOut(400);
				}
				modalWinKey = 'reload';
	  		}
		});	
	}
}



function deleteAdmin ( id,  parentId ) {
	var parentId = parentId || 0; 
	
	if ( confirm("Администратор будет удален из клиники!!! Вы подтверждаете удаление?") ) {
		//alert("docback"+path+"?"+$(form).serialize());
		$.ajax({
	  		url: "/clinic/service/deleteAdmin.htm",
			type: "post",
			data: "id="+id,
	  		async: true,
	  		dataType: 'json',
			evalJSON: 	true,
			error: function(xml,text){
				alert(text);
			},
	  		success: function(text){
	  			if (text['status'] == 'success') {
	  				editContent ( id, parentId, '2' );
				} else {
					$("#statusWin").html("Внимание! Ошибки: "+text['error']).show().delay(3000).fadeOut(400);
				}
				modalWinKey = 'reload';
	  		}
		});	
	}
}





function getLongLat() {
	
	$("#loader").show().html("<img src='/img/common/loading.gif'/>");
	var str = $("#city").val()+ " "+$("#cityStreet").val()+ " "+$("#addressEtc").val();
	$.ajax({
	  	type: "get",
	  	url: "/clinic/service/getLongLat.htm",
	  	async: false,
	  	dataType: 'json',
		evalJSON: 	true,
	  	data: "address="+str,
	  	success: function(text){
			if (text['status'] == 'success') {
  				$("#longitude").val(text['longitude']);
  				$("#latitude").val(text['latitude']);
  				$("#loader").fadeOut(200);
			} else {
				alert('ошиибка получения данных');
			}
		}
	}); 
}


function checkEmail ( eltVal ) {
	$.ajax({
  		url: "/clinic/service/checkEmail.htm",
		type: "get",
		data: "q="+eltVal,
  		async: true,
  		success: function(text){
  			if (text == '-2') {
  					// ничего 
			} else if (text == '0') {
				$("#loginCheck").html("<span class='green'>Ок!</span>");
			} else if (text != '-1') {
				$("#loginCheck").html("<span class='red'>Уже используется</span>");
			} else {
				$("#loginCheck").html("<span class='red'>Ошибка</span>");
			}
  		}
	});
}


function setPasswd () {	
	if ( checkPass( $("#passwd") ) && checkEmailFormat($("#adminEmail")) ) {
		//alert("docback/clinic/service/setPassword.htm?"+$("#editContactForm").serialize());
		$.ajax({
	  		url: "/clinic/service/setPassword.htm",
			type: "post",
			data: $("#editContactForm").serialize(),
	  		async: true,
	  		dataType: 'json',
			evalJSON: 	true,
			error: function(xml,text){
				alert(text);
			},
	  		success: function(text){

				var statusBar = $("#statusWin");
				statusBar.empty();

				if(text.error != undefined){

					var errors;

					if(typeof text.error == 'string'){
						errors = [text.error];
					} else {
						errors = text.error;
					}

					$(errors).each(function(k, v){
						statusBar.append("<span class='red'>Внимание! Ошибки: "+v+"</span>").show().delay(3000).fadeOut(400);
					})
				} else {
					if (text['status'] == 'success') {
						statusBar.html("<span class='green'>Пароль установлен!</span>").show().delay(3000).fadeOut(400);
						$('#passwd').val('');
						$('#passLine').hide();
					} else {
						statusBar.html("<span class='red'>Внимание! Непредвиденный ответ сервера</span>").show().delay(3000).fadeOut(400);
					}
				}

	  		}
		});	
	} else {
		$("#statusWin").html("<span class='red'>Внимание! Поля не прошли проверку.</span>").show().delay(3000).fadeOut(400);	
	}
}


function deleteDiagnostica ( id,  clinicId, parentId ) {
	var parentId = parentId || 0; 
	
	if ( confirm("Исследование будет удалено из клиники!!! Вы подтверждаете удаление?") ) {
		//alert("docback/clinic/service/deleteDiagnosticaData.htm?"+"id="+id+"&clinicId="+clinicId);
		$.ajax({
	  		url: "/clinic/service/deleteDiagnosticaData.htm",
			type: "get",
			data: "id="+id+"&clinicId="+clinicId,
	  		async: true,
	  		dataType: 'json',
			evalJSON: 	true,
			error: function(xml,text){
				alert(text);
			},
	  		success: function(text){
	  			if (text['status'] == 'success') {
	  				editContent ( clinicId, parentId, '4' );
				} else {
					$("#statusWin").html("Внимание! Ошибки: "+text['error']).show().delay(3000).fadeOut(400);
				}
				modalWinKey = 'reload';
	  		}
		});	
	}
}

function checkPass(element) {
	var regex = /[0-9a-z]+/i;
	if(  !regex.test(jQuery.trim(element.val())) ){
		return false;
	} else {
		return true;
	}
}


function checkEmailFormat ( element ) {
	if( jQuery.trim(element.val()) == '' ){
		return false;
	} else {
		return true;
	}
	
}


function checkAlias(eltVal, id) {
	$.ajax({
  		url: "/clinic/service/checkAlias.htm",
		type: "get",
		data: "q="+eltVal+"&id="+id,
  		async: true,
  		success: function(text){
			if (text == '0') {
				$("#aliasCheck").html("<span class='green'>Ок!</span>");
			} else if (text != '-1') {
				$("#aliasCheck").html("<span class='red'>Занято</span>");
			}
  		}
	});
}

function loadImg(id) {
	$("#imgWin div.modWinContent").html("");
	$('#imgWin').css("top",(windowCenterY - 100 +getBodyScrollTop())+"px");
	$('#imgWin').css("left",(windowCenterX - 600)+"px");
	$('#imgWin').show();

	$.ajax({
		type: "get",
		data: 'id='+id,
		url: "/clinic/chImage.htm",
		async: false,
		success: function(html){
			$("#imgWin div.modWinContent").html(html);
		}
	});
}

function deleteImg(id, parentId) {
	$.ajax({
		url: "/clinic/service/saveImages.htm",
		type: "post",
		data: "id=" + id + "&delete=1",
		dataType: 'json',
		evalJSON: true,
		async: true,
		error: function (xml, text) {
			alert(text);
		},
		success: function (text) {
			if (text['status'] == 'success') {
				editContent(id, parentId, 1);
			} else {
				alert(text['error']);
			}
		}
	});
}


function loadPhotos(clinicId) {
	$("#imgWin div.modWinContent").html("");
	$('#imgWin').css("top",(windowCenterY - 100 +getBodyScrollTop())+"px");
	$('#imgWin').css("left",(windowCenterX - 600)+"px");
	$('#imgWin').show();

	$.ajax({
		type: 'get',
		data: 'clinicId=' + clinicId,
		url: '/2.0/clinic/addImage',
		async: false,
		success: function(html) {
			$('#imgWin div.modWinContent').html(html);

			var $uploadPhotos = $('#UploadPhotos');

			var myDropzone = new Dropzone('#UploadPhotos', {
				url: '/2.0/clinic/saveImage?clinicId=' + clinicId,
				thumbnailWidth: 80,
				thumbnailHeight: 80,
				parallelUploads: 20,
				previewTemplate: $('.template', $uploadPhotos)[0].outerHTML,
				autoQueue: false,
				previewsContainer: $('.uploadActions .previews', $uploadPhotos).get(0),
				clickable: $('.uploadActions .addPhoto', $uploadPhotos).get(0)
			});

			$('.template', $uploadPhotos).remove();

			myDropzone.on("success", function(file, response) {
				var $template = $('#ClinicPhotoTemlate').clone();
				$('#ClinicPhotoList').append($template);
				$('img', $template).attr('src', response.url);
				$template
					.attr('id', 'ClinicPhoto_' + response.imgId)
					.data('ImgId', response.imgId)
					.show();
			});

			myDropzone.on("error", function(file, response, xhr) {
				if (xhr.status == 403 || xhr.status == 401) {
					document.location.reload(true);
				}
			});

			myDropzone.on("addedfile", function(file) {
				$(".start", file.previewElement).click(function() {
					myDropzone.enqueueFile(file);
				});
			});

			myDropzone.on("sending", function(file) {
				$(file.previewElement).remove();
			});

			$(".uploadActions .startUpload", $uploadPhotos).click(function() {
				myDropzone.enqueueFiles(myDropzone.getFilesWithStatus(Dropzone.ADDED));
			});
			$(".uploadActions .cancelUpload", $uploadPhotos).click(function() {
				myDropzone.removeAllFiles(true);
			});
		}
	});
}

function deletePhoto(imgId) {
	if (confirm('Вы подтверждаете удаление ?')) {
		$.ajax({
			type: 'get',
			data: 'imgId=' + imgId,
			url: '/2.0/clinic/deleteImage',
			async: false,
			success: function (response) {
				$('#ClinicPhoto_' + imgId).remove();
			}
		});
	}
}


function saveShablon(imgId) {
	$.ajax({
		url: "/clinic/service/saveImages.htm",
		type: "post",
		data: {
			id: imgId,
			fileName: $('.fileName').val()
		},
		dataType: 'json',
		evalJSON: true,
		async: true,
		error: function(xml,text){
			alert(text);
		},
		success: function(text){
			if (text['status'] != 'success') {
				alert(text['error']);
			}
		}
	});
}

function getImgContent (id, win) {
	$.ajax({
		url: "/service/image.htm",
		type: "get",
		async: false,
		data: 'id='+id,
		error: function(xml,text){
			alert(text);
		},
		success: function(text){
			$(win).html(text);
		}
	});
}

function moderateClinicContent(id, row) {
	var $modal = $('#moderationWin');

	$('.modWinContent', $modal).html("");
	$modal.css("top", (windowCenterY - winDeltaY + getBodyScrollTop()) + "px");
	$modal.css("left", (windowCenterX - winDeltaX) + "px");
	$modal.width(850);
	$modal.show();

	$.ajax({
		type: "get",
		url: "/2.0/clinic/moderation",
		async: false,
		data: "id=" + id,
		success: function (html) {
			$(".modWinContent", $modal).html(html);
			$(".modWinContent iframe", $modal).css("height", "150px");
			$('h1', $modal).html($('td.clinic_name a', row).html());

			$(".js-save", $modal).click(function() {
				if (!$('#DoctorModerationForm input:checked', $modal).length) {
					$('.modWinClose', $modal).trigger('click');
					return;
				}

				$.ajax({
					url: "/2.0/clinic/moderationApply",
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

/**
 * Редактирование ставок по тарифам
 */
$.bindEditContract = function(){
	$(".add_row").click(function(){
		var calc = $(this).parents(".contract_calc");
		var lastRow = calc.find("li:last");
		calc.find('.contract_costs').append('<li class="row contract_cost">' + calc.find("li:last").html() + '</li>');
		lastRow.find(".add_row").remove();
		$.bindEditContract();
	});

	$(".delete_row").click(function(){
		$(this).parent().remove();
		if ($(this).next(".add_row").length > 0) {
			$(".contract_costs").find("li:last").append('<span class="form add_row">+</span>');
			$.bindEditContract();
		}
	});
};

/**
 * Сохранение тарифов
 */
$.bindSaveClinicData = function(){
	$(".btn-save-clinic-data").click(function() {
		var form = $(this).parents('form');
		if (form.hasClass('calc-form')) {
			form.find("#costs").val(getContractCosts());
			if (form.find("#contract").val() == '') {
				return false;
			}
		} else if (form.hasClass('limits-form')) {
			form.find("#limits").val(getContractLimits());
		}
		$.ajax({
			url: form.attr('action'),
			type: "post",
			dataType: 'json',
			data: form.serialize(),
			success: function(text){
				var message = "";
				if (text['status'] == 'success') {
					message = "<span class='green'>Данные успешно сохранены!</span>";
				} else {
					message = "<span class='green'>Внимание! " + text['error'] + "</span>";
				}
				$("#statusWin").html(message).show().delay(3000).fadeOut(400);
			}
		});
	});

	$(".btn-save-clinic-details").click(function() {
		var form = $('#clinic-details-form');
		$.ajax({
			url: '/2.0/clinic/saveDetails',
			type: "post",
			dataType: 'json',
			data: form.serialize(),
			success: function(text){
				var message = "";
				if (text['status'] == 'success') {
					message = "<span class='green'>Данные успешно сохранены!</span>";
				} else {
					message = "<span class='green'>Внимание! " + text['message'] + "</span>";
				}
				$("#statusWin").html(message).show().delay(3000).fadeOut(400);
			}
		});
	});
};

/**
 * Отображение ставок по тарифу
 */
function renderContractCosts(clinicContractId)
{
	if (clinicContractId != '') {
		$.get("/2.0/clinic/contractCosts/" + clinicContractId, function (data) {
			$(".contract_calc").html(data).show();
			$.bindEditContract();
		});
	} else {
		$(".contract_calc").html('').hide();
	}
}

/**
 * Отображение лимитов
 * @param clinicContractId
 */
function renderContractLimits(clinicContractId)
{
	if (clinicContractId != '') {
		$.get("/2.0/clinic/contractGroupLimits/" + clinicContractId, function (data) {
			$(".contract_group_limits").html(data).show();
		});
	} else {
		$(".contract_group_limits").html('').hide();
	}
}

/**
 * Получение данных о тарифной лестницы из интерфейса
 */
function getContractCosts()
{
	var data = [];
	$(".contract_cost").each(function() {
		var isActive = $(this).children(".active_contract_cost");
		var cost = $(this).children(".cost").val();
		if (cost != '') {
			var contract = new Object;
			contract.serviceId = $(this).children(".service-id").val();
			contract.fromNum = $(this).children(".from_num").val();
			contract.cost = cost;
			if (isActive.attr('checked')) {
				contract.isActive = 1
			} else {
				contract.isActive = 0
			}
			data.push(contract);
		}
	});

	return JSON.stringify(data);
}

/**
 * Получение лимитов из интерфейса
 */
function getContractLimits()
{
	var data = [];
	$(".contract_limit").each(function() {
		var limit = $(this).children(".limit").val();
		if (limit > 0) {
			var groupLimit = new Object;
			groupLimit.groupId = $(this).data('group-id');
			groupLimit.limit = limit;
			data.push(groupLimit);
		}
	});

	return JSON.stringify(data);
}

$().ready(function() {
	if ($("#mobilePhone").length > 0) {
		$("#mobilePhone").mask("+7 (999) 999-99-99", {placeholder: " "});
	}

	$(".contracts-tab").click(function() {
		var id = $("#clinicId").val();
		$.ajax({
			url: "/2.0/clinic/contracts/" + id,
			type: "get",
			error: function(xml,text){
				alert(text);
			},
			success: function(html){
				$("#slide_6").html(html);
				$(function() {
					$("#tabs").tabs();
				});
				$(".calc-form #contract").change(function(){
					var contractClinicId = $(this).val();
					renderContractCosts(contractClinicId);
				});
				$(".limits-form #contract").change(function(){
					renderContractLimits($(this).val());
				});
				$.bindSaveClinicData();
			}
		});
	});

});
