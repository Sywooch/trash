var nameObject = widget.byClass("dd-request-text-field-name");
var phoneObject = widget.byClass("dd-request-text-field-phone");

DdEvent.add(widget.byClass("dd-button"), 'click', function(e) {

	var name = nameObject.value;
	var phone = phoneObject.value.replace(/\D/g, '');
	var errorNameRequestEmpty = widget.byClass("dd-name-request-empty");
	var errorPhoneRequestEmpty = widget.byClass("dd-phone-request-empty");
	var errorPhoneRequestIncorrect = widget.byClass("dd-phone-request-incorrect");

	errorNameRequestEmpty.style.display = 'none';
	errorPhoneRequestEmpty.style.display = 'none';
	errorPhoneRequestIncorrect.style.display = 'none';

	if (!name || name === "Ваше имя") {
		errorNameRequestEmpty.style.display = 'block';
	} else if (!phone) {
		errorPhoneRequestEmpty.style.display = 'block';
	} else if (
		phone.length < 10
		|| phone.length > 11
		|| (phone.length == 11 && (parseInt(phone[0]) < 7 || parseInt(phone[0]) > 8))
	) {
		errorPhoneRequestIncorrect.style.display = 'block';
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

DdEvent.add(document, 'Request_created_' + widget.options.id, function(e){
	widget.byClass("dd-request-form").style.display = 'none';
	if (e.data.status === true) {
		widget.byClass("dd-success-message").style.display = 'block';
	} else {
		widget.byClass("dd-error-message").style.display = 'block';
	}
});

setPlaceholder(nameObject);
setPlaceholder(phoneObject);
setPhoneMask(phoneObject);