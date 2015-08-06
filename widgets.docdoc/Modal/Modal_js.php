DdEvent.add(document, 'WidgetContentLoad_' + widget.options.id, function(e, data) {

	//если createRequest, то это показывать не нужно
	if (widget.options.action == "LoadWidget") {
		document.getElementById("dd-sign-up-popup").style.display = 'block';
		document.getElementById("dd-sign-up-overlay").style.display = 'block';
		widget.byClass("dd-widget-modal").setAttribute("class", "dd-widget dd-widget-modal " + widget.options.themeForCss);
		var phone = document.getElementById("dd-partner-phone");
		if (widget.options.partnerPhone != undefined && widget.options.partnerPhone != "") {
			phone.style.display = "block";
			document.getElementById("dd-partner-phone-value").innerHTML = widget.options.partnerPhone;
		} else {
			phone.style.display = "none";
		}
	}

	if (widget.alreadyInit != undefined) {
		return;
	}
	widget.alreadyInit = true;

	DdEvent.add(document.getElementById("dd-sign-up-overlay"), 'click', function(e) {
		document.getElementById("dd-sign-up-popup").style.display = 'none';
		document.getElementById("dd-sign-up-overlay").style.display = 'none';
		document.getElementById("dd-sign-up-popup-success").style.display = 'none';
		document.getElementById("dd-sign-up-popup-error").style.display = 'none';
		return false;
	});

	DdEvent.add(document.getElementById("dd-sign-up-popup-close"), 'click', function(e) {
		document.getElementById("dd-sign-up-popup").style.display = 'none';
		document.getElementById("dd-sign-up-overlay").style.display = 'none';
		document.getElementById("dd-sign-up-popup-success").style.display = 'none';
		document.getElementById("dd-sign-up-popup-error").style.display = 'none';
		return false;
	});

	DdEvent.add(widget.byClass("dd-submit"), 'click', function(e) {
		var name = document.getElementById("dd-name").value;
		var phone = document.getElementById("dd-phone").value.replace(/\D/g, '');

		document.getElementById("dd-name-empty").style.display = 'none';
		document.getElementById("dd-phone-empty").style.display = 'none';
		document.getElementById("dd-phone-incorrect").style.display = 'none';

		if (!name) {
			document.getElementById("dd-name-empty").style.display = 'block';
		} else if (!phone) {
			document.getElementById("dd-phone-empty").style.display = 'block';
		} else if (
			phone.length < 10
				|| phone.length > 11
				|| (phone.length == 11 && (parseInt(phone[0]) < 7 || parseInt(phone[0]) > 8))
			) {
			document.getElementById("dd-phone-incorrect").style.display = 'block';
		} else {
			this.setAttribute("disabled", "disabled");
			widget.options.clientName = name;
			widget.options.phone = phone;
			widget.options.action = "CreateRequest";
			widget.createUrl();
			widget.loadWidget();
			widget.sendBq();
		}

		return false;
	});

	DdEvent.add(document.getElementById('dd-name'), 'keyup', function() {
		document.getElementById("dd-name-empty").style.display = 'none';
		if (document.getElementById('dd-name').value !== "") {
			document.getElementById("dd-success-name").style.display = 'block';
		} else {
			document.getElementById("dd-success-name").style.display = 'none';
		}
	});

	DdEvent.add(document.getElementById('dd-phone'), 'keyup', function() {
		document.getElementById("dd-phone-empty").style.display = 'none';
		if (this.value.length == 14) {
			document.getElementById("dd-success-phone").style.display = 'block';
		} else {
			document.getElementById("dd-success-phone").style.display = 'none';
		}
	});

	DdEvent.add(document, 'Request_created_' + widget.options.id, function(e){
		widget.byClass("dd-sign-up-popup").style.display = 'none';

		widget.byClass("dd-submit").removeAttribute("disabled");
		if (e.data.status === true) {
			widget.byClass("dd-sign-up-popup-success").style.display = 'block';
		} else {
			widget.byClass("dd-sign-up-popup-error").style.display = 'block';
		}

	});

	DdEvent.add(document.getElementById("dd-submit-success"), 'click', function(e) {
		document.getElementById("dd-sign-up-popup-success").style.display = 'none';
		document.getElementById("dd-sign-up-overlay").style.display = 'none';
		return false;
	});

	DdEvent.add(document.getElementById("dd-submit-error"), 'click', function(e) {
		document.getElementById("dd-sign-up-popup-error").style.display = 'none';
		document.getElementById("dd-sign-up-overlay").style.display = 'none';
		return false;
	});

	setPhoneMask(document.getElementById("dd-phone"));
});
