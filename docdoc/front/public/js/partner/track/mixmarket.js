$(document).ready(function () {
	$(document).on('requestCreated', function (e, obj, req_id) {
		$(document.body).append('<img src="http://mixmarket.biz/uni/tev.php?id=1294941946&r='
			+ escape(document.referrer)
			+ '&t=' + (new Date()).getTime() + '&a1=' + req_id + '" width="1" height="1"/>'
		);
	})
})
