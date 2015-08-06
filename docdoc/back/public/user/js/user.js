/*	Редактировать или просматривать запись */
function editContent (id) {	
	$("#popUp").html("");
	$('#modalWin').css("top",(windowCenterY - winDeltaY +getBodyScrollTop())+"px");
	$('#modalWin').css("left",(windowCenterX - winDeltaX)+"px");
	$('#modalWin').show();
			  
	$.ajax({
	  type: "get",
	  url: "/user/user.htm",
	  async: false,
	  data: "id="+id,
	  success: function(html){
		$("#popUp").html(html);
	  }
	});
}	 




function saveContent () {	
	//alert("docback"+"/user/service/editData.htm?"+$("#editForm").serialize());
	$.ajax({
  		url: $("#editForm").attr('action'),
		type: "post",
		data: $("#editForm").serialize(),
  		async: true,
  		dataType: 'json',
		evalJSON: 	true,
		error: function(xml,text){
			alert(text);
		},
  		success: function(text){
  			if (text['status'] == 'success') {
  				$("#statusWin").html("<span class='green'>Данные успешно сохранены!</span>").show().delay(3000).fadeOut(400);	
			} else {
				$("#statusWin").html("Внимание! Ошибки: "+text['error']).show().delay(3000).fadeOut(400);
			}
			modalWinKey = 'reload';
  		}
	});	
}	



function setPasswd () {	
	if ( checkPass( $("#passwd") ) ) {
		$.ajax({
	  		url: "/user/service/setPassword.htm",
			type: "post",
			data: $("#editForm").serialize(),
	  		async: true,
	  		dataType: 'json',
			evalJSON: 	true,
			error: function(xml,text){
				alert(text);
			},
	  		success: function(text){
	  			if (text['status'] == 'success') {
	  				$("#statusWin").html("<span class='green'>Пароль установлен!</span>").show().delay(3000).fadeOut(400);	
					$('#passwd').val('');
					$('#passLine').hide();
				} else {
					$("#statusWin").html("Внимание! Ошибки: "+text['error']).show().delay(3000).fadeOut(400);
				}
	  		}
		});	
	} else {
		$("#statusWin").html("Внимание! Пароль не прошел валидацию.").show().delay(3000).fadeOut(400);	
	}
}
 

 

function checkPass(element) {
	if( jQuery.trim(element.val()) == '' ){
		return false;
	} else {
		return true;
	}
}




function checkForm (idForm) {
	var key = true;		  
	$("#statusWin").html("");	
	
	//$(idForm+" input,select,textarea").each(function(index){
		if(checkLastName()){
			if(checkFirstName()){
				if(checkMail()){
					return true;
				}else {
					$("#email").next().fadeIn().delay(3000).fadeOut(400);
					var str = "Ошибка! Не все данные введены.";
					$("#statusWin").html(str).show().delay(3000).fadeOut(400);
					return false;
				}
			}else {
				$("#checkFirstName").next().fadeIn().delay(3000).fadeOut(400);
				var str = "Ошибка! Не все данные введены.";
				$("#statusWin").html(str).show().delay(3000).fadeOut(400);
				return false;
			}
		}else{
			$("#checkLastName").next().fadeIn().delay(3000).fadeOut(400);
			var str = "Ошибка! Не все данные введены.";
			$("#statusWin").html(str).show().delay(3000).fadeOut(400);
			return false;
		}

}	 




