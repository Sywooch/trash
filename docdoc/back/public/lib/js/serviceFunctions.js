/*
 * Панель скрыть/показать
 * elt - управляющий элемент (ссылка)
 * blockName - id блока, который необходимо скрыть или показать
 * 
 * */
function switchPanel (elt, blockName) {
	if ( $(elt).hasClass('switchOn') ) {
		$(elt).removeClass('switchOn');
		$(elt).addClass('switchOff');
		$('#'+blockName).show();
	} else if ( $(elt).hasClass('switchOff') ) {
		$(elt).removeClass('switchOff');
		$(elt).addClass('switchOn');
		$('#'+blockName).hide();
	} 			
}	  




