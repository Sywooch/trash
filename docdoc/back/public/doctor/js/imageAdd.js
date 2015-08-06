/*	
	Работа с изображением
	Набор JS функций для страницы /image/xsl/imgAdd.xsl	
	Дополнительный PopUp в страницах редактирования статей и видео
*/

	
			
			
/*	getImgContent - получение контента для заданного Id изображения		*/			
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



/*	saveImgAddContent - сохранение описания изображения	*/	
function saveImgAddContent () {	
	$.ajax({
  		url: "/service/editData.htm",
		type: "post",
		data: $("#editImgForm").serialize(),
  		async: true,
		dataType: 'json',
		evalJSON: 	true,
		error: function(xml,text){
			alert(text);
		},
  		success: function(text){
			if (text['status'] == 'success') {
				getImgContent(text['id'], $("#imgWin .modWinContent"));
			}
  		}
	});	
}



/*	saveShablon - Сохранение шаблона изображения */
function saveShablon () {
	$.ajax({
  		url: "/doctor/service/saveImages.htm",
		type: "post",
		data: $("#cropForm").serialize(),
		dataType: 'json',
		evalJSON: 	true,
  		async: true,
		error: function(xml,text){
			alert(text);
		},
  		success: function(text){
			if (text['status'] == 'success') {
				getImgContent(imgId, $("#imgWin .modWinContent"));
			} else {
				alert(text['error']);
			}
  		}
	});
}