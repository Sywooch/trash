/*	Редактировать или просматривать запись */
function editContent (id) {
		
	$("#popUp").html("");
	$('#modalWin').css("top",(windowCenterY - winDeltaY +getBodyScrollTop())+"px");
	$('#modalWin').css("left",(windowCenterX - winDeltaX)+"px");
	$('#modalWin').show();
			  
	$.ajax({
	  type: "get",
	  url: "/opinion/opinion.htm",
	  async: false,
	  data: "id="+id,
	  success: function(html){
		$("#popUp").html(html);
	  }
	});
}	 




function saveContent ( form , path) {
	var path = path || '/opinion/service/editData.htm';
	var form = form || '#editForm';
	
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
  				editContent (text['id']);
			} else {
				$("#statusWin").html("<span class='red'>Внимание! "+text['error']+"</span>").show().delay(3000).fadeOut(400);
			}
			modalWinKey = 'reload';
  		}
	});	
}	




function deleteContent ( id ) {
	if ( confirm("Отзыв будет удален!!! Вы подтверждаете удаление?") ) {
		//alert("docback"+path+"?"+$(form).serialize());
		$.ajax({
	  		url: "/opinion/service/deleteData.htm",
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
					$("#statusWin").html("<span class=\"red\">Ошибки: "+text['error']+"</span>").show().delay(3000).fadeOut(400);
				}
				modalWinKey = 'reload';
	  		}
		});	
	}
}


function getSpecialization( id ) {
	
	$("#specialization").html("");
	$.ajax({
	  	type: "get",
	  	url: "/opinion/getSector.htm",
	  	async: false,
	  	data: "doctor="+id,
	  	success: function(html){
	  		$("#specialization").html(html);
		}
	}); 
}


function getRequestDetail( id ) {
	$("#requestDetail").html("");
	$.ajax({
	  	type: "get",
	  	url: "/opinion/getRequest.htm",
	  	async: false,
	  	data: "request="+id,
	  	success: function(html){
	  		$("#requestDetail").html(html);
		}
	}); 
}


function chImgType ( imgType ) {
	switch (imgType) {
		case 'oper' : $('#imgType').attr('src','/img/icon/receptionist.png');break;
		case 'cont' : $('#imgType').attr('src','/img/icon/business-contact.png');break;
		case 'gues' : $('#imgType').attr('src','/img/icon/earth.png');break;
		default: $('#imgType').attr('src','/img/common/null.gif');
	}
}

function chImgOrigin ( imgType ) {
	switch (imgType) {
		case 'original' : $('#imgOrigin').attr('src','/img/icon/woman.png');break;
		case 'editor' : $('#imgOrigin').attr('src','/img/icon/editor.png');break;
		case 'combine' : $('#imgOrigin').attr('src','/img/icon/yin-yang.png');break;
		default: $('#imgOrigin').attr('src','/img/common/null.gif');
	}
}




