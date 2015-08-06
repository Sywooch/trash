(function($) {
      
 
  // Define the hello button
  $.cleditor.buttons.threeLink = {
    name: "threeLink",
    image: "../../img/ui/addlinks.gif",
    title: "Добавление 3х ссылок",
    command: "inserthtml",
    popupName: "threeLink",
    popupClass: "cleditorPrompt",
    popupContent: "Добавьте 3 ссылки:<br/>"+
									"<table cellpadding='0' cellspacing='10' width='220' border='0'>"+
									"<tr>"+
									"<td>Загаловок 1<br/><input type='text' id='name1' size='10' maxlength='78'/></td>"+
									"<td>Адрес ссылки 1<br/><input type='text' id='link1' size='10'/></td>"+
									"</tr>"+
									"<tr>"+
									"<td>Загаловок 2<br/><input type='text' id='name2' size='10' maxlength='78'/></td>"+
									"<td>Адрес ссылки 2<br/><input type='text' id='link2' size='10'/></td>"+
									"</tr>"+
									"<tr>"+
									"<td>Загаловок 3<br/><input type='text' id='name3' size='10' maxlength='78'/></td>"+
									"<td>Адрес ссылки 3<br/><input type='text' id='link3' size='10'/></td>"+
									"</tr>"+
									"<tr>"+
									"<td>Загаловок 4<br/><input type='text' id='name4' size='10' maxlength='78'/></td>"+
									"<td>Адрес ссылки 4<br/><input type='text' id='link4' size='10'/></td>"+
									"</tr>"+
									"<tr>"+
									"<td>Загаловок 5<br/><input type='text' id='name5' size='10' maxlength='78'/></td>"+
									"<td>Адрес ссылки 5<br/><input type='text' id='link5' size='10'/></td>"+
									"</tr>"+
									"</table>"+
									"<input type=button value='Submit'/>",
    buttonClick: threeLink
  };
      
 
    
 
  // Handle the hello button click event
  function threeLink(e, data) {
      
 
    // Wire up the submit button click event
    $(data.popup).children(":button")
      .unbind("click")
      .bind("click", function(e) {
      
 
        // Get the editor
        var editor = data.editor;
      
 
        // Get the entered name
				
				
				
        var name1 = $("#name1").val();
        var link1 = $("#link1").val();
      
// alert(link1);

        // Insert some html into the document
        var html;
				html = '<div class="linkInContent rc10">';
				html += '<div class="h1">Это интересно</div>';
				html += '&#8226; <a href='+$("#link1").val()+' title='+$("#name1").val()+'>'+$("#name1").val()+'</a><br/>';
				if ($("#name2").val() != '' && $("#link2").val() != ''){ 
					html += '&#8226; <a href='+$("#link2").val()+' title='+$("#name2").val()+'>'+$("#name2").val()+'</a><br/>';
				}
				if ($("#name3").val() != '' && $("#link3").val() != ''){
					html += '&#8226; <a href='+$("#link3").val()+' title='+$("#name3").val()+'>'+$("#name3").val()+'</a><br/>';
				}
				if ($("#name4").val() != '' && $("#link4").val() != ''){
					html += '&#8226; <a href='+$("#link4").val()+' title='+$("#name4").val()+'>'+$("#name4").val()+'</a><br/>';
				}
				if ($("#name5").val() != '' && $("#link5").val() != ''){
					html += '&#8226; <a href='+$("#link5").val()+' title='+$("#name5").val()+'>'+$("#name5").val()+'</a><br/>';
				}
				html += '</div>';
				
        editor.execCommand(data.command, html, null, data.button);
      
 
        // Hide the popup and set focus back to the editor
        editor.hidePopups();
        editor.focus();
      
 
      });
      
 
  }
      
 
})(jQuery);