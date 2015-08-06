$(function() {
	$(document).on('requestCreated', function(e, form, req_id) {
		$(document.body).append('<img src="//n.actionpay.ru/ok/5426.png?actionpay=' + getCookie('partner_client_uid') + '&apid=' + req_id + '" height="1" width="1" />');
	});
});
