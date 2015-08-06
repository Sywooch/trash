(function($) {
      
 
  // Define the hello button
  $.cleditor.buttons.newImg = {
    name: "newImg",
    image: "../../img/ui/newimg.gif",
    title: "Добавление изображения с выравниванием",
    command: "inserthtml",
    popupName: "newImg",
    popupClass: "cleditorPrompt",
    popupContent: "Адрес изображения:<br/>"+
									"<table cellpadding='0' cellspacing='10' width='220' border='0'>"+
									"<tr>"+
									"<td colspan='2'><input type='text' id='newImg' style='width:200px;' value='http://'/></td>"+
									"</tr>"+
									"<tr>"+
									"<td><label for='toLeft'>По левому краю</label></td>"+
									"<td><input type='radio' id='toLeft' value='toLeft' name='newImgFloat'/></td>"+
									"</tr>"+
									"<tr>"+
									"<td><label for='toRight'>По правому краю</label></td>"+
									"<td><input type='radio' id='toRight' value='toRight' name='newImgFloat'/></td>"+
									"</tr>"+
									"<tr>"+
									"<td><label for='noFloat'>Без выравнивания</label></td>"+
									"<td><input type='radio' id='noFloat' value='noFloat' name='newImgFloat' checked='checked'/></td>"+
									"</tr>"+
									"<tr>"+
									"<td><label for='noFloatBlock'>На отдельной строке</label></td>"+
									"<td><input type='radio' id='noFloatBlock' value='noFloatBlock' name='newImgFloat'/></td>"+
									"</tr>"+
									"</table>"+
									"<input type=button value='Submit'/>",
    buttonClick: threeLink
  };
      
    /*
		/shablon/1336817532_1345633997.jpg
		*/
 
  // Handle the hello button click event
  function threeLink(e, data) {
      
 
    // Wire up the submit button click event
    $(data.popup).children(":button")
      .unbind("click")
      .bind("click", function(e) {
      
 
        // Get the editor
        var editor = data.editor;
      
 
        // Get the entered name
				
				
				
        var newImgFloat = $(data.popup).find(" :checked").val();
				var newImgFloatStyle;
				
				
				if(newImgFloat == 'toLeft'){
					newImgFloatStyle = 'float:left;margin:10px 10px 10px 0px;';
				}else if(newImgFloat == 'toRight'){
					newImgFloatStyle = 'float:right;margin:10px 0px 10px 10px;';
				}else if(newImgFloat == 'noFloat'){
					newImgFloatStyle = '';
				}else if(newImgFloat == 'noFloatBlock'){
					newImgFloatStyle = 'display:block;margin:0 auto;';
				}
      
// alert(link1);

        // Insert some html into the document
        var html = '<img alt="" src="'+$("#newImg").val()+'" style="'+newImgFloatStyle+'"/>';
				
				
        editor.execCommand(data.command, html, null, data.button);
      
 
        // Hide the popup and set focus back to the editor
        editor.hidePopups();
        editor.focus();
      
 
      });
      
 
  }
      
 
})(jQuery);