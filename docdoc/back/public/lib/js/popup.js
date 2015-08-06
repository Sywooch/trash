function Popup(filePath, dataIn, noClose){
	
	var afterFunction = '';
	if ( $("#PopupOverlay") ) $("#PopupOverlay").remove();
	if ( $("#PopupIn") ) $("#PopupIn").remove();
	
	if (noClose == 'noClose'){
		$('<div class="overlay" id="PopupOverlay"></div>').appendTo('body');
		$('<div id="PopupIn"><div id="PopupContent"></div></div>').appendTo('body');
	} else {
		$('<div class="overlay" id="PopupOverlay" onclick="clousePopup('+afterFunction+'); return false"></div>').appendTo('body');
		$('<div id="PopupIn"><a href="#" id="clousePopup" onclick="clousePopup('+afterFunction+'); return false" title="Закрыть" class="clousePopup"></a><div id="PopupContent"></div></div>').appendTo('body');
	}
	
	
	$.ajax({
		url: filePath,
		type: "post",
		data: dataIn,
		async: true,
		error: function(xml,text){
			alert(text);
		},
		success: function(text){
			$('#PopupContent').html(text);
			$("#PopupOverlay").fadeIn("slow");
			$('#PopupIn').css('top', ($(window).scrollTop() + $(window).height()/2)+'px');
			$('#PopupIn').css('margin-top', (-1)*($('#PopupIn').height()/2)+'px');
			$('#PopupIn').css('margin-left', (-1)*($('#PopupIn').width()/2)+'px');
		}
	});
}

function clousePopup(afterFunctionClouse){
	$("#PopupOverlay").remove();
	$("#PopupIn").remove();
	afterFunctionClouse
}