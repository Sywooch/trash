$(function() {
	$(document).on('requestCreated', function(e, form, req_id) {
		$(document.body).append('<img src="http://ad.admitad.com/register/00da8bef3b/script_type/img/payment_type/sale/product/1/cart/0/order_id/' + req_id + '/uid/' + getCookie('partner_client_uid') + '/" width="1" height="1" alt="" />');
	});
});
