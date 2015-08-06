/*	Редактировать или просматривать запись */
function editContent (id, pathKey) {
	var pathKey = pathKey || 'library';
		
	$("#popUp").html("");
	$('#modalWin').css("top",(windowCenterY - winDeltaY +getBodyScrollTop())+"px");
	$('#modalWin').css("left",(windowCenterX - winDeltaX)+"px");
	$('#modalWin').show();
	
	var path = '/article/library.htm';
	switch (pathKey) {
		case 'illness' : path = '/article/illness.htm'; break;
		case 'library' : path = '/article/library.htm'; break;
		default :  path = '/article/library.htm';
	} 
	
	
	
	$.ajax({
	  type: "get",
	  url: path,
	  async: false,
	  data: "id="+id,
	  success: function(html){
		$("#popUp").html(html);
		$("#textArticle").cleditor({
			width:        820,
			height:       400,
			controls:
						"bold italic underline "+
						"style | bullets numbering | " +
						" undo redo | " +
						" image link unlink | "+
						" source ",
			styles:         [["Header 1", "<h1>"], ["Header 2", "<h2>"], ["Header 3", "<h3>"], ["Paragraph", "<div>"]],
			useCSS:       false,
			bodyStyle:    "font:10pt Arial,Verdana; cursor:text; "
		});
		// Huck
		$("#popUp iframe").css("height","373px");
	  }
	});
	
}	 




function saveContent ( form , pathKey) {
	var pathKey = pathKey || 'library';
	
	var form = form || '#editForm';
	
	var path = '';
	switch (pathKey) {
		case 'illness' : path = '/article/service/editIllness.htm'; break;
		case 'library' : path = '/article/service/editData.htm'; break;
		default :  path = '';
	} 
	
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
  				editContent (text['id'], pathKey);
			} else {
				$("#statusWin").html("<span class='red'>Внимание! "+text['error']+"</span>").show().delay(3000).fadeOut(400);
			}
			modalWinKey = 'reload';
  		}
	});	
}	


function deleteContent ( id , pathKey) {
	var pathKey = pathKey || 'library';
	
	if ( confirm("Статья будет удалена!!! Вы подтверждаете удаление?") ) {
		//alert("docback"+path+"?"+$(form).serialize());
		
		var path = '';
		switch (pathKey) {
			case 'illness' : path = '/article/service/deleteIllness.htm'; break;
			case 'library' : path = '/article/service/deleteData.htm'; break;
			default :  path = '';
		} 
		
		$.ajax({
	  		url: path,
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
