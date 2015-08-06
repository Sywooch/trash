$('document').ready(function(){
	// Изменяем пользовательскую настройку доступности для крона.
	$('.isAvailabeGlobal').click(function(){
		var croneName = $(this).attr('cronename');
		
		var target = $(this);
		$.post('/adminservice/service/isAvailableGlobalToggle.php', {croneName: croneName}, function(data){
			if(data){
				var data = eval('('+data+')');
				$(target).html(data.content);
				$(target).removeClass('isAvailableGlobal_true').removeClass('isAvailableGlobal_false').removeClass('isAvailableGlobal_notExists');
				$(target).addClass('isAvailableGlobal_'+data.content);
			}
		});
	});
	
	
	
	
	// Изменяем системную настройку доступности для крона.
	$('.isAvailable').click(function(){
		var croneName = $(this).attr('cronename');
		
		var target = $(this);
		$.post('/adminservice/service/isAvailableToggle.php', {croneName: croneName}, function(data){
			if(data){
				var data = eval('('+data+')');
				$(target).html(data.content);
				$(target).removeClass('isAvailable_true').removeClass('isAvailable_false').removeClass('isAvailable_notExists');
				$(target).addClass('isAvailable_'+data.content);
			}
		});
	});
});




function sendMailFromQuery() {
	$("#mailSendStatus").html("");
	$("#mailSendStatistic").html("");
	$("#mailSendStatus").attr("class","load");
	$.ajax({
  		url: "/adminservice/service/sendMailFromQuery.php",
  		async: true,
  		dataType: 'json',
		evalJSON: 	true,
		error: function(xml,text){
			$("#callStatus").removeClass("load");
			alert(text);
		},
  		success: function(text){
  			$("#mailSendStatus").removeClass("load");
  			if (text['status'] == 'success') {
  				$("#mailSendStatus").html("<span class='green'>Обработано: "+text['readMessage']+". Отправлено "+text['sendMessage']+" писем</span>");
			} else {
				$("#mailSendStatus").html("<span class='red'>Внимание! "+text['error']+"</span>");
			}
  		}
	});	
}



function clearMailQuery() {
	$("#mailClearStatus").html("").attr("class","load");
	$.ajax({
  		url: "/adminservice/service/clearMailQuery.php",
  		async: true,
  		dataType: 'json',
		evalJSON: 	true,
		error: function(xml,text){
			$("#callStatus").removeClass("load");
			alert(text);
		},
  		success: function(text){
  			$("#mailClearStatus").removeClass("load");
  			if (text['status'] == 'success') {
  				$("#mailClearStatus").html("<span class='green ml10'>Удалено: "+text['message']+" писем</span>");
			} else {
				$("#mailClearStatus").html("<span class='red'>Внимание! "+text['error']+"</span>");
			}
  		}
	});	
}



function startStopSMSQuery(action) {
	//alert("/adminservice/service/startSMSQuery.php?"+"action="+action);
	$.ajax({
  		url: "/adminservice/service/startSMSQuery.php",
  		data: "action="+action,
  		type: "get",
  		async: true,
  		dataType: 'json',
		evalJSON: 	true,
		error: function(xml,text){
			alert(text);
		},
  		success: function(text){
  			if (text['status'] == 'stop') {
  				$("#smsStarted").attr("class","hd");
  				$("#smsStoped").removeClass("hd");
			} else if (text['status'] == 'start') {
				$("#smsStarted").removeClass("hd");
  				$("#smsStoped").attr("class","hd");
			} else {
				alert(text['error']);
			}
  		}
	});	
}


/*
function clearCrone(croneName) {
	$.ajax({
  		url: "/adminservice/service/clearCrone.php",
  		async: true,
  		data: "crone="+croneName,
  		dataType: 'json',
		evalJSON: 	true,
		error: function(xml,text){
			alert(text);
		},
  		success: function(text){
  			if ( text['status'] == 'success' ) {
  				//alert("Файл блокировки изменён");
  				window.location.reload();
			} else {
				alert(text['error']);
			}
  		}
	});	
}
*/



function clearAsteriskNumber( sip ) {
	$.ajax({
  		url: "/adminservice/service/clearAsteriskQuery.php",
  		data: "sip="+sip,
  		async: true,
  		dataType: 'json',
		evalJSON: 	true,
		error: function(xml,text){
			alert("Ошибка: "+text);
		},
  		success: function(text){
  			if (text['status'] == 'success') {
  				alert("Абонен "+sip+" удален из очереди");
  				window.location.reload();
			} else if (text['error'] != null )  {
				alert(text['error']);
			} else {
				alert("Не получилось удалить");
			}
  		}
	});	
}
