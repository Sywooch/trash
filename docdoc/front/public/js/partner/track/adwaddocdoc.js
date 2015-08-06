$(function() {
	$(document).on('requestCreated', function(e, form, req_id){
		$(document.body).append('<iframe src="http://track.adwad.ru/SL2LH?adv_sub=' + req_id + '" scrolling="no" frameborder="0" width="1" height="1"></iframe>');
	});
});
