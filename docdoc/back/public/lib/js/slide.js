function chSlide(elt,slide) {	   
	$('#slideNavigator .open').attr('class','close');
	$(elt).parent().attr('class','open');
	$('#slides div.slide').hide();
	$('#slide_'+slide).show();
}